@extends('layouts.admin')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Admin Login</h2>
        
        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}"
                       class="w-full rounded-lg border-gray-300 border p-3 focus:ring-primary focus:border-primary">
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                       class="w-full rounded-lg border-gray-300 border p-3 focus:ring-primary focus:border-primary">
            </div>
            
            <button type="submit" class="w-full bg-primary text-white font-bold py-3 px-4 rounded-lg hover:bg-emerald-600 transition">
                Sign In
            </button>
        </form>
    </div>
</div>
@endsection
