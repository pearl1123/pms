<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <form method="post" action="<?= base_url('UserManagement/update') ?>">
      <input type="hidden" name="id" value="<?= $user->id ?>">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit User</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="fullname" class="form-control" value="<?= $user->fullname ?>" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= $user->email ?>" required>
          </div>
          <div class="form-group">
            <label>Group</label>
            <select name="group_id" class="form-control" required>
              <?php foreach ($groups as $group): ?>
                <option value="<?= $group->id ?>" <?= $group->id == $user->group_id ? 'selected' : '' ?>>
                  <?= $group->name ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" name="banned" value="1" <?= $user->banned ? 'checked' : '' ?>> Inactive
            </label>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>
