<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="gradient-text">Hello, User!</h1>
            <div class="profile">
                <button class="profile-button">
                    <img src="{{ asset('images/user-profile.png') }}" alt="User">
                    Profile</button>
            </div>
        </div>
        <div class="search-container">
            <div class="search-input-container">
                <input type="text" placeholder="Search" class="search-input">
                <button class="search-button">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
            </div>
        </div>
        <div class="main-content">
            <div class="circle family-tree">
                Family Tree
            </div>
            <div class="circle import-gedcom">
                Import GEDCOM
            </div>
        </div>
    </div>
    <div class="footer">
        <p>Copyright 2024 | <a href="#">About</a></p>
    </div>
</body>
</html>