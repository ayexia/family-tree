@extends('layouts.custom')

@section('content')
<title>Edit Person</title>
<link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Tourney:ital,wght@0,100..900;1,100..900&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<style>
    .profile {
        position: absolute;
        right: -250px;
        top: -20%;
    }

    .home-button {
        position: absolute;
        left: -100px;
        top: -30px;
        height: 60px;
    }

    .back-to-tree-button {
        position: absolute;
        left: 75px;
        top: 12.5%;
        background-color: #00796b;
        color: #EDECD7;
        font-family: "Inika", serif;
        border-radius: 2.5rem;
        padding: 1rem 1.5rem;
        border: none;
        cursor: pointer;
        text-transform: none;
        text-decoration: none;
        display: flex;
        align-items: center;
        font-size: 1.4rem;
        font-weight: bold;
    }

    .back-to-tree-button img {
        width: 35px;
        height: 35px;
        opacity: 0.5;
        margin-right: 10px;
    }

    .back-to-tree-button:hover {
        background-color: #004d40;
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
        left: 0px;
        background-color: #004d40;
        width: 100%;
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

    .back-to-tree-button .tooltip-text {
        visibility: hidden;
        width: 120px;
        background-color: #004d40;
        color: #EDECD7;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -60px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 0.8rem;
    }

    .back-to-tree-button:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .back-to-tree-button .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #004d40 transparent transparent transparent;
    }
    .view-profile-link {
        position: absolute;
        top: 10px;
        right: 20px;
        padding: 5px 10px;
        background-color: #004d40;
        color: #EDECD7;
        text-decoration: none;
        border-radius: 15px;
        font-size: 0.8em;
        transition: background-color 0.3s;
        font-family: "Inika", serif;
    }

    .view-profile-link:hover {
        background-color: #00796b;
    }
</style>
<div class="container">
    <div class="header">
        <h1 class="subheading">Edit Person</h1>
        <p class="info">Edit details for this person in your family tree. Changes will be saved and reflected in your family tree view.</p>
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
        Home</a>
</div>
<div class="max-w-3xl mx-auto mt-8 custom-background">
    <div class="bg-white dark:bg-gray-800 overflow-hidden" style="box-shadow: none; padding: 2rem;">
    <a href="{{ route('member.profile', ['id' => $person->id]) }}" class="view-profile-link">View Profile</a>

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

        <form action="{{ route('person.update', $person->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $person->name) }}" required>
            </div>

            <div>
                <label for="birth_date">Date of Birth</label>
                <input type="date" id="birth_date" name="birth_date" class="form-control" value="{{ old('birth_date', $person->birth_date ? $person->birth_date->format('Y-m-d') : '') }}">
            </div>

            <div>
                <label for="death_date">Date of Death</label>
                <input type="date" id="death_date" name="death_date" class="form-control" value="{{ old('death_date', $person->death_date ? $person->death_date->format('Y-m-d') : '') }}">
            </div>

            <div>
                <label for="birth_place">Birth Place</label>
                <input type="text" name="birth_place" id="birth_place" class="form-control" value="{{ old('birth_place', $person->birth_place) }}">
            </div>

            <div>
                <label for="death_place">Death Place</label>
                <input type="text" name="death_place" id="death_place" class="form-control" value="{{ old('death_place', $person->death_place) }}">
            </div>

            <div id="marriages-container">
                @foreach ($person->firstSpouses as $index => $spouse)
                    <div class="marriage-group">
                        <input type="hidden" name="marriages[{{ $index }}][id]" value="{{ $spouse->id }}">

                        <label for="marriage_date_{{ $index }}">Marriage {{ $index + 1 }} Date</label>
                        <input type="date" id="marriage_date_{{ $index }}" name="marriages[{{ $index }}][marriage_date]" class="form-control" value="{{ old('marriages.' . $index . '.marriage_date', $spouse->marriage_date ? $spouse->marriage_date->format('Y-m-d') : '') }}">

                        <label for="divorce_date_{{ $index }}">Divorce {{ $index + 1 }} Date</label>
                        <input type="date" id="divorce_date_{{ $index }}" name="marriages[{{ $index }}][divorce_date]" class="form-control" value="{{ old('marriages.' . $index . '.divorce_date', $spouse->divorce_date ? $spouse->divorce_date->format('Y-m-d') : '') }}">
                    </div>
                @endforeach

                @foreach ($person->secondSpouses as $index => $spouse)
                    <div class="marriage-group">
                        <input type="hidden" name="marriages[{{ $index + $person->firstSpouses->count() }}][id]" value="{{ $spouse->id }}">

                        <label for="marriage_date_{{ $index + $person->firstSpouses->count() }}">Marriage {{ $index + $person->firstSpouses->count() + 1 }} Date</label>
                        <input type="date" id="marriage_date_{{ $index + $person->firstSpouses->count() }}" name="marriages[{{ $index + $person->firstSpouses->count() }}][marriage_date]" class="form-control" value="{{ old('marriages.' . ($index + $person->firstSpouses->count()) . '.marriage_date', $spouse->marriage_date ? $spouse->marriage_date->format('Y-m-d') : '') }}">

                        <label for="divorce_date_{{ $index + $person->firstSpouses->count() }}">Divorce {{ $index + $person->firstSpouses->count() + 1 }} Date</label>
                        <input type="date" id="divorce_date_{{ $index + $person->firstSpouses->count() }}" name="marriages[{{ $index + $person->firstSpouses->count() }}][divorce_date]" class="form-control" value="{{ old('marriages.' . ($index + $person->firstSpouses->count()) . '.divorce_date', $spouse->divorce_date ? $spouse->divorce_date->format('Y-m-d') : '') }}">
                    </div>
                @endforeach
            </div>
            <div>
                <label for="pets">Pets (comma-separated)</label>
                <input type="text" id="pets" name="pets" class="form-control" value="{{ old('pets', $person->pets ? implode(', ', $person->pets) : '') }}">
            </div>

            <div>
                <label for="hobbies">Hobbies (comma-separated)</label>
                <input type="text" id="hobbies" name="hobbies" class="form-control" value="{{ old('hobbies', $person->hobbies ? implode(', ', $person->hobbies) : '') }}">
            </div>

            <div>
                <label for="notes">Special Notes</label>
                <textarea id="notes" name="notes" class="form-control" rows="4">{{ old('notes', $person->notes) }}</textarea>
            </div>
            <button type="submit" class="custom-button">
                Save Changes
            </button>
        </form>
    </div>
</div>

<a href="{{ route('display') }}" class="back-to-tree-button">
    <img src="{{ asset('images/tree-icon.png') }}" alt="Tree Icon">
    Family Tree
    <span class="tooltip-text">View your family tree</span>
</a>

<footer class="footer">
        <p>
            Copyright 2024 | 
            <span class="tooltip-trigger">
                <a href="{{ route('about') }}">About MyStory</a>
                <span class="tooltip-text">Learn more about our application and its features</span>
            </span> | 
            <span class="tooltip-trigger">
                <a href="{{ route('feedback.create') }}">Submit Feedback</a>
                <span class="tooltip-text">Share your thoughts and suggestions to help us improve</span>
            </span>
        </p>
    </footer>
@endsection