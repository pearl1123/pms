<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Stock</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btnAdd">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Stock</button>
    </div>

    <hr />

    <!-- BREADCRUMBS
    ========================================================================================================================================= -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Libraries</li>
            <li class="breadcrumb-item active" aria-current="page">Stock</li>
        </ol>
    </nav>

    <!-- ALERTS
    ========================================================================================================================================= -->
    <?php if ($this->session->flashdata('fail') <> null) { ?>
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
            <span>
                <?php echo $this->session->flashdata('fail'); ?>
            </span>
        </div>
    <?php } ?>
    <?php if ($this->session->flashdata('success') <> null) { ?>
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
            <span>
                <?php echo $this->session->flashdata('success'); ?>
            </span>
        </div>
    <?php } ?>

    <!-- MAIN CONTENT
    ========================================================================================================================================= -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Stock Table</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tblStock" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Item</th>
                            <th>Unit</th>
                            <th>Stock Onhand</th>
                            <th>Encoded By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No.</th>
                            <th>Item</th>
                            <th>Unit</th>
                            <th>Stock Onhand</th>
                            <th>Encoded By</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody id="stockBody">
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
    let pendingStockData = null;

    function datatable() {
        var tblStock = $('#tblStock').DataTable({
            "responsive": true,
            "autoWidth": false,
            'serverSide': true,
            'processing': true,
            'paging': true,
            "lengthMenu": [10, 15, 20],
            "ajax": {
                url: "<?php echo base_url("Libraries/getStockList/"); ?>",
                type: 'GET',
                error: function(xhr, error, thrown) {
                    console.log(xhr.responseText);
                    console.log(error);
                    console.log(thrown);
                }
            },
            "language": {
                "emptyTable": "No Results"
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'item_description', name: 'item_description' },
                { data: 'unit_code', name: 'unit_code' },
                { data: 'stock_onhand', name: 'stock_onhand' },
                { data: 'fullname', name: 'fullname' },
                { data: 'actions', name: 'actions', orderable: false }
            ],
            'columnDefs': [{
                'targets': [0],
                'visible': false,
                'orderable': false
            }],
            order: [1, 'asc']
        });

        $('#stockBody').on('click', '#btnUpdate', function() {
            var data = ($(this).parents('tr').hasClass('child')) ?
                tblStock.row($(this).parents().prev('tr')).data() :
                tblStock.row($(this).parents('tr')).data();

            pendingStockData = data;

            $('#stockID').val(data.id);
            $('#stockItem_update').val(data.item_id);
            $('#stockUnit_update').val(data.unit_id);
            $('#stockOnhand_update').val(data.stock_onhand);
            $('#stockUpdateModal').modal('show');
        });

        $('#stockBody').on('click', '#btnDel', function() {
            var data = ($(this).parents('tr').hasClass('child')) ?
                tblStock.row($(this).parents().prev('tr')).data() :
                tblStock.row($(this).parents('tr')).data();
            var id = data.id;

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
                        url: "<?php echo base_url('Libraries/deleteStock'); ?>",
                        async: false,
                        type: "POST",
                        datatype: "json",
                        data: {
                            id: id
                        },
                        success: function(data) {
                            Swal.fire({
                                title: 'Success',
                                html: "<span class='text-success'><b>SUCCESS!</b></span> Successfully deleted stock record.",
                                type: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK!'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });
    }

    $(document).ready(function() {
        datatable();

        $('#btnAdd').on('click', function() {
            $('#stockAddModal').modal("show");
        });

        $('#stockAddModal').on('shown.bs.modal', function() {
            $('#stockItem').select2({
                dropdownParent: $('#stockAddModal'),
                width: '100%',
                placeholder: "-- Select Item --",
                allowClear: true
            });
        });

        $('#stockUpdateModal').on('shown.bs.modal', function() {
            if ($('#update_item_id').hasClass('select2-hidden-accessible')) {
                $('#update_item_id').select2('destroy');
            }

            $('#update_item_id').select2({
                dropdownParent: $('#stockUpdateModal'),
                width: '100%',
                placeholder: "-- Select Item --",
                allowClear: true
            });

            if (pendingStockData) {
                const itemId = pendingStockData.item_id;
                const itemDesc = pendingStockData.item_description.trim().toLowerCase();

                const matchingOption = $('#update_item_id option').filter(function() {
                    const val = $(this).val();
                    const text = $(this).text().trim().toLowerCase();
                    
                    if (val === 'pharm_' + itemId) return true;

                    const idMatch = val === 'lib_' + itemId || val === 'diet_' + itemId;
                    return idMatch && text === itemDesc;
                });

                if (matchingOption.length) {
                    $('#update_item_id').val(matchingOption.val()).trigger('change');
                }

                $('#stockUnit_update').val(pendingStockData.unit_id);
                $('#stockOnhand_update').val(pendingStockData.stock_onhand);

                pendingStockData = null;
            }
        });

        $('#stockUpdateModal').on('hidden.bs.modal', function() {
            if ($('#update_item_id').hasClass('select2-hidden-accessible')) {
                $('#update_item_id').select2('destroy');
            }
        });

    });
</script>