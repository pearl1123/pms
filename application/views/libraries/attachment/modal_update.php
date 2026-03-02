<div class="modal fade" id="attachmentUpdateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Attachment</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("Libraries/updateAttachment");?>" method="post" class="form-horizontal" id="formAttachmentUpdate">
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="attachment_id" id="attachmentID">

                    <div class="mb-3">
                        <label for="attachmentName_update">Attachment Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="attachment_name" id="attachmentName_update">
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