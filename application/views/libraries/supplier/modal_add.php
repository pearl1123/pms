<div class="modal fade" id="supplierAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Supplier</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("Libraries/saveSupplier"); ?>" method="post" class="form-horizontal" id="formSupplierUpdate">
                <div class="modal-body">
                    <input type="hidden" name="supplier_id" id="supplierID">

                    <div class="mb-3">
                        <label for="addSupplierName">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="supplier_name" id="addSupplierName" required>
                    </div>

                    <div class="mb-3">
                        <label for="addSupplierAddress">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="supplier_address" id="addSupplierAddress" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="addSupplierEmail">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="supplier_email" id="addSupplierEmail" required>
                    </div>

                    <div class="mb-3">
                        <label for="addSupplierContact">Contact Number<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" name="supplier_contact" id="addSupplierContact" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="addSupplierContactPerson">Contact Person<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" name="supplier_contact_person" id="addSupplierContactPerson" rows="2" required></textarea>
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