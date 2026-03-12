<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Permission <small class="text-muted">Manager</small></h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btnAdd">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Permission
        </button>
    </div>

    <hr />

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Libraries</li>
            <li class="breadcrumb-item active" aria-current="page">Permission</li>
        </ol>
    </nav>

    <?php if ($this->session->flashdata('fail')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('fail'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $this->session->flashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list"></i> Permission Table</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tblPermission" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Permission Name</th>
                            <th>Encoded By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="permissionBody">
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
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/datatables/dataTables.bootstrap4.min.js'); ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // 1. Initialize Datatable with the variable scoped correctly
        var tblPermission = $('#tblPermission').DataTable({
            "responsive": true,
            "autoWidth": false,
            "serverSide": true,
            "processing": true,
            "ajax": {
                "url": "<?php echo base_url('Libraries/getAllPermissions'); ?>",
                "type": 'GET'
            },
            "columns": [{
                    data: 'id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'encoded_by'
                },
                {
                    data: 'actions'
                }
            ],
            "columnDefs": [{
                    "targets": [0],
                    "visible": false,
                    "orderable": false
                },
                {
                    "targets": [3],
                    "orderable": false
                }
            ],
            "order": [
                [1, 'asc']
            ]
        });

        // 2. Add Button
        $('#btnAdd').on('click', function() {
            $('#permAddModal').modal("show");
        });

        // 3. Edit Button (Event Delegation)
        // 3. Edit Button (Event Delegation) - UPDATED IDs
        $('#permissionBody').on('click', '.btnUpdate', function() {
            var tr = $(this).closest('tr');
            var data = tblPermission.row(tr).data();

            // Check kung mobile view/responsive view (child row)
            if (tr.hasClass('child')) {
                data = tblPermission.row(tr.prev('.parent')).data();
            }

            // Siguraduhing tugma ang IDs dito sa HTML mo
            $('#permID_u').val(data.id); // 'permID_u' mula sa HTML mo
            $('#permName_u').val(data.name); // 'permName_u' mula sa HTML mo

            // Buksan ang modal gamit ang tamang ID
            $('#permUpdateModal').modal('show'); // 'permUpdateModal' mula sa HTML mo
        });
        // 4. Delete Button
        $('#permissionBody').on('click', '.btnDel', function() {
            var tr = $(this).closest('tr');
            var data = tblPermission.row(tr).data();

            if (tr.hasClass('child')) {
                data = tblPermission.row(tr.prev('.parent')).data();
            }

            var id = data.id;

            Swal.fire({
                title: 'Delete?',
                text: "Warning! You will not be able to undo this action.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "<?php echo base_url('Libraries/deletePermission'); ?>",
                        type: "POST",
                        data: {
                            id: id,
                            // CSRF Protection
                            "<?php echo $this->security->get_csrf_token_name(); ?>": "<?php echo $this->security->get_csrf_hash(); ?>"
                        },
                        success: function() {
                            Swal.fire('Deleted!', 'Permission record removed.', 'success')
                                .then(() => {
                                    tblPermission.ajax.reload();
                                });
                        }
                    });
                }
            });
        });
    });
</script>