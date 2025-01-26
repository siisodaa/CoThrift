<?php
require "utilities.php";
require "mailsent.php";
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $school = $_POST['school'];
    $phone = $_POST['phone'];

    // make sure the email domain matches the school they chose
    $email_domain = substr($email, strpos($email, '@') + 1);
    $email_domain = substr($email_domain, 0, strpos($email_domain, '.'));
    $email_domain = strtolower($email_domain);


    if($password != $_POST['confirm_password']){
        $error = "Passwords do not match";
    }
    
    elseif ($school != $email_domain) {
        $error = "Email domain does not match the selected school";
    }
    else{
       // verify password

        $code = send_email($email, $name);


        header("Location: verify.php?email=$email&code=$code&name=$name&school=$school&phone=$phone&password=$password");
       

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
    <title>Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --teal-color: #008080;
            --black-color: #000000;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 40px 0;
        }

        .form-section {
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            padding: 60px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        .form-section h2 {
            color: var(--black-color);
            font-weight: bold;
            font-size: 28px;
        }

        .form-section p {
            color: #6c757d;
            margin-top: -10px;
            font-size: 14px;
        }

        .form-section .form-label {
            color: var(--black-color);
            font-weight: 600;
        }

        .form-section .form-control {
            border-color: var(--teal-color);
            border-radius: 8px;
        }

        .form-section .btn-primary {
            background-color: var(--teal-color);
            border-color: var(--teal-color);
            border-radius: 8px;
        }

        .form-section .btn-primary:hover {
            background-color: var(--black-color);
            border-color: var(--black-color);
        }

        .signin-link {
            color: var(--teal-color);
            text-decoration: none;
            font-weight: bold;
        }

        .signin-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="form-section">
        <h2>Get started</h2>
        <p>Already have an account? <a href="login.php" class="signin-link">Sign in</a></p>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label"> Full Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name"
                    required>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="location" class="form-label">School</label>
                    <select class="form-control" id="school" name="school" required>
                        <option value="">Select your school</option>
                        <option value="wesleyan">Wesleyan University</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter your phone"
                        required>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                    placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Enter your password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                    placeholder="Confirm your password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Signup</button>
        </form>
    </div>

    <div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="verificationModalLabel">Add New Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <!-- Modal Body -->
        <div class="modal-body">
          <form>
            <div class="mb-3">
              <label for="itemName" class="form-label">Item Name</label>
              <input type="text" class="form-control" id="itemName" name="name" placeholder="Enter Item Name">
            </div>
            <div class="mb-3">
              <label for="itemQuantity" class="form-label">Quantity</label>
              <input type="number" class="form-control" id="itemQuantity" name="quantity" placeholder="Enter Item Quantity">
            </div>

            <div class="mb-3">
                <label for="itemCategory" class="form-label">Category</label>
                <select class="form-select" id="itemCategory" name="category">
                  <option selected disabled>Choose a category</option>
                  <option value="electronics">Electronics</option>
                  <option value="furniture">Furniture</option>
                  <option value="clothing">Clothing</option>
                  <option value="toys">Toys</option>
                  <option value="books">Books</option>
                  <option value="sports">Sports Equipment</option>
                  <option value="beauty">Beauty Products</option>
                </select>
            </div>
          </form>
        </div>
        <!-- Modal Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
</body>

</html>