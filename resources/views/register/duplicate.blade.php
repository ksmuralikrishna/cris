@extends('layouts.app')

@section('title', 'Already Registered')

@section('content')
<div class="bg-white rounded-xl shadow-lg p-8 max-w-md w-full text-center">
    <div class="w-20 h-20 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
    </div>
    
    <h2 class="text-3xl font-bold text-gray-800 mb-4">Already Registered</h2>
    <p class="text-gray-600 text-lg mb-8">This Emirates ID is already registered in our system. If you believe this is an error, please contact customer service.</p>
    
    <a href="{{ route('register.show') }}" class="block w-full bg-primary text-white font-bold py-4 rounded-lg text-lg hover:bg-emerald-600 transition">
        Return to Home
    </a>
</div>
@endsection
