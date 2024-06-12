<?php

include 'koneksi.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include("koneksi.php");

// Ambil informasi pengguna dari database berdasarkan ID yang diberikan
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM pengguna WHERE id_user = $id";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "User not found!";
        exit();
    }
} else {
    echo "No user ID specified!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1>Edit Profile</h1>
        <form action="prosesUpdateProfile.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_user" value="<?php echo $id; ?>">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama:</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $user['nama']; ?>">
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username"
                    value="<?php echo $user['username']; ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="mb-3">
                <label for="profile" class="form-label">Profile Picture:</label>
                <input type="file" class="form-control-file" id="profile" name="profile">
                <img src="uploads/<?php echo $user['profile']; ?>" alt="Profile Picture"
                    style="width: 100px; height: auto;">
            </div>
            <button type="submit" name="updateProfile" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>

</html>