<div class="modal fade" id="unitUpdateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Update Unit</h5>
                <button class="close" type="button" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>

            <form action="<?php echo base_url("Libraries/updateUnit"); ?>" 
                  method="post" 
                  id="formUnitUpdate">

                <div class="modal-body">

                    <!-- Hidden Unit ID -->
                    <input type="hidden" name="unit_id" id="unitID">

                    <div class="form-group">
                        <label for="unitCode_update">
                            Unit Code <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="unit_code" id="unitCode_update" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        Cancel
                    </button>
                    <button class="btn btn-primary" type="submit">
                        Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>