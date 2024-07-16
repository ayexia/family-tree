<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Tree</title>
    @viteReactRefresh 
    @vite('resources/js/app.jsx')
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        .family-tree {
            list-style-type: none;
            padding-left: 0;
        }

        .family-tree li {
            margin: 5px 0;
        }

        .family-tree ul {
            margin-left: 20px;
        }

        .person {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Family Tree</h1>

    <form method="GET" action="{{ route('family.tree') }}">
        <label for="desiredName">Name:</label>
        <input type="text" id="desiredName" name="desiredName" value="{{ request('desiredName') }}">
        <button type="submit">Search</button>
    </form>

    @if ($allPersons->isEmpty())
        <div>No results found for the given query.</div>
    @else
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
    @endif

    <h2>Tree Display:</h2>
    <ul class="family-tree">
        @foreach($trees as $tree)
            @foreach($tree as $entry)
                <li>{{ $entry }}</li>
            @endforeach
        @endforeach
    </ul>

    <div id="root"></div>
</body>
</html>
