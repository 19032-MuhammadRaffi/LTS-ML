<?php
require '../../../conn.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header('location: ../../../index.php');
    exit;
}

// Logout
if (isset($_POST['btn_logout'])) {
    session_destroy();
    header('location: ../../../index.php');
    exit;
}

// Get data from table data_master
$id = isset($_GET['id']) ? $_GET['id'] : '';
$query = "SELECT * FROM data_master WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$data_master = mysqli_fetch_assoc($result);

// Update data_master
if (isset($_POST['btn_update'])) {
    $description = $_POST['description'];
    $category = $_POST['category'];

    $query = "UPDATE data_master SET description = '$description', category = '$category' WHERE id = '$id'";
    mysqli_query($conn, $query);
    // Alert Success
    echo "<script>alert('Success');</script>";
    echo "<script>window.location.href = '../index.php';</script>";
    exit;
}

// Delete data_master
if (isset($_POST['btn_delete'])) {
    $query = "DELETE FROM data_master WHERE id = '$id'";
    mysqli_query($conn, $query);
    // Alert Success
    echo "<script>alert('Success');</script>";
    echo "<script>window.location.href = '../index.php';</script>";
    exit;
}
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>AC System</title>
    <script src="../../../js/color-modes.js"></script>
    <script src="../../../js/jquery-3.7.1.js"></script>
    <script src="../../../js/jquery-ui.js"></script>
    <link rel="stylesheet" href="../../../css/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/style.css" rel="stylesheet">
    <style>
        .btn-sm {
            width: 80px;
        }
    </style>
</head>

<body>
    <!-- Themes Mode -->
    <?php include '../../../library/themes.php'; ?>

    <div class="container-fluid text-center">
        <!-- ROW 1 -->
        <div class="row mt-3">
            <div class="col text-end">
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</button>
            </div>

            <!-- Modal Logout -->
            <div class="text-start modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="" method="POST">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="logoutModalLabel">Notification</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Logout?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                <button type="submit" class="btn btn-primary" name="btn_logout">Yes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- ROW 2 -->
        <h3>Data Master</h3>
        <div class="row mt-3">
            <!-- Data Master Management -->
            <div class="col-4 mx-auto">
                <div class="card text-center">
                    <div class="card-header">
                        <div>Data Master Management</span>
                        </div>
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingInput" name="description" value="<?php echo htmlspecialchars($data_master['description']); ?>">
                                    <label for="floatingInput">Description</label>
                                </div>
                                <div class="form-floating mt-3">
                                    <select class="form-select" id="floatingSelect" name="category">
                                        <option value="Defect" <?php if ($data_master['category'] == 'Defect') echo 'selected'; ?>>Defect</option>
                                        <option value="Model" <?php if ($data_master['category'] == 'Model') echo 'selected'; ?>>Model</option>
                                        <option value="Category" <?php if ($data_master['category'] == 'Category') echo 'selected'; ?>>Category</option>
                                        <option value="Root_Cause" <?php if ($data_master['category'] == 'Root_Cause') echo 'selected'; ?>>Root Cause</option>
                                        <option value="Action_Production" <?php if ($data_master['category'] == 'Action_Production') echo 'selected'; ?>>Action Production</option>
                                    </select>
                                    <label for="floatingSelect">Category</label>
                                </div>
                                <!-- Button Edit -->
                                <button type="submit" class="btn btn-primary w-100 mt-3" name="btn_update">Update</button>
                                <!-- Button Delete -->
                                <button type="button" class="btn btn-danger w-100 mt-3" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    Delete
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="deleteModalLabel">Delete Data Master</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete data "<?php echo htmlspecialchars($data_master['description']); ?>"?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                                <button type="submit" name="btn_delete" class="btn btn-danger">Yes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Javascript -->
            <script src="../../../js/bootstrap.bundle.min.js"></script>
</body>

</html>