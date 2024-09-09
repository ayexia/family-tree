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
    .profile-button {
        text-decoration: none;
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
        top: 161.67px;
        left: 12%;
    }

    .home-button img {
        width: 30px;
        height: 30px;
        margin-right: 20px;
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

    .tree-display-box {
        border: 2px solid #00796b;
        padding: 20px;
        border-radius: 20px;
        background-color: #00796b;
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

    .search-container p {
        font-family: "Inika", serif;
        font-size: 0.9em;
        color: #EDECD7;
        left: -50px;
        position: relative;
    }

    .gradient-text {
        background: repeating-linear-gradient(90deg, #00796b, #004d40, #00796b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .search-input {
        background-color: #00796b;
        color: #EDECD7;
    }

    .search-input::placeholder {
        color: #EDECD7;
    }
</style>
<body>
    <div class="container">
        <header class="header">
            <h1 class="gradient-text">Search</h1>
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

        <div class="search-container">
        <p>Search for a family member whose information you'd like to see in greater detail.</p>
            <div class="search-input-container">
            @if($familyTreeId)
    <form method="GET" action="{{ route('family.tree', ['familyTreeId' => $familyTreeId]) }}">
        <input type="text" id="desiredName" name="desiredName" value="{{ request('desiredName') }}" class="search-input" placeholder="Search">
        <button type="submit" class="search-button">
            <img src="{{ asset('images/search.png') }}" alt="Search">
        </button>
    </form>
        @else
            <p>You don't have a family tree yet. Please import a GEDCOM file first.</p>
        @endif
</div>
</div>
        @if ($allPersons->isEmpty())
            <div>No results found for the given query.</div>
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

        <h2>Tree Display:</h2>
        <div class="tree-display-box">
            <ul class="family-tree">
                @foreach($trees as $tree)
                    @foreach($tree as $entry)
                        <li>{{ $entry }}</li>
                    @endforeach
                @endforeach
            </ul>
        </div>
    </div>
    <footer class="footer">
    <p>Copyright 2024 | <a href="{{ route('about') }}">About MyStory</a> | <a href="{{ route('feedback.create') }}">Submit Feedback</a></p>
    </footer>
</body>
</html>