<div class="modal fade" id="prAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Purchase Request</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("PurchaseRequest/savePR");?>" method="post" class="form-horizontal" id="formPRAdd">
                <div class="modal-body">
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label for="prNumber">PR Number. <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="prNumber" id="prNumber" inputmode="numeric" pattern="[0-9]*" required>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="saiNumber">SAI Number</label>
                            <input type="text" class="form-control" name="saiNumber" id="saiNumber" inputmode="numeric" pattern="[0-9]*">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="prOffice">Office <span class="text-danger">*</span></label>
                        <select name="prOffice" id="prOffice" class="form-control select2" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="prStock">Stock <span class="text-danger">*</span></label>
                        <select name="prStock" id="prStock" class="form-control select2" required></select>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-lg-4">
                            <label for="prQuantity">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="prQuantity" id="prQuantity" min="1" step="1" placeholder="0" required>
                        </div>
                        <div class="mb-3 col-lg-4">
                            <label for="prUnitCost">Unit Cost <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="prUnitCost" id="prUnitCost" min="1" step="0.01" placeholder="0.00" required>
                        </div>
                        <div class="mb-3 col-lg-4">
                            <label for="prTotalCost">Total Cost <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="prTotalCost" id="prTotalCost" placeholder="0.00" readonly>
                        </div>
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
    $(document).ready(function () {
        const $prAddModal = $("#prAddModal");

        $prAddModal.on('shown.bs.modal', function () {
            $(this).find('#prNumber').trigger('focus');
        });

        $prAddModal.off("input", "#prQuantity, #prUnitCost").on("input", "#prQuantity, #prUnitCost", function () {
            const prQty   = $prAddModal.find("#prQuantity").val() || 0;
            const prCost  = $prAddModal.find("#prUnitCost").val() || 0;
            const prTotal = $prAddModal.find("#prTotalCost");
            const total = prQty * prCost;

            prTotal.val(total.toFixed(2));
        })

        document.querySelectorAll('#prNumber, #saiNumber').forEach(function (el) {
            el.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    });
</script>

<!-- <style>
    /* Main Select2 box */
    .select2-container--bootstrap-4 .select2-selection {
        height: 38px; /* match your input height */
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5; 
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    /* Text inside main box */
    .select2-container--bootstrap-4 .select2-selection__rendered {
        line-height: 28px; /* vertically center text */
    }

    /* Arrow on the right */
    .select2-container--bootstrap-4 .select2-selection__arrow {
        height: 36px;
        right: 10px;
    }

    /* The dropdown search box */
    .select2-container--bootstrap-4 .select2-search--dropdown .select2-search__field {
        height: 38px; /* match your inputs */
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ced4da;
        /* border-radius: 0.375rem; */
        width: 100%; /* make sure it fills dropdown */
        box-sizing: border-box;
    }
    /* Dropdown search box focus */
    .select2-container--bootstrap-4 .select2-search--dropdown .select2-search__field:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        outline: 0;
    }
    /* Dropdown options default */
    .select2-container--bootstrap-4 .select2-results__option {
        padding: 0.375rem 0.75rem; /* match Bootstrap input padding */
        font-size: 1rem;
        line-height: 1.5;
        cursor: pointer;
    }

    /* Hover effect */
    .select2-container--bootstrap-4 .select2-results__option--highlighted {
        background-color: #f8f9fa; /* light gray like Bootstrap hover */
        color: #212529; /* default text color */
    }

    /* Selected option */
    .select2-container--bootstrap-4 .select2-results__option[aria-selected="true"] {
        background-color: #e9ecef; /* slightly darker gray */
        color: #212529;
    }

    /* Optional: smooth transition */
    .select2-container--bootstrap-4 .select2-results__option {
        transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
    }
</style> -->