<?php

require "utilities.php";

if(!isset($_SESSION['unique_email']))
{
    header('Location: login.php');
}

$email = $_SESSION['unique_email'];
$member_info = get_member_details($email);
$member_id = $member_info['member_id'];
$school = $member_info['school'];
$full_name = $member_info['name'];
$fromme = $_GET['fromme'] ?? 0;

if(isset($_GET['fromme']) && $_GET['fromme'] == 1){
    $requests = get_requests_to_me($member_id,$school );
}
else{
    $requests = get_requests_from_me($member_id, $school);
}

$fromme = isset($_GET['fromme']) ? $_GET['fromme'] : 0;

// echo print("<pre>".print_r($requests,true)."</pre>");


if(isset($_POST['accept'])){
    $request_id = $_POST['request_id'];
    $status = 1;
    
    echo update_requests($request_id, $status);
    header("Location: requests.php?fromme=$fromme");

}

if(isset($_POST['decline'])){
    $request_id = $_POST['request_id'];
    $status = 2;
    
    echo update_requests($request_id, $status);
    header("Location: requests.php?fromme=$fromme");
}

if(isset($_POST['logout'])){
    logout();
  }
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        nav {
            background-color: #f8f9fa;
        }
        .navbar-brand img {
            height: 50px;
        }
        .table thead {
            background-color: #343a40;
            color: #fff;
        }
        .modal-header {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light px-4 shadow-sm">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="CoThrift.svg" alt="Logo" class="me-2">
        <span><?php echo $full_name; ?></span>
    </a>
    <div class="ms-auto">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-cog"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
            <!-- <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profileModal">Profile</button></li> -->
            <li>
                <form method="post">
                    <button type="submit" class="dropdown-item" name="logout">Logout</button>
                </form>
            </li>
        </ul>
    </div>
</nav>
<div class="container my-5">
    <h2 class="mb-4 text-center">
        <?= $fromme == 1 ? "Requests Sent To Me" : "Requests Sent By Me"; ?>
    </h2>

    <?php if (empty($requests)): ?>
        <div class="alert alert-secondary text-center" role="alert">
            No requests to display.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover table-bordered align-middle">
            <thead class="table-secondary text-black text-center">
                <tr>
                    <?php if ($fromme == 1): ?>
                        <th>Requestor Name</th>
                    <?php endif; ?>
                    <th>Item Requested</th>
                    <th>Quantity Requested</th>
                    <th>Request Date</th>
                    <th>Status/Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Pending Requests -->
                <?php foreach ($requests as $request): ?>
                    <?php if ($request['status'] == 0): ?>
                        <tr>
                            <?php if ($fromme == 1): ?>
                                <td class="text-center"><?= who_is_member($request['member_from']); ?></td>
                            <?php endif; ?>
                            <td class="text-center"><?= who_is_item($request['item_id']); ?></td>
                            <td class="text-center"><?= $request['quantity']; ?></td>
                            <td class="text-center"><?= $request['updated_at']; ?></td>
                            <td class="text-center">
                                <?php if ($fromme == 1): ?>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#acceptModal" data-request-id="<?= $request['request_id']; ?>">Accept</button>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#declineModal" data-request-id="<?= $request['request_id']; ?>">Decline</button>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- Accepted/Declined Requests -->
                <?php foreach ($requests as $request): ?>
                    <?php if ($request['status'] != 0): ?>
                        <tr>
                            <?php if ($fromme == 1): ?>
                                <td class="text-center"><?= who_is_member($request['member_from']); ?></td>
                            <?php endif; ?>
                            <td class="text-center"><?= who_is_item($request['item_id']); ?></td>
                            <td class="text-center"><?= $request['quantity']; ?></td>
                            <td class="text-center"><?= $request['updated_at']; ?></td>
                            <td class="text-center">
                                <?php if ($request['status'] == 1): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Declined</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>


<!-- Modals -->
<div class="modal fade" id="acceptModal" tabindex="-1" aria-labelledby="acceptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Accept - Request ID: <span id="acceptRequestIdTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to accept this request?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="post">
                    <input type="hidden" name="request_id" id="acceptRequestId">
                    <input type="hidden" name="action" value="accept">
                    <button type="submit" class="btn btn-primary" name="accept">Accept</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Decline - Request ID: <span id="declineRequestIdTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to decline this request?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="post">
                    <input type="hidden" name="request_id" id="declineRequestId">
                    <input type="hidden" name="action" value="decline">
                    <button type="submit" class="btn btn-danger" name="decline">Decline</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
    button.addEventListener('click', () => {
        const requestId = button.getAttribute('data-request-id');
        const modalId = button.getAttribute('data-bs-target');
        const modal = document.querySelector(modalId);

        if (modal) {
            modal.querySelector('.modal-title span').textContent = requestId;
            modal.querySelector('input[name="request_id"]').value = requestId;
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
