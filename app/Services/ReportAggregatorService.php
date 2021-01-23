<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Report;
use App\Repositories\CommentsRepository;
use Carbon\Carbon;

class ReportAggregatorService
{
    public function __construct(
        public CommentsRepository $commentsRepository
    )
    {

    }

    public function prepareReport(Carbon $fromDate, Carbon $toDate, int $remoteUserId): Report
    {
        $report = new Report();
        $tags = [];
        $comments = $this->commentsRepository->findAllByRepositoryDateTimeAndRemoteUserId(
            $fromDate,
            $toDate,
            $remoteUserId
        );
        foreach ($comments as $comment) {
            /** @var Comment $comment */
            $tag = $comment->content->tag;
            if ($tag !== null) {
                if (isset($tags[$tag])) {
                    ++$tags[$tag];
                } else {
                    $tags[$tag] = 1;
                }
            }
        }
        foreach ($tags as $tag => $count) {
            $report->addTag($tag, $count);
        }

        return $report;
    }
}