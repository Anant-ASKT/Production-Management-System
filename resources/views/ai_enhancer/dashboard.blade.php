@extends('layouts.ai_enhancer')

@section('title', 'AI Enhancer Dashboard')

@section('content')

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="welcome-card position-relative overflow-hidden" style="background: linear-gradient(135deg, #2b5288, #193b68); color: white; border-radius: 15px; padding: 25px;">
                <h2 class="fw-bold mb-2">Welcome back, {{ auth('ai_enhancer')->user()->first_name }}!</h2>
                <p class="mb-0 text-white-50">Here's what's happening with your AI enhancements today.</p>
            </div>
        </div>
    </div>
@endsection
