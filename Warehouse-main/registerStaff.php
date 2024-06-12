<?php
session_start();
$errors = array();
$errorsu = array();
$success = "";

if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
} else if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
} else if (isset($_SESSION['errorsu'])) {
    $errorsu = $_SESSION['errorsu'];
    unset($_SESSION['errorsu']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link rel="stylesheet" href="css/registerStaff.css">
  
</head>


<body class="gradienHabibi">
    <div class="container">
        <div class="register">
            <h1>Registrasi</h1>
            <div class="line gradienHabibi"></div>

            <form action="auth.php" method="POST" enctype="multipart/form-data" class="firstForm">
                <div class="details">
                    <label for="name"><p>Full Name</p></label>
                    <input class="registration" type="text" name="registerFullName" id="registerFullName" placeholder="Enter your name">
                </div>

                <div class="details">
                    <label for="email"><p>E-mail</p></label>
                    <input class="registration" type="text" name="registerEmail" id="registerEmail" placeholder="Enter your Email">
                </div>

                <div class="details">
                    <label for="username"><p>Username</p></label>
                    <input class="registration" type="text" name="registerUsername" id="registerUsername" placeholder="Enter your username">
                </div>

                <div class="details">
                    <label for="password"><p>Password</p></label>
                    <input class="registration" type="password" name="registerPassword" id="registerPassword" placeholder="Enter your password">
                </div>
                
                <div class="details">
                    <label for="confirmPassword"><p>Confirm Password</p></label>
                    <input class="registration" type="password" name="registerPasswordConfirmation" id="registerPasswordConfirmation" placeholder="Enter your password">
                </div>

                <div class="details">
                    <label for="profilePicture"><p>Profile Picture</p></label>
                    <input class="registration" type="file" id="profile" name="profile" accept="image/*">
                </div>

                <div class="button-container">
                    <!-- Tombol Kembali -->
                    <a href="menuAdmin.php" class="btn-back">Kembali</a>
                </div>
                <div class="button-container">
                    <!-- Tombol Register -->
                    <button type="submit" name="registerSubmit">Submit</button>
                </div>
            </form>
            

            <?php if (!empty($errors)) : ?>
                <div class="text-danger">
                    <h4>Pesan Kesalahan Pada Registrasi:</h4>
                    <ul>
                        <?php foreach ($errors as $error) : ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif (!empty($success)) : ?>
                <div class="text-success">
                    <p><?php echo $success; ?></p>
                </div>
            <?php endif; ?>
            

            

            
        </div>
    </div>
</body>

</html>
