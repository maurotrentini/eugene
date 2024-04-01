<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Specialty;
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

    public function testCreateMethod()
    {
        // Call the create method
        $response = $this->get('/doctors/create');
    
        // Assert response is successful
        $response->assertStatus(200);
    
        // Assert correct view is returned
        $response->assertViewIs('doctors.create');
    
        // Assert that some text is present
        $response->assertSeeText('Add Doctor');
        $response->assertSeeText('Specialties');
        $response->assertSee('Add Clinic');

        // Assert that the specialties list is present with all specialties listed in a multi-select dropdown
        $response->assertSee('<select name="specialties[]"', false); // Ensure the select element is present
        foreach (Specialty::all() as $specialty) {
            $response->assertSee($specialty->name); // Ensure each specialty name is present as an option
        }        
    }
        
}
