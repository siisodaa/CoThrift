<?php
error_reporting(E_ALL);
require "connection.php";
require "utilities.php";

if(isset($_POST['email']) && isset($_POST['password']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    if(login($email, $password))
    {   
        $_SESSION['unique_email'] = $email;
        header('Location: index.php');
    }
    else
    {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --teal-color: #008080;
            --black-color: #000000;
        }

        body {
            background: linear-gradient(135deg, #f0f9f9, #ffffff);
            font-family: 'Inter', sans-serif;
            color: var(--black-color);
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            color: var(--teal-color);
            text-align: center;
            margin-bottom: 10px;
        }

        .login-container p {
            color: #555;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control {
            border: 1px solid var(--teal-color);
            border-radius: 8px;
        }

        .form-control:focus {
            border-color: var(--teal-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 128, 128, 0.25);
        }

        .btn-primary {
            background-color: var(--teal-color);
            border: none;
            border-radius: 8px;
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        .btn-primary:hover {
            background-color: #006666;
        }

        .alert {
            font-size: 14px;
            border-radius: 8px;
        }

        .alert-danger {
            color: #ffffff;
            background-color: #d9534f;
            border-color: #d43f3a;
        }

        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: var(--teal-color);
            font-size: 14px;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }
        .logo {
    display: flex;
    justify-content: center; /* Centers the logo horizontally */
    align-items: center;    /* Centers the logo vertically */
    flex-direction: column; /* Allows for stacked content if needed */
}

        .logo img {
            width: 70px;
            margin-bottom: 20px;

        }
    </style>
</head>

<body>
    <div class="login-container">
        
        <h2>Login</h2>
        <div class="logo">
            <img src="CoThrift.svg" alt="CoThrift Logo">
        </div>
        <p>Do not have an account? <a href="signup.php" class="signin-link">Sign up</a></p>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email"
                    required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="forgot_password.html" class="forgot-password">Forgot Password?</a>
        </form>
    </div>
</body>

</html>