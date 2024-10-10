<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @viteReactRefresh 
    @vite('resources/js/app.jsx')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<style>
    body {
        padding-bottom: 60px;
    }

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
        top: 0px;
        left: 100px;
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
        position: fixed;
        right: 0;
        bottom: 0;
        left: 0;
        padding: 0.53rem;
        color: #EDECD7;
        background-color: #004d40;
        width: 100%;
    }

    .admin-container {
        margin: 20px auto;
        max-width: 80%;
    }

    .admin-section {
        border: 2px solid #00796b;
        padding: 20px;
        border-radius: 20px;
        background-color: #00796b;
        margin: 20px 0;
        color: #EDECD7;
    }

    .admin-section h2 {
        margin-top: 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #EDECD7;
    }

    th {
        background-color: #004d40;
    }

    .action-button {
        position: relative;
        background-color: #004d40;
        color: #EDECD7;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        font-family: "Inika", serif;
        font-size: 0.8em;
        margin-right: 5px;
    }

    .action-button:hover {
        background-color: #00695c;
    }

    .action-button .tooltip-text {
        visibility: hidden;
        width: 200px;
        background-color: #004d40;
        color: #EDECD7;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1001;
        bottom: 125%;
        left: 50%;
        margin-left: -100px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 0.8em;
    }

    .action-button:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .action-button .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #004d40 transparent transparent transparent;
    }

    .verified, .not-verified {
        position: relative;
        font-weight: bold;
        padding: 2px 5px;
        border-radius: 3px;
        cursor: help;
    }

    .verified {
        color: #004d40;
        background-color: #EDECD7;
    }

    .not-verified {
        color: #EDECD7;
        background-color: #d32f2f;
    }

    .verified::before {
        content: "✓ ";
    }

    .not-verified::before {
        content: "✗ ";
    }

    .verified .tooltip-text, .not-verified .tooltip-text {
        visibility: hidden;
        width: 200px;
        background-color: #004d40;
        color: #EDECD7;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1001;
        bottom: 125%;
        left: 50%;
        margin-left: -100px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 0.8em;
    }

    .verified:hover .tooltip-text, .not-verified:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .verified .tooltip-text::after, .not-verified .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #004d40 transparent transparent transparent;
    }

    .gradient-text {
        background: repeating-linear-gradient(90deg, #00796b, #004d40, #00796b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
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
        left: 200px;
        top: 5px;
        transition: none;
        z-index: 1000;
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
    }

    .footer .tooltip-trigger .tooltip-text {
        bottom: 100%;
        top: auto;
        margin-bottom: 5px;
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
</head>
<body>
    <div class="container">
        <header class="header">
            <h1 class="gradient-text">Admin Dashboard</h1>
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
            <div class="circle info-tooltip">
                ?
                <span class="tooltip">
                    <p>Welcome to the Admin Dashboard! Here you can:</p>
                    <p>- View and manage all users</p>
                    <p>- Toggle admin status for users</p>
                    <p>- Delete user accounts</p>
                    <p>- View user verification status and last login</p>
                    <p>- Review user feedback</p>
                </span>
            </div>
        </header>

        <div class="admin-container">
            <div class="admin-section">
                <h2>Users</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Verified</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="verified">
                                            Verified
                                            <span class="tooltip-text">User has confirmed their email address</span>
                                        </span>
                                    @else
                                        <span class="not-verified">
                                            Not Verified
                                            <span class="tooltip-text">User has not confirmed their email address yet</span>
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never' }}</td>
                                <td>
                                    <form action="{{ route('admin.toggle-admin', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="action-button">
                                            {{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}
                                            <span class="tooltip-text">
                                                {{ $user->is_admin ? 'Remove admin privileges from this user' : 'Grant admin privileges to this user' }}
                                            </span>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.delete-user', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-button" onclick="return confirm('Are you sure you want to delete this user?')">
                                            Delete
                                            <span class="tooltip-text">Permanently remove this user and all associated data</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="admin-section">
                <h2>Feedback</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Content</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($feedback as $item)
                            <tr>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ $item->content }}</td>
                                <td>{{ $item->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
        </p>
    </footer>
</body>
</html>