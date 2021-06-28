<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Comment
 *
 * @property int $id
 * @property string $web_link
 * @property int $remote_user_id
 * @property bool $isDeleted
 * @property int $pull_request_id
 * @property string $repository_created_at
 * @property string $repository_updated_at
 * @property int $remote_id
 * @property int|null $parent_remote_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property CommentContent $content
 * @property RemoteUser $remoteUser
 * @property-read Collection|Comment[] $children
 * @property-read int|null $children_count
 * @property-read PullRequest $pullRequest
 *
 * @method static Builder|Comment newModelQuery()
 * @method static Builder|Comment newQuery()
 * @method static Builder|Comment query()
 * @method static Builder|Comment whereCreatedAt($value)
 * @method static Builder|Comment whereId($value)
 * @method static Builder|Comment whereIsDeleted($value)
 * @method static Builder|Comment wherePullRequestId($value)
 * @method static Builder|Comment whereRemoteUserId($value)
 * @method static Builder|Comment whereRepositoryCreatedAt($value)
 * @method static Builder|Comment whereRepositoryUpdatedAt($value)
 * @method static Builder|Comment whereUpdatedAt($value)
 * @method static Builder|Comment whereWebLink($value)
 * @method static Builder|Comment whereRemoteId($value)
 * @method static Builder|Comment whereParentId($value)
 * @mixin Eloquent
 * @method static Builder|Comment whereParentRemoteId($value)
 */
class Comment extends Model
{
    use HasFactory;

    public function content(): HasOne
    {
        return $this->hasOne(CommentContent::class);
    }

    public function pullRequest(): BelongsTo
    {
        return $this->belongsTo(PullRequest::class);
    }

    public function remoteUser(): BelongsTo
    {
        return $this->belongsTo(RemoteUser::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_remote_id', 'remote_id');
    }
}
