<?php

namespace Database\Factories;

use App\Models\RemoteUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class RemoteUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RemoteUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'display_name' => $this->faker->name(),
            'uuid'         => Str::uuid(),
            'web_link'     => $this->faker->text(),
            'nickname'     => $this->faker->firstName(),
            'account_id'   => $this->faker->text(),
            'avatar'       => $this->faker->randomNumber(),
        ];
    }
}
