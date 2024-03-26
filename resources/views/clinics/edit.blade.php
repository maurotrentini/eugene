@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Edit Clinic: {{ $clinic->name }}</h1>
    </div>

    <form action="{{ route('clinics.update', $clinic) }}" method="post">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block mb-2">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $clinic->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('name')
                <p class="text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="address" class="block mb-2">Address</label>
            <input type="text" name="address" id="address" value="{{ old('address', $clinic->address) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('address')
                <p class="text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Show all doctors associated with the clinic -->
        <div class="mb-4" x-data="{ doctors: {{ json_encode($clinic->doctors->toArray()) }} }">
            <label class="block mb-2">Doctors</label>
            <template x-for="(doctor, index) in doctors" :key="index">
                <div class="flex items-center">
                    <input disabled type="text" :value="doctor.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-2">
                    <input name="existing_doctor_id[]" type="hidden" :value="doctor.id" >
                    <button type="button" @click="doctors.splice(index, 1)" class="text-red-500">Remove</button>
                </div>
            </template>
        </div>        

        <!--Section for adding new doctors -->
        <div x-data="{ doctors: [] }">
            <template x-for="(doctor, index) in doctors" :key="index">
                <div class="mb-4">
                    <label class="block mb-2" x-text="'Doctor ' + (index + 1)"></label>
                    <select name="doctor_id[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select Doctor</option>
                        @foreach($doctors as $doctor)
                            @php
                                $specialties = $doctor->specialty->pluck('name')->implode(', ');
                            @endphp
                            <option value="{{ $doctor->id }}">{{ $doctor->name }} ({{ $specialties }})</option>
                        @endforeach
                    </select>
                    <button type="button" @click="doctors.splice(index, 1)" class="text-red-500 mt-2">Remove</button>
                </div>
            </template>
            <button type="button" @click="doctors.push({})" class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-xs px-5 py-2.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">Add Doctor</button>
        </div>

        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mt-10 rounded">Update Clinic</button>
    </form>
</div>
@endsection
