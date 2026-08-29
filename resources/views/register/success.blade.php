@extends('layouts.app')

@section('title', 'Registration Successful')

@section('content')
<div class="bg-white rounded-xl shadow-lg p-8 max-w-md w-full text-center">
    <div class="w-20 h-20 bg-green-100 text-primary rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    
    <h2 class="text-3xl font-bold text-gray-800 mb-4">Thank You!</h2>
    <p class="text-gray-600 text-lg mb-8">Your registration has been submitted successfully.</p>
    
    <p class="text-sm text-gray-500 mb-4">Redirecting back in <span id="countdown" class="font-bold">15</span> seconds...</p>
    
    <a href="{{ route('register.show') }}" class="block w-full bg-primary text-white font-bold py-4 rounded-lg text-lg hover:bg-emerald-600 transition">
        Register Another
    </a>
</div>

<script>
    let timeLeft = 15;
    const countdownEl = document.getElementById('countdown');
    
    const timer = setInterval(() => {
        timeLeft--;
        countdownEl.textContent = timeLeft;
        
        if (timeLeft <= 0) {
            clearInterval(timer);
            window.location.href = "{{ route('register.show') }}";
        }
    }, 1000);
</script>
@endsection
