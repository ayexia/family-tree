@extends('layouts.custom')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Tourney:ital,wght@0,100..900;1,100..900&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<style>
    .profile {
        position: absolute;
        right: -35%;
        top: -15%;
    }

    .home-button {
        position: absolute;
        left: -17%;
        top: 15%;
    }

    .custom-background {
        background-color: #00796b;
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
        background-color: #004d40;
        font-family: "Inika", serif;
    }

    .custom-button {
        background-color: #004d40;
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
        background-color: #00695c;
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
        background-color: #004d40;
    }

    .subheading {
        color: #004d40;
    }

    .info {
        color: #004d40;
        font-family: "Inika", serif;
        font-size: 0.8rem;
    }

    .profile-button, .home-button {
        background-color: #00796b;
        color: #EDECD7;
    }

    .profile-button:hover, .home-button:hover {
        background-color: #004d40;
    }

    .tooltip-trigger {
        position: relative;
        cursor: help;
    }

    .tooltip-trigger .tooltip-text {
        visibility: hidden;
        width: 200px;
        background-color: #004d40;
        color: #EDECD7;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        left: 50%;
        margin-left: -100px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip-trigger:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .profile .tooltip-trigger .tooltip-text {
        top: 100%;
        bottom: auto;
        margin-top: 5px;        
        font-size: 0.5em;
    }

    .footer .tooltip-trigger .tooltip-text {
        bottom: 100%;
        top: auto;
        margin-bottom: 5px;
        font-size: 0.8em;
    }

    .tooltip-trigger .tooltip-text::after {
        content: "";
        position: absolute;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
    }

    .profile .tooltip-trigger .tooltip-text::after {
        top: -10px;
        border-color: transparent transparent #004d40 transparent;
    }

    .footer .tooltip-trigger .tooltip-text::after {
        bottom: -10px;
        border-color: #004d40 transparent transparent transparent;
    }
</style>
<div class="container">
    <div class="header">
        <h1 class="subheading">Submit Feedback</h1>
        <p class="info">Your feedback helps us improve MyStory. We appreciate your thoughts and suggestions!</p>
        <div class="profile">
            <a href="{{ route('profile.edit') }}" class="profile-button tooltip-trigger">
                <img src="{{ asset('images/user-profile.png') }}" alt="User">
                Profile
                <span class="tooltip-text">Edit your profile, logout or delete your account</span>
            </a>
        </div>
    </div>
    <a href="{{ route('home') }}" class="profile-button home-button">
        <img src="{{ asset('images/home.png') }}" alt="Home">
        Home
    </a>
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

<footer class="footer">
    <p>
        Copyright 2024 | 
        <span class="tooltip-trigger">
            <a href="{{ route('about') }}">About MyStory</a>
            <span class="tooltip-text">Learn more about our application and its features</span>
        </span> | 
        <span class="tooltip-trigger">
            <a href="#">Submit Feedback</a>
            <span class="tooltip-text">Share your thoughts and suggestions to help us improve</span>
        </span>
    </p>
</footer>
@endsection