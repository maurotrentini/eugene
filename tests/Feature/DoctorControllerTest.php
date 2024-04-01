<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Support\Facades\Log;
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

    public function testMergeDoctors()
    {
        // Create some dummy doctors, clinics, and specialties
        $targetDoctor = Doctor::factory()->create();
        $doctorsToMerge = Doctor::factory()->count(2)->create();
        $clinics = Clinic::all();
        $specialties = Specialty::all();

        // Attach clinics and specialties to the doctors
        $targetDoctor->clinic()->attach($clinics->random()->id);
        $targetDoctor->specialty()->attach($specialties->random()->id);
        foreach ($doctorsToMerge as $doctor) {
            $doctor->clinic()->attach($clinics->random()->id);
            $doctor->clinic()->attach($clinics->random()->id);
            $doctor->clinic()->attach($clinics->random()->id);
            $doctor->specialty()->attach($specialties->random()->id);
            $doctor->specialty()->attach($specialties->random()->id);
        }

        // Generate fake data for testing
        $targetDoctorId = $targetDoctor->id;
        $sourceDoctorIds = $doctorsToMerge->pluck('id')->toArray();


        // Before merge
        Log::info('Target Doctor Clinics Before Merge: ' . $targetDoctor->clinic->pluck('id'));
        Log::info('Target Doctor Specialties Before Merge: ' . $targetDoctor->specialty->pluck('id'));

        // Call the merge method with fake data
        $response = $this->post('/doctors/merge', [
            'target_doctor' => $targetDoctorId,
            'selected_doctors' => implode(',', $sourceDoctorIds),
        ]);

        // After merge
        Log::info('Target Doctor Clinics After Merge: ' . $targetDoctor->fresh()->clinic->pluck('id'));
        Log::info('Target Doctor Specialties After Merge: ' . $targetDoctor->fresh()->specialty->pluck('id'));        

        // Assert that the response is successful and redirects to the index page
        $response->assertRedirect('/doctors');

        // Assert that the target doctor now has the clinics and specialties of the merged doctors
        foreach ($doctorsToMerge as $doctor) {
            foreach ($doctor->clinic as $clinic) {
                $this->assertTrue($targetDoctor->clinic->contains($clinic));
            }
            foreach ($doctor->specialty as $specialty) {
                $this->assertTrue($targetDoctor->specialty->contains($specialty));
            }
        }

        // Assert that the merged doctors are deleted from the database
        foreach ($sourceDoctorIds as $doctorId) {
            $this->assertNull(Doctor::find($doctorId));
        }
    }
        
}
