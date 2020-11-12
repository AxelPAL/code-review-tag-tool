<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\PullRequest
 *
 * @property int $id
 * @property string $web_link
 * @property string $title
 * @property string $description
 * @property int $remote_id
 * @property string $destination_branch
 * @property string $destination_commit
 * @property string $repository_created_at
 * @property string $repository_updated_at
 * @property int $comment_count
 * @property string $state
 * @property int $remote_author_id
 * @property int $closed_by_remote_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PullRequest newModelQuery()
 * @method static Builder|PullRequest newQuery()
 * @method static Builder|PullRequest query()
 * @method static Builder|PullRequest whereClosedByRemoteUserId($value)
 * @method static Builder|PullRequest whereCommentCount($value)
 * @method static Builder|PullRequest whereCreatedAt($value)
 * @method static Builder|PullRequest whereDescription($value)
 * @method static Builder|PullRequest whereDestinationBranch($value)
 * @method static Builder|PullRequest whereDestinationCommit($value)
 * @method static Builder|PullRequest whereId($value)
 * @method static Builder|PullRequest whereRemoteAuthorId($value)
 * @method static Builder|PullRequest whereRemoteId($value)
 * @method static Builder|PullRequest whereRepositoryCreatedAt($value)
 * @method static Builder|PullRequest whereRepositoryUpdatedAt($value)
 * @method static Builder|PullRequest whereState($value)
 * @method static Builder|PullRequest whereTitle($value)
 * @method static Builder|PullRequest whereUpdatedAt($value)
 * @method static Builder|PullRequest whereWebLink($value)
 * @mixin Eloquent
 */
class PullRequest extends Model
{
    use HasFactory;
}
