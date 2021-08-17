<?php

namespace Tests\Unit;

use App\Http\Livewire\Report;
use App\Models\Comment;
use App\Models\CommentContent;
use App\Models\PullRequest;
use App\Models\RemoteUser;
use App\Models\Repository;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected const REMOTE_USER_IDS = [1, 2];

    private Report $report;

    public function setUp(): void
    {
        parent::setUp();
        $this->report = app()->get(Report::class);
        $this->prepareData();
    }

    protected function prepareData(): void
    {
        RemoteUser::factory()->count(2)->state(
            new Sequence(
                [
                    'id' => 1,
                ],
                [
                    'id' => 2,
                ],
            )
        )->create();
        Repository::factory()->has(
            PullRequest::factory()->count(2)->has(
                Comment::factory()->count(5)->state(['isDeleted' => false])
                       ->state(
                           new Sequence(
                               ['repository_created_at' => '2021-08-14'],
                               ['repository_created_at' => '2021-08-15'],
                           )
                       )->state(
                           new Sequence(
                               ['remote_user_id' => 1],
                               ['remote_user_id' => 2],
                           )
                       )->has(
                           CommentContent::factory()->state(
                               new Sequence(
                                   ['tag' => 'cs'],
                                   ['tag' => 'design'],
                               )
                           )
                       )
            )->state(
                new Sequence(
                    ['remote_author_id' => 1],
                    ['remote_author_id' => 2],
                )
            )
        )->create();
    }

    public function testLiveWireReport(): void
    {
        $this->report->fromDate     = '2021-08-13 23:59:59';
        $this->report->toDate       = '2021-08-14 00:00:01';
        $this->report->remoteUserId = 1;
        $this->report->prepare();
        $this->assertCount(3, $this->report->users);
        $this->assertEquals(3, $this->report->tags['cs']);
        $this->assertEmpty($this->report->comments);
        $this->report->showTagData('cs');
        $this->assertCount(3, current($this->report->comments));
    }
}
