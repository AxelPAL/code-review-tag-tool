<?php

namespace App\Repositories;

use App\Models\Comment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\LazyCollection;

class CommentsRepository
{
    protected const DB_DATE_FORMAT = 'Y-m-d H:i:s';

    public function save(Comment $comment): bool
    {
        return $comment->save();
    }

    public function findAllByRepositoryDateTimeAndRemoteUserId(
        Carbon $fromDate,
        Carbon $toDate,
        int $remoteUserId
    ): LazyCollection {
        $fromDateString = $fromDate->format(self::DB_DATE_FORMAT);
        $toDateString = $toDate->format(self::DB_DATE_FORMAT);

        return Comment::with('content')->whereHas('pullRequest', function ($query) use ($remoteUserId) {
            return $query->whereRemoteAuthorId($remoteUserId);
        })
            ->whereBetween('repository_created_at', [$fromDateString, $toDateString])
            ->where('isDeleted', '=', false)
            ->get()
            ->lazy();
    }

    public function findByRemoteId(int $id): ?Comment
    {
        return Comment::whereRemoteId($id)->first();
    }

    public function findAllByDateUserAndTag(
        CarbonPeriod $datePeriod,
        int $remoteUserId,
        string $tag
    ): array {
        $fromDateString = $datePeriod->getStartDate();
        $toDateString = $datePeriod->getEndDate();
        $commentQuery = DB::table('comments')
            ->join('comment_contents', 'comments.id', '=', 'comment_contents.comment_id')
            ->join('pull_requests', 'comments.pull_request_id', '=', 'pull_requests.id')
            ->select('comments.id')
            ->whereBetween('comments.repository_created_at', [
                $fromDateString->format(self::DB_DATE_FORMAT),
                $toDateString !== null ? $toDateString->format(self::DB_DATE_FORMAT) : null,
            ])
            ->where('pull_requests.remote_author_id', '=', $remoteUserId)
            ->where('comment_contents.tag', '=', $tag)
            ->get();
        $commentIds = $commentQuery->pluck('id');

        return Comment::with(['remoteUser', 'content', 'children.content', 'children.remoteUser'])
            ->where('isDeleted', '=', false)
            ->findMany($commentIds)
            ->toArray();
    }

    public function getCountByPullRequest(int $pullRequestId): int
    {
        return Comment::wherePullRequestId($pullRequestId)->count('id');
    }
}
