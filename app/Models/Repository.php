<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Repository
 *
 * @property int $id
 * @property string $web_link
 * @property string $name
 * @property string $owner_name
 * @property string $workspace
 * @property string $slug
 * @property string $language
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Repository newModelQuery()
 * @method static Builder|Repository newQuery()
 * @method static Builder|Repository query()
 * @method static Builder|Repository whereCreatedAt($value)
 * @method static Builder|Repository whereId($value)
 * @method static Builder|Repository whereLanguage($value)
 * @method static Builder|Repository whereName($value)
 * @method static Builder|Repository whereOwnerName($value)
 * @method static Builder|Repository whereSlug($value)
 * @method static Builder|Repository whereUpdatedAt($value)
 * @method static Builder|Repository whereWebLink($value)
 * @method static Builder|Repository whereWorkspace($value)
 * @mixin Eloquent
 */
class Repository extends Model
{
    use HasFactory;
}
