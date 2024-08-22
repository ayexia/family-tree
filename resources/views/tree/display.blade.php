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
    }

    .info-text {
        background-color: #9BB08C;
        padding: 15px;
        border-radius: 15px;
        max-width: 600px;
        width: 90%;
        margin: 125px auto;
        color: #EDECD7;
        text-align: center;
        font-family: "Inika", serif;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        font-size: 0.8em;
    }

    .info-text p {
        margin: 10px 0;
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
    </header>

    <div id="root"></div>
    <div class="info-text">
        <p>Welcome to your Family Tree page! Here you can:</p>
        <p>View and explore your family tree - the default tree view allows you to search for a specific family line (surname). After clicking "search" this then automatically updates when typing a new name. You are also able to adjust the number of generations in your family that you wish to see. Hovering over each member will give you additional details.</p>
        <p>Click to switch views to alternate between the tree and graph views, which displays all members of the family tree. This can also be filtered via generations.</p>
        <p>Edit family members - clicking on a person will open up a sidebar which displays more information. Through this you can upload images and memories of that person, and open a page to update information.</p>
        <p>Search for specific individuals - feel like your family is too large? No problem! The additional searchbar will allow you to search for a family member and centre the focus on them.</p>
        <p>Export as PDF book - click to lead to a page to print your personalised family tree book, with the details you wish to include.</p>
    </div>

    <footer class="footer">
        <p>Copyright 2024 | <a href="{{ route('about') }}">About MyStory</a></p>
    </footer>
</body>
</html>