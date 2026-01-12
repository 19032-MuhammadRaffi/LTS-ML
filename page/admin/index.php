<?php
// ================= CONNECT DB =================
require '../../conn.php';

// ================= SESSION CHECK =================
session_start();
function setAlert($type, $msg)
{
    $_SESSION['alert'] = [
        'type' => $type, // success | danger
        'msg'  => $msg
    ];
}
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header('location: ../../index.php');
    exit;
}

// ================= LOGOUT =================
if (isset($_POST['btn_logout'])) {
    session_destroy();
    header('location: ../../index.php');
    exit;
}

// ================= ADD USER =================
if (isset($_POST['btn_add_user'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $name     = $_POST['name'];
    $area     = $_POST['area'];
    $role     = $_POST['role'];

    if (mysqli_query(
        $conn,
        "INSERT INTO user_master (username,password,name,area,role)
         VALUES ('$username','$password','$name','$area','$role')"
    )) {
        setAlert('success', 'User added successfully');
    } else {
        setAlert('danger', mysqli_error($conn));
    }

    header("Location: index.php");
    exit;
}

// ================= GENERIC ADD MASTER =================
$masterMap = [
    'area'     => ['table' => 'area_master', 'field' => 'area_name'],
    'defect'   => ['table' => 'defect_master', 'field' => 'defect_name'],
    'cause'    => ['table' => 'cause_master', 'field' => 'cause_name'],
    'category' => ['table' => 'category_master', 'field' => 'category_name'],
    'action'   => ['table' => 'action_master', 'field' => 'action_name'],
    'model'    => ['table' => 'model_master', 'field' => 'model_code'],
];

foreach ($masterMap as $key => $cfg) {
    if (isset($_POST["add_$key"])) {

        $val = mysqli_real_escape_string($conn, $_POST[$cfg['field']]);

        if (mysqli_query(
            $conn,
            "INSERT INTO {$cfg['table']} ({$cfg['field']}) VALUES ('$val')"
        )) {
            setAlert('success', ucfirst($key) . ' added successfully');
            $_SESSION['active_master'] = $key; // <<< INI PENTING
        } else {
            setAlert('danger', mysqli_error($conn));
        }

        header("Location: index.php");
        exit;
    }
}

// ================= FETCH DATA =================
function fetchAll($conn, $table)
{
    $data = [];
    $q = mysqli_query($conn, "SELECT * FROM $table ORDER BY 1 DESC");
    while ($r = mysqli_fetch_assoc($q)) $data[] = $r;
    return $data;
}

$users     = fetchAll($conn, 'user_master');
$area      = fetchAll($conn, 'area_master');
$defect    = fetchAll($conn, 'defect_master');
$cause     = fetchAll($conn, 'cause_master');
$category  = fetchAll($conn, 'category_master');
$action    = fetchAll($conn, 'action_master');
$model     = fetchAll($conn, 'model_master');

// ================= EDIT USER =================
if (isset($_POST['btn_update_user'])) {

    $id   = $_POST['user_id'];
    $name = $_POST['name'];
    $area = $_POST['area'];
    $role = $_POST['role'];

    if (mysqli_query(
        $conn,
        "UPDATE user_master SET name='$name', area='$area', role='$role'
         WHERE user_id='$id'"
    )) {
        setAlert('success', 'User updated successfully');
    } else {
        setAlert('danger', mysqli_error($conn));
    }

    header("Location: index.php");
    exit;
}

// ================= DELETE USER =================
if (isset($_POST['btn_delete_user'])) {

    $id = $_POST['user_id'];

    if (mysqli_query(
        $conn,
        "DELETE FROM user_master WHERE user_id='$id'"
    )) {
        setAlert('success', 'User deleted successfully');
    } else {
        setAlert('danger', mysqli_error($conn));
    }

    header("Location: index.php");
    exit;
}

// ================= UPDATE MASTER =================
if (isset($_POST['btn_update_master'])) {

    $id    = $_POST['master_id'];
    $table = $_POST['master_type'];
    $value = mysqli_real_escape_string($conn, $_POST['master_name']);

    $map = [
        'area_master'     => ['pk' => 'area_id', 'field' => 'area_name'],
        'defect_master'   => ['pk' => 'defect_id', 'field' => 'defect_name'],
        'cause_master'    => ['pk' => 'cause_id', 'field' => 'cause_name'],
        'category_master' => ['pk' => 'category_id', 'field' => 'category_name'],
        'action_master'   => ['pk' => 'action_id', 'field' => 'action_name'],
        'model_master'    => ['pk' => 'model_code', 'field' => 'model_code'],
    ];

    if (!isset($map[$table])) {
        setAlert('danger', 'Invalid master table');
        header("Location: index.php");
        exit;
    }

    $pk    = $map[$table]['pk'];
    $field = $map[$table]['field'];

    if (mysqli_query(
        $conn,
        "UPDATE $table SET $field='$value' WHERE $pk='$id'"
    )) {
        setAlert('success', 'Data updated successfully');
    } else {
        setAlert('danger', mysqli_error($conn));
    }

    header("Location: index.php");
    exit;
}

// ================= DELETE MASTER =================
if (isset($_POST['btn_delete_master'])) {

    $id    = $_POST['master_id'];
    $table = $_POST['master_type'];

    $pkMap = [
        'area_master'     => 'area_id',
        'defect_master'   => 'defect_id',
        'cause_master'    => 'cause_id',
        'category_master' => 'category_id',
        'action_master'   => 'action_id',
        'model_master'    => 'model_code',
    ];

    if (!isset($pkMap[$table])) {
        setAlert('danger', 'Invalid master table');
        header("Location: index.php");
        exit;
    }

    if (mysqli_query(
        $conn,
        "DELETE FROM $table WHERE {$pkMap[$table]}='$id'"
    )) {
        setAlert('success', 'Data deleted successfully');
    } else {
        setAlert('danger', mysqli_error($conn));
    }

    header("Location: index.php");
    exit;
}
?>
<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>TCS-Production</title>
    <script src="../../js/color-modes.js"></script>
    <script src="../../js/jquery-3.7.1.js"></script>
    <script src="../../js/jquery-ui.js"></script>
    <link rel="stylesheet" href="../../css/jquery-ui.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css" rel="stylesheet">
    <style>
        .btn-sm {
            width: 100px;
        }

        .master-card {
            display: none;
        }

        .btn-master {
            width: 100px;
        }
    </style>

</head>

<body>
    <!-- Themes Mode -->
    <?php include '../../library/themes.php'; ?>
    <div class="container-fluid text-center">
        <!-- ROW 1 -->
        <div class="row my-3">
            <div class="col text-start">
                <button class="btn btn-sm btn-outline-success" disabled>Admin</button>
            </div>
            <div class="col text-center">
                <a href="index.php" class="btn btn-sm btn-primary">Dashboard</a>
                <a href="linking.php" class="btn btn-sm btn-outline-primary">Linking</a>
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
        <!-- ROW 2 -->
        <h3 class="mt-5">Data Master</h3>
        <?php if (isset($_SESSION['alert'])): ?>
            <div class="alert alert-<?= $_SESSION['alert']['type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['alert']['msg'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>
        <div class="mb-3">
            <button class="btn btn-sm btn-outline-primary btn-master" onclick="showMaster(this,'user')">User</button>
            <button class="btn btn-sm btn-outline-primary btn-master" onclick="showMaster(this,'area')">Area</button>
            <button class="btn btn-sm btn-outline-primary btn-master" onclick="showMaster(this,'defect')">Defect</button>
            <button class="btn btn-sm btn-outline-primary btn-master" onclick="showMaster(this,'cause')">Cause</button>
            <button class="btn btn-sm btn-outline-primary btn-master" onclick="showMaster(this,'category')">Category</button>
            <button class="btn btn-sm btn-outline-primary btn-master" onclick="showMaster(this,'action')">Action</button>
            <button class="btn btn-sm btn-outline-primary btn-master" onclick="showMaster(this,'model')">Model</button>
        </div>
        <!-- ROW 3 -->
        <div class="row">
            <!-- ================= USER MASTER ================= -->
            <div class="col-lg-6 mx-auto master-card" id="card-user">
                <div class="card">
                    <div class="card-header">
                        <b>User Master</b>
                        <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addUser">Add</button>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Area</th>
                                    <th>Role</th>
                                    <th style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($u['username']) ?></td>
                                        <td><?= htmlspecialchars($u['name']) ?></td>
                                        <td><?= htmlspecialchars($u['area']) ?></td>
                                        <td><?= htmlspecialchars($u['role']) ?></td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-warning btn-manage-user"
                                                data-id="<?= $u['user_id'] ?>"
                                                data-username="<?= htmlspecialchars($u['username']) ?>"
                                                data-name="<?= htmlspecialchars($u['name']) ?>"
                                                data-area="<?= htmlspecialchars($u['area']) ?>"
                                                data-role="<?= htmlspecialchars($u['role']) ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#manageUserModal">
                                                Manage
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- ADD USER MODAL -->
                <div class="modal fade" id="addUser">
                    <div class="modal-dialog modal-dialog-centered">
                        <form method="POST" class="modal-content">
                            <div class="modal-header">
                                <h5>Add User</h5>
                            </div>
                            <div class="modal-body">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingUsername" name="username">
                                    <label for="floatingUsername">Username</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingPassword" name="password">
                                    <label for="floatingPassword">Password</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingName" name="name">
                                    <label for="floatingName">Name</label>
                                </div>
                                <div class="form-floating mt-3">
                                    <select class="form-select" id="floatingSelect" name="area">
                                        <option value="IDU">IDU</option>
                                        <option value="ODU">ODU</option>
                                    </select>
                                    <label for="floatingSelect">Area</label>
                                </div>
                                <div class="form-floating mt-3">
                                    <select class="form-select" id="floatingSelect" name="role">
                                        <option value="Operator">Operator</option>
                                        <option value="Repair">Repair</option>
                                        <option value="Leader">Leader</option>
                                    </select>
                                    <label for="floatingSelect">Role</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" name="btn_add_user">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- EDIT USER MODAL -->
                <div class="modal fade" id="manageUserModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <form method="POST" class="modal-content">
                            <input type="hidden" name="user_id" id="editUserId">

                            <div class="modal-header">
                                <h5 class="modal-title">Manage User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="username" id="editUsername" readonly>
                                    <label>Username</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="name" id="editName">
                                    <label>Name</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <select class="form-select" name="area" id="editArea">
                                        <option value="IDU">IDU</option>
                                        <option value="ODU">ODU</option>
                                        <option value="ALL">ALL</option>
                                    </select>
                                    <label>Area</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <select class="form-select" name="role" id="editRole">
                                        <option value="Operator">Operator</option>
                                        <option value="Repair">Repair</option>
                                        <option value="Leader">Leader</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                    <label>Role</label>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" name="btn_delete_user" class="btn btn-danger me-auto"
                                    onclick="return confirm('Delete this user?')">
                                    Delete
                                </button>
                                <button type="submit" name="btn_update_user" class="btn btn-primary">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ================= MASTER GENERIC ================= -->
            <?php
            $masters = [
                [
                    'id'    => 'area',
                    'data'  => $area,
                    'title' => 'Area',
                    'field' => 'area_name',
                    'pk'    => 'area_id',
                    'table' => 'area_master',
                    'btn'   => 'add_area'
                ],
                [
                    'id'    => 'defect',
                    'data'  => $defect,
                    'title' => 'Defect',
                    'field' => 'defect_name',
                    'pk'    => 'defect_id',
                    'table' => 'defect_master',
                    'btn'   => 'add_defect'
                ],
                [
                    'id'    => 'cause',
                    'data'  => $cause,
                    'title' => 'Cause',
                    'field' => 'cause_name',
                    'pk'    => 'cause_id',
                    'table' => 'cause_master',
                    'btn'   => 'add_cause'
                ],
                [
                    'id'    => 'category',
                    'data'  => $category,
                    'title' => 'Category',
                    'field' => 'category_name',
                    'pk'    => 'category_id',
                    'table' => 'category_master',
                    'btn'   => 'add_category'
                ],
                [
                    'id'    => 'action',
                    'data'  => $action,
                    'title' => 'Action',
                    'field' => 'action_name',
                    'pk'    => 'action_id',
                    'table' => 'action_master',
                    'btn'   => 'add_action'
                ],
                [
                    'id'    => 'model',
                    'data'  => $model,
                    'title' => 'Model',
                    'field' => 'model_code',
                    'pk'    => 'model_code',
                    'table' => 'model_master',
                    'btn'   => 'add_model'
                ],
            ];

            // ================= Render master cards =================
            foreach ($masters as $m): ?>
                <div class="col-lg-6 master-card mx-auto" id="card-<?= $m['id'] ?>">
                    <?php
                    $data  = $m['data'];
                    $title = $m['title'];
                    $field = $m['field'];
                    $pk    = $m['pk'];
                    $table = $m['table'];
                    $btn   = $m['btn'];
                    ?>
                    <?php include 'partial/master_card.php'; ?>
                </div>
            <?php endforeach ?>

            <!-- ================= Modal Master ================= -->
            <div class="modal fade" id="manageMasterModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" class="modal-content">
                        <input type="hidden" name="master_id" id="editMasterId">
                        <input type="hidden" name="master_type" id="editMasterType">

                        <div class="modal-header">
                            <h5 class="modal-title">Manage Master</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="editMasterName" name="master_name">
                                <label>Master Name</label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" name="btn_delete_master"
                                class="btn btn-danger me-auto"
                                onclick="return confirm('Delete this data?')">
                                Delete
                            </button>
                            <button type="submit" name="btn_update_master" class="btn btn-primary">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Javascript -->
        <script src="../../js/bootstrap.bundle.min.js"></script>
        <!-- Button Menu -->
        <script>
            // Tampilkan master sesuai tombol diklik
            function showMaster(btn, type) {
                // simpan master aktif
                localStorage.setItem('activeMaster', type);

                document.querySelectorAll('.master-card').forEach(c => c.style.display = 'none');
                document.querySelectorAll('.btn-master').forEach(b => {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-primary');
                });

                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');

                document.getElementById('card-' + type).style.display = 'block';
            }
            document.addEventListener('DOMContentLoaded', function() {
                const active = localStorage.getItem('activeMaster');

                if (active) {
                    const btn = document.querySelector(
                        `.btn-master[onclick*="'${active}'"]`
                    );
                    if (btn) btn.click();
                }
            });
            // Cegah submit form saat tekan enter
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    const tag = e.target.tagName.toLowerCase();

                    // Cegah enter di input (kecuali textarea)
                    if (tag === 'input') {
                        e.preventDefault();
                        return false;
                    }
                }
            });
            // Isi data di modal manage user
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-manage-user')) {
                    document.getElementById('editUserId').value = e.target.dataset.id;
                    document.getElementById('editUsername').value = e.target.dataset.username;
                    document.getElementById('editName').value = e.target.dataset.name;
                    document.getElementById('editArea').value = e.target.dataset.area;
                    document.getElementById('editRole').value = e.target.dataset.role;
                }
            });
            // Isi data di modal manage master
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-manage-master')) {

                    document.getElementById('editMasterId').value =
                        e.target.dataset.id;

                    document.getElementById('editMasterName').value =
                        e.target.dataset.name;

                    document.getElementById('editMasterType').value =
                        e.target.dataset.table;
                }
            });
        </script>
        <?php if (isset($_SESSION['active_master'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    showMaster(
                        document.querySelector(
                            "button[onclick=\"showMaster(this,'<?= $_SESSION['active_master'] ?>')\"]"
                        ),
                        "<?= $_SESSION['active_master'] ?>"
                    );
                });
            </script>
            <?php unset($_SESSION['active_master']); ?>
        <?php endif; ?>
</body>

</html>