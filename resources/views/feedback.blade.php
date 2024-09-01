@extends('layouts.custom')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Tourney:ital,wght@0,100..900;1,100..900&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<style>
    .home-button {
        position: absolute;
        left: -35%;
        top: 15%;
    }

    .custom-background {
        background-color: #9BB08C;
        padding: 0rem;
        border-radius: 2.5rem;
        box-shadow: none;
        width: 35rem;
        margin: auto;
        bottom: 30px;
        position: relative;
        right: 0px;
    }

    .custom-background label {
        color: #EDECD7;
        font-family: "Inika", serif;
    }

    .form-control {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #EDECD7;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        color: #EDECD7;
        background-color: #678A5C;
        font-family: "Inika", serif;
    }

    .custom-button {
        background-color: #587353;
        color: #EDECD7;
        font-family: "Inika", serif;
        border-radius: 2.5rem;
        padding: 0.5rem 1rem;
        border: none;
        cursor: pointer;
        text-transform: none;
        width: 100%;
        margin-top: 1rem;
    }

    .custom-button:hover {
        background-color: #4a6848;
    }

    .custom-error, .custom-success {
        border-radius: 0.25rem;
        padding: 0.75rem 1.25rem;
        font-family: "Inika", serif;
        font-size: 0.875rem;
    }

    .custom-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .custom-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .footer {
        text-align: center;
        font-family: "Inika", serif;
        margin-top: 2rem;
        padding: 0.53rem;
        color: #EDECD7;
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        background-color: #6C9661;
    }
</style>
<div class="container">
    <div class="header">
        <h1 class="subheading">Submit Feedback</h1>
    </div>
    <a href="{{ route('home') }}" class="profile-button home-button">
        <img src="{{ asset('images/home.png') }}" alt="Home">
        Home</a>
</div>
<div class="max-w-3xl mx-auto mt-8 custom-background">
    <div class="bg-white dark:bg-gray-800 overflow-hidden" style="box-shadow: none; padding: 2rem;">

        @if (session('success'))
            <div class="custom-success mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="custom-error mb-4 rounded">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('feedback.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="content">Your Feedback</label>
                <textarea id="content" name="content" class="form-control" rows="6" required>{{ old('content') }}</textarea>
            </div>

            <button type="submit" class="custom-button">
                Submit Feedback
            </button>
        </form>
    </div>
</div>

    <div class="footer">
        <p>Copyright 2024 | <a href="{{ route('about') }}">About MyStory</a> | <a href="#">Submit Feedback</a></p>
    </div>
@endsection