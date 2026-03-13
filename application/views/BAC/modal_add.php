<div class="modal fade" id="bacAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Bids and Awards</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("BidsAndAwards/saveBAC"); ?>" method="post" class="form-horizontal" id="formBACAdd">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                
                <div class="modal-body" style="max-height:70vh; overflow-y:auto;">
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label for="bacNumber">BAC Number. <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="bacNumber" id="bacNumber" inputmode="numeric" pattern="[0-9]*" required>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="saiNumber">SAI Number</label>
                            <input type="text" class="form-control" name="saiNumber" id="saiNumber" inputmode="numeric" pattern="[0-9]*">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="proc_id">Procurement Mode <span class="text-danger">*</span></label>
                        <select name="proc_id" id="proc_id" class="form-control select2" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="bacOffice">Office <span class="text-danger">*</span></label>
                        <select name="bacOffice" id="bacOffice" class="form-control select2" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="bacRemarks">Remarks</label>
                        <textarea name="bacRemarks" id="bacRemarks" class="form-control" rows="3" style="resize: none;"></textarea>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label for="bacRequestedBy">Requested By <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="bacRequestedBy" id="bacRequestedBy" required>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="bacDesignation">Designation <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="bacDesignation" id="bacDesignation" required>
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
        var $bacAddModal = $('#bacAddModal');

        $bacAddModal.on('shown.bs.modal', function() {
            $(this).find('#bacNumber').trigger('focus');
        });

        $bacAddModal.off("input", "#bacQuantity, #bacUnitCost").on("input", "#bacQuantity, #bacUnitCost", function() {
            const bacQty = $bacAddModal.find("#bacQuantity").val() || 0;
            const bacCost = $bacAddModal.find("#bacUnitCost").val() || 0;
            const bacTotal = $bacAddModal.find("#bacTotalCost");
            const total = bacQty * bacCost;

            bacTotal.val(total.toFixed(2));
        })

        document.querySelectorAll('#bacNumber, #saiNumber').forEach(function(el) {
            el.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    });
</script>