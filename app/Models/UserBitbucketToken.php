<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserBitbucketToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $scopes
 * @property string $access_token
 * @property Carbon|null $expires_at
 * @property string $token_type
 * @property string $state
 * @property string $refresh_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|UserBitbucketToken newModelQuery()
 * @method static Builder|UserBitbucketToken newQuery()
 * @method static Builder|UserBitbucketToken query()
 * @method static Builder|UserBitbucketToken whereAccessToken($value)
 * @method static Builder|UserBitbucketToken whereCreatedAt($value)
 * @method static Builder|UserBitbucketToken whereExpiresAt($value)
 * @method static Builder|UserBitbucketToken whereId($value)
 * @method static Builder|UserBitbucketToken whereRefreshToken($value)
 * @method static Builder|UserBitbucketToken whereScopes($value)
 * @method static Builder|UserBitbucketToken whereState($value)
 * @method static Builder|UserBitbucketToken whereTokenType($value)
 * @method static Builder|UserBitbucketToken whereUpdatedAt($value)
 * @method static Builder|UserBitbucketToken whereUserId($value)
 * @mixin Eloquent
 */
class UserBitbucketToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'scopes',
        'access_token',
        'expires_at',
        'token_type',
        'state',
        'refresh_token',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
