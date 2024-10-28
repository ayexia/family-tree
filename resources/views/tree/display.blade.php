<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $surname }}'s Family Tree</title>
    @viteReactRefresh 
    @vite('resources/js/app.jsx')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<style>
    .title {
        left: 450px;
        top: 0px;
        position: absolute;
        font-family: "Inika", serif;
        font-size: 40px;
        color: #004d40;
    }

    .profile-button {
        text-decoration: none;
        font-family: "Inika", serif;
        color: #EDECD7;
    }

    .home-button {
        display: flex;
        align-items: center;
        background-color: #00796b;
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

    .home-button:hover {
        background-color: #004d40;
    }

    .footer {
        text-align: center;
        font-family: "Inika", serif;
        margin-top: 2rem;
        position: fixed;
        right: 0%;
        padding: 0.53rem;
        color: #EDECD7;
        background-color: #004d40;
        z-index: 1000;
    }

    .search-results .family-tree li {
        background-color: #00796b;
        padding: 15px;
        border: 1px solid #004d40;
        border-radius: 20px;
        margin: 10px auto;
        width: 80%;
        max-width: 600px;
        text-align: left;
        color: #EDECD7;
        font-family: "Inika", serif;
    }

    .search-results ul li:first-child {
        background: #00796b;
        padding: 15px;
        border-radius: 20px;
        border: none;
        color: #EDECD7;
        font-family: "Inika", serif;
    }

    .tree-display-box {
        border: 2px solid #00796b;
        padding: 20px;
        border-radius: 20px;
        background-color: #00796b;
        margin: 20px auto;
        max-width: 60%;
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
        top: -110px;
    }
    
    .circle.info-tooltip {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #00796b;
        color: #EDECD7;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 16px;
        font-family: "Inika", serif;
        font-weight: bold;
        cursor: pointer;
        position: absolute;
        left: 420px;
        top: 5px;
        transition: none;
        z-index: 1003;
    }

    .circle.info-tooltip .tooltip {
        visibility: hidden;
        width: 300px;
        background-color: #004d40;
        color: #EDECD7;
        text-align: left;
        padding: 10px;
        border-radius: 6px;
        position: absolute;
        z-index: 1001;
        top: 120%;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.8em;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .circle.info-tooltip:hover .tooltip {
        visibility: visible;
        opacity: 1;
    }

    .circle.info-tooltip .tooltip::after {
        content: "";
        position: absolute;
        bottom: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: transparent transparent #00796b transparent;
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
        font-size: 0.5em;
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
    
    .gedcom .tooltip-trigger .tooltip-text::after {
        top: -10px;
        border-color: transparent transparent #004d40 transparent;
    }
    .gedcom .tooltip-trigger .tooltip-text {
        top: 100%;
        bottom: auto;
        margin-top: 5px;
        z-index: 1002;
        font-size: 0.8em;
    }

    .footer .tooltip-trigger .tooltip-text::after {
        bottom: -10px;
        border-color: #004d40 transparent transparent transparent;
    }
    .home-button.import-gedcom, .home-button.export-gedcom {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100px;
        height: 40px;
        padding: 10px;
        border-radius: 50px;
        top: 0px;
        position: relative;
        z-index: 1001;
        font-size: 0.5em;
    }

    .home-button.import-gedcom {
        left: 210px;
        top: 2.5px;
        height: 37.5px;
    }

    .home-button.export-gedcom {
        left: 335px;
        top: -75px;
        height: 37.5px;
    }

    .home-button.import-gedcom img, .home-button.export-gedcom img {
        width: 30px;
        height: 30px;
        opacity: 0.5;
    }
</style>
</head>
<body>
    <header class="header">
        <h1 class="title">{{ $surname }}'s Family Tree</h1>
        <div class="profile">
                <a href="{{ route('profile.edit') }}" class="profile-button tooltip-trigger">
                    <img src="{{ asset('images/user-profile.png') }}" alt="User">
                    Profile
                    <span class="tooltip-text">Edit your profile, logout or delete your account</span>
                </a>
            </div>
        <a href="{{ route('home') }}" class="profile-button home-button">
            <img src="{{ asset('images/home.png') }}" alt="Home">
            Home
        </a>
        <div class="gedcom">
        <a href="{{ route('import.form') }}" class="profile-button home-button import-gedcom tooltip-trigger">
            <img src="{{ asset('images/upload.png') }}" alt="Import GEDCOM">
            Import
            <span class="tooltip-text">Import a GEDCOM file to create your family tree</span>
        </a>
        </div>
        <div class="gedcom">
        <a href="{{ route('export.gedcom') }}" class="profile-button home-button export-gedcom tooltip-trigger">
            <img src="{{ asset('images/download.png') }}" alt="Export GEDCOM">
            Export
            <span class="tooltip-text">Export your family tree as a GEDCOM file</span>
        </a>
        </div>
        <div class="circle info-tooltip">
            ?
            <span class="tooltip">
                <p>Welcome to your Family Tree page! Here you can:</p>
                <p>- View and explore your family tree</p>
                <p>- Search for specific family lines</p>
                <p>- Adjust the number of visible generations</p>
                <p>- Switch between tree and graph views</p>
                <p>- View various family statistics (graph view)</p>
                <p>- Edit information, view profiles and upload images by clicking on any member</p>
                <p>- Customise the colour and look of family member icons and relationship lines</p>
                <p>- Search for specific individuals</p>
                <p>- Import and export your GEDCOM file</p>
                <p>- Export as PDF book, with options to customise its contents</p>
            </span>
        </div>
    </header>

    <div id="root"></div>

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
        </p>
    </footer>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    function handleLinkClick(e) {
        e.preventDefault();
        window.location.href = e.currentTarget.href;
    }

    const profileLink = document.getElementById('profile-link');
    const importLink = document.getElementById('import-link');
    const exportLink = document.getElementById('export-link');

    if (profileLink) profileLink.addEventListener('click', handleLinkClick);
    if (importLink) importLink.addEventListener('click', handleLinkClick);
    if (exportLink) exportLink.addEventListener('click', handleLinkClick);
});
</script>
</body>
</html>