<?php
require_once('utilities.php');

if(isset($_SESSION['unique_email']))
{
    $email = $_SESSION['unique_email'];
    $member_info = get_member_details($email);
    $member_id = $member_info['member_id'];
    $full_name = $member_info['name'];
    $item_id = $_GET['item_id'];
}
else
{
    header('Location: login.php');
}


$item_information = get_item_info($item_id);

if(isset($_POST['add_to_cart']))
{
    $quantity = $_POST['quantity'];
    $item = [
        'item_id' => $item_id,
        'quantity' => $quantity
    ];

    if(isset($_SESSION['items']))
    {
        $_SESSION['items'][] = $item;
    }
    else
    {
        $_SESSION['items'] = [$item];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f9f9f9;
      font-family: 'Arial', sans-serif;
    }

    .product-details-container {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 30px;
    }

    .product-image {
      border-radius: 8px;
      overflow: hidden;
    }

    .product-image img {
      width: 100%;
      object-fit: cover;
    }

    .product-title {
      font-size: 1.8rem;
      font-weight: 700;
      color: #333333;
    }

    .product-description {
      font-size: 1rem;
      color: #666666;
      line-height: 1.6;
    }

    .product-price {
      font-size: 1.2rem;
      font-weight: 600;
      color: #28a745;
    }

    .btn-primary {
      background-color: #007bff;
      border-color: #007bff;
      border-radius: 8px;
      padding: 10px 20px;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-primary:hover {
      background-color: #0056b3;
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="product-details-container row align-items-center">
      <!-- Product Image -->
      <div class="col-md-6 product-image">
        <img src="<?php echo $item_information['image_url']; ?>" alt="Product Image" class="img-fluid">
      </div>

      <!-- Product Details -->
      <div class="col-md-6">
        <h1 class="product-title"><?php echo $item_information['name']; ?></h1>
        <p class="product-description"><?php echo $item_information['description']; ?></p>
        <p class="product-price"><strong>Price:</strong> FREE</p>
        <button class="btn btn-primary"><i class="fas fa-shopping-cart me-2"></i>Add to Cart</button>
      </div>
    </div>
  </div>
</body>
</html>

