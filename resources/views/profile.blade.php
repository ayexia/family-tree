@extends('layouts.custom')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
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
        opacity: 0.3;
        margin-right: 10px;
    }

    .back-to-tree-button:hover {
        background-color: #004d40;
    }

    .custom-background {
        background-color: #00796b;
        padding: 2rem;
        border-radius: 2.5rem;
        box-shadow: none;
        width: 35rem;
        margin: auto;
        position: relative;
    }

    .profile-info, .family-tree-display {
        color: #EDECD7;
        font-family: "Inika", serif;
    }

    .profile-image {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 20px;
        display: block;
    }

    .family-tree-display {
        margin-top: 2rem;
    }

    .family-tree {
        font-family: monospace;
        white-space: pre-wrap;
        list-style-type: none;
        padding-left: 0;
        margin: 0;
        text-align: left;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .footer {
        text-align: center;
        font-family: "Inika", serif;
        margin-top: 2rem;
        padding: 0.53rem;
        color: #EDECD7;
        position: fixed;
        left: 0px;
        bottom: 0px;
        background-color: #004d40;
        width: 100%;
    }

    .subheading {
        color: #004d40;
    }

    .profile-button, .home-button {
        background-color: #00796b;
        color: #EDECD7;
    }

    .profile-button:hover, .home-button:hover {
        background-color: #004d40;
    }

    .search-container {
        max-width: 250px;
        position: absolute;
        top: 100px;
        right: 30px;
    }

    .search-input {
        background-color: #00796b;
        color: #EDECD7;
        max-width: 300px;
    }

    .search-input::placeholder {
        color: #EDECD7;
    }
</style>
<div class="container">
    <div class="header">
        <h1 class="subheading">{{ $person->name }}'s Profile</h1>
        <div class="profile">
            <a href="{{ route('profile.edit') }}" class="profile-button">
                <img src="{{ asset('images/user-profile.png') }}" alt="User">
                Profile
            </a>
        </div>
    </div>
    <a href="{{ route('home') }}" class="profile-button home-button">
        <img src="{{ asset('images/home.png') }}" alt="Home">
        Home
    </a>
</div>
<div class="search-container">
            <div class="search-input-container">
            @if($familyTreeId)
    <form method="GET" action="{{ route('family.tree', ['familyTreeId' => $familyTreeId]) }}">
        <input type="text" id="desiredName" name="desiredName" value="{{ request('desiredName') }}" class="search-input" placeholder="Search for family">
        <button type="submit" class="search-button">
            <img src="{{ asset('images/search.png') }}" alt="Search">
        </button>
    </form>
    @else
            <p>You don't have a family tree yet. Please import a GEDCOM file first.</p>
        @endif
</div>
</div>
<div class="max-w-3xl mx-auto mt-8 custom-background">
    <div class="bg-white dark:bg-gray-800 overflow-hidden" style="box-shadow: none; padding: 2rem;">
        <div class="profile-info">
            @if($person->image)
                <img src="{{ asset('storage/' . $person->image) }}" alt="{{ $person->name }}" class="profile-image">
            @else
                <img src="{{ asset('images/user-profile.png') }}" alt="{{ $person->name }}" class="profile-image">
            @endif

            <h2>Personal Information</h2>
            <p><strong>Name:</strong> {{ $person->name }}</p>
            <p><strong>Surname:</strong> {{ $person->surname }}</p>
            <p><strong>Birth Date:</strong> {{ $person->birth_date ?? 'Unknown' }}</p>
            <p><strong>Birth Place:</strong> {{ $person->birth_place ?? 'Unknown' }}</p>
            <p><strong>Death Date:</strong> {{ $person->death_date ?? 'N/A' }}</p>
            <p><strong>Death Place:</strong> {{ $person->death_place ?? 'N/A' }}</p>
            <p><strong>Gender:</strong> {{ $person->gender ?? 'Unknown' }}</p>
            <p><strong>Pets:</strong> {{ is_array($person->pets) ? implode(', ', $person->pets) : $person->pets }}</p>
            <p><strong>Hobbies:</strong> {{ is_array($person->hobbies) ? implode(', ', $person->hobbies) : $person->hobbies }}</p>
            <p><strong>Notes:</strong> {{ $person->notes ?? 'None' }}</p>
        </div>

        <div class="family-tree-display">
            <h2><b><u>Family Tree</u></b></h2>
            <pre class="family-tree">@if(!empty($tree))
@foreach($tree as $entry){{ $entry }}
@endforeach
@else
No family tree data available.
@endif</pre>
        </div>
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