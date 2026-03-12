<div class="modal fade" id="groupAddModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Group</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            
            <form action="<?php echo base_url("Libraries/saveGroup"); ?>" method="post" class="form-horizontal" id="frmGroupAdd">
                <div class="modal-body">
                    <?php echo $csrf; ?>
                    <input type="hidden" name="csrf_data" id='csrf_data' value="<?php echo $csrf_ajax['hash'] ?>">
                    
                    <div class="mb-3">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter group name">
                    </div>

                    <div class="mb-3">
                        <label for="definition">Definition <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="definition" id="definition" placeholder="Enter definition">
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