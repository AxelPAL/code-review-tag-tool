<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\RemoteUser
 *
 * @property int $id
 * @property string $display_name
 * @property string $uuid
 * @property string $web_link
 * @property string $nickname
 * @property string $account_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|RemoteUser newModelQuery()
 * @method static Builder|RemoteUser newQuery()
 * @method static Builder|RemoteUser query()
 * @method static Builder|RemoteUser whereAccountId($value)
 * @method static Builder|RemoteUser whereCreatedAt($value)
 * @method static Builder|RemoteUser whereDisplayName($value)
 * @method static Builder|RemoteUser whereId($value)
 * @method static Builder|RemoteUser whereNickname($value)
 * @method static Builder|RemoteUser whereUpdatedAt($value)
 * @method static Builder|RemoteUser whereUuid($value)
 * @method static Builder|RemoteUser whereWebLink($value)
 * @mixin Eloquent
 */
class RemoteUser extends Model
{
    use HasFactory;
}
