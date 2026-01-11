<div class="card">
    <div class="card-header">
        <b><?= $title ?> Master</b>
        <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal"
            data-bs-target="#add<?= $title ?>">
            Add
        </button>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="90">No</th>
                    <th><?= $title ?> Name</th>
                    <th width="90">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($data as $r): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($r[$field]) ?></td>
                        <td class="text-end">
                            <button
                                type="button"
                                class="btn btn-sm btn-warning btn-manage-master"
                                data-id="<?= $r[$pk] ?>"
                                data-name="<?= htmlspecialchars($r[$field]) ?>"
                                data-table="<?= $table ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#manageMasterModal">
                                Manage
                            </button>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="add<?= $title ?>">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5>Add <?= $title ?></h5>
            </div>
            <div class="modal-body">
                <input name="<?= $field ?>" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" name="<?= $btn ?>">Save</button>
            </div>
        </form>
    </div>
</div>