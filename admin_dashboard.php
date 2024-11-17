<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            background-color: white;
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #333; 
            height: 100vh;
            width: 200px;
            position: fixed;
            top: 0;
            left: 0;
            overflow: auto;
        }
        .navbar a {
            padding: 15px;
            color: white;
            display: block;
            text-decoration: none;
        }
        .content {
            margin-left: 220px; 
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="manage_candidates.php">Manage Candidates</a>
        <a href="manage_voters.php">Manage Voters</a>
    </div>
    <div class="content">
        <h1>Vous êtes connecté(e) en tant que administrateur.</h1>
        
        <?php
        ?>
    </div>
</body>
</html>