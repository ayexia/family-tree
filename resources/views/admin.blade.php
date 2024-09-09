<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @viteReactRefresh 
    @vite('resources/js/app.jsx')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
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

    .verified, .not-verified {
    font-weight: bold;
    padding: 2px 5px;
    border-radius: 3px;
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

    .gradient-text {
        background: repeating-linear-gradient(90deg, #00796b, #004d40, #00796b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>
<body>
    <div class="container">
        <header class="header">
            <h1 class="gradient-text">Admin Dashboard</h1>
            <div class="profile">
                <a href="{{ route('profile.edit') }}" class="profile-button">
                    <img src="{{ asset('images/user-profile.png') }}" alt="User">
                    Profile
                </a>
            </div>
            <a href="{{ route('home') }}" class="profile-button home-button">
                <img src="{{ asset('images/home.png') }}" alt="Home">
                Home
            </a>
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
                                        <span class="verified">Verified</span>
                                    @else
                                        <span class="not-verified">Not Verified</span>
                                    @endif
                                </td>
                                <td>{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never' }}</td>
                                <td>
                                    <form action="{{ route('admin.toggle-admin', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="action-button">{{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}</button>
                                    </form>
                                    <form action="{{ route('admin.delete-user', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-button" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
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
    <p>Copyright 2024 | <a href="{{ route('about') }}">About MyStory</a> | <a href="{{ route('feedback.create') }}">Submit Feedback</a></p>
    </footer>
</body>
</html>