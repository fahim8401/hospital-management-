@extends('layouts.app')

@section('title', 'Edit Patient')
@section('page-title', 'Edit Patient')

@section('content')
<div class="max-w-4xl mx-auto">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-6">
        <a href="{{ route('patients.index') }}" class="hover:text-blue-700 transition">Patients</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('patients.show', $patient) }}" class="hover:text-blue-700 transition">{{ $patient->name }}</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-700 font-medium">Edit</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 px-8 py-6">
            <h2 class="text-xl font-bold text-white">Edit Patient — {{ $patient->patient_id }}</h2>
        </div>

        <form action="{{ route('patients.update', $patient) }}" method="POST" class="px-8 py-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $patient->name) }}"
                        class="w-full px-4 py-2.5 border {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1.5">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $patient->phone) }}" maxlength="11"
                        class="w-full px-4 py-2.5 border {{ $errors->has('phone') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('phone')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="nid_number" class="block text-sm font-semibold text-slate-700 mb-1.5">NID Number <span class="text-slate-400 font-normal">(optional)</span></label>
                    <input type="text" id="nid_number" name="nid_number" value="{{ old('nid_number', $patient->nid_number) }}"
                        class="w-full px-4 py-2.5 border {{ $errors->has('nid_number') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('nid_number')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="dob" class="block text-sm font-semibold text-slate-700 mb-1.5">Date of Birth <span class="text-red-500">*</span></label>
                    <input type="date" id="dob" name="dob" value="{{ old('dob', $patient->dob->format('Y-m-d')) }}"
                        max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                        class="w-full px-4 py-2.5 border {{ $errors->has('dob') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    @error('dob')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="blood_group" class="block text-sm font-semibold text-slate-700 mb-1.5">Blood Group <span class="text-red-500">*</span></label>
                    <select id="blood_group" name="blood_group"
                        class="w-full px-4 py-2.5 border {{ $errors->has('blood_group') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                        <option value="">— Select —</option>
                        @foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group', $patient->blood_group) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                    @error('blood_group')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Gender <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="gender" value="{{ $value }}"
                                    {{ old('gender', $patient->gender) === $value ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                                <span class="text-sm text-slate-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('gender')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-semibold text-slate-700 mb-1.5">Address <span class="text-red-500">*</span></label>
                    <textarea id="address" name="address" rows="3"
                        class="w-full px-4 py-2.5 border {{ $errors->has('address') ? 'border-red-400 bg-red-50' : 'border-slate-300' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                    >{{ old('address', $patient->address) }}</textarea>
                    @error('address')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

            </div>

            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
                <a href="{{ route('patients.show', $patient) }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-blue-700 hover:bg-blue-800 rounded-xl shadow-sm transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
