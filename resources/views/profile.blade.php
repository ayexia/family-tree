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
    .edit-profile-link {
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
    }

    .edit-profile-link:hover {
        background-color: #00796b;
    }
</style>
<div class="container">
    <div class="header">
        <h1 class="subheading">{{ $person->name }}'s Profile</h1>
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
<div class="search-container">
            <div class="search-input-container">
            @if($familyTreeId)
    <form method="GET" action="{{ route('family.tree', ['familyTreeId' => $familyTreeId]) }}">
        <input type="text" id="desiredName" name="desiredName" value="{{ request('desiredName') }}" class="search-input" placeholder="Search for family">
        <button type="submit" class="search-button">
            <img src="{{ asset('images/search.png') }}" alt="Search">
        </button>
    </form>
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
            <a href="{{ route('person.edit', ['id' => $person->id]) }}" class="edit-profile-link">Edit Profile</a>

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