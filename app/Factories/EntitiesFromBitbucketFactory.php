<?php

namespace App\Factories;

use App\Contracts\Services\TagParsingServiceInterface;
use App\Models\Comment;
use App\Models\CommentContent;
use App\Models\PullRequest;
use App\Models\RemoteUser;
use App\Models\Repository;
use App\Repositories\CommentContentsRepository;
use App\Repositories\CommentsRepository;
use App\Repositories\PullRequestsRepository;
use App\Repositories\RemoteUsersRepository;
use App\Repositories\RepositoriesRepository;
use JsonException;
use Log;
use Throwable;

class EntitiesFromBitbucketFactory
{

    public function __construct(
        private RepositoriesRepository $repositoriesRepository,
        private PullRequestsRepository $pullRequestsRepository,
        private CommentsRepository $commentsRepository,
        private CommentContentsRepository $commentContentsRepository,
        private RemoteUsersRepository $remoteUsersRepository,
        private TagParsingServiceInterface $tagParsingService
    ) {
    }

    public function createRepositoryIfNotExists(array $bitbucketApiData): Repository
    {
        $workspace = $bitbucketApiData['workspace']['name'];
        $name = $bitbucketApiData['name'];
        $ownerName = $bitbucketApiData['owner']['display_name'];
        $webLink = $bitbucketApiData['links']['self']['href'];
        $slug = $bitbucketApiData['slug'];
        $language = $bitbucketApiData['language'];
        $uuid = $bitbucketApiData['uuid'];
        $repository = $this->repositoriesRepository->findByUUID($uuid);
        if ($repository === null) {
            $repository = new Repository();
            $repository->web_link = $webLink;
            $repository->name = $name;
            $repository->owner_name = $ownerName;
            $repository->workspace = $workspace;
            $repository->slug = $slug;
            $repository->language = $language;
            $repository->uuid = $uuid;
            $this->repositoriesRepository->save($repository);
        }

        return $repository;
    }

    public function createPullRequestIfNotExists(array $bitbucketApiData): PullRequest
    {
        $pullRequestWebLink = $bitbucketApiData['links']['self']['href'];
        $pullRequest = $this->pullRequestsRepository->findByWebLink($pullRequestWebLink);
        if ($pullRequest === null) {
            $pullRequest = new PullRequest();
            $pullRequest->web_link = $pullRequestWebLink;
            $pullRequest->title = $bitbucketApiData['title'];
            $pullRequest->description = $bitbucketApiData['description'];
            $pullRequest->remote_id = $bitbucketApiData['id'];
            $destination = $bitbucketApiData['destination'];
            $repository = $this->findRepositoryByBitbucketResponseDestination($destination);
            $pullRequest->destination_branch = $destination['branch']['name'];
            $pullRequest->destination_commit = $destination['commit']['hash'];
            $pullRequest->repository_created_at = $bitbucketApiData['created_on'];
            $pullRequest->repository_updated_at = $bitbucketApiData['updated_on'];
            $pullRequest->comment_count = $bitbucketApiData['comment_count'];
            $pullRequest->state = $bitbucketApiData['state'];
            $remoteAuthor = $this->createRemoteUserIfNotExists($bitbucketApiData['author']);
            $pullRequest->remote_author_id = $remoteAuthor->id;
            if (!empty($bitbucketApiData['closed_by'])) {
                $closedByUser = $this->createRemoteUserIfNotExists($bitbucketApiData['closed_by']);
                $pullRequest->closed_by_remote_user_id = $closedByUser->id;
            }
            if ($repository !== null) {
                $pullRequest->repository_id = $repository->id;
            }
            $this->pullRequestsRepository->save($pullRequest);
        }

        return $pullRequest;
    }

    /**
     * @param array $bitbucketApiData
     * @return Comment
     * @throws JsonException
     */
    public function createCommentIfNotExists(array $bitbucketApiData): Comment
    {
        $comment = $this->commentsRepository->findByRemoteId($bitbucketApiData['id']);
        if ($comment === null) {
            $comment = new Comment();
            $comment->remote_id = $bitbucketApiData['id'];
        }
        $comment->web_link = $bitbucketApiData['links']['html']['href'];
        $remoteUser = $this->remoteUsersRepository->findByUUID($bitbucketApiData['user']['uuid']);
        if ($remoteUser === null) {
            $remoteUser = $this->createRemoteUserIfNotExists($bitbucketApiData['user']);
        }
        $comment->remote_user_id = $remoteUser->id;
        $comment->isDeleted = $bitbucketApiData['deleted'];
        $pullRequestWebLink = $bitbucketApiData['pullrequest']['links']['self']['href'];
        $pullRequest = $this->pullRequestsRepository->findByWebLink($pullRequestWebLink);
        if ($pullRequest !== null) {
            $comment->pull_request_id = $pullRequest->id;
        }
        if (!empty($bitbucketApiData['parent'])) {
            $comment->parent_remote_id = $bitbucketApiData['parent']['id'];
        }
        $comment->repository_created_at = $bitbucketApiData['created_on'];
        $comment->repository_updated_at = $bitbucketApiData['updated_on'];
        $comment->remote_id = $bitbucketApiData['id'];
        if (!empty($bitbucketApiData['content']) && $this->tryToSaveComment($comment)) {
            $content = $bitbucketApiData['content'];
            $this->createCommentContentIfNotExists($comment, $content);
        }

        return $comment;
    }

    public function createCommentContentIfNotExists(Comment $comment, array $contentData): CommentContent
    {
        $commentContent = $this->commentContentsRepository->findByCommentId($comment->id);
        if ($commentContent === null) {
            $commentContent = new CommentContent();
        }
        $commentContent->comment_id = $comment->id;
        $commentContent->raw = $contentData['raw'];
        $commentContent->html = $contentData['html'];
        $commentContent->markup = $contentData['markup'];
        $commentContent->type = $contentData['type'];
        $commentContent->tag = $this->tagParsingService->getTagFromCommentContent($contentData['html']);
        $this->commentContentsRepository->save($commentContent);

        return $commentContent;
    }

    public function createRemoteUserIfNotExists(array $bitbucketApiData): RemoteUser
    {
        $remoteUser = $this->remoteUsersRepository->findByUUID($bitbucketApiData['uuid']);
        if ($remoteUser === null) {
            $remoteUser = new RemoteUser();
            $this->setRemoteUserFields($remoteUser, $bitbucketApiData);
            $this->remoteUsersRepository->save($remoteUser);
        }

        return $remoteUser;
    }

    public function createOrUpdateRemoteUser(array $bitbucketApiData): ?RemoteUser
    {
        $remoteUser = $this->remoteUsersRepository->findByUUID($bitbucketApiData['uuid']);
        if ($remoteUser === null) {
            $remoteUser = new RemoteUser();
        }
        $this->setRemoteUserFields($remoteUser, $bitbucketApiData);
        $this->remoteUsersRepository->save($remoteUser);

        return $remoteUser;
    }

    protected function setRemoteUserFields(RemoteUser $remoteUser, array $bitbucketApiData): RemoteUser
    {
        $remoteUser->account_id = $bitbucketApiData['account_id'];
        $remoteUser->nickname = $bitbucketApiData['nickname'];
        $remoteUser->display_name = $bitbucketApiData['display_name'];
        $remoteUser->uuid = $bitbucketApiData['uuid'];
        $remoteUser->web_link = $bitbucketApiData['links']['html']['href'];
        $remoteUser->avatar = $bitbucketApiData['links']['avatar']['href'];

        return $remoteUser;
    }

    /**
     * @param Comment $comment
     * @return bool
     * @throws JsonException
     */
    private function tryToSaveComment(Comment $comment): bool
    {
        $saved = false;
        try {
            $saved = $this->commentsRepository->save($comment);
        } catch (Throwable) {
            Log::warning('cannot save comment', [
                'comment' => json_encode($comment->getAttributes(), JSON_THROW_ON_ERROR),
            ]);
        }
        return $saved;
    }
    private function findRepositoryByBitbucketResponseDestination(array $destination): ?Repository
    {
        $repositoryUuid = $destination['repository']['uuid'];
        return $this->repositoriesRepository->findByUUID($repositoryUuid);
    }
}
