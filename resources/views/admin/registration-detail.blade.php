@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Registration Details</h1>
    </div>
    <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
        {{ $registration->submitted_at->format('M d, Y H:i:s') }}
    </span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column (Info) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Personal Information -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Personal Information</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                        <dd class="mt-1 text-base text-gray-900 font-semibold">{{ $registration->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Mobile Number</dt>
                        <dd class="mt-1 text-base text-gray-900">{{ $registration->mobile_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nationality</dt>
                        <dd class="mt-1 text-base text-gray-900">{{ $registration->nationality }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Area of Residence</dt>
                        <dd class="mt-1 text-base text-gray-900">{{ $registration->area_of_residence }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Preferred Language</dt>
                        <dd class="mt-1 text-base text-gray-900 uppercase">{{ $registration->preferred_language }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Age Group</dt>
                        <dd class="mt-1 text-base text-gray-900">{{ str_replace('_', '-', $registration->age_group) }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Emirates ID -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Emirates ID Details</h3>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <span class="text-sm font-medium text-gray-500">ID Number</span>
                    <p class="mt-1 font-mono text-xl text-gray-900">{{ $registration->emirates_id_number }}</p>
                </div>
                
                @if($registration->emirates_id_image_path)
                <div>
                    <span class="text-sm font-medium text-gray-500 block mb-2">ID Image</span>
                    <div class="border rounded-lg overflow-hidden p-2 bg-gray-50">
                        <img src="{{ route('admin.dashboard') /* Placeholder if we don't have a route to serve private disk files. In a real app we'd add a route to serve this */ }}" alt="Emirates ID Image" class="w-full h-auto object-contain max-h-96">
                        <p class="text-xs text-gray-400 mt-2 text-center">Path: {{ $registration->emirates_id_image_path }}</p>
                    </div>
                </div>
                @else
                <div class="p-4 bg-gray-50 border border-dashed rounded-lg text-center text-gray-500">
                    No image uploaded
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column (Consents & Visits) -->
    <div class="space-y-6">
        
        <!-- Consents -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Consents</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($registration->consentRecords as $consent)
                <div class="p-4 flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        @if($consent->granted)
                            <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3 w-full">
                        <div class="flex justify-between items-center">
                            <h4 class="text-sm font-medium text-gray-900 capitalize">{{ $consent->consent_type }}</h4>
                            <span class="text-xs text-gray-500">{{ $consent->document_version }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $consent->granted_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Visits -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Visit History</h3>
            </div>
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @foreach($registration->visits->sortByDesc('visited_at') as $visit)
                <div class="p-4">
                    <div class="flex items-center mb-1">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">{{ $visit->visited_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="pl-6 text-sm text-gray-500">
                        <p>Tablet: {{ $visit->tablet->label ?? 'Unknown' }}</p>
                        @if($visit->location_zone)
                            <p>Zone: {{ $visit->location_zone }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
