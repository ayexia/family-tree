@extends('layouts.custom')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Tourney:ital,wght@0,100..900;1,100..900&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<style>
    .profile {
        position: absolute;
        right: -60%;
        top: -15%;
    }

    .home-button {
        position: absolute;
        left: -35%;
        top: 15%;
    }

    .back-to-tree-button {
        position: absolute;
        left: 5%;
        top: 20%;
        background-color: #587353;
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
        opacity: 0.3;
        margin-right: 10px;
    }

    .back-to-tree-button:hover {
        background-color: #4a6848;
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
        left: 0px;
    }
</style>
<div class="container">
    <div class="header">
        <h1 class="subheading">Edit Person</h1>
        <div class="profile">
            <a href="{{ route('profile.edit') }}" button class="profile-button">
                <img src="{{ asset('images/user-profile.png') }}" alt="User">
                Profile</a>
        </div>
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
</a>

<div class="footer">
    <p>Copyright 2024 | <a href="{{ route('about') }}">About MyStory</a> | <a href="{{ route('feedback.create') }}">Submit Feedback</a></p>
</div>
@endsection