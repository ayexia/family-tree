<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About MyStory</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<style>
.profile-button {
    text-decoration: none;
}
</style>
<body>
    <div class="container">
        <div class="header">
            <h1 class="subheading">About MyStory</h1>
            <div class="profile">
            <a href="{{route('profile.edit') }}" class="profile-button">
                <img src="{{ asset('images/user-profile.png') }}" alt="User">
                Profile
            </a>
            </div>
        </div>
        <a href="{{ route('home') }}" class="profile-button home-button">
                <img src="{{ asset('images/home.png') }}" alt="Home">
                Home
            </a>                    
        <div class="main-content">
            <div class="textbox one">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
            </div>
            <div class="about tree">
            <img src="{{ asset('images/about-image-1.png') }}" alt="User 1">
            </div>
            <div class="textbox two">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
            </div>
            <div class="about dna">
            <img src="{{ asset('images/about-image-2.png') }}" alt="User 1">
            </div>
        </div>
    </div>
    <div class="footer">
        <p>Copyright 2024 | <a href="#">About</a></p>
    </div>
</body>
</html>