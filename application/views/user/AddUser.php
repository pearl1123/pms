<div class="modal fade" id="userAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add User</h5>
                <button class="close" type="button" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>

            <form action="<?php echo base_url("UserManagement/saveUser"); ?>"
                method="post"
                id="formUserAdd">

                <div class="modal-body">

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone_number" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Office</label>
                        <select name="office" class="form-control" required>
                            <option value="">-- Select Office --</option>

                            <?php if (!empty($offices)): ?>
                                <?php foreach ($offices as $office): ?>
                                    <option value="<?= $office->office_id; ?>">
                                        <?= $office->office_desc; ?> (<?= $office->office_abbr; ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_pass" class="form-control" required>
                    </div>

                    <select name="group" class="form-control" required>
                        <option value="">-- Select Group --</option>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?= $group->id; ?>">
                                <?= $group->name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="banned" value="1"> Inactive
                        </label>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        Cancel
                    </button>
                    <button class="btn btn-primary" type="submit">
                        Save
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>