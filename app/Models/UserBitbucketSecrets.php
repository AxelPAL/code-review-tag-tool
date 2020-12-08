<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserBitbucketSecrets
 *
 * @property int $id
 * @property int $user_id
 * @property string $client_id
 * @property string $client_secret
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|UserBitbucketSecrets newModelQuery()
 * @method static Builder|UserBitbucketSecrets newQuery()
 * @method static Builder|UserBitbucketSecrets query()
 * @method static Builder|UserBitbucketSecrets whereClientId($value)
 * @method static Builder|UserBitbucketSecrets whereClientSecret($value)
 * @method static Builder|UserBitbucketSecrets whereCreatedAt($value)
 * @method static Builder|UserBitbucketSecrets whereId($value)
 * @method static Builder|UserBitbucketSecrets whereUpdatedAt($value)
 * @method static Builder|UserBitbucketSecrets whereUserId($value)
 * @mixin Eloquent
 */
class UserBitbucketSecrets extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_secret',
    ];
}
