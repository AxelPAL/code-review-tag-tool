<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\PullRequest;
use App\Models\RemoteUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'web_link'              => $this->faker->text(),
            'remote_user_id'        => RemoteUser::factory(),
            'isDeleted'             => $this->faker->boolean(),
            'pull_request_id'       => PullRequest::factory(),
            'repository_created_at' => $this->faker->dateTime(),
            'repository_updated_at' => $this->faker->dateTime(),
            'remote_id'             => $this->faker->randomNumber(),
        ];
    }
}
