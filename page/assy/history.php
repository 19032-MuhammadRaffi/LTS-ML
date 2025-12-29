<!-- Gabungkan dengan function.php -->
<?php
require 'function.php';
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
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .charts-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: space-between;
        }

        .chart-container {
            width: 32%;
            /* karena sekarang ada 3 chart */
            height: 300px;
        }
    </style>
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
                <a href="index.php" class="btn btn-sm btn-outline-primary" style="width: 150px;">Dashboard</a>
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
        <!-- Form Select Date -->
        <div class="row">
            <form method="POST" class="mt-3 d-flex gap-2 align-items-center justify-content-center">
                <select name="month" class="form-select w-auto">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $m == (int)$selectedMonth ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <select name="year" class="form-select w-auto">
                    <?php for ($y = 2023; $y <= date('Y'); $y++): ?>
                        <option value="<?= $y ?>" <?= $y == (int)$selectedYear ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </form>
        </div>
        <!-- Table Report -->
        <div class="row">
            <div class="col">
                <!-- Production History Table IN PRESS -->
                <div class="table-responsive">
                    <h4 class="text-center my-3">History Production - <?= date('F', mktime(0, 0, 0, $selectedMonth, 1)) ?> <?= $selectedYear ?></h5>
                        <table class="table table-bordered table-striped" style="font-size: 12px;">
                            <thead class="text-center">
                                <th rowspan="2" class="align-middle" style="min-width: 120px;">
                                    Part Code
                                </th>
                                <th rowspan="2" class="align-middle text-primary" style="min-width: 80px;">
                                    Total Press
                                </th>
                                <th rowspan="2" class="align-middle text-success" style="min-width: 80px;">
                                    Total Paint
                                </th>
                                <th rowspan="2" class="align-middle text-warning" style="min-width: 80px;">
                                    Total Assy
                                </th>
                                <?php foreach ($dates as $date): ?>
                                    <th colspan="3">
                                        <?= date('j', strtotime($date)) ?>
                                    </th>
                                <?php endforeach; ?>
                                <tr>
                                    <?php foreach ($dates as $date): ?>
                                        <th class="align-middle text-primary">Press</th>
                                        <th class="align-middle text-success">Paint</th>
                                        <th class="align-middle text-warning">Assy</th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <?php foreach ($allParts as $partCode): ?>
                                    <?php
                                    $totalPress = 0;
                                    $totalPaint = 0;
                                    $totalAssy  = 0;
                                    // Hitung total bulanan
                                    foreach ($dates as $date) {
                                        $totalPress += array_sum($dataPress[$partCode][$date] ?? []);
                                        $totalPaint += array_sum($dataPaint[$partCode][$date] ?? []);
                                        $totalAssy  += array_sum($dataAssy[$partCode][$date] ?? []);
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?= $partCode ?></td>
                                        <!-- TOTAL BULANAN -->
                                        <td class="fw-bold text-primary"><?= $totalPress ?></td>
                                        <td class="fw-bold text-success"><?= $totalPaint ?></td>
                                        <td class="fw-bold text-warning"><?= $totalAssy ?></td>
                                        <!-- DATA PER TANGGAL -->
                                        <?php foreach ($dates as $date): ?>
                                            <?php
                                            $press = array_sum($dataPress[$partCode][$date] ?? []);
                                            $paint = array_sum($dataPaint[$partCode][$date] ?? []);
                                            $assy  = array_sum($dataAssy[$partCode][$date] ?? []);
                                            ?>
                                            <td class="text-primary"><?= $press ?></td>
                                            <td class="text-success"><?= $paint ?></td>
                                            <td class="text-warning"><?= $assy ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                </div>
            </div>
            <!-- Javascript -->
            <script src="../../js/bootstrap.bundle.min.js"></script>
</body>

</html>