@extends('layouts.custom')

@section('content')
<title>Import a GEDCOM file</title>
<link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Tourney:ital,wght@0,100..900;1,100..900&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<style>
    body {
        padding-top: 60px;
    }

    .profile {
        position: fixed;
        right: 2%;
        top: 10px;
    }

    .custom-background {
        background-color: #00796b;
        padding: 2rem;
        border-radius: 2.5rem;
        box-shadow: none;
        height: 28rem;
        width: 35rem;
        margin: auto;
        bottom: 8%;
        position: fixed;
        right: 27.5%;
        overflow-y: auto;
    }

    .custom-background label {
        color: #EDECD7;
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
        right: 5%;
        position: absolute;
    }

    .custom-button:hover {
        background-color: #00695c;
    }

    .custom-error, .custom-success {
        border-radius: 0.25rem;
        padding: 0.75rem 1.25rem;
        font-family: "Inika", serif;
        font-size: 0.875rem;
        margin-bottom: 1rem;
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

    .file-type-info {
        margin-top: 1em;
        color: #EDECD7;
        font-size: 0.875rem;
        font-family: "Inika", serif;
    }

    .home-button {
        position: fixed;
        left: 180px;
        top: 8px;
        height: 60px;
    }

    .back-to-tree-button {
        position: fixed;
        left: 80px;
        top: 80%;
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

    .footer {
        text-align: center;
        font-family: "Inika", serif;
        margin-top: 2rem;
        position: fixed;
        right: 0%;
        padding: 0.53rem;
        color: #EDECD7;
        background-color: #004d40;
    }

    .instructions {
        margin-top: 2rem;
        color: #EDECD7;
        font-size: 0.875rem;
        font-family: "Inika", serif;
    }

    .instructions ul {
        padding-left: 0rem;
    }

    .subheading {
        color: #004d40;
        position: fixed;
        left: 500px;
        top: 75px;
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
</style>
<div class="container">
    <div class="header">
        <h1 class="subheading">Import GEDCOM</h1>
        <div class="profile">
                <a href="{{ route('profile.edit') }}" class="profile-button tooltip-trigger">
                    <img src="{{ asset('images/user-profile.png') }}" alt="User">
                    Profile
                    <span class="tooltip-text">Edit your profile, logout or delete your account</span>
                </a>
            </div>
    </div>
    </div>
    <a href="{{ route('home') }}" class="profile-button home-button">
        <img src="{{ asset('images/home.png') }}" alt="Home">
        Home
    </a>
</div>
<div class="max-w-3xl mx-auto mt-8 custom-background">
    <div class="bg-white dark:bg-gray-800 overflow-hidden" style="box-shadow: none;">
        @if (session('success'))
            <div class="custom-success rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="custom-error rounded">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <p class="instructions">To upload your GEDCOM file, follow these steps:</p>
        <ol class="instructions">
            <li>Click on the "Choose GEDCOM file" button to select your file.</li>
            <li>Ensure your file has the ".ged" extension. Files with other extensions will not be accepted.</li>
            <li>Click "Upload GEDCOM" to start the upload process.</li>
        </ol>

        <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label for="gedcom_file">Choose GEDCOM file</label>
                <input type="file" name="gedcom_file" id="gedcom_file" accept=".ged" style="margin-top: 0.25rem; display: block; width: 100%; font-size: 0.875rem; color: #EDECD7; border: 1px solid #004d40; border-radius: 0.375rem; cursor: pointer; background-color: #004d40;">
                <p class="file-type-info">Files must be of .ged extension</p>
            </div>
            <div>
                <button type="submit" class="custom-button">
                    Upload GEDCOM
                </button>
            </div>
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