<div class="modal fade" id="prAddItem" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Item</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("PurchaseRequest/savePRitem"); ?>" method="post" class="form-horizontal" id="formPRAddItem">
                <input type="hidden" name="pr_id" id="pr_id">

                <div class="modal-body" style="max-height:70vh; overflow-y:auto;">
<<<<<<< HEAD
                    <input type="hidden" name="pr_id" value="">

                    <div class="row">
                        <div class="mb-3 col-lg-5">
                            <label for="prStock">Item <span class="text-danger">*</span></label>
                            <select name="prStock" id="prStock" class="form-control select2" required></select>
                        </div>

                        <div class="mb-3 col-lg-2">
                            <label for="prQuantity">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="prQuantity" id="prQuantity" min="1" step="1" placeholder="0" required>
                        </div>
                        <div class="mb-3 col-lg-2">
                            <label for="prUnitCost">Unit Cost <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="prUnitCost" id="prUnitCost" min="1" step="0.01" placeholder="0.00" required>
                        </div>
                        <div class="mb-3 col-lg-2">
                            <label for="prTotalCost">Total Cost <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="prTotalCost" id="prTotalCost" placeholder="0.00" readonly>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-lg-1">
                                <button class="btn btn-danger">X</button>
=======
                    <div id="itemRowsContainer">
                        <!-- First item row -->
                        <div class="row item-row mb-2">
                            <input type="hidden" name="prItemId[]" class="prItemId" value="">
                            <div class="mb-3 col-lg-5">
                                <label>Item <span class="text-danger">*</span></label>
                                <select name="prStock[]" class="form-control select2 prStock" required></select>
                            </div>
                            <div class="mb-3 col-lg-2">
                                <label>Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="prQuantity[]" class="form-control prQuantity" min="1" step="1" placeholder="0" required>
                            </div>
                            <div class="mb-3 col-lg-2">
                                <label>Unit Cost <span class="text-danger">*</span></label>
                                <input type="number" name="prUnitCost[]" class="form-control prUnitCost" min="1" step="0.01" placeholder="0.00" required>
                            </div>
                            <div class="mb-3 col-lg-2">
                                <label>Total Cost</label>
                                <input type="number" name="prTotalCost[]" class="form-control prTotalCost" placeholder="0.00" readonly>
                            </div>
                            <div class="mb-3 col-lg-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btnRemoveRow">X</button>
>>>>>>> 1271112f1ee0d0f9611ccfa5590b425ec2a7d3d4
                            </div>
                        </div>
                    </div>

<<<<<<< HEAD
                    <!-- <div class="row">
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
                    </div> -->

=======
                    <div class="text-right">
                        <button type="button" class="btn btn-success" id="btnAddRow">+ Add Row</button>
                    </div>
>>>>>>> 1271112f1ee0d0f9611ccfa5590b425ec2a7d3d4
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
<<<<<<< HEAD
=======
                    <button class="btn btn-primary" type="submit">Save</button>
>>>>>>> 1271112f1ee0d0f9611ccfa5590b425ec2a7d3d4
                </div>
            </form>
        </div>
    </div>
<<<<<<< HEAD
</div>
=======
</div>
>>>>>>> 1271112f1ee0d0f9611ccfa5590b425ec2a7d3d4
