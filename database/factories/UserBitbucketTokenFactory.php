<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserBitbucketToken;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserBitbucketTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserBitbucketToken::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'       => User::factory(),
            'scopes'        => $this->faker->text(),
            'access_token'  => $this->faker->text(100),
            'expires_at'    => $this->faker->dateTime(),
            'token_type'    => 'bearer',
            'state'         => $this->faker->randomElement(
                [
                    'authorization_code',
                    'refresh_token',
                ]
            ),
            'refresh_token' => $this->faker->text(100),
        ];
    }
}
