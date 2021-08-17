<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\CommentContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentContentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CommentContent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'comment_id' => Comment::factory(),
            'raw'        => $this->faker->text(),
            'html'       => $this->faker->text(),
            'markup'     => $this->faker->text(),
            'type'       => $this->faker->text(),
            'tag'        => $this->faker->slug(1),
        ];
    }
}
