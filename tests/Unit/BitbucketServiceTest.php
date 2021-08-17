<?php

namespace Tests\Unit;

use App\Contracts\Services\BitbucketServiceInterface;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserBitbucketToken;
use App\Services\BitbucketService;
use Bitbucket\Api\Repositories\Workspaces;
use Generator;
use Http;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class BitbucketServiceTest extends TestCase
{
    use RefreshDatabase;

    private BitbucketServiceInterface $bitbucketService;

    public function setUp(): void
    {
        parent::setUp();
        $this->prepareDbData();
        $this->prepareData();
    }

    private function prepareDbData(): void
    {
        User::factory()->state(['id' => BitbucketServiceInterface::ADMIN_USER_ID])->create();
        UserBitbucketToken::factory()->create(['user_id' => BitbucketServiceInterface::ADMIN_USER_ID]);
        Setting::factory()->count(2)->state(
            new Sequence(
                [
                    'id' => Setting::BITBUCKET_CLIENT_ID_ID,
                ],
                [
                    'id' => Setting::BITBUCKET_CLIENT_SECRET_ID,
                ],
            )
        )->create();
    }

    private function prepareData(): void
    {
        $this->bitbucketService = app(BitbucketServiceInterface::class);
    }

    public function testGetAvailableWorkspaces()
    {
        $this->instance(
            BitbucketServiceInterface::class,
            $this->partialMock(BitbucketService::class, function (MockInterface $mock) {
                $mock->shouldAllowMockingProtectedMethods()
                     ->shouldReceive('getAllWorkspaces')->andReturn($this->workspaces());
            })
        );
        $this->bitbucketService = app(BitbucketServiceInterface::class);
        $expectedArray          = [
            "test"  => "test",
            "test2" => "test2",
        ];
        $this->assertEquals($expectedArray, $this->bitbucketService->getAvailableWorkspaces());
    }

    public function testGetAvailableRepositories()
    {
        $this->instance(
            BitbucketService::class,
            $this->partialMock(BitbucketService::class, function (MockInterface $mock) {
                $mock->shouldAllowMockingProtectedMethods();
                $mock->shouldReceive('getAllListPages')->andReturn($this->repositories());
                $mock->shouldReceive('getQueryForGettingRepositories')->andReturn(app()->make(
                    Workspaces::class,
                    [
                        'workspace' => 'test',
                    ]
                ));
                $mock->shouldReceive('processRepository');
            })
        );

        $bitbucketService = $this->app->get(BitbucketServiceInterface::class);
        $this->assertCount(1, $bitbucketService->getAvailableRepositories('test'));
//        $this->assertDatabaseCount(Repository::class, 1); //todo make processRepository workable in testing
    }

    public function testGetPullRequests()
    {
        $this->markTestSkipped();
    }

    public function testGetActivePullRequests()
    {
        $this->markTestSkipped();
    }

    public function testGetPullRequestData()
    {
        $this->markTestSkipped();
    }

    public function testGetAllCommentsOfPullRequest()
    {
        $this->markTestSkipped();
    }

    public function testGetOAuthCodeUrl()
    {
        $this->markTestSkipped();
    }

    public function testGetAndSaveOAuthAccessToken()
    {
        $this->markTestSkipped();
    }

    public function testGetUsersInfo()
    {
        $this->markTestSkipped();
    }

    public function testUpdateRemoteUser()
    {
        $this->markTestSkipped();
    }


    public function testRefreshToken()
    {
        Http::fake(
            [
                'https://bitbucket.org/site/oauth2/access_token' => Http::response($this->refreshTokenData()),
            ]
        );
        $this->bitbucketService->refreshToken();
        $userBitbucketToken = UserBitbucketToken::first();
        $dataToCheck        = $this->refreshTokenData();
        unset($dataToCheck['expires_in']);
        foreach ($dataToCheck as $field => $dataElement) {
            $this->assertEquals(
                $dataElement,
                $userBitbucketToken->{$field}
            );
        }
    }

    private function refreshTokenData(): array
    {
        return [
            "scopes"        => "snippet project pipeline pullrequest team account wiki webhook issue",
            "access_token"  => "h_DsbHr42aWPlA2I-FK2gdVBxWUSSlC3FS6mQ7wwnnoJQihtzgzurt_hKe",
            "expires_in"    => 7200,
            "token_type"    => "bearer",
            "state"         => "refresh_token",
            "refresh_token" => "8WqdjjZ3xXwD6GthZc",
        ];
    }

    private function repositories(): Generator
    {
        yield [
            "scm"         => "git",
            "website"     => "",
            "has_wiki"    => true,
            "uuid"        => "{9561dcaa-f739-48ac-af30-cf039145a13d}",
            "links"       => [],
            "fork_policy" => "no_public_forks",
            "full_name"   => "test/test",
            "name"        => "test",
            "project"     => [],
            "language"    => "php",
            "created_on"  => "2014-07-28T07:28:05.847864+00:00",
            "mainbranch"  => [],
            "workspace"   => [
                "slug"  => "test",
                "type"  => "workspace",
                "name"  => "test",
                "links" => [],
                "uuid"  => "{b640c071-f246-45de-badb-a83925bb19af}",
            ],
            "has_issues"  => true,
            "owner"       => [],
            "updated_on"  => "2021-08-17T10:56:37.159419+00:00",
            "size"        => 261017937,
            "type"        => "repository",
            "slug"        => "test",
            "is_private"  => true,
            "description" => "Test description",
        ];
    }

    private function workspaces(): array
    {
        return [
            'pagelen' => 10,
            'values'  =>
                [
                    [
                        'uuid'       => '{7913f8af-b9ec-49b4-ae39-2cd72c5ad1d6}',
                        'links'      =>
                            [
                                'owners'       =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/workspaces/test/members',
                                    ],
                                'hooks'        =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/workspaces/test/hooks',
                                    ],
                                'self'         =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/workspaces/test',
                                    ],
                                'repositories' =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/repositories/test',
                                    ],
                                'html'         =>
                                    [
                                        'href' => 'https://bitbucket.org/test/',
                                    ],
                                'avatar'       =>
                                    [
                                        'href' => 'https://bitbucket.org/workspaces/test/avatar/?ts=1543622550',
                                    ],
                                'members'      =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/workspaces/test/members',
                                    ],
                                'projects'     =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/workspaces/test/projects',
                                    ],
                                'snippets'     =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/snippets/test',
                                    ],
                            ],
                        'created_on' => '2018-12-01T00:02:30.084912+00:00',
                        'type'       => 'workspace',
                        'slug'       => 'test',
                        'is_private' => false,
                        'name'       => 'test',
                    ],
                    [
                        'uuid'       => '{b640c071-f246-45de-badb-a83925bb19af}',
                        'links'      =>
                            [
                                'owners'       =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/workspaces/test2/members',
                                    ],
                                'hooks'        =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/workspaces/test2/hooks',
                                    ],
                                'self'         =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/workspaces/test2',
                                    ],
                                'repositories' =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/repositories/test2',
                                    ],
                                'html'         =>
                                    [
                                        'href' => 'https://bitbucket.org/test2/',
                                    ],
                                'avatar'       =>
                                    [
                                        'href' => 'https://bitbucket.org/workspaces/test2/avatar/?ts=1543622674',
                                    ],
                                'members'      =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/workspaces/test2/members',
                                    ],
                                'projects'     =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/workspaces/test2/projects',
                                    ],
                                'snippets'     =>
                                    [
                                        'href' => 'https://api.bitbucket.org/2.0/snippets/test2',
                                    ],
                            ],
                        'created_on' => '2018-12-01T00:04:34.245569+00:00',
                        'type'       => 'workspace',
                        'slug'       => 'test2',
                        'is_private' => false,
                        'name'       => 'test2',
                    ],
                ],
            'page'    => 1,
            'size'    => 2,
        ];
    }
}
