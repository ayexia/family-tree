<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About MyStory</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<style>
.profile-button {
    text-decoration: none;
    right: -30px;
    height: 42.5px;
}
.home-button {
        display: flex;
        align-items: center;
        background-color: #00796b;
        color: #EDECD7;
        text-decoration: none;
        padding: 10px 25px;
        border-radius: 50px;
        font-size: 1.5em;
        font-weight: bold;
        transition: background-color 0.3s;
        margin: 10px;
        font-family: "Inika", serif;
        position: absolute;
        top: -1.5px;
        left: 12%;
        width: 125px;
        height: 38px;
    }

    .home-button img {
        width: 35px;
        height: 35px;
        margin-right: 20px;
        opacity: 0.5;
    }

    .home-button:hover {
        background-color: #004d40;
    }

.subheading {
    color: #004d40;
}

body {
    padding-bottom: 100px;
    position: relative;
    min-height: 100vh;
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

.footer {
    text-align: center;
    font-family: "Inika", serif;
    padding: 0.53rem;
    color: #EDECD7;
    background-color: #004d40;
    position: fixed;
    bottom: 0;
    width: 100%;
    left: 0;
}

</style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="subheading">About MyStory</h1>
            @auth
            <div class="profile">
                <a href="{{ route('profile.edit') }}" class="profile-button tooltip-trigger">
                    <img src="{{ asset('images/user-profile.png') }}" alt="User">
                    Profile
                    <span class="tooltip-text">Edit your profile, logout or delete your account</span>
                </a>
            </div>
            @endauth
        </div>
        <a href="{{ route('home') }}" class="home-button">
            <img src="{{ asset('images/home.png') }}" alt="Home">
            Home
        </a>
        
        <div class="main-content">
            <div class="textbox one">
            Welcome to MyStory, the ultimate tool for preserving and exploring your family history. Whether you're looking to visualise your ancestry or share cherished memories, MyStory is designed to make your journey into genealogy both comprehensive and accessible.
        <br><br> Our goal is to make family history convenient and enjoyable for everyone. We strive for accuracy in the details and simplicity in navigation, ensuring that your experience is both personalised and inclusive. At MyStory, we believe families are more than simply genetics and blood relations!
<br><br>All icons used are from Google Material Icons and fonts from Google Fonts.</a></div>
            <div class="about tree">
            <img src="{{ asset('images/about-image-1.png') }}" alt="About 1">
            </div>
            <div class="textbox two">
            Features:
        <br><br>GEDCOM File Import: Easily import your existing family history data through GEDCOM files, instantly creating a rich, interactive family tree.
                <br><br>Visualisations: Explore your family history through various visual formats, making it easy to see connections and understand your lineage at a glance -- control the view you wish to see!
                <br><br>Edit Family Information: Update and refine your family tree through editing information and uploading images, documenting important events as accurately as possible.
                <br><br>PDF Book Creation: Compile your family tree into a beautifully designed PDF book, customisable for your needs. It makes the perfect gift for sharing with loved ones or even preserving as a keepsake.</div>
            <div class="about dna">
            <img src="{{ asset('images/about-image-2.png') }}" alt="About 2">
            </div>
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
                <a href="{{ route('feedback.create') }}">Submit Feedback</a>
                <span class="tooltip-text">Share your thoughts and suggestions to help us improve</span>
            </span>
            | Images designed by <a href="http://www.freepik.com/">FreePik</a>
        </p>
    </footer>
</body>
</html>