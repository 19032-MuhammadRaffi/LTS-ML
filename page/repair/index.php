<!-- Gabungkan dengan function.php -->
<?php
require '../../conn.php';
session_start();
// Cek sesi, redirect kalau udah login
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] != 'Repair') {
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: ../../index.php');
    exit();
}
// Set tanggal dan shift produksi
date_default_timezone_set('Asia/Jakarta');
$productionDate = date('Y-m-d');
$productionDateDisplay = date('d-m-Y', strtotime($productionDate));
$hour = date('H');
if ($hour >= 7 && $hour < 15) {
    $productionShift = 1;
} elseif ($hour >= 15 && $hour < 23) {
    $productionShift = 2;
} else {
    $productionShift = 3;
}
$productionShiftDisplay = $productionShift;
// Logout
if (isset($_POST['btn_logout'])) {
    session_destroy();
    header('Location: ../../index.php');
    exit();
}
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>AC System</title>
    <script src="../../js/color-modes.js"></script>
    <script src="../../js/jquery-3.7.1.js"></script>
    <script src="../../js/jquery-ui.js"></script>
    <link rel="stylesheet" href="../../css/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Themes Mode -->
    <?php include '../../library/themes.php'; ?>

    <div class="container-fluid text-center">
        <!-- Heading -->
        <div class="row mt-3">
            <div class="col text-start">
                <button class="btn btn-sm btn-outline-success" disabled><?php echo "Leader " . $_SESSION['role'] . " - " . $productionDateDisplay . " - Shift " . $productionShiftDisplay ?></button>
            </div>
            <div class="col text-center">
                <a href="history.php" class="btn btn-sm btn-outline-primary" style="width: 150px;">Production Report</a>
            </div>
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
        <!-- Data -->
        <div class="row mt-3">

        </div>
    </div>

    <!-- Javascript -->
    <script src="../../js/bootstrap.bundle.min.js"></script>
</body>

</html>