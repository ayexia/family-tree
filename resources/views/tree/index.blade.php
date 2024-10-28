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
    .header {
        font-family: "Inika", serif;
    }

    .profile-button {
        text-decoration: none;
        color: #EDECD7;
        right:-30px;
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
        font-size: 0.7em;
        font-weight: bold;
        transition: background-color 0.3s;
        margin: 10px;
        font-family: "Inika", serif;
        position: absolute;
        top: 0px;
        left: 12%;
        width: 120px;
        height: 40px;
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

    .footer {
        text-align: center;
        font-family: "Inika", serif;
        margin-top: 2rem;
        position: fixed;
        right: 0%;
        padding: 0.53rem;
        color: #EDECD7;
        background-color: #004d40;
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
    }

    .search-results ul li:first-child {
        background: #00796b;
        padding: 15px;
        border-radius: 20px;
        border: none;
        color: #EDECD7;
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

    .search-container p {
        font-family: "Inika", serif;
        font-size: 1em;
        color: #EDECD7;
        left: -50px;
        position: relative;
    }

    .gradient-text {
        background: repeating-linear-gradient(90deg, #00796b, #004d40, #00796b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 50px;
    }

    .search-input {
        background-color: #00796b;
        color: #EDECD7;
    }

    .search-input::placeholder {
        color: #EDECD7;
    }

    .view-profile-link {
        display: inline-block;
        margin-left: 10px;
        padding: 5px 10px;
        background-color: #004d40;
        color: #EDECD7;
        text-decoration: none;
        border-radius: 15px;
        font-size: 0.8em;
    }

    .view-profile-link:hover {
        background-color: #00796b;
    }
    .no-tree-message, .no-results-message {
        font-family: "Inika", serif;
        font-size: 1.1em;
        color: #00796b;
        text-align: center;
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

</style>
<body>
    <div class="container">
        <header class="header">
            <h1 class="gradient-text">Search</h1>
            <div class="profile">
                <a href="{{ route('profile.edit') }}" class="profile-button tooltip-trigger">
                    <img src="{{ asset('images/user-profile.png') }}" alt="User">
                    Profile
                    <span class="tooltip-text">Edit your profile, logout or delete your account</span>
                </a>
            </div>
            <a href="{{ route('home') }}" class="home-button">
                <img src="{{ asset('images/home.png') }}" alt="Home">
                Home
            </a>
        </header>

        <div class="search-container">
    <p>Search for a family member whose information you'd like to see in greater detail.</p>
        <div class="search-input-container">
            <form method="GET" action="{{ route('family.tree', ['familyTreeId' => $familyTreeId ?? '']) }}">
        <input type="text" id="desiredName" name="desiredName" value="{{ request('desiredName') }}" class="search-input" placeholder="Search for a family member">
        <button type="submit" class="search-button">
            <img src="{{ asset('images/search.png') }}" alt="Search">
        </button>
    </form>
</div>
</div>

    <div class="results-container">
        @if(!$familyTreeId)
            <p class="no-tree-message">You don't have a family tree yet. Please import a GEDCOM file first.</p>
        @elseif($allPersons->isEmpty())
            <p class="no-results-message">No results found for the given query.</p>
            @else
        <div class="search-results">
            <ul class="family-tree">
                @foreach($allPersons as $person)
                    @if(isset($familyTree[$person->id]))
                        @php
                            $node = $familyTree[$person->id];
                        @endphp
                        <li class="person">
                            <strong>{{ $node->name }}</strong> ({{ $node->birth_date }} - {{ $node->death_date }})
                            <a href="{{ route('member.profile', ['id' => $person->id]) }}" class="view-profile-link">View Profile</a>

                                @if (!empty($node->getParents()))
                                    <ul>
                                        <li><strong>Parents:</strong></li>
                                        @foreach ($node->getParents() as $parent)
                                            <li>
                                                {{ $parent->name }}
                                                ({{ $parent->birth_date }} - {{ $parent->death_date }})
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if (!empty($node->getSpouses()))
                                    <ul>
                                        <li><strong>Spouse(s):</strong></li>
                                        @foreach ($node->getSpouses() as $spouse)
                                            <li>
                                                {{ $spouse->name }}
                                                ({{ $spouse->birth_date }} - {{ $spouse->death_date }})
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if (!empty($node->getChildren()))
                                    <ul>
                                        <li><strong>Children:</strong></li>
                                        @foreach ($node->getChildren() as $child)
                                            <li>
                                                {{ $child->name }}
                                                ({{ $child->birth_date }} - {{ $child->death_date }})
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
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
        </p>
    </footer>
</body>
</html>