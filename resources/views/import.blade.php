@extends('layouts.custom')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Tourney:ital,wght@0,100..900;1,100..900&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <style>
        .profile {
            position: fixed;
            right: 2%;
            top: 5%;
        }
        
        .home-button {
            position: fixed;
            left: 15%;
            top: 11%;
        }

        .custom-background {
            background-color: #9BB08C;
            padding: 2rem;
            border-radius: 2.5rem;
            box-shadow: none;
            height: 15rem;
            width: 35rem;
            margin:auto;
            bottom: 35%;
            position: fixed;
            right:27.5%;
        }

        .custom-background label {
            color: #EDECD7;
            font-family: "Inika", serif;
        }

        .custom-button {
            background-color: #587353;
            color: #EDECD7;
            font-family: "Inika", serif;
            border-radius: 2.5rem;
            padding: 0.5rem 1rem;
            border: none;
            cursor: pointer;
            text-transform: none;
            right: 30%;
            top: 55%;
            position: fixed;
        }

        .custom-button:hover {
            background-color: #4a6848;
        }

        .custom-error, .custom-success {
            border-radius: 0.25rem;
            padding: 0.75rem 1.25rem;
            font-family: "Inika", serif;
            font-size: 0.875rem;
        }

        .custom-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .custom-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .file-type-info {
        margin-top: 0.5rem;
        color: #EDECD7;
        font-size: 0.875rem;
        font-family: "Inika", serif;
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
    </style>
    <div class="container">
        <div class="header">
            <h1 class="subheading">Import GEDCOM</h1>
            <div class="profile">
                <button class="profile-button">
                    <img src="{{ asset('images/user-profile.png') }}" alt="User">
                    Profile</button>
            </div>
        </div>
        <button class="profile-button home-button" onclick="window.location.href='{{ route('home') }}'">
                    <img src="{{ asset('images/home.png') }}" alt="Home">
                    Home</button>      
    </div>
    <div class="max-w-3xl mx-auto mt-8 custom-background">
        <div class="bg-white dark:bg-gray-800 overflow-hidden" style="box-shadow: none;">

                @if (session('success'))
                    <div class="custom-success mb-4 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="custom-error mb-4 rounded">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="gedcom_file">Choose GEDCOM file</label>
                        <input type="file" name="gedcom_file" id="gedcom_file" accept=".ged" style="margin-top: 0.25rem; display: block; width: 100%; font-size: 0.875rem; color: #1a202c; border: 1px solid #cbd5e0; border-radius: 0.375rem; cursor: pointer; background-color: #f7fafc;">
                        <p class="file-type-info">Files must be of .ged extension</p>
                        @error('gedcom_file')
                        @enderror
                    </div>
                    <div>
                        <button type="submit" class="custom-button">
                            Upload GEDCOM
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="footer">
        <p>Copyright 2024 | <a href="{{ route('about') }}">About</a></p>
    </div>
@endsection