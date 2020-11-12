<?php

namespace App\Factories;

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

class EntitiesFromBitbucketFactory
{
    /**
     * @var RepositoriesRepository
     */
    private RepositoriesRepository $repositoriesRepository;
    /**
     * @var PullRequestsRepository
     */
    private PullRequestsRepository $pullRequestsRepository;
    /**
     * @var CommentsRepository
     */
    private CommentsRepository $commentsRepository;
    /**
     * @var CommentContentsRepository
     */
    private CommentContentsRepository $commentContentsRepository;
    /**
     * @var RemoteUsersRepository
     */
    private RemoteUsersRepository $remoteUsersRepository;

    public function __construct(
        RepositoriesRepository $repositoriesRepository,
        PullRequestsRepository $pullRequestsRepository,
        CommentsRepository $commentsRepository,
        CommentContentsRepository $commentContentsRepository,
        RemoteUsersRepository $remoteUsersRepository
    ) {
        $this->repositoriesRepository = $repositoriesRepository;
        $this->pullRequestsRepository = $pullRequestsRepository;
        $this->commentsRepository = $commentsRepository;
        $this->commentContentsRepository = $commentContentsRepository;
        $this->remoteUsersRepository = $remoteUsersRepository;
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
        $pullRequest = $this->pullRequestsRepository->findByRemoteId($bitbucketApiData['id']);
        if ($pullRequest === null) {
            $destination = $bitbucketApiData['destination'];
            $pullRequest = new PullRequest();
            $pullRequest->web_link = $bitbucketApiData['links']['self']['href'];
            $pullRequest->title = $bitbucketApiData['title'];
            $pullRequest->description = $bitbucketApiData['description'];
            $pullRequest->remote_id = $bitbucketApiData['id'];
            $pullRequest->destination_branch = $destination['branch']['name'];
            $pullRequest->destination_commit = $destination['commit']['hash'];
            $pullRequest->repository_created_at = $bitbucketApiData['created_on'];
            $pullRequest->repository_updated_at = $bitbucketApiData['updated_on'];
            $pullRequest->comment_count = $bitbucketApiData['comment_count'];
            $pullRequest->state = $bitbucketApiData['state'];
            $remoteAuthor = $this->createRemoteUserIfNotExists($bitbucketApiData['author']);
            if ($remoteAuthor !== null) {
                $pullRequest->remote_author_id = $remoteAuthor->id;
            }
            if (!empty($bitbucketApiData['closed_by'])) {
                $closedByUser = $this->createRemoteUserIfNotExists($bitbucketApiData['closed_by']);
                if ($closedByUser !== null) {
                    $pullRequest->closed_by_remote_user_id = $closedByUser->id;
                }
            }
            $repository = $this->repositoriesRepository->findByUUID($destination['repository']['uuid']);
            if ($repository !== null) {
                $pullRequest->repository_id = $repository->id;
            }
            $this->pullRequestsRepository->save($pullRequest);
        }

        return $pullRequest;
    }

    public function createCommentIfNotExists(array $bitbucketApiData): Comment
    {
        $comment = $this->commentsRepository->findById($bitbucketApiData['id']);
        if ($comment !== null) {
            $comment = new Comment();
            $comment->web_link = $bitbucketApiData['links']['html']['href'];
            $remoteUser = $this->remoteUsersRepository->findByUUID($bitbucketApiData['user']['uuid']);
            if ($remoteUser !== null) {
                $comment->remote_user_id = $remoteUser->id;
            }
            $comment->isDeleted = $bitbucketApiData['deleted'];
            $pullRequest = $this->pullRequestsRepository->findByRemoteId($bitbucketApiData['pullrequest']['id']);
            if ($pullRequest !== null) {
                $comment->pull_request_id = $pullRequest->id;
            }
            $comment->repository_created_at = $bitbucketApiData['created_on'];
            $comment->repository_updated_at = $bitbucketApiData['updated_on'];
            $comment->remote_id = $bitbucketApiData['id'];
            $this->commentsRepository->save($comment);
        }
        if (!empty($bitbucketApiData['content'])) {
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
        $this->commentContentsRepository->save($commentContent);

        return $commentContent;
    }

    public function createRemoteUserIfNotExists(array $bitbucketApiData): RemoteUser
    {
        $remoteUser = $this->remoteUsersRepository->findByUUID($bitbucketApiData['uuid']);
        if ($remoteUser === null) {
            $remoteUser = new RemoteUser();
            $remoteUser->account_id = $bitbucketApiData['account_id'];
            $remoteUser->nickname = $bitbucketApiData['nickname'];
            $remoteUser->display_name = $bitbucketApiData['display_name'];
            $remoteUser->uuid = $bitbucketApiData['uuid'];
            $remoteUser->web_link = $bitbucketApiData['links']['html']['href'];
            $remoteUser->avatar = $bitbucketApiData['links']['avatar']['href'];
            $this->remoteUsersRepository->save($remoteUser);
        }

        return $remoteUser;
    }
}
