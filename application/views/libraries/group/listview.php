<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Groups <small class="text-muted">Manager</small></h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btnCreateGroup">
            <i class="fas fa-plus fa-sm text-white-50"></i> Create Group
        </button>
    </div>

    <hr />

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Libraries</li>
            <li class="breadcrumb-item active" aria-current="page">Groups</li>
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
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list"></i> List of Groups</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="groupTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Definition</th>
                            <th>Encoded By</th>
                            <th>Modified By</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="groupBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Group Modal -->
<div class="modal fade" id="groupAddModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Group</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("Libraries/saveGroup"); ?>" method="post" id="frmGroupAdd">
                <div class="modal-body">
                    <?php echo $csrf; ?>
                    <div class="mb-3">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter group name" required>
                    </div>
                    <div class="mb-3">
                        <label for="definition">Definition <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="definition" id="definition" placeholder="Enter definition" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Save Group</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Group Modal -->
<div class="modal fade" id="modalUpdateGroup" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Group</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("Libraries/updateGroup"); ?>" method="post" id="frmGroupUpdate">
                <div class="modal-body">
                    <?php echo $csrf; ?>
                    <input type="hidden" name="id" id="id">
                    <div class="mb-3">
                        <label for="name_u">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name_u" id="name_u" placeholder="Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="definition_u">Definition <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="definition_u" id="definition_u" placeholder="Definition" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Update Group</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Permissions Modal -->
<div class="modal fade" id="modalGroupPermissions" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-key"></i> Permissions — <span id="permGroupName"></span>
                </h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="permissionsContainer">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-2x text-success"></i>
                    <p class="mt-2 text-muted">Loading permissions...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>

</div>
</div>
</div>
</body>
</html>

<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/datatables/dataTables.bootstrap4.min.js'); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    $(document).ready(function() {

        // Initialize DataTable
        var groupTable = $('#groupTable').DataTable({
            "responsive"  : true,
            "autoWidth"   : false,
            "serverSide"  : true,
            "processing"  : true,
            "ajax": {
                "url"  : "<?php echo base_url('Libraries/getAllGroups'); ?>",
                "type" : "GET"
            },
            "columns": [
                { data: 'id'          },
                { data: 'name'        },
                { data: 'definition'  },
                { data: 'encoded_by'  },
                { data: 'modified_by' },
                { data: 'status'      },
                { data: 'actions'     }
            ],
            "columnDefs": [
                {
                    "targets"  : [0],
                    "visible"  : false
                },
                {
                    "targets" : [5],
                    "render"  : function(data, type, row) {
                        return data == 0
                            ? '<span class="badge badge-success">Active</span>'
                            : '<span class="badge badge-danger">Archived</span>';
                    }
                },
                {
                    "targets"    : [6],
                    "orderable"  : false
                }
            ],
            "order": [[1, 'desc']]
        });

        // Trigger Create Modal
        $('#btnCreateGroup').on('click', function() {
            $('#groupAddModal').modal('show');
        });

        // Edit Button Click
        $('#groupBody').on('click', '.btnUpdate', function() {
            var tr   = $(this).closest('tr');
            var data = groupTable.row(tr).data();

            if (tr.hasClass('child')) {
                data = groupTable.row(tr.prev('.parent')).data();
            }

            $('#id').val(data.id);
            $('#name_u').val(data.name);
            $('#definition_u').val(data.definition);
            $('#modalUpdateGroup').modal('show');
        });

        // Permissions Button Click
        $('#groupBody').on('click', '.btnPermissions', function() {
            var tr   = $(this).closest('tr');
            var data = groupTable.row(tr).data();

            if (tr.hasClass('child')) {
                data = groupTable.row(tr.prev('.parent')).data();
            }

            $('#permGroupName').text(data.name);

            $('#permissionsContainer').html(
                '<div class="text-center py-5">' +
                    '<i class="fas fa-spinner fa-spin fa-2x text-success"></i>' +
                    '<p class="mt-2 text-muted">Loading permissions...</p>' +
                '</div>'
            );

            $('#modalGroupPermissions').modal('show');

            $.ajax({
                url     : "<?php echo base_url('user/permissions'); ?>",
                type    : "GET",
                data    : { group_id: data.id },
                success : function(response) {
                    $('#permissionsContainer').html(response);
                },
                error   : function() {
                    $('#permissionsContainer').html(
                        '<div class="alert alert-danger m-3">' +
                            '<i class="fas fa-exclamation-triangle"></i> ' +
                            'Failed to load permissions. Please try again.' +
                        '</div>'
                    );
                }
            });
        });

        // Delete Button Click
        $('#groupBody').on('click', '.btnDel', function() {
            var tr   = $(this).closest('tr');
            var data = groupTable.row(tr).data();

            if (tr.hasClass('child')) {
                data = groupTable.row(tr.prev('.parent')).data();
            }

            var id = data.id;

            Swal.fire({
                title              : 'Delete Group?',
                text               : "You won't be able to revert this!",
                icon               : 'warning',
                showCancelButton   : true,
                confirmButtonColor : '#4e73df',
                cancelButtonColor  : '#e74a3b',
                confirmButtonText  : 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url  : "<?php echo base_url('Libraries/deleteGroup'); ?>",
                        type : "POST",
                        data : {
                            id: id,
                            "<?php echo $this->security->get_csrf_token_name(); ?>": "<?php echo $this->security->get_csrf_hash(); ?>"
                        },
                        success : function(response) {
                            var res = JSON.parse(response);
                            if (res.success) {
                                Swal.fire('Deleted!', 'Group has been deleted.', 'success')
                                .then(() => {
                                    groupTable.ajax.reload();
                                });
                            } else {
                                Swal.fire('Error!', 'Failed to delete group.', 'error');
                            }
                        },
                        error : function() {
                            Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                        }
                    });
                }
            });
        });

    });
</script>