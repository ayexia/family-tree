<!DOCTYPE html>
<html>
<head>
    <title>Family Tree</title>
</head>
<body>
    <h1>Search Family Tree</h1>

    <form method="GET" action="{{ route('family.tree') }}">
        <label for="desiredUserId">Search by ID:</label>
        <input type="text" id="desiredUserId" name="desiredUserId" value="{{ request('desiredUserId') }}">
        
        <label for="desiredName">or by name:</label>
        <input type="text" id="desiredName" name="desiredName" value="{{ request('desiredName') }}">

        <button type="submit">Search</button>
    </form>

    <h1>Pedigree Family Tree</h1>

    @if (isset($desiredUserId) && !$persons->contains('id', $desiredUserId))
        <div>No person found with that ID.</div>
    @endif

    <ul>
        @foreach($persons as $person)
            @if(array_key_exists($person->id, $familyTree))
                <li>
                    {{ $person->name }} ({{ $person->birth_date }} - {{ $person->death_date }})

                    @if (!empty($familyTree[$person->id]['spouses']))
                        <ul>
                            <strong>Spouse:</strong>
                            @foreach ($familyTree[$person->id]['spouses'] as $spouseId)
                                <li>
                                    {{ $persons->firstWhere('id', $spouseId)->name ?? 'Unknown Spouse' }}
                                </li>
                            @endforeach
                        </ul>       
                    @endif

                    @if (!empty($familyTree[$person->id]['children']))
                        <ul>
                            <strong>Children:</strong>
                            @foreach ($familyTree[$person->id]['children'] as $childId)
                                <li>
                                    {{ $persons->firstWhere('id', $childId)->name ?? 'Unknown Child' }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endif
        @endforeach
    </ul>
</body>
</html>
