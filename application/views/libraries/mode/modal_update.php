<div class="modal fade" id="modeUpdateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Procurement Mode</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("Libraries/updateMode");?>" method="post" class="form-horizontal" id="formOfficeUpdate">
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="proc_id" id="modeID">

                    <div class="mb-3">
                        <label for="modeCode_update">Procurement Mode Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="proc_code" id="modeCode_update">
                    </div>
                    <div class="mb-3">
                        <label for="modeName_update">Procurement Mode Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="proc_name" id="modeName_update">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>