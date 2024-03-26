<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateDoctorSpecialties extends Migration
{
    public function up()
    {
        // Create specialties table
        Schema::create('specialties', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Create pivot table for doctors and specialties
        Schema::create('doctor_specialty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('specialty_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Get all records from doctors table
        $doctors = DB::table('doctors')->get();

        // Filter out null specialties
        $specialties = $doctors->pluck('specialty')->filter()->unique();

        // Insert distinct specialties into specialties table
        foreach ($specialties as $specialty) {
            DB::table('specialties')->insert(['name' => $specialty,'created_at'=>now(),'updated_at'=>now()]);
        }

        // Migrate specialties for each doctor
        foreach ($doctors as $doctor) {
            if ($doctor->specialty) {
                $specialtyId = DB::table('specialties')->where('name', $doctor->specialty)->value('id');
                DB::table('doctor_specialty')->insert([
                    'doctor_id' => $doctor->id,
                    'specialty_id' => $specialtyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Remove specialty column from doctors table
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn('specialty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Add specialty column back to doctors table
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('specialty')->nullable();
        });

       // Get all doctors with their specialties from the pivot table
        $doctorSpecialties = DB::table('doctor_specialty')
                            ->join('doctors', 'doctor_specialty.doctor_id', '=', 'doctors.id')
                            ->join('specialties', 'doctor_specialty.specialty_id', '=', 'specialties.id')
                            ->select('doctors.id as doctor_id', 'specialties.name as specialty')
                            ->get();

        // Map specialties back to doctors
        foreach ($doctorSpecialties as $doctorSpecialty) {
            DB::table('doctors')
                ->where('id', $doctorSpecialty->doctor_id)
                ->update(['specialty' => $doctorSpecialty->specialty]);
        }

        // Drop specialties table
        Schema::dropIfExists('specialties');

        // Drop pivot table
        Schema::dropIfExists('doctor_specialty');
    }
}
