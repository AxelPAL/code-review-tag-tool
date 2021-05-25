<?php

namespace App\Http\Livewire;

use App\Contracts\Services\ReportAggregatorServiceInterface;
use App\Models\Comment;
use App\Models\RemoteUser;
use App\Repositories\CommentsRepository;
use App\Repositories\RemoteUsersRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Class Report
 * @package App\Http\Livewire
 * @uses Report::showTagData()
 */
class Report extends Component
{
    private const DEFAULT_USERS_DROPDOWN_VALUE = 0;

    public string $fromDate = '';
    public string $toDate = '';
    public ?int $remoteUserId = null;
    public array $tags = [];
    public array $users = [];
    public array $comments = [];
    public ?int $commentsId = null;

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
        $this->checkComments();
        if (!empty($this->fromDate) && !empty($this->toDate) && !empty($this->remoteUserId)) {
            $report = $this->generateReportData(
                new Carbon($this->filterDateValue($this->fromDate)),
                new Carbon($this->filterDateValue($this->toDate)),
                $this->remoteUserId
            );
            $this->tags = $report->tags;
        }
    }

    public function filterDateValue(string $value): ?string
    {
        return preg_replace('/[^0-9\-]/', '', $value);
    }

    public function loadingComments(): void
    {
    }

    public function showTagData(string $tag): void
    {
        $this->commentsId = $this->remoteUserId;
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
                $this->comments[$comment['parent_remote_id']][] = $comment;
            }
        }
    }

    private function generateReportData(Carbon $fromDate, Carbon $toDate, int $remoteUserId): \App\Models\Report
    {
        return $this->getReportAggregatorService()->prepareReport($fromDate, $toDate, $remoteUserId);
    }

    private function getAllUsers(): array
    {
        $users = ['Choose developer' => self::DEFAULT_USERS_DROPDOWN_VALUE];
        $remoteUsers = $this->getRemoteUsersRepository()->getAll();
        foreach ($remoteUsers as $remoteUser) {
            /** @var RemoteUser $remoteUser */
            $users[$remoteUser->display_name] = $remoteUser->id;
        }
        ksort($users);

        return $users;
    }

    private function getReportAggregatorService(): ReportAggregatorServiceInterface
    {
        return resolve(ReportAggregatorServiceInterface::class);
    }

    private function getRemoteUsersRepository(): RemoteUsersRepository
    {
        return resolve(RemoteUsersRepository::class);
    }

    private function getCommentsRepository(): CommentsRepository
    {
        return resolve(CommentsRepository::class);
    }

    private function checkComments(): void
    {
        if ($this->remoteUserId !== $this->commentsId) {
            $this->comments = [];
        }
    }
}
