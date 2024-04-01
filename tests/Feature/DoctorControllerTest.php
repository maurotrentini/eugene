<?php

namespace Tests\Feature;

use App\Models\Doctor;
use Tests\TestCase;

class DoctorControllerTest extends TestCase
{
    public function testIndexMethod()
    {
        // Create some dummy doctors
        $doctors = Doctor::factory()->count(3)->create();

        // Call the index method
        $response = $this->get('/doctors');

        // Assert response is successful
        $response->assertStatus(200);

        // Assert correct view is returned
        $response->assertViewIs('doctors.index');

        // Assert that the view contains the list of doctors
        foreach ($doctors as $doctor) {
            $response->assertSee($doctor->name);
        }
    }
}
