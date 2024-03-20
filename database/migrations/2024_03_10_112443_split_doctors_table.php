<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SplitDoctorsTable extends Migration
{
    public function up(): void
    {
        // Create the clinics table
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        // Create a pivot table to manage the many-to-many relationship between doctors and clinics
        Schema::create('clinic_doctor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('clinic_id');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');
            $table->timestamps();
        });
            
        // Transfer clinic data from doctors to clinics table
        DB::statement('INSERT INTO clinics (name, address, created_at, updated_at) 
                        SELECT DISTINCT clinic_name, clinic_address, datetime(), datetime() FROM doctors');

        // Create associations between doctors and clinics in the pivot table
        DB::statement('INSERT INTO clinic_doctor (doctor_id, clinic_id, created_at, updated_at)
                        SELECT doctors.id AS doctor_id, clinics.id AS clinic_id, datetime(), datetime()
                        FROM doctors
                        INNER JOIN clinics ON doctors.clinic_name = clinics.name AND doctors.clinic_address = clinics.address');


        // Remove clinic_name and clinic_address columns from doctors table
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['clinic_name', 'clinic_address']);
        });
    }

    public function down(): void
    {
        // Add clinic_name and clinic_address columns back to doctors table
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('clinic_name')->nullable();
            $table->string('clinic_address')->nullable();
        });

        // Transfer clinic data back to doctors table
        DB::statement('UPDATE doctors 
                        SET clinic_name = (
                            SELECT GROUP_CONCAT(clinics.name, ", ") 
                            FROM clinic_doctor 
                            INNER JOIN clinics ON clinic_doctor.clinic_id = clinics.id 
                            WHERE clinic_doctor.doctor_id = doctors.id
                        ),
                        clinic_address = (
                            SELECT GROUP_CONCAT(clinics.address, ", ") 
                            FROM clinic_doctor 
                            INNER JOIN clinics ON clinic_doctor.clinic_id = clinics.id 
                            WHERE clinic_doctor.doctor_id = doctors.id
                        )');

        // Drop the clinics table and clinic_doctor pivot table
        Schema::dropIfExists('clinics');
        Schema::dropIfExists('clinic_doctor');
    }
};
