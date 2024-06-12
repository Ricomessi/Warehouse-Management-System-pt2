<?php
session_start();
include("koneksi.php");
$errors = array();


// Registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registerSubmit'])) {
    $fullName = mysqli_real_escape_string($koneksi, $_POST['registerFullName']);
    $username = mysqli_real_escape_string($koneksi, $_POST['registerUsername']);
    $email = mysqli_real_escape_string($koneksi, $_POST['registerEmail']);
    $password = mysqli_real_escape_string($koneksi, $_POST['registerPassword']);
    $passwordConfirmation = mysqli_real_escape_string($koneksi, $_POST['registerPasswordConfirmation']);
    $profilePicture = $_FILES['profile'];

    $uploadDir = "uploads/";
    $uploadPath = $uploadDir . basename($profilePicture['name']);
    $imageFileType = strtolower(pathinfo($uploadPath, PATHINFO_EXTENSION));

    // Validation for registration fields
    if (empty($fullName)) {
        $errors[] = "Nama tidak boleh kosong.";
    }

    if (empty($username)) {
        $errors[] = "Username tidak boleh kosong.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $errors[] = "Username hanya boleh mengandung huruf, angka, dan underscore.";
    }

    if (empty($email)) {
        $errors[] = "Email tidak boleh kosong.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid. Mohon masukkan alamat email yang valid.";
    }

    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password harus memiliki setidaknya 8 karakter.";
    }

    if (empty($passwordConfirmation)) {
        $errors[] = "Konfirmasi password tidak boleh kosong.";
    } elseif ($passwordConfirmation !== $password) {
        $errors[] = "Konfirmasi password tidak sesuai dengan password.";
    }

    // Check if username is already used
    $checkUsernameQuery = "SELECT * FROM pengguna WHERE username = ?";
    $checkStmt = $koneksi->prepare($checkUsernameQuery);
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $checkStmt->close();

    if ($checkResult->num_rows > 0) {
        $errors[] = "Username sudah digunakan. Silakan pilih username yang lain.";
    }

    // Check if the file is an image
    if (!empty($profilePicture['tmp_name']) && is_uploaded_file($profilePicture['tmp_name'])) {
        $allowedFormats = array("jpg", "jpeg", "png", "gif");

        // Check file type
        if (!in_array($imageFileType, $allowedFormats)) {
            $errors[] = "Hanya file JPG, JPEG, PNG, dan GIF yang diizinkan.";
        }

        // Check image size
        $isValid = getimagesize($profilePicture['tmp_name']);
        if (!$isValid) {
            $errors[] = "File yang diunggah bukan gambar.";
        }

        // Use the original filename
        $profilePictureFileName = basename($profilePicture['name']);

        // Move the uploaded file to the destination directory
        $uploadPath = $uploadDir . $profilePictureFileName;
        if (!move_uploaded_file($profilePicture['tmp_name'], $uploadPath)) {
            $errors[] = "Gagal mengunggah file. Silakan coba lagi.";
        }
    } else {
        $errors[] = "Gagal mengunggah file. Silakan coba lagi.";
    }



    // If there are no errors, proceed with registration
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Query untuk memasukkan data pengguna baru ke dalam tabel pengguna
        $insertQuery = "INSERT INTO pengguna (username, nama, email, password, role, profile) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($insertQuery);
        $stmt->bind_param("ssssss", $username, $fullName, $email, $hashedPassword, $role, $profilePictureFileName);
        $role = 'staff';
        if ($stmt->execute()) {
            $_SESSION['success'] = "Registrasi Berhasil!";
            header("Location: registerStaff.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
            exit;
        }
    } else {
        $_SESSION['errors'] = $errors;
        header("Location: registerStaff.php");
        exit;
    }
}


// Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['loginSubmit'])) {
    $errorsu = array();
    $username = mysqli_real_escape_string($koneksi, $_POST['loginUsername']);
    $password = mysqli_real_escape_string($koneksi, $_POST['loginPassword']);

    $recaptcha_secret_key = '6LeCJx4pAAAAAGktvemOUkAuceeYhJIE_U8F7YWo';
    $recaptcha_response = $_POST['g-recaptcha-response'];

    $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
    $recaptcha_data = [
        'secret' => $recaptcha_secret_key,
        'response' => $recaptcha_response,
    ];

    $recaptcha_options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($recaptcha_data),
        ],
    ];

    $recaptcha_context = stream_context_create($recaptcha_options);
    $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
    $recaptcha_result_data = json_decode($recaptcha_result, true);

    if (!$recaptcha_result_data['success']) {
        $errorsu = "*Captcha kosong atau tidak valid, silahkan dicoba kembali";
        $_SESSION['errorsu'] = $errorsu;
        header("Location: login.php");
        exit;
    }

    $stmt = $koneksi->prepare("SELECT * FROM pengguna WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];
        $userRole = $row['role'];
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['role'] = $userRole;
            if ($userRole === 'admin') {
                $_SESSION['username'] = $row['username'];
                header("Location: mainAdmin.php");
                exit;
            } elseif ($userRole === 'staff') {
                $_SESSION['username'] = $row['username'];
                header("Location: mainStaff.php");
                exit;
            }
        } else {
            $errorsu  = "*Gagal, silahkan login kembali";
            $_SESSION['errorsu'] = $errorsu;
            header("Location: login.php");
            exit;
        }
    } else {
        $errorsu  = "*Gagal, silahkan login kembali";
        $_SESSION['errorsu'] = $errorsu;
        header("Location: login.php");
        exit;
    }
}
