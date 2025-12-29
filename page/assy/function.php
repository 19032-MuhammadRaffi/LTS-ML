<?php
// Database Connection
require '../../conn.php';

// Session Check
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Assy') {
    header('location: ../../index.php');
    exit;
}

// Auto Refresh 60s
echo '<meta http-equiv="refresh" content="60">';

// Logout
if (isset($_POST['btn_logout'])) {
    session_destroy();
    header('location: ../../index.php');
    exit;
}

date_default_timezone_set('Asia/Jakarta');
$currentDate = date('Y-m-d');

// Function Production Date
function getProductionDateOnly($datetime)
{
    $time = date('H:i', strtotime($datetime));
    $date = date('Y-m-d', strtotime($datetime));

    if ($time < '08:00') {
        return date('Y-m-d', strtotime($date . ' -1 day'));
    }
    return $date;
}

// Function Shift
function getShift($time)
{
    if ($time >= '08:00' && $time < '17:00') return 1;
    if ($time >= '17:00' || $time < '00:30') return 2;
    return 3;
}

// GET DATA PART
$partResult = mysqli_query($conn, "
    SELECT part_code, part_name 
    FROM part
");

$komponen = [];
while ($row = mysqli_fetch_assoc($partResult)) {
    $komponen[$row['part_code']] = [
        'part_code' => $row['part_code'],
        'part_name' => $row['part_name'],
        'total_press'  => 0,
        'total_paint'  => 0,
        'total_assy'   => 0,
        'daily_press'  => 0,
        'daily_paint'  => 0,
        'daily_assy'   => 0,
        'shift1_press' => 0,
        'shift1_paint' => 0,
        'shift1_assy'  => 0,
        'shift2_press' => 0,
        'shift2_paint' => 0,
        'shift2_assy'  => 0,
        'shift3_press' => 0,
        'shift3_paint' => 0,
        'shift3_assy'  => 0,
        'stock_press' => 0,
        'stock_paint' => 0,
        'stock_assy'  => 0
    ];
}

// GET TRANSACTIONS (BULAN INI)
function getTrans($status)
{
    return "
        SELECT part_code, date_tr, shift, qty
        FROM `transaction`
        WHERE status='$status'
        AND DATE_FORMAT(date_tr, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
    ";
}

$pressData  = mysqli_query($conn, getTrans('PRESS'));
$paintData = mysqli_query($conn, getTrans('PAINT'));
$assyData  = mysqli_query($conn, getTrans('ASSY'));

// PROCESS PRESS
while ($tr = mysqli_fetch_assoc($pressData)) {
    $p     = $tr['part_code'];
    $shift = (int)$tr['shift'];
    $qty   = (int)$tr['qty'];
    $prodDate = getProductionDateOnly($tr['date_tr']);
    // Monthly
    $komponen[$p]['total_press'] += $qty;
    // Daily
    if ($prodDate === $currentDate) {
        $komponen[$p]['daily_press'] += $qty;
        $komponen[$p]["shift{$shift}_press"] += $qty;
    }
}

// PROCESS PAINT
while ($tr = mysqli_fetch_assoc($paintData)) {
    $p     = $tr['part_code'];
    $shift = (int)$tr['shift'];
    $qty   = (int)$tr['qty'];
    $prodDate = getProductionDateOnly($tr['date_tr']);
    // Monthly
    $komponen[$p]['total_paint'] += $qty;
    // Daily
    if ($prodDate === $currentDate) {
        $komponen[$p]['daily_paint'] += $qty;
        $komponen[$p]["shift{$shift}_paint"] += $qty;
    }
}

// PROCESS ASSY
while ($tr = mysqli_fetch_assoc($assyData)) {
    $p     = $tr['part_code'];
    $shift = (int)$tr['shift'];
    $qty   = (int)$tr['qty'];
    $prodDate = getProductionDateOnly($tr['date_tr']);
    // Monthly
    $komponen[$p]['total_assy'] += $qty;
    // Daily
    if ($prodDate === $currentDate) {
        $komponen[$p]['daily_assy'] += $qty;
        $komponen[$p]["shift{$shift}_assy"] += $qty;
    }
}

// HITUNG STOCK
foreach ($komponen as &$d) {
    // Press
    $queryStock = mysqli_query($conn, "SELECT * FROM part WHERE part_code='" . $d['part_code'] . "'");
    $stockData = mysqli_fetch_assoc($queryStock);

    $d['stock_press'] = (int)$stockData['qty_press'];
    $d['stock_paint'] = (int)$stockData['qty_paint'];
}
unset($d);

// HANDLE FINISH PRODUCTION - ASSY
if (isset($_POST['btn_finish'])) {

    $partCode = $_POST['part_code'] ?? '';
    $qty      = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;

    // VALIDASI INPUT
    if ($partCode === '' || $qty <= 0) {
        echo "<script>
            alert('Part code atau qty tidak valid');
            history.back();
        </script>";
        exit;
    }

    $now   = date('Y-m-d H:i:s');
    $shift = getShift(date('H:i'));

    // START TRANSACTION
    mysqli_begin_transaction($conn);

    try {
        // CEK STOK PAINT
        $checkStock = mysqli_query($conn, "
            SELECT qty_paint 
            FROM part 
            WHERE part_code = '$partCode'
            FOR UPDATE
        ");

        if (!$checkStock || mysqli_num_rows($checkStock) === 0) {
            throw new Exception('Part tidak ditemukan');
        }

        $stockPaint = (int)mysqli_fetch_assoc($checkStock)['qty_paint'];

        if ($stockPaint < $qty) {
            throw new Exception('Stok Paint tidak mencukupi');
        }

        // INSERT TRANSACTION ASSY
        if (!mysqli_query($conn, "
            INSERT INTO `transaction`
            (part_code, date_tr, shift, qty, status)
            VALUES
            ('$partCode', '$now', '$shift', '$qty', 'ASSY')
        ")) {
            throw new Exception('Gagal insert transaction ASSY');
        }

        // UPDATE STOCK PAINT (KURANG)
        if (!mysqli_query($conn, "
            UPDATE part 
            SET qty_paint = qty_paint - $qty 
            WHERE part_code = '$partCode'
        ")) {
            throw new Exception('Gagal update stok PAINT');
        }

        // COMMIT
        mysqli_commit($conn);

        echo "<script>
            alert('Finish production ASSY recorded successfully');
            location.href='index.php';
        </script>";
        exit;
    } catch (Exception $e) {
        // ROLLBACK
        mysqli_rollback($conn);

        echo "<script>
            alert('ERROR: {$e->getMessage()}');
            history.back();
        </script>";
        exit;
    }
}

// Form Selected Month & Year
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

// Production History Data Press
$historyPressQuery = "
    SELECT 
        DATE(date_tr) AS tanggal,
        shift,
        part_code,
        SUM(qty) AS total_qty
    FROM transaction
    WHERE status = 'PRESS'
        AND MONTH(date_tr) = '$selectedMonth'
        AND YEAR(date_tr) = '$selectedYear'
    GROUP BY DATE(date_tr), shift, part_code
    ORDER BY tanggal ASC, shift ASC, part_code ASC
";
$historyPressResult = mysqli_query($conn, $historyPressQuery);

// Production History Data Paint
$historyPaintQuery = "
    SELECT 
        DATE(date_tr) AS tanggal,
        shift,
        part_code,
        SUM(qty) AS total_qty
    FROM transaction
    WHERE status = 'PAINT'
        AND MONTH(date_tr) = '$selectedMonth'
        AND YEAR(date_tr) = '$selectedYear'
    GROUP BY DATE(date_tr), shift, part_code
    ORDER BY tanggal ASC, shift ASC, part_code ASC
";
$historyPaintResult = mysqli_query($conn, $historyPaintQuery);

// Production History Data Assy
$historyAssyQuery = "
    SELECT 
        DATE(date_tr) AS tanggal,
        shift,
        part_code,
        SUM(qty) AS total_qty
    FROM transaction
    WHERE status = 'ASSY'
        AND MONTH(date_tr) = '$selectedMonth'
        AND YEAR(date_tr) = '$selectedYear'
    GROUP BY DATE(date_tr), shift, part_code
    ORDER BY tanggal ASC, shift ASC, part_code ASC
";
$historyAssyResult = mysqli_query($conn, $historyAssyQuery);

// Date Headers
$dates = [];
$startDate = new DateTime("$selectedYear-$selectedMonth-01");
$endDate   = clone $startDate;
$endDate->modify('last day of this month');
while ($startDate <= $endDate) {
    $dates[] = $startDate->format('Y-m-d');
    $startDate->modify('+1 day');
}

// Data Arrays
$dataPress = [];
$dataPaint = [];
$dataAssy = [];

// Process Press Data
while ($row = mysqli_fetch_assoc($historyPressResult)) {
    $dataPress[$row['part_code']][$row['tanggal']][$row['shift']] = (int)$row['total_qty'];
}

// Process Paint Data
while ($row = mysqli_fetch_assoc($historyPaintResult)) {
    $dataPaint[$row['part_code']][$row['tanggal']][$row['shift']] = (int)$row['total_qty'];
}

// Process Assy Data
while ($row = mysqli_fetch_assoc($historyAssyResult)) {
    $dataAssy[$row['part_code']][$row['tanggal']][$row['shift']] = (int)$row['total_qty'];
}

// Get all unique part codes
$allParts = array_unique(array_merge(
    array_keys($dataPress),
    array_keys($dataPaint),
    array_keys($dataAssy)
));
sort($allParts);

$now = date('Y-m-d H:i:s');

$productionDateDisplay = date(
    'd/m/Y',
    strtotime(getProductionDateOnly($now))
);

$productionShiftDisplay = getShift(date('H:i'));
