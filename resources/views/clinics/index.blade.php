@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4" x-data="{ selectedClinics: [], selectedTargetClinic: '' }">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Clinics</h1>
            <div class="flex">
                <form action="{{ route('clinics.index') }}" method="GET">
                    <input type="text" name="search" placeholder="Search by name or address" class="mr-2 w-64" value="{{ request()->input('search') }}">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Search</button>
                </form>
                <a href="{{ route('clinics.create') }}"
                    class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Clinic</a>
            </div>
        </div>

        <hr>
        <br>
        {{ $clinics->links() }}
        <br>
        <hr>

        <x-modal title="Merge Clinics">
            @slot('body')
                <section class="bg-white">
                    <div class="px-4 mx-auto max-w-screen-md">
                        <form action="{{ route('clinics.merge') }}" method="post" class="space-y-4">
                        @csrf
                            <p class="mb-8 lg:mb-8 font-light">You have selected the following records: <span x-text="selectedClinics.join(', ')"></span></p>
                            <input type="hidden" name="selected_clinics" x-model="selectedClinics"/>
                            <div>
                                <label for="target_clinic" class="block mb-2">Select Record to Merge Into</label>
                                <select name="target_clinic" id="target_clinic" x-model="selectedTargetClinic"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Select Target Doctor</option>
                                    @foreach($clinics as $clinic)
                                        <option value="{{ $clinic->id }}">{{ $clinic->id }} - {{ $clinic->name }} ({{ $clinic->address }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" x-show="selectedTargetClinic"
                                    class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded absolute bottom-4 right-4">Confirm Merge</button>
                        </form>
                    </div>
                </section>
            @endslot
        </x-modal>

        <button x-data 
                x-on:click="$dispatch('open-modal')" 
                x-show="selectedClinics.length > 0"
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
                    <th class="border px-4 py-2">Clinic Address</th>
                    <th class="border px-4 py-2"># Doctors</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clinics as $clinic)
                    <tr>
                        <td class="border px-4 py-2">
                            <input type="checkbox" 
                                    value="{{ $clinic->id }}" 
                                    x-model="selectedClinics"
                                    id="checkbox_{{ $clinic->id }}">
                        </td>
                        <td class="border px-4 py-2">{{ $clinic->id }}</td>
                        <td class="border px-4 py-2">{{ $clinic->updated_at?->format('Y-m-d H:i:s') }}</td>
                        <td class="border px-4 py-2">{{ $clinic->name }}</td>
                        <td class="border px-4 py-2">{{ $clinic->address }}</td>
                        <td class="border px-4 py-2">{{ $clinic->doctors_count }}</td>
                        <td class="border px-4 py-2">
                            <a href="{{ route('clinics.show', $clinic) }}" class="text-blue-500">View</a>
                            <a href="{{ route('clinics.edit', $clinic) }}" class="text-green-500">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
