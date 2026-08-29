@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
</div>

<!-- Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Registrations</h3>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalRegistrations) }}</p>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-emerald-500">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Today</h3>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($todayRegistrations) }}</p>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">This Week</h3>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($thisWeekRegistrations) }}</p>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Marketing Opt-ins</h3>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($marketingOptIns) }}</p>
    </div>
</div>

<!-- Filters & Actions -->
<div class="bg-white rounded-lg shadow mb-8">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-medium text-gray-900">Filter Registrations</h2>
        <a href="{{ route('admin.export', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:border-emerald-900 focus:ring ring-emerald-300 disabled:opacity-25 transition ease-in-out duration-150">
            Export to Excel
        </a>
    </div>
    <div class="p-6">
        <form action="{{ route('admin.dashboard') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 border p-2 text-sm">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300 border p-2 text-sm">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                <select name="nationality" class="w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    <option value="All">All</option>
                    @foreach($nationalities as $nat)
                        <option value="{{ $nat }}" {{ request('nationality') == $nat ? 'selected' : '' }}>{{ $nat }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                <select name="preferred_language" class="w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    <option value="All">All</option>
                    <option value="en" {{ request('preferred_language') == 'en' ? 'selected' : '' }}>English</option>
                    <option value="ar" {{ request('preferred_language') == 'ar' ? 'selected' : '' }}>Arabic</option>
                    <option value="ur" {{ request('preferred_language') == 'ur' ? 'selected' : '' }}>Urdu</option>
                    <option value="hi" {{ request('preferred_language') == 'hi' ? 'selected' : '' }}>Hindi</option>
                    <option value="tl" {{ request('preferred_language') == 'tl' ? 'selected' : '' }}>Filipino</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Age Group</label>
                <select name="age_group" class="w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                    <option value="All">All</option>
                    <option value="18_24" {{ request('age_group') == '18_24' ? 'selected' : '' }}>18-24</option>
                    <option value="25_34" {{ request('age_group') == '25_34' ? 'selected' : '' }}>25-34</option>
                    <option value="35_44" {{ request('age_group') == '35_44' ? 'selected' : '' }}>35-44</option>
                    <option value="45_54" {{ request('age_group') == '45_54' ? 'selected' : '' }}>45-54</option>
                    <option value="55_plus" {{ request('age_group') == '55_plus' ? 'selected' : '' }}>55+</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Name/Mobile</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="w-full rounded-md border-gray-300 border p-2 text-sm">
            </div>
            
            <div class="md:col-span-3 lg:col-span-6 flex items-center gap-2 mt-2">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Apply Filters</button>
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Clear Filters</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emirates ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demographics</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marketing</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered At</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($registrations as $reg)
                    @php
                        $marketingConsent = $reg->consentRecords->firstWhere('consent_type', 'marketing');
                        $eid = $reg->emirates_id_number;
                        $maskedEid = substr($eid, 0, 3) . '-****-*******-' . substr($eid, -1);
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $loop->iteration + ($registrations->currentPage() - 1) * $registrations->perPage() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $reg->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $reg->mobile_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                            {{ $maskedEid }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $reg->nationality }} &bull; {{ $reg->area_of_residence }}</div>
                            <div class="text-xs text-gray-500">Lang: {{ strtoupper($reg->preferred_language) }} | Age: {{ str_replace('_', '-', $reg->age_group) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($marketingConsent && $marketingConsent->granted)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Yes</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $reg->submitted_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.registrations.show', $reg->id) }}" class="text-primary hover:text-emerald-900">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                            No registrations found matching the filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $registrations->links() }}
    </div>
</div>
@endsection
