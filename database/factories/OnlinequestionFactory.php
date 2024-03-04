<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Onlinequestion;
use App\JobCategory;
use App\Skill;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Onlinequestion>
 */
class OnlinequestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Onlinequestion::class;
    public function definition()
    {
        $faker = $this->faker;
        return [
            'question' => $faker->name,
            'jobcategory_id' => $this->faker->randomElement(JobCategory::pluck('id')),
            'skill_id'=>$this->faker->randomElement(Skill::pluck('id')),
            'options'=>$faker->randomElement(["house", "flat", "apartment", "room"]),
            'answer'=>$faker->randomElement(["house", "flat", "apartment", "room"]),
            'status'=>$faker->randomElement(['active', 'inactive']), 
        ];
    }
}
