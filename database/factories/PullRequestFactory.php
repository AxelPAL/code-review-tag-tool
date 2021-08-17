<?php

namespace Database\Factories;

use App\Models\PullRequest;
use App\Models\RemoteUser;
use App\Models\Repository;
use Illuminate\Database\Eloquent\Factories\Factory;

class PullRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PullRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'web_link'              => $this->faker->text(),
            'title'                 => $this->faker->text(),
            'description'           => $this->faker->text(),
            'remote_id'             => $this->faker->randomNumber(),
            'destination_branch'    => $this->faker->slug(1),
            'destination_commit'    => $this->faker->slug(1),
            'repository_created_at' => $this->faker->dateTime(),
            'repository_updated_at' => $this->faker->dateTime(),
            'comment_count'         => $this->faker->randomNumber(),
            'state'                 => $this->faker->slug(1),
            'remote_author_id'      => RemoteUser::factory(),
            'repository_id'         => Repository::factory(),
        ];
    }
}
