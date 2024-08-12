<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About MyStory</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<style>
.profile-button {
    text-decoration: none;
}
.footer {
    position: absolute;
    bottom: -150px;
}
</style>
<body>
    <div class="container">
        <div class="header">
            <h1 class="subheading">About MyStory</h1>
            <div class="profile">
            <a href="{{route('profile.edit') }}" class="profile-button">
                <img src="{{ asset('images/user-profile.png') }}" alt="User">
                Profile
            </a>
            </div>
        </div>
        <a href="{{ route('home') }}" class="profile-button home-button">
                <img src="{{ asset('images/home.png') }}" alt="Home">
                Home
            </a>                    
        <div class="main-content">
            <div class="textbox one">
            Welcome to MyStory, the ultimate tool for preserving and exploring your family history. Whether you're looking to visualise your ancestry or share cherished memories, MyStory is designed to make your journey into genealogy both comprehensive and accessible.
        <br><br> Our goal is to make family history convenient and enjoyable for everyone. We strive for accuracy in the details and simplicity in navigation, ensuring that your experience is both personalised and inclusive.
    <br><br>At MyStory, we believe families are more than simply genetics and blood relations!</div>
            <div class="about tree">
            <img src="{{ asset('images/about-image-1.png') }}" alt="About 1">
            </div>
            <div class="textbox two">
            Features:
        <br><br>GEDCOM File Import: Easily import your existing family history data through GEDCOM files, instantly creating a rich, interactive family tree.
                <br><br>Visualisations: Explore your family history through various visual formats, making it easy to see connections and understand your lineage at a glance -- control the view you wish to see!
                <br><br>Edit Family Information: Update and refine your family tree through editing information and uploading images, documenting important events as accurately as possible.
                <br><br>Add Memories: Attach photos, stories, and other memories to individual profiles, making sure each and every family member's legacy is fully captured.
                <br><br>PDF Book Creation: Compile your family tree into a beautifully designed PDF book, customisable for your needs. It makes the perfect gift for sharing with loved ones or even preserving as a keepsake.</div>
            <div class="about dna">
            <img src="{{ asset('images/about-image-2.png') }}" alt="About 2">
            </div>
        </div>
    </div>
    <div class="footer">
        <p>Copyright 2024 | <a href="#">About</a></p>
    </div>
</body>
</html>