<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Erf;
use App\JobCategory;
use App\JobLocation;
use App\JobType;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Erf>
 */
class ErfFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker = $this->faker;
        return [
            'department'=>$faker->name,
            'requisition_date'=>$faker->date('yy-m-d'),
            'project_name'=>$faker->title,
            'ref_no'=>'CIPL'.rand(1,5),
            'required_position_details'=>$faker->paragraph,
            'erf_type'=>$faker->randomElement(['fresh', 'replacement']),
            'target_date'=>$faker->randomElement(date('yy-m-d')),
            'level'=>$faker->title,
            'project_manager_name'=>$faker->name,
            'team_lead'=>$faker->title,
            'total_positions'=>$faker->numberBetween(1, 20),
            'category_id'=>$faker->randomElement(JobCategory::pluck('id')),
            'location_id'=>$faker->randomElement(JobLocation::pluck('id')),
            'jobtype_id'=>$faker->randomElement(JobType::pluck('id')),
            'positionbudget'=>$faker->name,
            'reasonfor'=>$faker->name,
            'replacement'=>$faker->name,
            'status'=>$faker->randomElement(['1','2','3','4','5']),

        ];
    }
}
