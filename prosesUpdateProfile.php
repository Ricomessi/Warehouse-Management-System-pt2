<?php
include 'firebaseconfig.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['updateProfile'])) {
    $id_user = $_POST['id_user'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $profilePictureFileName = null;

    // File upload handling
    if (!empty($_FILES['profile']['tmp_name']) && is_uploaded_file($_FILES['profile']['tmp_name'])) {
        $uploadDir = "uploads/";
        $profilePictureFileName = basename($_FILES['profile']['name']);
        $uploadPath = $uploadDir . $profilePictureFileName;
        $imageFileType = strtolower(pathinfo($uploadPath, PATHINFO_EXTENSION));

        $allowedFormats = array("jpg", "jpeg", "png", "gif");
        if (in_array($imageFileType, $allowedFormats)) {
            $isValid = getimagesize($_FILES['profile']['tmp_name']);
            if ($isValid) {
                if (!move_uploaded_file($_FILES['profile']['tmp_name'], $uploadPath)) {
                    $_SESSION['errors'] = array("Gagal mengunggah file. Silakan coba lagi.");
                    header("Location: editProfile.php?username=$id_user");
                    exit();
                }
            } else {
                $_SESSION['errors'] = array("File yang diunggah bukan gambar.");
                header("Location: editProfile.php?username=$id_user");
                exit();
            }
        } else {
            $_SESSION['errors'] = array("Hanya file JPG, JPEG, PNG, dan GIF yang diizinkan.");
            header("Location: editProfile.php?username=$id_user");
            exit();
        }
    }

    // Prepare data for updating
    $data = [
        'nama' => $nama,
        'username' => $username,
        'email' => $email,
    ];

    if (!empty($password)) {
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    if ($profilePictureFileName) {
        $data['profile'] = $profilePictureFileName;
    }

    // Update the user data in Firebase
    $reference = $database->getReference('users/' . $id_user);
    $reference->update($data);

    $_SESSION['success'] = "Data Staff updated successfully!";
    header("Location: editProfile.php?username=$id_user");
    exit();
}
?>
