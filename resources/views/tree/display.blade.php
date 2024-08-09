<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Tree</title>
    @viteReactRefresh 
    @vite('resources/js/app.jsx')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<style>
    .title {
        left: 550px;
        top: 100px;
        position: absolute;
    }
    .home-button {
        display: flex;
        align-items: center;
        background-color: #587353;
        color: #EDECD7;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 0.7em;
        font-weight: bold;
        transition: background-color 0.3s;
        margin: 10px;
        font-family: "Inika", serif;
        position: absolute;
        top: 161.67px;
        left: 12%;
    }

    .home-button img {
        width: 30px;
        height: 30px;
        margin-right: 20px;
    }

    .home-button:hover {
        background-color: #4a6848;
    }

    .footer {
        text-align: center;
        font-family: "Inika", serif;
        margin-top: 2rem;
        position: fixed;
        right: 0%;
        padding: 0.53rem;
        color: #EDECD7;
    }

    .search-results .family-tree li {
        background-color: #9BB08C;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 20px;
        margin: 10px auto;
        width: 80%;
        max-width: 600px;
        text-align: left;
        color: #EDECD7;
    }

    .search-results ul li:first-child {
        background: #9BB08C;
        padding: 15px;
        border-radius: 20px;
        border: none;
        color: #EDECD7;
    }

    .tree-display-box {
        border: 2px solid #9BB08C;
        padding: 20px;
        border-radius: 20px;
        background-color: #9BB08C;
        margin: 20px auto;
        max-width: 60%;
    }

    .family-tree {
        list-style-type: none;
        padding-left: 0;
        margin: 0;
    }

    .family-tree ul {
        list-style-type: none;
        padding-left: 0;
        margin: 0;
    }

    .family-tree ul li {
        margin: 5px 0;
    }

    .family-tree li {
        background: none;
        padding: 0;
        border: none;
        color: #EDECD7;
    }

    #root {
        background-color: transparent;
        padding: 20px;
        border-radius: 15px;
        max-width: 1200px;
        width: 80%;
        margin: auto;
        position: relative;
        top: 250px;
        padding-bottom: 80px;
    }
</style>
<body>
    <header class="header">
        <h1 class="subheading title">Family Tree</h1>
        <div class="profile">
            <button class="profile-button">
                <img src="{{ asset('images/user-profile.png') }}" alt="User">
                Profile
            </button>
        </div>
        <a href="{{ route('home') }}" class="profile-button home-button">
            <img src="{{ asset('images/home.png') }}" alt="Home">
            Home
        </a>
    </header>

    <div id="root"></div>

    <footer class="footer">
        <p>Copyright 2024 | <a href="{{ route('about') }}">About</a></p>
    </footer>
</body>
</html>