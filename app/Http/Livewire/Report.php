<?php

namespace App\Http\Livewire;

use App\Models\RemoteUser;
use App\Repositories\RemoteUsersRepository;
use App\Services\ReportAggregatorService;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Report extends Component
{
    public string $fromDate = '';
    public string $toDate = '';
    public ?int $remoteUserId = null;
    public array $tags = [];
    public array $users = [];

    protected $queryString = ['fromDate', 'toDate', 'remoteUserId']; //@phpstan-ignore-line

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

    private function generateReportData(Carbon $fromDate, Carbon $toDate, int $remoteUserId): \App\Models\Report
    {
        return $this->getReportAggregatorService()->prepareReport($fromDate, $toDate, $remoteUserId);
    }

    private function getAllUsers(): array
    {
        $users = [];
        $remoteUsers = $this->getRemoteUsersRepository()->getAll();
        foreach ($remoteUsers as $remoteUser) {
            /** @var RemoteUser $remoteUser */
            $users[$remoteUser->id] = $remoteUser->display_name;
        }

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
}
