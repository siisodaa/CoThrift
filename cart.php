<?php

require "utilities.php";
require "mailsent.php";
if(!isset($_SESSION['unique_email']))
{
    header('Location: login.php');
}

$email = $_SESSION['unique_email'];
$member_info = get_member_details($email);
$member_id = $member_info['member_id'];
$full_name = $member_info['name'];
if (isset($_POST['increase'])) {
  $item_id = $_POST['item_id'];

  // Get the available quantity for the item
  $available_quantity = get_item_quantity($item_id);

  // Increase the quantity of the item in the session, if within limits
  foreach ($_SESSION['items'] as $key => $item) {
      if ($item['item_id'] == $item_id) {
          if ($_SESSION['items'][$key]['quantity'] < $available_quantity) {
              $_SESSION['items'][$key]['quantity']++;
          } else {
            $_SESSION['alert'] = "Maximum quantity reached for " . who_is_item($item['item_id']) . ".";
          }
          break;
      }
  }
  header("Location: cart.php");
  exit;
}

if (isset($_POST['decrease'])) {
  $item_id = $_POST['item_id'];

  // Decrease the quantity of the item in the session
  foreach ($_SESSION['items'] as $key => $item) {
      if ($item['item_id'] == $item_id) {
          $_SESSION['items'][$key]['quantity']--;
          if ($_SESSION['items'][$key]['quantity'] <= 0) {
              // Remove the item from the session
              unset($_SESSION['items'][$key]);
              $_SESSION['items'] = array_values($_SESSION['items']); // Reindex array
          }
          break;
      }
  }
  header("Location: cart.php");
  exit;
}

if(isset($_POST['sendRequest'])){

  // get seller for each item
  $sellers = [];
  foreach ($_SESSION['items'] as $item) {
    $seller_info = get_seller_details($item['seller']);
    $sellers[$item['seller']][] = $item;
  }

  // create a new request for each unique seller
  foreach ($sellers as $seller_id => $items) {
    $current_seller_info = get_seller_details($seller_id);
    send_email_for_requests($current_seller_info['email'], $current_seller_info['name'], $items, $member_id);
    foreach($items as $item){
      $item_id = $item['item_id'];
      $quantity = $item['quantity'];


      if(create_request($member_id, $seller_id, $item_id, $quantity)){

        unset($_SESSION['items']);
        header('Location: index.php');
      }else{
        echo '<div class="alert alert-danger" role="alert">An error occurred. Please try again.</div>';
    }

  }


}

}

if (isset($_SESSION['alert'])) {
  echo '<div class="alert alert-warning" role="alert">' . $_SESSION['alert'] . '</div>';
  unset($_SESSION['alert']); // Clear the alert after displaying it
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .cart-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px 0;
      border-bottom: 1px solid #eee;
    }

    .cart-item img {
      width: 50px;
      height: 50px;
      object-fit: contain;
      border-radius: 8px;
    }

    .cart-item-details {
      flex-grow: 1;
      margin-left: 15px;
    }

    .cart-item-actions {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .seller-info {
      font-size: 0.9rem;
      color: #6c757d;
    }

    .confirm-order-btn {
      width: 100%;
      padding: 10px;
      font-size: 1.1rem;
    }

    .cart-container {
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .cart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .cart-header h1 {
      margin: 0;
    }

    .cart-header a {
      text-decoration: none;
      color: #007bff;
    }

    .cart-header a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container my-5">
    <div class="cart-container p-4 border rounded">
        <div class="cart-header d-flex justify-content-between align-items-center mb-4">
            <h1><?php echo $full_name; ?>'s Cart</h1>
            <a href="index.php" class="btn btn-outline-primary">Continue Thrifting</a>
        </div>

        <!-- Cart Items -->
        <div id="cart">
            <?php foreach($_SESSION['items'] as $item): ?>
            <?php
                $item_details = get_item_info($item['item_id']);
                $seller_info = get_seller_details($item['seller']);
            ?>
            <div class="cart-item d-flex align-items-center mb-3 p-3 border rounded">
                <img src="<?php echo $item_details['image_url']; ?>" alt="Item Image" class="img-thumbnail" style="width: 100px; height: 100px;">
                <div class="cart-item-details flex-grow-1 ms-3">
                    <h5 class="mb-1"><?php echo $item_details['name']; ?></h5>
                    <p class="seller-info mb-1">Sold by: <?php echo $seller_info['name']; ?> (<?php echo $seller_info['email']; ?>)</p>
                </div>
                <div class="cart-item-actions d-flex align-items-center">
                    <form method="post" action="" class="me-2">
                        <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                        <button type="submit" name="decrease" class="btn btn-sm btn-outline-secondary">-</button>
                    </form>
                    <span class="me-2"><?php echo $item['quantity']; ?></span>
                    <form method="post" action="">
                        <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                        <button type="submit" name="increase" class="btn btn-sm btn-outline-secondary">+</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <form method="post" class="mt-4">
            <?php if(!empty($_SESSION['items'])): ?>
                <button type="submit" class="btn btn-primary confirm-order-btn" name="sendRequest">Send Request</button>
            <?php else: ?>
                <div class="alert alert-info" role="alert">Your cart is empty. <a href="index.php">Continue Thrifting</a></div>  
            <?php endif; ?>
        </form>
    </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
