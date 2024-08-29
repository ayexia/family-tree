<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Tree</title>
    @viteReactRefresh 
    @vite('resources/js/app.jsx')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<style>
    .title {
        left: 515px;
        top: 0px;
        position: absolute;
        font-family: "Inika", serif;
        font-size: 40px;
    }

    .profile-button {
        text-decoration: none;
        font-family: "Inika", serif;
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
        left: 6.75%;
        top: 25px;
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
        z-index: 1000;
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
        font-family: "Inika", serif;
    }

    .search-results ul li:first-child {
        background: #9BB08C;
        padding: 15px;
        border-radius: 20px;
        border: none;
        color: #EDECD7;
        font-family: "Inika", serif;
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
        font-family: "Inika", serif;
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
        font-family: "Inika", serif;
    }

    #root {
        background-color: transparent;
        padding: 20px;
        border-radius: 15px;
        max-width: 1200px;
        width: 100%;
        margin: 0px auto 80px;
        position: relative;
        top: 45px;
    }
    
    .circle.info-tooltip {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #587353;
        color: #EDECD7;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 16px;
        font-family: "Inika", serif;
        font-weight: bold;
        cursor: pointer;
        position: absolute;
        left: 34%;
        top: 25px;
    }

    .circle .tooltip {
        visibility: hidden;
        width: 300px;
        background-color: #9BB08C;
        color: #EDECD7;
        text-align: left;
        padding: 10px;
        border-radius: 6px;
        position: absolute;
        z-index: 1;
        top: 125%;
        left: 50%;
        font-size: 0.8em;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .circle .tooltip::after {
        content: "";
        position: absolute;
        top: 125%;
        left: 50%;
        border-width: 5px;
        border-style: solid;
        border-color: #9BB08C transparent transparent transparent;
    }

    .circle:hover .tooltip {
        visibility: visible;
        opacity: 1;
    }

    .home-button.import-gedcom {
        left: 22.75%;
    }

    .info-text {
        display: none;
    }
</style>
</head>
<body>
    <header class="header">
        <h1 class="title">Family Tree</h1>
        <div class="profile">
            <a href="{{route('profile.edit') }}" class="profile-button">
                <img src="{{ asset('images/user-profile.png') }}" alt="User">
                Profile
            </a>
        </div>
        <a href="{{ route('home') }}" class="profile-button home-button">
            <img src="{{ asset('images/home.png') }}" alt="Home">
            Home
        </a>
        <a href="{{ route('import.form') }}" class="profile-button home-button import-gedcom">
            Import GEDCOM
        </a>
        <div class="circle info-tooltip">
            ?
            <span class="tooltip">
                <p>Welcome to your Family Tree page! Here you can:</p>
                <p>- View and explore your family tree</p>
                <p>- Search for specific family lines</p>
                <p>- Adjust the number of visible generations</p>
                <p>- Switch between tree and graph views</p>
                <p>- Edit family members and upload images/memories</p>
                <p>- Search for specific individuals</p>
                <p>- Export as PDF book</p>
            </span>
        </div>
    </header>

    <div id="root"></div>

    <footer class="footer">
        <p>Copyright 2024 | <a href="{{ route('about') }}">About MyStory</a></p>
    </footer>
</body>
</html>