<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<style>
    .profile-button {
        text-decoration: none;
        color: #EDECD7;
    }

    .circle .tooltip {
        visibility: hidden;
        width: 160px;
        background-color: #00796b;
        color: #EDECD7;
        text-align: center;
        padding: 8px 12px;
        border-radius: 6px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.8em;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .circle .tooltip::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: #00796b transparent transparent transparent;
    }

    .circle:hover .tooltip {
        visibility: visible;
        opacity: 1;
    }

    .search-container p {
        font-family: "Inika", serif;
        font-size: 0.8em;
        color: #EDECD7;
        left: -20px;
        position: relative;
    }
    .admin-button {
        background-color: #00796b;
        color: #EDECD7;
        border: none;
        padding: 10px 20px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: bold;
        font-size: 0.8em;
        font-family: "Inika", serif;
        text-decoration: none;
        position: absolute;
        left: -450px;
        top: -112px;
    }
    .admin-button:hover {
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

    .profile .tooltip-trigger .tooltip-text,
    .admin-button .tooltip-text {
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

    .profile .tooltip-trigger .tooltip-text::after,
    .admin-button .tooltip-text::after {
        top: -10px;
        border-color: transparent transparent #004d40 transparent;
    }

    .footer .tooltip-trigger .tooltip-text::after {
        bottom: -10px;
        border-color: #004d40 transparent transparent transparent;
    }
</style>
<body>
    <div class="container">
        <div class="header">
            <h1 class="gradient-text">Hello, {{ Auth::user()->name }}!</h1>
            <div class="profile">
                <a href="{{ route('profile.edit') }}" class="profile-button tooltip-trigger">
                    <img src="{{ asset('images/user-profile.png') }}" alt="User">
                    Profile
                    <span class="tooltip-text">Edit your profile, logout or delete your account</span>
                </a>
            </div>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="admin-button tooltip-trigger">
                    For Admin
                    <span class="tooltip-text">Go to the admin dashboard to manage users and view feedback</span>
                </a>
            @endif
        </div>
        <div class="search-container">
            <p>This is your dashboard - you can start your journey by uploading a GEDCOM file, view or edit your tree, and search for family members.</p>
            <div class="search-input-container">
                <form method="GET" action="{{ route('family.tree', ['familyTreeId' => $familyTreeId]) }}">
                    <input type="text" id="desiredName" name="desiredName" value="{{ request('desiredName') }}" class="search-input" placeholder="Search for a family member">
                    <button type="submit" class="search-button">
                        <img src="{{ asset('images/search.png') }}" alt="Search">
                    </button>
                </form>
            </div>
        </div>
        <div class="main-content">
            <a href="{{ route('display') }}" class="circle family-tree">
                Family Tree
                <span class="tooltip">View your family tree and explore your ancestry</span>
            </a>
            <a href="{{ route('import.form') }}" class="circle import-gedcom">
                Import GEDCOM
                <span class="tooltip">Import a GEDCOM file to build your family tree</span>
            </a>
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
</body>
</html>