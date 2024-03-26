@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4" x-data="{ selectedDoctors: [], selectedTargetDoctor: '' }">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Doctors</h1>
            <div class="flex">
                <form action="{{ route('doctors.index') }}" method="GET">
                    <input type="text" name="search" placeholder="Search by name, specialty, or clinic" class="mr-2 w-64" value="{{ request()->input('search') }}">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Search</button>
                </form>
                <a href="{{ route('doctors.create') }}" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Doctor</a>
            </div>
        </div>

        <hr>
        <br>
        {{ $doctors->links() }}
        <br>
        <hr>

        <x-modal title="Merge Doctors">
            @slot('body')
                <section class="bg-white">
                    <div class="px-4 mx-auto max-w-screen-md">
                        <form action="{{ route('doctors.merge') }}" method="post" class="space-y-4">
                        @csrf
                            <p class="mb-8 lg:mb-8 font-light">You have selected the following records: <span x-text="selectedDoctors.join(', ')"></span></p>
                            <input type="hidden" name="selected_doctors" x-model="selectedDoctors"/>
                            <div>
                                <label for="target_doctor" class="block mb-2">Select Record to Merge Into</label>
                                <select name="target_doctor" id="target_doctor" x-model="selectedTargetDoctor"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Select Target Doctor</option>
                                    @foreach($doctors as $doctor)
                                        @php
                                            $specialties = $doctor->specialty->pluck('name')->implode(', ');
                                        @endphp
                                        <option value="{{ $doctor->id }}">{{ $doctor->id }} - {{ $doctor->name }} ({{ $specialties }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" x-show="selectedTargetDoctor"
                                    class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded absolute bottom-4 right-4">Confirm Merge</button>
                        </form>
                    </div>
                </section>
            @endslot
        </x-modal>

        <button x-data 
                x-on:click="$dispatch('open-modal')" 
                x-show="selectedDoctors.length > 0"
                style="display:none;"
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded merge-button">
                    Merge Selected
        </button>

        <table class="bg-white w-full border-collapse">
            <thead>
                <tr class="bg-gray-300">
                    <th class="border px-4 py-2">Merge</th>
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Updated</th>
                    <th class="border px-4 py-2">Name</th>
                    <th class="border px-4 py-2">Specialties</th>
                    <th class="border px-4 py-2">Clinics</th>
                    <th class="border px-4 py-2"># Tests</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($doctors as $doctor)
                    <tr>
                        <td class="border px-4 py-2">
                            <input type="checkbox" 
                                    value="{{ $doctor->id }}" 
                                    x-model="selectedDoctors"
                                    id="checkbox_{{ $doctor->id }}">
                        </td>
                        <td class="border px-4 py-2">{{ $doctor->id }}</td>
                        <td class="border px-4 py-2">{{ $doctor->updated_at->format('Y-m-d H:i:s') }}</td>
                        <td class="border px-4 py-2">{{ $doctor->name }}</td>
                        <td class="border px-4 py-2">
                            @foreach ($doctor->specialty()->orderBy('name', 'asc')->get() as $specialty)
                                <p>{{ $specialty->name }}</p>
                            @endforeach
                        </td>
                        <td class="border px-4 py-2">
                            @foreach ($doctor->clinic()->orderBy('name', 'asc')->get() as $clinic)
                                <p><a href="{{ route('clinics.show', $clinic) }}">{{ $clinic->name }} ({{ $clinic->address }})</a></p>
                            @endforeach
                        </td>
                        <td class="border px-4 py-2">{{ $doctor->tests_count }}</td>
                        <td class="border px-4 py-2">
                            <a href="{{ route('doctors.show', $doctor) }}" class="text-blue-500">View</a>
                            <a href="{{ route('doctors.edit', $doctor) }}" class="text-green-500">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
