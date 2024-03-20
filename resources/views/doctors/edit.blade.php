@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Edit Doctor: {{ $doctor->name }}</h1>
    </div>

    <form action="{{ route('doctors.update', $doctor) }}" method="post">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block mb-2">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $doctor->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('name')
                <p class="text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="specialty" class="block mb-2">Specialty</label>
            <input type="text" name="specialty" id="specialty" value="{{ old('specialty', $doctor->specialty) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('specialty')
                <p class="text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Show all clinics associated with the doctor -->
        <div class="mb-4" x-data="{ clinics: {{ json_encode($doctor->clinic->toArray()) }} }">
            <label class="block mb-2">Clinics</label>
            <template x-for="(clinic, index) in clinics" :key="index">
                <div class="flex items-center">
                    <input disabled type="text" :value="clinic.name + ' (' + clinic.address + ')'" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-2">
                    <input name="existing_clinic_id[]" type="hidden" :value="clinic.id" >
                    <button type="button" @click="clinics.splice(index, 1)" class="text-red-500">Remove</button>
                </div>
            </template>
        </div>        

        <!--Section for adding new clinics -->
        <div x-data="{ clinics: [] }">
            <template x-for="(clinic, index) in clinics" :key="index">
                <div class="mb-4">
                    <label class="block mb-2" x-text="'Clinic ' + (index + 1)"></label>
                    <select name="clinic_id[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select Clinic</option>
                        @foreach($clinics as $clinic)
                            <option value="{{ $clinic->id }}">{{ $clinic->name }} ({{ $clinic->address }})</option>
                        @endforeach
                    </select>
                    <button type="button" @click="clinics.splice(index, 1)" class="text-red-500 mt-2">Remove</button>
                </div>
            </template>
            <button type="button" @click="clinics.push({})" class="text-green-700 hover:text-white border border-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-xs px-5 py-2.5 text-center me-2 mb-2 dark:border-green-500 dark:text-green-500 dark:hover:text-white dark:hover:bg-green-600 dark:focus:ring-green-800">Add Clinic</button>
        </div>

        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mt-10 rounded">Update Doctor</button>
    </form>
</div>
@endsection
