<div class="modal fade" id="prUpdateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Purchase Request</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("PurchaseRequest/updatePR"); ?>" method="post" class="form-horizontal" id="formPRAdd">
                <div class="modal-body">
                    <input type="hidden" name="prId" id="prId" value="">
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label for="prNumber">PR Number. <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="prNumber" id="prNumber" inputmode="numeric" pattern="[0-9]*" readonly>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="saiNumber">SAI Number</label>
                            <input type="text" class="form-control" name="saiNumber" id="saiNumber" inputmode="numeric" pattern="[0-9]*" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="proc_id">Procurement Mode <span class="text-danger">*</span></label>
                        <select name="proc_id" id="proc_id" class="form-control select2" required>
                            <option></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="prOffice">Office <span class="text-danger">*</span></label>
                        <select name="prOffice" id="prOffice" class="form-control select2" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="prRemarks">Remarks</label>
                        <textarea name="prRemarks" id="prRemarks" class="form-control" rows="3" style="resize: none;"></textarea>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label for="prRequestedBy">Requested By <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="prRequestedBy" id="prRequestedBy" required>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="prDesignation">Designation <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="prDesignation" id="prDesignation" required>
                        </div>
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


<script>
    $(document).ready(function() {
        const $prUpdateModal = $("#prUpdateModal");

        $prUpdateModal.on('shown.bs.modal', function() {
            $(this).find('#prNumber').trigger('focus');
        });

        document.querySelectorAll('#prNumber, #saiNumber').forEach(function(el) {
            el.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    })
</script>