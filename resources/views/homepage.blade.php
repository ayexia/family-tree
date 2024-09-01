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
    }

    .circle .tooltip {
        visibility: hidden;
        width: 160px;
        background-color: #9BB08C;
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
        border-color: #9BB08C transparent transparent transparent;
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
        background-color: #587353;
        color: #EDECD7;
        border: none;
        padding: 10px 20px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: bold;
        font-size: 0.8em;
        font-family: "Inika", serif;
        text-decoration: none;
        left: 20px;
        top: 10px;
        position: absolute;
    }
    .admin-button:hover {
        background-color: #4a6848;
    }
</style>
<body>
    <div class="container">
        <div class="header">
            <h1 class="gradient-text">Hello, {{ Auth::user()->name }}!</h1>
            <div class="profile">
                <a href="{{ route('profile.edit') }}" class="profile-button">
                    <img src="{{ asset('images/user-profile.png') }}" alt="User">
                    Profile
                </a>
            </div>
            @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="admin-button">For Admin</a>
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
    <div class="footer">
        <p>Copyright 2024 | <a href="{{ route('about') }}">About MyStory</a> | <a href="{{ route('feedback.create') }}">Submit Feedback</a></p>
    </div>
</body>
</html>