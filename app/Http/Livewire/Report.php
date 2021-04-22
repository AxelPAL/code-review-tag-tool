<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\RemoteUser;
use App\Repositories\CommentsRepository;
use App\Repositories\RemoteUsersRepository;
use App\Services\ReportAggregatorService;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Report extends Component
{
    private const DEFAULT_USERS_DROPDOWN_VALUE = 0;

    public string $fromDate = '';
    public string $toDate = '';
    public ?int $remoteUserId = null;
    public array $tags = [];
    public array $users = [];
    public array $comments = [];

    protected $queryString = ['fromDate', 'toDate', 'remoteUserId']; //@phpstan-ignore-line

    protected $listeners = ['showTagData']; //@phpstan-ignore-line

    public function render(): Factory | View | Application
    {
        $this->prepare();
        return view('livewire.report');
    }

    public function mount(): void
    {
        $this->fill(request());
    }

    public function prepare(): void
    {
        $this->mount();
        $this->users = $this->getAllUsers();
        if (!empty($this->fromDate) && !empty($this->toDate) && !empty($this->remoteUserId)) {
            $report = $this->generateReportData(
                new Carbon($this->fromDate),
                new Carbon($this->toDate),
                $this->remoteUserId
            );
            $this->tags = $report->tags;
        }
    }

    public function showTagData(string $tag): void
    {
        $this->comments = [];
        $fromDate = new Carbon($this->fromDate);
        $toDate = new Carbon($this->toDate);
        $datePeriod = $fromDate->toPeriod($toDate);
        if ($this->remoteUserId !== null) {
            $comments = $this->getCommentsRepository()->findAllByDateUserAndTag(
                $datePeriod,
                $this->remoteUserId,
                $tag
            );
            /** @var Comment[] $comments */
            foreach ($comments as $comment) {
                $this->comments[$comment->parent_remote_id][] = $comment;
            }
        }
    }

    private function generateReportData(Carbon $fromDate, Carbon $toDate, int $remoteUserId): \App\Models\Report
    {
        return $this->getReportAggregatorService()->prepareReport($fromDate, $toDate, $remoteUserId);
    }

    private function getAllUsers(): array
    {
        $users = [self::DEFAULT_USERS_DROPDOWN_VALUE => 'Choose developer'];
        $remoteUsers = $this->getRemoteUsersRepository()->getAll();
        foreach ($remoteUsers as $remoteUser) {
            /** @var RemoteUser $remoteUser */
            $users[$remoteUser->id] = $remoteUser->display_name;
        }
        asort($users);

        return $users;
    }

    private function getReportAggregatorService(): ReportAggregatorService
    {
        return resolve(ReportAggregatorService::class);
    }

    private function getRemoteUsersRepository(): RemoteUsersRepository
    {
        return resolve(RemoteUsersRepository::class);
    }

    private function getCommentsRepository(): CommentsRepository
    {
        return resolve(CommentsRepository::class);
    }
}
