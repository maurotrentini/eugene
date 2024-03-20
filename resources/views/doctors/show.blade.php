@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4">

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Doctor Detail: {{ $doctor->name }}</h1>
        <a href="{{ route('doctors.edit', $doctor) }}"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit Doctor</a>
    </div>

    <div class="mb-4">
        <strong>Name:</strong> {{ $doctor->name }}
    </div>
    <div class="mb-4">
        <strong>Specialty:</strong> {{ $doctor->specialty }}
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <h2 class="text-xl font-bold mb-4">Related Tests</h2>
            <table class="bg-white w-full border-collapse">
                <thead>
                    <tr class="bg-gray-300">
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Updated</th>
                        <th class="border px-4 py-2">Description</th>
                        <th class="border px-4 py-2">Test Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doctor->tests()->orderBy('created_at', 'desc')->get() as $test)
                        <tr>
                            <td class="border px-4 py-2">{{ $test->id }}</td>
                            <td class="border px-4 py-2">{{ $test->updated_at->format('Y-m-d') }}</td>
                            <td class="border px-4 py-2">{{ $test->name }}</td>
                            <td class="border px-4 py-2">{{ $test->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <h2 class="text-xl font-bold mb-4">Doctor's Clinics</h2>
            <table class="bg-white w-full border-collapse">
                <thead>
                    <tr class="bg-gray-300">
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Updated</th>
                        <th class="border px-4 py-2">Name</th>
                        <th class="border px-4 py-2">Address</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doctor->clinic()->orderBy('name', 'asc')->get() as $clinic)
                        <tr>
                            <td class="border px-4 py-2">{{ $clinic->id }}</td>
                            <td class="border px-4 py-2">{{ $clinic->updated_at->format('Y-m-d') }}</td>
                            <td class="border px-4 py-2"><a href="{{ route('clinics.show', $clinic) }}">{{ $clinic->name }}</a></td>
                            <td class="border px-4 py-2">{{ $clinic->address }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
