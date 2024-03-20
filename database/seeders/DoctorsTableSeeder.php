<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DoctorsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('en_AU');

        $names = [];
        for ($i = 0; $i < 100; $i++) {
            $names[] = $faker->name;
        }
        $names = collect($names);

        $clinics = [];
        for ($i = 0; $i < 100; $i++) {
            $clinics[] = [
                'name' => $faker->company,
                'address' => preg_replace('#\n#', ', ', trim($faker->address))
            ];
        }
        $clinics = collect($clinics);

        $titles = collect([
            null,
            'General Practitioner',
            'GP',
            'Gynaecologist',
            'OB-GYN',
            'Obstetrician',
        ]);

        for ($i = 0; $i < 500; $i++) {
            $clinic = $clinics->random();

            // Randomly muddle up the data so it's not obvious which doctor or clinic goes where
            if (rand(1, 10) === 10) {
                $clinic['name'] = null;
            }

            if (rand(1, 10) === 10) {
                $clinic['address'] = null;
            }

            if ($clinic['name'] && rand(0, 1)) {
                $clinic['name'] = $clinic['name'] . ' ' . ucfirst($faker->word);
            }

            if ($clinic['name'] && rand(0, 1)) {
                $clinic['name'] = ucfirst($faker->word) . ' ' . $clinic['name'];
            }

            if ($clinic['name'] && rand(0, 5) === 5) {
                $clinic['name'] = substr($clinic['name'], 0, 10);
            }

            if ($clinic['address'] && rand(0, 1)) {
                $parts = collect(explode(', ', $clinic['address']));
                $muddle = $parts->random(2)->implode(' ');
                if (str_contains($muddle, '/')) {
                    list($ignore, $muddle) = explode('/', $muddle);
                }

                if (rand(0, 10) === 10) {
                    $muddle .= $faker->word;
                }

                $clinic['address'] = $muddle;
            }

            $date = $faker->dateTimeThisYear;

            $name = $names->random();

            if (rand(0, 1)) {
                $parts = collect(explode(' ', $name));
                $name = $parts->random(2)->implode(' ');
            }

            $doctor = [
                'name' => $name,
                'specialty' => $titles->random(),
                'clinic_name' => $clinic['name'],
                'clinic_address' => str_replace('\n', ', ', $clinic['address']),
                'created_at' => $date,
                'updated_at' => $date
            ];

            Doctor::create($doctor);
        }
    }
}