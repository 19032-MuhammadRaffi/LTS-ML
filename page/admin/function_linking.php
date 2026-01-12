<?php
require '../../conn.php';

// ================= AUTHENTICATION =================
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] != 'Admin') {
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: ../../index.php');
    exit();
}

// ================= FUNCTION =================
if (isset($_GET['model_id'])) {

    $model_id = $_GET['model_id'];

    $map = [
        'defect'   => ['model_defect', 'defect_id'],
        'cause'    => ['model_cause', 'cause_id'],
        'category' => ['model_category', 'category_id'],
        'action'   => ['model_action', 'action_id'],
    ];

    $result = [];

    foreach ($map as $key => [$table, $field]) {
        $result[$key] = [];
        $q = mysqli_query($conn, "SELECT $field FROM $table WHERE model_id='$model_id'");
        while ($r = mysqli_fetch_assoc($q)) {
            $result[$key][] = (string)$r[$field];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

/* ================= SAVE ================= */
if (isset($_POST['btn_save'])) {

    $model_id = $_POST['model_id'];

    function saveLink($conn, $table, $model_id, $field, $data)
    {
        mysqli_query($conn, "DELETE FROM $table WHERE model_id='$model_id'");
        foreach ($data as $id) {
            mysqli_query($conn, "
                INSERT INTO $table (model_id, $field)
                VALUES ('$model_id', '$id')
            ");
        }
    }

    saveLink($conn, 'model_defect', $model_id, 'defect_id', $_POST['defect'] ?? []);
    saveLink($conn, 'model_cause', $model_id, 'cause_id', $_POST['cause'] ?? []);
    saveLink($conn, 'model_category', $model_id, 'category_id', $_POST['category'] ?? []);
    saveLink($conn, 'model_action', $model_id, 'action_id', $_POST['action'] ?? []);

    header('Location: linking.php');
    exit;
}
/* ================= CLONE MASSAL ================= */
if (isset($_POST['clone_massal'])) {

    $source = $_POST['source'];
    $targets = $_POST['targets'];

    $map = [
        'model_defect'   => 'defect_id',
        'model_cause'    => 'cause_id',
        'model_category' => 'category_id',
        'model_action'   => 'action_id',
    ];

    foreach ($targets as $target) {

        if ($target == $source) continue;

        foreach ($map as $table => $field) {

            // hapus data target
            mysqli_query($conn, "
                DELETE FROM $table WHERE model_id='$target'
            ");

            // copy dari source
            $q = mysqli_query($conn, "
                SELECT $field FROM $table WHERE model_id='$source'
            ");

            while ($r = mysqli_fetch_assoc($q)) {
                mysqli_query($conn, "
                    INSERT INTO $table (model_id, $field)
                    VALUES ('$target', '{$r[$field]}')
                ");
            }
        }
    }

    echo "Cloning berhasil";
    exit;
}
