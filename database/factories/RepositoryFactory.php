<?php

namespace Database\Factories;

use App\Models\Repository;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class RepositoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Repository::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->name();
        return [
            'web_link'   => $this->faker->text(),
            'name'       => $name,
            'owner_name' => $name,
            'workspace'  => $this->faker->slug(1),
            'slug'       => $this->faker->slug(),
            'language'   => $this->faker->languageCode,
            'uuid'       => Str::uuid(),
        ];
    }
}
