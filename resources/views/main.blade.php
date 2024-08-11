<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyStory</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Tourney:ital,wght@0,100..900;1,100..900&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
    <style>
        html, body {
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        display: flex;
        flex-direction: column;
        margin: 0;
        padding: 0;
    }

    .container {
        display: flex;
        flex-direction: column;
        height: 100%;
        width: 100%;
    }

    .header {
        position: fixed;
        top: 0;
        width: 100vw;
        height: 100vh;
        background-image: url('{{ asset("images/tree.png") }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        overflow: hidden;
    }

    .user-icon {
        position: absolute;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        border: 5px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        background-color: transparent;
    }

    .user-icon img {
        width: 60%;
        height: 60%;
        border-radius: 50%;
        opacity: 0.3;
    }

    .icon1 {
        top: 10%;
        left: 5%;
        border-color: #FF6347;
    }

    .icon2 {
        top: 20%;
        left: 45%;
        border-color: #2B7563;
    }

    .icon3 {
        top: 30%;
        left: 65%;
        border-color: #B797EB;
    }

    .icon4 {
        top: 10%;
        left: 35%;
        border-color: #EBBF97;
    }

    .icon5 {
        top: 20%;
        left: 57%;
        border-color: #8A2BE2;
    }

    .icon6 {
        top: 40%;
        left: 55%;
        border-color: #EB97CF;
    }

    .icon7 {
        top: 5%;
        left: 45%;
        border-color: #EB97CF;
    }

    .icon8 {
        top: 25%;
        left: 40%;
        border-color: #97EBE6;
    }

    .icon9 {
        top: 15%;
        left: 87%;
        border-color: #97EBE6;
    }

    .title {
        font-family: "Tourney", sans-serif;
        background: repeating-linear-gradient(90deg, #264E02, #37672F, #79966e, #99AF89);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 3em;
        font-weight: bold;
        margin: 0;
        position: absolute;
        bottom: 350px;
        left: 180px;
    }

    .subheading {
        font-family: "Waiting for the Sunrise", cursive;
        font-size: 1.5em;
        color: #264E02;
        margin-top: 10px;
        position: absolute;
        bottom: 250px;
        left: 60px;
    }

    .login-button {
        font-size: 0.6em;
        padding: 8px 40px;
        position: absolute;
        top: 20px;
        right: 75px;
        text-decoration: none;
    }

    .register-button {
        font-size: 1em;
        padding: 20px 40px;
        position: absolute;
        bottom: 100px;
        left: 30%;
        transform: translateX(-50%);
        text-decoration: none;
    }

    .about-button {
        font-size: 1em;
        padding: 20px 40px;
        position: absolute;
        bottom: 100px;
        left: 60%;
        transform: translateX(-50%);
        text-decoration: none;
    }

    .user-about-button {
        font-size: 1em;
        padding: 20px 40px;
        position: absolute;
        bottom: 100px;
        left: 46.25%;
        transform: translateX(-50%);
        text-decoration: none;
    }

    .main-content {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .footer {
        background-color: #6C9661;
        padding: 10px;
        width: 100%;
        box-sizing: border-box;
        text-align: center;
        color: #EDECD7;
        font-weight: bold;
        position: fixed;
        bottom: 0;
        left: 0;
        font-family: "Inika", serif;
        font-size: 0.5em;
    }

    .footer a {
        color: #EDECD7;
        text-decoration: none;
    }

    .footer a:hover {
        text-decoration: underline;
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
        <div class="user-icon icon1">
            <img src="{{ asset('images/user.png') }}" alt="User 1">
        </div>
        <div class="user-icon icon2">
            <img src="{{ asset('images/user.png') }}" alt="User 2">
        </div>
        <div class="user-icon icon3">
            <img src="{{ asset('images/user.png') }}" alt="User 3">
        </div>
        <div class="user-icon icon4">
            <img src="{{ asset('images/user.png') }}" alt="User 4">
        </div>
        <div class="user-icon icon5">
            <img src="{{ asset('images/user.png') }}" alt="User 5">
        </div>
        <div class="user-icon icon6">
            <img src="{{ asset('images/user.png') }}" alt="User 6">
        </div>
        <div class="user-icon icon7">
            <img src="{{ asset('images/user.png') }}" alt="User 7">
        </div>
        <div class="user-icon icon8">
            <img src="{{ asset('images/user.png') }}" alt="User 8">
        </div>
        <div class="user-icon icon9">
            <img src="{{ asset('images/user.png') }}" alt="User 9">
        </div>
            <h1 class="title">MyStory</h1>
            <h2 class="subheading">Where does my family come from?</h2>
            <div class="profile">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/home') }}" class="profile-button login-button">
                    <img src="{{ asset('images/home.png') }}" alt="Home">Home
                    </a>
                @else
                <a href="{{ route('login') }}" class="profile-button login-button">Login</a>
            @endauth
                @endif
            </div>

        <div class="main-content">
        @if (!auth()->check())
            <a href="{{ route('register') }}" class="profile-button register-button">Register Today</a>
            <a href="{{ route('about') }}" class="profile-button about-button">About MyStory</a>
        </div>
        @else        
        <div class="main-content">
        <a href="{{ route('about') }}" class="profile-button user-about-button">About MyStory</a>
        </div>
        @endif
        <div class="footer">
            <p>Copyright 2024</p>
        </div>
    </div>
    
</body>
</html>