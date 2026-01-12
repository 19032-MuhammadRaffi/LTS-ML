<?php
require 'function_linking.php';
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

        .scroll-box {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <?php include '../../library/themes.php'; ?>
    <div class="container-fluid">
        <!-- ================= HEADER ================= -->
        <div class="row my-3 align-items-center">
            <div class="col text-start">
                <button class="btn btn-sm btn-outline-success" disabled>Admin</button>
            </div>
            <div class="col text-center">
                <a href="index.php" class="btn btn-sm btn-outline-primary">Dashboard</a>
                <a href="linking.php" class="btn btn-sm btn-primary">Linking</a>
            </div>
            <div class="col text-end">
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    Logout
                </button>
            </div>
        </div>
        <h3 class="text-center mt-5">Linking Master Edit</h3>
        <!-- ================= MAIN CONTENT ================= -->
        <div class="row g-3 mt-3">
            <!-- ================= LINKING CARD ================= -->
            <div class="col-lg-9">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        Linking Model
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Select Model</label>
                            <select class="form-select" id="model_id">
                                <option value="">-- Select Model --</option>
                                <?php
                                $q = mysqli_query($conn, "SELECT model_id, model_code FROM model_master");
                                while ($r = mysqli_fetch_assoc($q)):
                                ?>
                                    <option value="<?= $r['model_id'] ?>">
                                        <?= $r['model_code'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <form method="POST" action="function_linking.php">
                            <input type="hidden" name="model_id" id="hidden_model">
                            <div class="row">
                                <?php
                                $map = [
                                    'defect'   => ['Defect', 'defect_master', 'defect_id', 'defect_name'],
                                    'cause'    => ['Cause', 'cause_master', 'cause_id', 'cause_name'],
                                    'category' => ['Category', 'category_master', 'category_id', 'category_name'],
                                    'action'   => ['Action', 'action_master', 'action_id', 'action_name'],
                                ];
                                foreach ($map as $key => [$title, $table, $id, $name]):
                                ?>
                                    <div class="col-md-3 d-flex">
                                        <div class="card h-100 w-100 mb-3">
                                            <div class="card-header d-flex justify-content-between">
                                                <span><?= $title ?></span>
                                                <span class="badge bg-primary badge-count" data-type="<?= $key ?>">0</span>
                                            </div>
                                            <div class="card-body p-2 scroll-box">
                                                <?php
                                                $q = mysqli_query($conn, "SELECT $id, $name FROM $table");
                                                while ($r = mysqli_fetch_assoc($q)):
                                                ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input link-checkbox"
                                                            type="checkbox"
                                                            data-type="<?= $key ?>"
                                                            name="<?= $key ?>[]"
                                                            value="<?= $r[$id] ?>">
                                                        <label class="form-check-label">
                                                            <?= $r[$name] ?>
                                                        </label>
                                                    </div>
                                                <?php endwhile; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button class="btn btn-primary w-100 mt-3" name="btn_save">
                                Save Linking
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- ================= CLONING CARD ================= -->
            <div class="col-lg-3">
                <div class="card h-100">
                    <div class="card-header bg-warning text-dark">
                        Cloning Linking
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Source Model</label>
                                <select class="form-select" id="clone_source">
                                    <option value="">-- Select Source Model --</option>
                                    <?php
                                    $q = mysqli_query($conn, "SELECT model_id, model_code FROM model_master");
                                    while ($r = mysqli_fetch_assoc($q)):
                                    ?>
                                        <option value="<?= $r['model_id'] ?>">
                                            <?= $r['model_code'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">
                                    Target Model
                                    <span class="badge bg-primary" id="badge_count">0</span>
                                </label>
                                <div class="border rounded p-2 scroll-box " style="max-height: 427px;">
                                    <?php
                                    $q = mysqli_query($conn, "SELECT model_id, model_code FROM model_master");
                                    while ($r = mysqli_fetch_assoc($q)):
                                    ?>
                                        <div class="form-check">
                                            <input class="form-check-input clone-target"
                                                type="checkbox"
                                                value="<?= $r['model_id'] ?>">
                                            <label class="form-check-label">
                                                <?= $r['model_code'] ?>
                                            </label>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-warning w-100" id="btn_clone">
                            Clone Linking
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= SCRIPT ================= -->
    <script src="../../js/bootstrap.bundle.min.js"></script>
    <!-- LOAD DATA & SAVE DATA -->
    <script>
        function updateLinkingBadges() {
            $('.badge-count').each(function() {
                const type = $(this).data('type');
                const count = $(`.link-checkbox[data-type="${type}"]:checked`).length;
                $(this).text(count);
            });
        }

        $(document).on('change', '.link-checkbox', updateLinkingBadges);

        $('#model_id').on('change', function() {
            const modelId = this.value;
            $('#hidden_model').val(modelId);
            $('.link-checkbox').prop('checked', false);
            updateLinkingBadges();

            if (!modelId) return;

            $.getJSON('function_linking.php', {
                model_id: modelId
            }, function(res) {
                Object.keys(res).forEach(type => {
                    res[type].forEach(id => {
                        $(`.link-checkbox[data-type="${type}"][value="${id}"]`).prop('checked', true);
                    });
                });
                updateLinkingBadges();
            });
        });
    </script>
    <!-- CLONING -->
    <script>
        function updateBadge() {
            $('#badge_count').text($('.clone-target:checked').length);
        }

        $('.clone-target').on('change', updateBadge);

        $('#clone_source').on('change', function() {
            const source = this.value;

            $('.clone-target')
                .prop('checked', false)
                .prop('disabled', false);

            updateBadge();

            if (source) {
                $(`.clone-target[value="${source}"]`)
                    .prop('disabled', true);
            }
        });

        $('#btn_clone').on('click', function() {

            const source = $('#clone_source').val();
            const targets = $('.clone-target:checked')
                .map(function() {
                    return this.value;
                }).get();

            if (!source || targets.length === 0) {
                alert('Pilih source dan minimal 1 target');
                return;
            }

            if (!confirm('Yakin clone linking ke model terpilih?')) return;

            $.post('function_linking.php', {
                clone_massal: 1,
                source: source,
                targets: targets
            }, function(res) {
                alert(res);
                $('.clone-target').prop('checked', false);
                updateBadge();
            });
        });
    </script>
</body>

</html>