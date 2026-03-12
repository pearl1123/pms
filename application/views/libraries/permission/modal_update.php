<div class="modal fade" id="permUpdateModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Permission</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("Libraries/updatePermission"); ?>" method="post" id="frmPermUpdate">
                <div class="modal-body">
                    <?php echo $csrf; ?>
                    <input type="hidden" name="id" id="permID_u">
                    <div class="mb-3">
                        <label for="permName_u">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name_u" id="permName_u" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Update Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>