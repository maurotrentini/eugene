<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClinicController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $clinics = Clinic::withCount('doctors')
                            ->when($search, function($query) use ($search){
                                $query->where('name','like','%'.$search.'%')
                                      ->orWhere('address','like','%'.$search.'%');
                            })
                            ->orderBy('updated_at', 'desc')
                            ->paginate(100);

        return view('clinics.index',compact('clinics'));
    }

    public function create()
    {
        $doctors = Doctor::all();
        return view('clinics.create',compact('doctors'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);
        $doctors = $request->input('doctor_id') ?? [];
        Clinic::create($validatedData)->doctors()->attach(array_unique(array_filter($doctors)));

        return redirect()->route('clinics.index')->with('success', 'Clinic created successfully.');
    }


    public function show(Clinic $clinic)
    {
        return view('clinics.show', compact('clinic'));
    }

    public function edit(Clinic $clinic)
    {
        $doctors = Doctor::with('specialty')->get();
        return view('clinics.edit', compact('clinic','doctors'));
    }

    public function update(Request $request, Clinic $clinic)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $doctors = $request->input('doctor_id') ?? [];
        $existingDoctors = $request->input('existing_doctor_id') ?? [];
        $newDoctors = array_unique(array_filter(array_merge($doctors, $existingDoctors)));

        $clinic->doctors()->sync($newDoctors);
        $clinic->update($validatedData);

        return redirect()->route('clinics.index')->with('success', 'Clinic updated successfully.');
    }
    
    /**
     * Merge selected clinics into a chosen clinic.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function merge(Request $request)
    {
        // Get the selected clinic ID to merge into
        $targetClinicId = $request->input('target_clinic');

        // Get the array of clinic IDs to be merged
        $sourceClinicIds = $request->input('selected_clinics');
        $clinicsToMerge = explode(',',$sourceClinicIds);

        // Perform the merge operation
        foreach ($clinicsToMerge as $clinicId) {
            if ($clinicId == $targetClinicId) {
                continue;
            }
            // Merge clinics
            $sourceClinic = Clinic::find($clinicId);
            $targetClinic = Clinic::find($targetClinicId);
            foreach ($sourceClinic->doctors as $doctor) {
                // Check if the clinic already exists for the target clinic
                if (!$targetClinic->doctors->contains($doctor->id)) {
                    // If not, attach the clinic to the target clinic
                    $targetClinic->doctors()->attach($doctor->id);
                }
            }
            
            // After attaching all clinics, detach them from the source clinic
            $sourceClinic->doctors()->detach();

            // Delete the merged clinic
            Clinic::find($clinicId)->delete();
        }

        // Redirect to index page
        return redirect()->route('clinics.index')->with('success', 'Clinic(s) merged successfully.');
    }    
}
