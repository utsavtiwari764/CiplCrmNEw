<?php

namespace Database\Factories;
use App\Subject;
use App\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Subject::class;
    public function definition()
    {
        $faker = $this->faker;
        return [
            'name' => $faker->name,
            'course_id' => $this->faker->randomElement(Course::pluck('id')),
            'status'=>$faker->randomElement(['active', 'inactive']),
        ];
    }
}
