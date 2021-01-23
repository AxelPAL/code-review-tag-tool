<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\CommentContent
 *
 * @property int $id
 * @property int $comment_id
 * @property string $raw
 * @property string $html
 * @property string $markup
 * @property string $type
 * @property string $tag
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|CommentContent newModelQuery()
 * @method static Builder|CommentContent newQuery()
 * @method static Builder|CommentContent query()
 * @method static Builder|CommentContent whereCommentId($value)
 * @method static Builder|CommentContent whereCreatedAt($value)
 * @method static Builder|CommentContent whereHtml($value)
 * @method static Builder|CommentContent whereId($value)
 * @method static Builder|CommentContent whereMarkup($value)
 * @method static Builder|CommentContent whereRaw($value)
 * @method static Builder|CommentContent whereType($value)
 * @method static Builder|CommentContent whereUpdatedAt($value)
 * @method static Builder|CommentContent whereTag($value)
 * @mixin Eloquent
 */
class CommentContent extends Model
{
    use HasFactory;
}
