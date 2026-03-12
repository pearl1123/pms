<style>
    #permsToGroupTable tr.group-header td {
        background: #f8f9fc;
        font-weight: 700;
        text-transform: uppercase;
        color: #4e73df;
        padding: 12px;
        border-left: 4px solid #4e73df;
    }
    #permsToGroupTable .sub-indent {
        padding-left: 15px;
    }
    .perm-status {
        display: block;
        font-style: italic;
        font-size: 11px;
    }
</style>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Permissions <small class="text-muted">Per Group Manager</small>
        </h1>
        <a href="<?= base_url('Libraries/group'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Groups
        </a>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('Libraries/groups'); ?>">Groups</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage Permissions</li>
        </ol>
    </nav>

    <?php if ($this->session->flashdata('fail')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('fail'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-shield-alt"></i> Permissions for: <u><?php echo $group_details->name; ?></u>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="permsToGroupTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Main Module</th> 
                            <th>Sub-Module</th>
                            <th class="text-center" width="10%">Check All</th>
                            <th class="text-center" width="8%">View</th>
                            <th class="text-center" width="8%">Add</th>
                            <th class="text-center" width="8%">Edit</th>
                            <th class="text-center" width="8%">Delete</th>
                            <th width="20%">Extra Permissions</th>
                        </tr>
                    </thead>
                    <tbody id="permToGroupBody">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


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
        var groupTable = $('#permsToGroupTable').DataTable({
            responsive: true,
            autoWidth: false,
            serverSide: true,
            processing: true,
            paging: false,
            searching: true,
            ajax: {
                url: "<?= base_url('GroupPermissions/get_permissions/' . $group_details->id); ?>",
                type: 'GET'
            },
            columns: [
                { data: 'main_module', name: 'main_module' },
                { data: 'sub_module', name: 'sub_module' },
                { data: 'check_all', orderable: false, searchable: false, className: "text-center" },
                { data: 'view', name: 'view', orderable: false, searchable: false, className: "text-center" },
                { data: 'add', name: 'add', orderable: false, searchable: false, className: "text-center" },
                { data: 'edit', name: 'edit', orderable: false, searchable: false, className: "text-center" },
                { data: 'delete', name: 'delete', orderable: false, searchable: false, className: "text-center" },
                { data: 'extra', name: 'extra' }
            ],
            columnDefs: [
                { targets: 0, visible: false }
            ],
            drawCallback: function(settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;
                var colspan = api.columns(':visible').count();

                api.column(0, { page: 'current' }).data().each(function(group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before(
                            '<tr class="group-header"><td colspan="' + colspan + '"><i class="fas fa-folder-open mr-2"></i>' + group + '</td></tr>'
                        );
                        last = group;
                    }
                });
            }
        });

        // AJAX update and notification logic
     $('#permsToGroupTable').on('change', '.perm-toggle', function() {
    let $cb    = $(this);
    let perm_id  = $cb.data('perm');
    let group_id = $cb.data('group');
    let sub_id   = $cb.data('sub');       // ← ADD THIS (was missing!)
    let checked  = $cb.is(':checked') ? 1 : 0;

    $.ajax({
        url: "<?= base_url('GroupPermissions/updatePermToGroup'); ?>",
        type: "POST",
        data: {
            group_id: group_id,
            perm_id:  perm_id,
            sub_id:   sub_id,             // ← ADD THIS
            checked:  checked,
            "<?= $this->security->get_csrf_token_name(); ?>": "<?= $this->security->get_csrf_hash(); ?>"
        },
        success: function(res) {
            showNotify($cb, "Saved ✅");
        },
        error: function() {
            showNotify($cb, "❌ Error", false);
        }
    });
});
    });

    function showNotify($checkbox, message, success = true) {
        $checkbox.closest('td').find(".perm-status").remove();
        let color = success ? "green" : "red";
        let $notify = $("<span class='perm-status' style='color:"+color+";'></span>").text(message);
        $checkbox.after($notify);
        setTimeout(() => { $notify.fadeOut(500, function() { $(this).remove(); }); }, 2000);
    }
</script>