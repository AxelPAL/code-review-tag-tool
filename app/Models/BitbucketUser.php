<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\BitbucketUser
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property string $nickname
 * @property string $uuid
 * @property string $link
 * @property string $avatar
 * @property string $type
 * @property string $account_id
 * @method static Builder|BitbucketUser newModelQuery()
 * @method static Builder|BitbucketUser newQuery()
 * @method static Builder|BitbucketUser query()
 * @method static Builder|BitbucketUser whereAccountId($value)
 * @method static Builder|BitbucketUser whereAvatar($value)
 * @method static Builder|BitbucketUser whereCreatedAt($value)
 * @method static Builder|BitbucketUser whereId($value)
 * @method static Builder|BitbucketUser whereLink($value)
 * @method static Builder|BitbucketUser whereName($value)
 * @method static Builder|BitbucketUser whereNickname($value)
 * @method static Builder|BitbucketUser whereType($value)
 * @method static Builder|BitbucketUser whereUpdatedAt($value)
 * @method static Builder|BitbucketUser whereUuid($value)
 * @mixin Eloquent
 */
class BitbucketUser extends Model
{
    //
}
