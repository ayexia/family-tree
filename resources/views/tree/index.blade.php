<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Tree</title>
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
        <label for="desiredUserId"></label>
        <input type="text" id="desiredName" name="desiredName" value="{{ request('desiredName') }}">

        <button type="submit">Search</button>
    </form>

    @if (isset($desiredUserId) && !$allPersons->contains('id', $desiredUserId))
        <div>No person found with that ID.</div>
    @endif

    @if ($allPersons->isEmpty())
        <div>No results found for the given query.</div>
    @else
        <ul class="family-tree">
            @foreach($allPersons as $person)
                @if(array_key_exists($person->id, $familyTree))
                    <li class="person">
                        <strong>{{ $person->name }}</strong> ({{ $person->birth_date }} - {{ $person->death_date }})

                        
                        @if (!empty($familyTree[$person->id]['parents']))
                            <ul>
                                <li><strong>Parents:</strong></li>
                                @foreach ($familyTree[$person->id]['parents'] as $parentId)
                                    <li>
                                        {{ $relatives->firstWhere('id', $parentId)->name ?? 'Unknown Parent' }}
                                        ({{ $relatives->firstWhere('id', $parentId)->birth_date ?? 'Unknown' }} - {{ $relatives->firstWhere('id', $parentId)->death_date ?? 'Unknown' }})
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        
                        @if (!empty($familyTree[$person->id]['spouses']))
                            <ul>
                                <li><strong>Spouse(s):</strong></li>
                                @foreach ($familyTree[$person->id]['spouses'] as $spouseId)
                                    <li>
                                        {{ $relatives->firstWhere('id', $spouseId)->name ?? 'Unknown Spouse' }}
                                        ({{ $relatives->firstWhere('id', $spouseId)->birth_date ?? 'Unknown' }} - {{ $relatives->firstWhere('id', $spouseId)->death_date ?? 'Unknown' }})
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        
                        @if (!empty($familyTree[$person->id]['children']))
                            <ul>
                                <li><strong>Children:</strong></li>
                                @foreach ($familyTree[$person->id]['children'] as $childId)
                                    <li>
                                        {{ $relatives->firstWhere('id', $childId)->name ?? 'Unknown Child' }}
                                        ({{ $relatives->firstWhere('id', $childId)->birth_date ?? 'Unknown' }} - {{ $relatives->firstWhere('id', $childId)->death_date ?? 'Unknown' }})
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
    </div>
</body>
</html>
