<?php

namespace Database\Seeders;

use App\Models\Test;
use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TestsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $names = collect([
            'Carrier Screening - Individual',
            'Carrier Screening - Couple',
            'Cancer Screening',
            'Cardiac Screening',
        ]);

        for ($i = 0; $i < 10000; $i++) {
            $date = $faker->dateTimeThisYear;
            Test::create([
                'name' => $names->random(),
                'description' => $faker->text,
                'referring_doctor_id' => rand(0, 5) === 5 ? null : Doctor::inRandomOrder()->first()->id,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}