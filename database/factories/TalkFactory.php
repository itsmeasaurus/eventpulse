<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Conference;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models;
use App\Models\Speaker;
use App\Models\Talk;

class TalkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Talk::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'conference_id' => Conference::factory(),
            'speaker_id' => Speaker::factory(),
            'category_id' => Category::factory(),
        ];
    }
}
