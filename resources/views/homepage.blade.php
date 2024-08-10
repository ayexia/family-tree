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
.circle {
    text-decoration: none;
}
</style>
<body>
    <div class="container">
        <div class="header">
            <h1 class="gradient-text">Hello, User!</h1>
            <div class="profile">
            <a href="{{route('profile.edit') }}" class="profile-button">
                <img src="{{ asset('images/user-profile.png') }}" alt="User">
                Profile
            </a>
            </div>
        </div>
        <div class="search-container">
            <div class="search-input-container">
                <form method="GET" action="{{ route('family.tree') }}">
                    <input type="text" id="desiredName" name="desiredName" value="{{ request('desiredName') }}" class="search-input" placeholder="Search">
                    <button type="submit" class="search-button">
                        <img src="{{ asset('images/search.png') }}" alt="Search">
                    </button>
                </form>
            </div>
        </div>
        <div class="main-content">
            <a href="{{route('display') }}" class="circle family-tree">
                Family Tree
            </a>
            <a href="{{route('import') }}" class="circle import-gedcom">
                Import GEDCOM
            </a>
        </div>
    </div>
    <div class="footer">
        <p>Copyright 2024 | <a href="{{ route('about') }}">About</a></p>
    </div>
</body>
</html>