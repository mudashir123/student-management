<?php
session_start();
include('connection.php');

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to index page if the user is already logged in
    header("Location: index.php");
    exit();
}

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $user = mysqli_real_escape_string($conn, $user);
    $pass = mysqli_real_escape_string($conn, $pass);

    // SQL query to select user based on username
    $sql = "SELECT id, password FROM users WHERE username = '$user'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashed_password = $row['password'];

        // Verify the password
        if (md5($pass) === $hashed_password) {
            // Store session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $user;

            // Redirect to dashboard
            header("Location: index.php");
            exit();
        } else {
            // Set error message for invalid password
            $error_message = "Invalid password.";
        }
    } else {
        // Set error message for non-existent user
        $error_message = "Invalid username";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #ece9e6, #ffffff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            transition: all 0.3s ease;
        }

        .login-container:hover {
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.15);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #4CAF50;
            background-color: #fff;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }

        .forgot-password a {
            color: #4CAF50;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #45a049;
        }

        @media (max-width: 500px) {
            .login-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>

    <!-- Display error message if exists -->
    <?php if (!empty($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form id="loginForm" action="login.php" method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autocomplete="off">
        <span class="error" id="usernameError"></span>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="off">
        <span class="error" id="passwordError"></span>

        <input type="submit" value="Login">
    </form>

    <div class="forgot-password">
        <a href="#">Forgot your password?</a>
    </div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', function (event) {
        let isValid = true;
        let username = document.getElementById('username').value.trim();
        let password = document.getElementById('password').value.trim();

        // Clear any previous errors
        document.getElementById('usernameError').textContent = '';
        document.getElementById('passwordError').textContent = '';

        // Validate username
        if (username == '') {
            document.getElementById('usernameError').textContent = 'Username is required';
            isValid = false;
        }

        // Validate password
        if (password == '') {
            document.getElementById('passwordError').textContent = 'Password is required';
            isValid = false;
        }

        if (!isValid) {
            // Prevent form submission if validation fails
            event.preventDefault();
        }
    });
</script>

</body>
</html>
