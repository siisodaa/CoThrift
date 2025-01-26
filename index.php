<?php
require "utilities.php";

if(!isset($_SESSION['unique_email'])){
  header("Location: login.php");
}

$email = $_SESSION['unique_email'];

$member_info = get_member_details($email);
$member_id = $member_info['member_id'];
$full_name = $member_info['name'];
$school = $member_info['school'];

$members_belonging_to_school = get_members_belonging_to_school($school, $member_id);

// echo print_r($members_belonging_to_school);

$items_to_shop = [];

foreach($members_belonging_to_school as $member){
  $items_to_shop = array_merge($items_to_shop, get_items_to_shop($member['member_id']));
}

$myitems = get_my_items($member_id);
// echo print_r($items);

if(!isset($_SESSION['items'])){
  $_SESSION['items'] = [];
}

if(isset($_POST['addToCart'])){
  $item_id = $_POST['product_id'];
  $quantity = 1; // Default quantity to 1, you can modify this as needed
  $seller_id = $_POST['seller_id'];

  // Check if the item is already in the cart
  $item_exists = false;
  foreach($_SESSION['items'] as &$cart_item){
    if($cart_item['item_id'] == $item_id){
      echo '<div class="alert alert-warning" role="alert">Item already in cart.</div>';
      $item_exists = true;
      break;
    }
  }

  // If the item is not in the cart, add it
  if(!$item_exists){
    $_SESSION['items'][] = [
      'item_id' => $item_id,
      'quantity' => $quantity,
      'seller' => $seller_id
    ];
  }
}

if(isset($_POST['logout'])){
  logout();
}


if(isset($_POST['addItem'])){
  $name = $_POST['name'];
  $quantity = $_POST['quantity'];
  $category = $_POST['category'];
  $desctiption = $_POST['description'];
  $image = $_FILES['image'];


  // add image to images folder
  $target_dir = "images/";
  $uploadOk = 1;
  $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

  // Check if image file is a actual image or fake image
  $check = getimagesize($_FILES["image"]["tmp_name"]);
  if($check !== false) {
    $uploadOk = 1;
  } else {
    echo '<div class="alert alert-danger" role="alert">File is not an image.</div>';
    $uploadOk = 0;
  }

  // Check if file already exists
  if (file_exists($target_dir . basename($_FILES["image"]["name"]))) {
    echo '<div class="alert alert-danger" role="alert">Sorry, file already exists.</div>';
    $uploadOk = 0;
  }

  // Check file size
  if ($_FILES["image"]["size"] > 10485760) {
    echo "The file is too large. Maximum allowed size is 10 MB.";
    $uploadOk = 0;
  }

  // Allow certain file formats
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    echo '<div class="alert alert-danger" role="alert">Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>';
    $uploadOk = 0;
  }

  // Generate a unique filename for the image
  $unique_id = uniqid();
  $target_file = $target_dir . $member_id . "_" . $unique_id . "." . $imageFileType;

  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
    echo '<div class="alert alert-danger" role="alert">Sorry, your file was not uploaded.</div>';
  // if everything is ok, try to upload file
  } else {
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
      $image_path = $target_file;

      if(add_item($name, $quantity, $category, $member_id, $desctiption, $image_path)){
        echo '<div class="alert alert-success" role="alert">Item added successfully</div>';
      }
      else{
        echo '<div class="alert alert-danger" role="alert">Failed to add item</div>';
      }
    } else {
      echo '<div class="alert alert-danger" role="alert">Sorry, there was an error uploading your file.</div>';
    }
  }
  

}

if(isset($_POST['delete'])){
  $item_id = $_POST['item_id'];
  if(delete_item($item_id, $member_id)){
    echo '<div class="alert alert-success" role="alert">Item deleted successfully</div>';
  }
  else{
    echo '<div class="alert alert-danger" role="alert">Failed to delete item</div>';
  }
}
// echo print_r($_SESSION['items']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  
  <style>
    .product-card {
  border: 1px solid #e0e0e0;
  border-radius: 12px; /* Softer rounded corners */
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transitions */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
  padding: 15px; /* Slightly more padding for content spacing */
  background-color: #ffffff; /* Ensure a consistent background */
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15); /* Enhance hover effect */
  cursor: pointer;;
}

.product-image {
  background-color: #f5f5f5; /* Softer background color */
  height: 180px; /* Slightly taller for better image display */
  display: flex;
  align-items: center;
  justify-content: center;
  border-bottom: 1px solid #e0e0e0; /* Separates image from content */
  overflow: hidden; /* Ensures image fits */
}

.product-image img {
  max-height: 100%;
  max-width: 100%;
  object-fit: contain; /* Ensures image scaling */
}
.product-info {
  display: flex;
  justify-content: space-between; /* Ensures spacing between left and right content */
  align-items: center;
  width: 100%; /* Ensures full width alignment */
  padding: 10;
  margin-top: 10px;
  border: none;
  background: none;
}

.product-details {
  text-align: left; /* Aligns text to the far left */
}

.product-name {
  font-size: 1rem;
  font-weight: bold;
  color: #212529; /* Dark text for better readability */
  text-decoration: none; /* Removes underline */
  margin: 0;
}

.product-category {
  font-size: 0.85rem;
  color: #6c757d; /* Muted text color */
  margin: 0;
}

.product-price {
  font-size: 1rem;
  color:rgb(34, 106, 1); /* Muted text color */
  margin: 0;
  font-weight: bold;
}

.add-to-cart {
  font-size: 0.9rem;
  color: #ffffff; /* White text color */
  border: none;
  padding: 5px 10px;
  cursor: pointer;
}

.add-to-cart:hover {
  background-color: #0056b3;
  transform: translateY(-2px);
}


@media (max-width: 768px) {
  .product-card {
    padding: 10px;
  }

  .product-image {
    height: 150px; /* Smaller height for mobile */
  }

  .product-name {
    font-size: 1rem;
  }

  .product-price {
    font-size: 0.85rem;
  }

  .add-to-cart {
    font-size: 0.85rem;
    padding: 6px 12px;
  }
}

.product-modal-content {
  border-radius: 1rem;
}

.product-modal-body {
  padding: 2rem;
}

.product-image-container {
  width: 100%;
  height: 350px;
  background-color: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
}

.product-modal-image {
  max-height: 100%;
  max-width: 100%;
}

.product-modal-name {
  font-size: 1.5rem;
  color: #212529;
}

.product-modal-quantity {
  font-size: 1rem;
  color: #6c757d;
}

.product-seller-info {
  background-color: #f8f9fa;
}

.product-modal-seller-name,
.product-modal-seller-email {
  font-size: 1rem;
}

.product-modal-add-to-cart {
  font-size: 1.2rem;
  font-weight: 600;
  background-color: #007bff;
  border-color: #007bff;
}

.product-modal-add-to-cart:hover {
  background-color: #0056b3;
  border-color: #0056b3;
}
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-light bg-light px-4">
    <div class="container-fluid">
        <!-- Left Section: Logo and School -->
        <div class="d-flex align-items-center">
            <img src="CoThrift.svg" alt="Logo" style="height: 80px; margin-right: 10px;">
            <span class="navbar-brand mb-0 h1"><?php echo $full_name; ?></span>
        </div>

        <!-- Right Section: Buttons and Dropdowns -->
        <div class="d-flex">
            <button class="btn btn-outline-secondary mx-1" data-bs-toggle="modal" data-bs-target="#addItemModal">Add Item</button>
            <button class="btn btn-outline-secondary mx-1" data-bs-toggle="modal" data-bs-target="#myItemsModal">My Items</button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary mx-1 dropdown-toggle" type="button" id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Requests
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                    <li><a class="dropdown-item" href="requests.php?fromme=1">Sent to me</a></li>
                    <li><a class="dropdown-item" href="requests.php?fromme=0">Sent by me</a></li>
                </ul>
            </div>
            <a href="cart.php" class="btn btn-outline-success mx-1">
                <i class="fas fa-shopping-cart"></i> Cart (<?php echo count($_SESSION['items']); ?>)
            </a>
            <div class="dropdown">
                <button class="btn btn-outline-secondary mx-1 dropdown-toggle" type="button" id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-cog"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                    <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profileModal">Profile</button></li>
                    <li>
                        <form method="post">
                            <button type="submit" class="dropdown-item" name="logout">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>


  <!-- Search and Filter -->
  <div class="container my-3">
    <div class="row">
      <div class="col-md-8">
        <input type="text" class="form-control" id="searchInput" placeholder="Search by name" onkeyup="filterProducts()">

      </div>
      <div class="col-md-4">
        <select class="form-select" id="categoryFilter" onchange="filterByCategory()">
          <option value="">All Categories</option>
          <option value="electronics">Electronics</option>
          <option value="furniture">Furniture</option>
          <option value="clothing">Clothing</option>
          <option value="toys">Toys</option>
          <option value="books">Books</option>
          <option value="sports">Sports Equipment</option>
          <option value="beauty">Beauty Products</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Product Grid -->
  <div class="container">
    <div class="row g-4">
      <!-- Check if there are items to display -->
      <?php if (!empty($items_to_shop)): ?>
        <!-- Product Card -->
        <?php foreach($items_to_shop as $item): ?>
          <?php $current_seller_info = get_member_details_by_id($item['member_id']); ?>
          <div class="col-md-3">
            <div 
              class="product-card position-relative" 
              data-bs-toggle="modal" 
              data-bs-target="#productModal"
              data-name="<?php echo $item['name']; ?>"
              data-quantity="<?php echo $item['quantity']; ?>"
              data-category="<?php echo $item['category']; ?>"
              data-price="Free"
              data-image="<?php echo $item['image_url']; ?>"
              data-description="<?php echo $item['description']; ?>"
              data-seller-name="<?php echo $current_seller_info['name']; ?>"
              data-seller-contact="<?php echo $current_seller_info['phone']; ?>"
              data-seller-email="<?php echo $current_seller_info['email']; ?>">
              <div class="product-image">
                <img src="<?php echo $item['image_url']; ?>" alt="Product Image" class="img-fluid">
              </div>
              <div class="product-info d-flex justify-content-between align-items-center">
                <div class="product-details text-start">
                  <p class="product-name"><?php echo $item['name']; ?></p>
                  <p class="product-category"><?php echo $item['category']; ?></p>
                  <p class="product-price">Free</p>
                </div>
                <form method="post" class="d-inline">
                  <input type="hidden" name="product_id" value="<?php echo $item['item_id']; ?>">
                  <input type="hidden" name="seller_id" value="<?php echo $item['member_id']; ?>">
                  <?php $seller_info = get_member_details_by_id($item['member_id']); ?>
                  <input type="hidden" name="seller_name" value="<?php echo $seller_info['name']; ?>">
                  <button type="submit" class="btn btn-sm btn-primary add-to-cart" name="addToCart">
                    <i class="fas fa-shopping-cart"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Display a message when no items are available -->
        <div class="col-12 text-center">
          <p class="text-muted mt-4">Sorry, there is no one throwing stuff away at your school right now. 🌱</p>
          <p class="text-success">Every small action counts! By participating in this platform, you are contributing to sustainability and reducing waste. Keep checking back for new items!</p>
        </div>
      <?php endif; ?>
    </div>
</div>


  <!-- Add Item Modal -->
  <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="addItemModalLabel">Add New Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <!-- Modal Body -->
        <div class="modal-body">
          <form method="post" enctype="multipart/form-data">
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
            <div class="mb-3">
              <label for="itemDescription" class="form-label">Description</label>
              <textarea class="form-control" id="itemDescription" name="description" rows="3" placeholder="Enter Item Description" maxlength="100"></textarea>
              <small class="form-text text-muted">Maximum 100 characters.</small>
            </div>

            <div>
              <label for="itemImage" class="form-label">Image</label>
              <input type="file" class="form-control" id="itemImage" name="image">
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary" name="addItem">Save</button>
            </div>
          </form>
        </div>
        
      </div>
    </div>
  </div>
    </div>
  </div>

  <!-- Product Details Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content product-modal-content border-0 rounded-4 shadow-lg">
      <!-- Modal Body -->
      <div class="modal-body product-modal-body p-4">
        <div class="row g-3">
          <!-- Product Image Section -->
          <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div class="product-image-container p-3 border rounded-4">
              <img id="modalProductImage" src="" alt="Product Image" class="product-modal-image img-fluid rounded-4 shadow-sm">
            </div>
          </div>

          <!-- Product Details Section -->
          <div class="col-md-6">
            <div class="d-flex flex-column h-100 justify-content-between">
              <!-- Product Info -->
              <div>
                <div class="mb-3">
                  <h4 id="modalProductName" class="product-modal-name fw-bold mb-2">Product Name</h4>
                  <p id="modalProductPrice" class="product-modal-price text-success fw-bold fs-5 mb-2">Free</p>
                  <p id="modalProductQuantity" class="product-modal-quantity text-muted mb-2">
                    Quantity Available: <span>0</span>
                  </p>
                  <p id="modalProductDescription" class="product-modal-description text-muted">
                    Description: <span>Loading...</span>
                  </p>
                </div>

                <!-- Seller Info -->
                <div class="product-seller-info mb-3 p-3 border rounded-4">
                  <h6 class="fw-bold mb-2">Seller Information</h6>
                  <p id="sellerName" class="product-modal-seller-name mb-1">
                    <strong>Name:</strong> <span></span>
                  </p>
                  <p id="sellerEmail" class="product-modal-seller-email mb-0">
                    <strong>Email:</strong> <span></span>
                  </p>
                </div>
              </div>

              <!-- Add to Cart Button -->
              <!-- <div class="mt-4">
                <button class="product-modal-add-to-cart btn btn-primary w-100 p-3 rounded-4">
                  <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                </button>
              </div> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




  <!-- My Items Modal -->
  <div class="modal fade" id="myItemsModal" tabindex="-1" aria-labelledby="myItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="myItemsModalLabel">My Items</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <!-- Modal Body -->
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Item Name</th>
                  <th>Quantity</th>
                  <th>Category</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($myitems as $item): ?>
                <tr>
                  <td><?php echo $item['name']; ?></td>
                  <td><?php echo $item['quantity']; ?></td>
                  <td><?php echo $item['category']; ?></td>
                  <td>
                    <?php 
                      if ($item['status'] == 1) {
                        echo '<span class="badge bg-success">Available</span>';
                      } else {
                        echo '<span class="badge bg-secondary">Unavailable</span>';
                      }
                    ?>
                  </td>
                  <td>
                    <form method="post">
                      <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                      <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>

                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Profile Modal -->
  <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="profileModalLabel">Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <!-- Modal Body -->
        <div class="modal-body">
          <form method="post">
            <div class="mb-3">
              <label for="profileName" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="profileName" name="profile_name" value="<?php echo $full_name; ?>" readonly>
            </div>
            <div class="mb-3">
              <label for="profileEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="profileEmail" name="profile_email" value="<?php echo $email; ?>" readonly>
            </div>
            <div class="mb-3">
              <label for="profilePhone" class="form-label">Phone</label>
              <input type="text" class="form-control" id="profilePhone" name="profile_phone" value="<?php echo $member_info['phone']; ?>" readonly>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

  <script>

const productModal = document.getElementById('productModal');

productModal.addEventListener('show.bs.modal', function (event) {
  const card = event.relatedTarget; // Card that triggered the modal

  // Extract product data attributes
  const name = card.getAttribute('data-name');
  const quantity = card.getAttribute('data-quantity') || '0'; // Default to '0' if not provided
  const price = "Free"; // Set price to Free
  const image = card.getAttribute('data-image');
  const description = card.getAttribute('data-description') || 'No description available.';
  const sellerName = card.getAttribute('data-seller-name');
  const sellerEmail = card.getAttribute('data-seller-email');

  // Update modal content
  document.getElementById('modalProductName').textContent = name;
  document.getElementById('modalProductPrice').textContent = price;
  document.getElementById('modalProductQuantity').querySelector('span').textContent = quantity;
  document.getElementById('modalProductDescription').querySelector('span').textContent = description;
  document.getElementById('modalProductImage').setAttribute('src', image);
  document.getElementById('sellerName').querySelector('span').textContent = sellerName;
  document.getElementById('sellerEmail').querySelector('span').textContent = sellerEmail;
});

    function filterProducts() {
      var input, filter, cards, cardContainer, title, i;
      input = document.getElementById("searchInput");
      filter = input.value.toLowerCase();
      cardContainer = document.getElementsByClassName("row g-4")[0];
      cards = cardContainer.getElementsByClassName("col-md-3");

      for (i = 0; i < cards.length; i++) {
        title = cards[i].getElementsByClassName("product-name")[0];
        if (title.innerText.toLowerCase().indexOf(filter) > -1) {
          cards[i].style.display = "";
        } else {
          cards[i].style.display = "none";
        }
      }
    }

    function filterByCategory() {
      var select, filter, cards, cardContainer, category, i;
      select = document.getElementById("categoryFilter");
      filter = select.value.toLowerCase();
      cardContainer = document.getElementsByClassName("row g-4")[0];
      cards = cardContainer.getElementsByClassName("col-md-3");

      for (i = 0; i < cards.length; i++) {
        category = cards[i].getElementsByClassName("product-category")[0];
        if (filter === "" || category.innerText.toLowerCase().indexOf(filter) > -1) {
          cards[i].style.display = "";
        } else {
          cards[i].style.display = "none";
        }
      }
    }
  </script>
</body>
</html>