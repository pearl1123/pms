<div class="modal fade" id="supplierUpdateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Supplier</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("Libraries/updateSupplier");?>" method="post" class="form-horizontal" id="formSupplierUpdate">
                <div class="modal-body">
                    <input type="hidden" name="supplier_id" id="supplierID">

                    <div class="mb-3">
                        <label for="supplierName_update">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="supplier_name" id="supplierName_update" required>
                    </div>

                    <div class="mb-3">
                        <label for="supplierAddress_update">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="supplier_address" id="supplierAddress_update" rows="2" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="supplierEmail_update">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="supplier_email" id="supplierEmail_update" required>
                    </div>

                    <div class="mb-3">
                        <label for="supplierContact_update">Contact Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="supplier_contact" id="supplierContact_update" required>
                    </div>

                    <div class="mb-3">
                        <label for="supplierContactPerson_update">Contact Person <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="supplier_contact_person" id="supplierContactPerson_update" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>