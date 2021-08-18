<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Setting
 *
 * @mixin Eloquent
 * @property int $id
 * @property string $title
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Setting newModelQuery()
 * @method static Builder|Setting newQuery()
 * @method static Builder|Setting query()
 * @method static Builder|Setting whereCreatedAt($value)
 * @method static Builder|Setting whereId($value)
 * @method static Builder|Setting whereTitle($value)
 * @method static Builder|Setting whereUpdatedAt($value)
 * @method static Builder|Setting whereValue($value)
 */
class Setting extends Model
{
    use CrudTrait;
    use HasFactory;

    public const BITBUCKET_CLIENT_ID_ID = 1;
    public const BITBUCKET_CLIENT_SECRET_ID = 2;
    public const BITBUCKET_REQUESTS_USER_ID = 3;

    public const ALL_SETTINGS = [
        self::BITBUCKET_CLIENT_ID_ID     => 'Bitbucket Client Id',
        self::BITBUCKET_CLIENT_SECRET_ID => 'Bitbucket Client Secret',
        self::BITBUCKET_REQUESTS_USER_ID => 'Bitbucket requests user id',
    ];

    protected $fillable = [
        'title',
        'value',
    ];
}
