<?php
// Initialize session
session_start();

// Check if the user is already logged in (+ redirect if necessary)
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

// Require the database connection file
require_once 'dbconnexion.php';

// Handle user login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_login'])) {
  
    $admin_username = $_POST['admin_username'];
    $admin_password = $_POST['admin_password'];

    // admin authentication
    $admin_query = "SELECT * FROM administrators WHERE username = '$admin_username' AND password = '$admin_password'";
    $admin_result = mysqli_query($db, $admin_query);

    if (mysqli_num_rows($admin_result) == 1) {
        $admin_row = mysqli_fetch_assoc($admin_result);
        $_SESSION['admin_id'] = $admin_row['id'];
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $admin_login_error = "nom d'administrateur / mot de passe invalides";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accès Administrateur</title>
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        h1 {
            color: #333; 
        }
        .login-section {
            background-color: #f0f8ff; 
            padding: 30px; 
            border-radius: 10px;
            margin: 20px auto;
            width: 350px; 
            border: 2px solid #333; 
        }
        h2 {
            color: #333; 
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #3498db; 
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: #3498db; 
        }
        p {
            color: #f00; 
        }
    </style>
</head>
<body>
    
    <!-- Admin Login Form -->
    <div class="login-section">
        <form method="post">
             <img src='https://univ-internationale.com/sites/default/files/logo_uit_300x200.png' alt='logo' class='form-image'>
            <h2>Accès administrateur</h2>
            <input type="text" name="admin_username" placeholder="Username">
            <input type="password" name="admin_password" placeholder="Password">
            <button type="submit" name="admin_login">Connexion</button>
        </form>
        <?php if (isset($admin_login_error)) echo "<p>$admin_login_error</p>"; ?>
    </div>
</body>
</html>
