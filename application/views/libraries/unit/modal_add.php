<div class="modal fade" id="unitAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add Unit</h5>
                <button class="close" type="button" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>

            <form action="<?php echo base_url("Libraries/saveUnit"); ?>"
                method="post"
                id="formUnitAdd">

                <div class="modal-body">

                    <div class="form-group">
                        <label for="unitCode">
                            Unit Code <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="unit_code" id="unitCode" required>
                    </div>

                    <div class="form-group">
                        <label for="unitDescription">
                            Unit Description
                        </label>
                        <input type="text" class="form-control" name="unit_description" id="unitDescription">
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