<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\Specialty;
use App\Models\Test;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $doctors = Doctor::withCount('tests')
                            ->with('clinic')
                            ->with('specialty')
                            ->when($search, function ($query) use ($search) {
                                $query->where('name', 'like', '%'.$search.'%')
                                        ->orWhereHas('specialty', function ($query) use ($search) {
                                            $query->where('name', 'like', '%'.$search.'%');
                                        })
                                        ->orWhereHas('clinic', function ($query) use ($search) {
                                            $query->where('name', 'like', '%'.$search.'%');
                                        });
                            })
                            ->orderBy('updated_at', 'desc')->paginate(100);

        return view('doctors.index', compact('doctors'));
    }

    public function create()
    {
        $clinics = Clinic::all();
        $specialties = Specialty::all();

        return view('doctors.create',compact('clinics','specialties'));
    }

    public function store(StoreDoctorRequest $request)
    {
        $clinics = $request->input('clinic_id') ?? [];

        $doctor = Doctor::create($request->validated());
        $doctor->clinic()->attach(array_unique(array_filter($clinics)));
        $doctor->specialty()->attach($request->input('specialties', []));

        return redirect()->route('doctors.index')->with('success', 'Doctor created successfully.');
    }

    public function show(Doctor $doctor)
    {
        return view('doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        $specialties = Specialty::all();
        $clinics = Clinic::all();

        $doctor->load('specialty', 'clinic');

        return view('doctors.edit', compact('doctor', 'specialties', 'clinics'));

    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        $doctor->update($request->validated());

        $clinics = $request->input('clinic_id') ?? [];
        $existingClinics = $request->input('existing_clinic_id') ?? [];
        $newClinics = array_unique(array_filter(array_merge($clinics, $existingClinics)));

        $doctor->clinic()->sync($newClinics);
        $doctor->specialty()->sync($request->input('specialties', []));

        return redirect()->route('doctors.index')->with('success', 'Doctor updated successfully.');
    }

    /**
     * Merge selected doctors into a chosen doctor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function merge(Request $request)
    {
        // Get the selected doctor ID to merge into
        $targetDoctorId = $request->input('target_doctor');

        // Get the array of doctor IDs to be merged
        $sourceDoctorIds = $request->input('selected_doctors');
        $doctorsToMerge = explode(',',$sourceDoctorIds);
        Log::info('Merging '.$sourceDoctorIds.' into '.$targetDoctorId);

        // Perform the merge operation
        foreach ($doctorsToMerge as $doctorId) {
            if ($doctorId == $targetDoctorId) {
                Log::info('Skip doctor '.$doctorId);
                continue;
            }
            // Update tests to reference the chosen doctor
            Test::where('referring_doctor_id', $doctorId)->update(['referring_doctor_id' => $targetDoctorId]);

            // Merge clinics
            $sourceDoctor = Doctor::find($doctorId);
            $targetDoctor = Doctor::find($targetDoctorId);
            foreach ($sourceDoctor->clinic as $clinic) {
                // Check if the clinic already exists for the target doctor
                if (!$targetDoctor->clinic->contains($clinic->id)) {
                    // If not, attach the clinic to the target doctor
                    $targetDoctor->clinic()->attach($clinic->id);
                }
            }
            
            // After attaching all clinics, detach them from the source doctor
            $sourceDoctor->clinic()->detach(); //may not be required because of on delete cascade declared in migration

            // Merge specialties
            foreach ($sourceDoctor->specialty as $specialty) {
                if (!$targetDoctor->specialty->contains($specialty)) {
                    $targetDoctor->specialty()->attach($specialty->id);
                }
            }           
            // $sourceDoctor->specialty()->detach(); 

            // Delete the merged doctor
            Doctor::find($doctorId)->delete();
        }

        // Redirect to index page
        return redirect()->route('doctors.index')->with('success', 'Doctor(s) merged successfully.');
    }
}