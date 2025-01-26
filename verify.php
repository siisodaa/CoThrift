<?php
require "utilities.php";

$code = $_GET['code'];
$email = $_GET['email'];
$name = $_GET['name'];
$password = $_GET['password'];
$school = $_GET['school'];
$phone = $_GET['phone'];

if(isset($_POST['submit'])){
    $code_recieved = $_POST['code'];
    
    if($code_recieved == $code){
        $output = create_member($name, $phone, $email, $password, $school);
        if( $output === True){
            // header('Location: login.php');
        
            header("Location: login.php");
        }
        else{
            echo  "An error occurred. Please try again:" . $output ;
        }
    }
    else{
        echo "Invalid code";
    }

  
}

// $output = create_member($name, $phone, $email, $password, $school);
// if( $output === True){
//     // header('Location: login.php');

//     header("Location: login.php");
// }
// else{
//     $error = "An error occurred. Please try again:" . $output ;
// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2>Verify Your Account</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" class="verification-form">
                            <div class="form-group">
                                <label for="code">Enter the code sent to your email:</label>
                                <input type="text" name="code" id="code" class="form-control" required>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary btn-block">Verify</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
</html>