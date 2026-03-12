<div class="modal fade" id="modalUpdateGroup" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Group</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            
            <form action="<?php echo base_url("Libraries/updateGroup"); ?>" method="post" class="form-horizontal" id="frmGroupUpdate">
                <div class="modal-body">
                    <?php echo $csrf; ?>
                    <input type="hidden" name="csrf_data" id='csrf_data_u' value="<?php echo $csrf_ajax['hash'] ?>">
                    <input type="hidden" name="id" id='id'>

                    <div class="mb-3">
                        <label for="name_u">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name_u" id="name_u" placeholder="Name">
                    </div>

                    <div class="mb-3">
                        <label for="definition_u">Definition <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="definition_u" id="definition_u" placeholder="Definition">
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