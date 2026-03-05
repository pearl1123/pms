<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Supplier</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btnAdd">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Supplier</button>
    </div>

    <hr />

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Libraries</li>
            <li class="breadcrumb-item active" aria-current="page">Supplier</li>
        </ol>
    </nav>

    <?php if ($this->session->flashdata('fail') <> null) { ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
            <span><?php echo $this->session->flashdata('fail'); ?></span>
        </div>
    <?php } ?>
    <?php if ($this->session->flashdata('success') <> null) { ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
            <span><?php echo $this->session->flashdata('success'); ?></span>
        </div>
    <?php } ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Supplier Table</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="lib_supplier" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Supplier Name</th>
                            <th>Email Address</th>
                            <th>Address</th>
                            <th>Contact No.</th>
                            <th>Contact Person</th>
                            <th>Encoded By</th>
                            <th>Modified By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No.</th>
                            <th>Supplier Name</th>
                            <th>Email Address</th>
                            <th>Address</th>
                            <th>Contact No.</th>
                            <th>Contact Person</th>
                            <th>Encoded By</th>
                            <th>Modified By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody id="supplierBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- CLOSING TAGS
========================================================================================================================================= -->
</div>
</div>
</div>
</body>

</html>


<!-- SCRIPTS
========================================================================================================================================= -->

<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/datatables/dataTables.bootstrap4.min.js'); ?>"></script>

<script>
function datatable() {
    var tblSupplier = $('#lib_supplier').DataTable({
        "responsive": true,
        "autoWidth": false,
        'serverSide': true,
        'processing': true,
        'paging': true,
        "lengthMenu": [10, 15, 20],
        "ajax": {
            url: "<?php echo base_url('Libraries/getSupplierList/'); ?>",
            type: 'GET'
        },
        "language": {
            "emptyTable": "No Results"
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'supplier_name', name: 'supplier_name' },
            { data: 'supplier_email', name: 'supplier_email' },
            { data: 'supplier_address', name: 'supplier_address' },
            { data: 'supplier_contact', name: 'supplier_contact' },
            { data: 'supplier_contact_person', name: 'supplier_contact_person' },
            { data: 'encoded_by', name: 'encoded_by' },
            { data: 'modified_by', name: 'modified_by' },
            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions' },
        ],
        'columnDefs': [
            { 'targets': [0], 'visible': false, 'orderable': false },
            { 'targets': [9], 'orderable': false } // actions column
        ],
        order: [1, 'asc']
    });

    // UPDATE BUTTON
    $('#supplierBody').on('click', '.btnUpdate', function() {
        var rowData = tblSupplier.row($(this).closest('tr')).data();

        $('#supplierID').val(rowData.supplier_id);
        $('#supplierName_update').val(rowData.supplier_name);
        $('#supplierAddress_update').val(rowData.supplier_address);
        $('#supplierEmail_update').val(rowData.supplier_email);
        $('#supplierContact_update').val(rowData.supplier_contact);
        $('#supplierContactPerson_update').val(rowData.supplier_contact_person);

        $('#supplierUpdateModal').modal('show');
    });

        // DELETE BUTTON
        $('#supplierBody').on('click', '.btnDel', function() {
            var id = $(this).data('id'); // ← get ID directly from button

            Swal.fire({
                title: 'Delete?',
                html: "<span class='text-danger'><b>WARNING!</b></span> You will not be able to undo this action.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "<?php echo base_url('Libraries/deleteSupplier'); ?>",
                        type: "POST",
                        data: {
                            id: id
                        },
                        success: function(data) {
                            Swal.fire({
                                title: 'Success',
                                html: "<span class='text-success'><b>SUCCESS!</b></span> Successfully deleted supplier record.",
                                type: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK!'
                            }).then(() => location.reload());
                        }
                    });
                }
            });
        });
    }

    $(document).ready(function() {
        datatable();

        $('#btnAdd').on('click', function() {
            $('#supplierAddModal').modal("show");
        });
    });
</script>