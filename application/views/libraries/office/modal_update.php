<div class="modal fade" id="officeUpdateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Office</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("Libraries/updateOffice");?>" method="post" class="form-horizontal" id="formOfficeUpdate">
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="office_id" id="officeID">

                    <div class="mb-3">
                        <label for="officeDescription_update">Office Description <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="office_desc" id="officeDescription_update">
                    </div>
                    <div class="mb-3">
                        <label for="itemAbbreviation_update">Office Abbreviation </label>
                        <input type="text" class="form-control" name="office_abbr" id="officeAbbreviation_update">
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