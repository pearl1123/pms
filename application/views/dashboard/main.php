<div class="container-fluid">
    <!-- PAGE HEADER
    ========================================================================================================================================= -->
    <div class="d-sm-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-line mr-2 text-primary"></i>DASHBOARD
            </h1>
        </div>
    </div>

    <!-- BREADCRUMBS
    ========================================================================================================================================= -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fas fa-chart-line fa-sm"></i> Dashboard</a></li>
        </ol>
    </nav>

    <!-- ALERTS
    ========================================================================================================================================= -->
    <?php if ($this->session->flashdata('fail') <> null) { ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?php echo $this->session->flashdata('fail'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php } ?>
    <?php if ($this->session->flashdata('success') <> null) { ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo $this->session->flashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php } ?>

    <!-- MAIN CONTENT
    ========================================================================================================================================= -->
    <div class = "col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                Something Awesome is Coming Soon!
            </div>
        </div>
    </div>
    <div class = "row">
        <div class = "col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table mr-2"></i>For Review - PPMP
                    </h6>
                    <span class="text-muted small" id="recordCount"><?php echo $for_review_count; ?></span>
                </div>
                <div class="card-body">
                    
                </div>
            </div>
        </div>
        <div class = "col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table mr-2"></i>For Review - PR
                    </h6>
                    <span class="text-muted small" id="recordCount"><?php echo $for_approval_count; ?></span>
                </div>
                <div class="card-body">
                    <?php echo $for_approval_count; ?>
                </div>
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
    /* ── Helper: get initials from full name ─────────────────────────────── */
    function getInitials(name) {
        if (!name) return '?';
        var parts = name.trim().split(' ');
        if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
        return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
    }

    /* ── Helper: fiscal year label ───────────────────────────────────────── */
    function yearBadge(year) {
        var current = <?php echo (int) $current_year; ?>;
        var y = parseInt(year);
        var tag = '';
        if (y === current) {
            tag = '<span class="badge badge-success ml-2" style="font-size:0.68rem;">Current</span>';
        } else if (y === current - 1) {
            tag = '<span class="badge badge-secondary ml-2" style="font-size:0.68rem;">Previous</span>';
        } else if (y > current) {
            tag = '<span class="badge badge-warning ml-2" style="font-size:0.68rem;">Upcoming</span>';
        }
        return '<div class="year-cell">'
            + '<div class="year-icon"><i class="fas fa-calendar-alt"></i></div>'
            + '<div>'
            +   '<div class="year-label">' + year + tag + '</div>'
            +   '<div class="year-sub">Fiscal Year</div>'
            + '</div>'
            + '</div>';
    }

    function datatable() {
        var tblPPMP = $('#tblPPMP').DataTable({
            "responsive": true,
            "autoWidth": false,
            'serverSide': true,
            'processing': true,
            'paging': true,
            "lengthMenu": [10, 15, 20, 50],
            "pageLength": 10,
            "ajax": {
                url: "<?php echo base_url("Dashboard/getPPMPForReviewList"); ?>",
                type: 'GET'
            },
            "language": {
                "emptyTable":        "<div class='py-4 text-muted'><i class='fas fa-inbox fa-2x mb-2 d-block'></i>No PPMP records found.</div>",
                "processing":        "<i class='fas fa-spinner fa-spin mr-2'></i> Loading...",
                "search":            "<i class='fas fa-search mr-1'></i>",
                "searchPlaceholder": "Search year or encoder..."
            },
            columns: [
                { data: 'year',       name: 'year'       },
                { data: 'encoded_by', name: 'encoded_by' },
                { data: 'actions',    name: 'actions', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data) {
                        return yearBadge(data);
                    }
                },
                {
                    targets: 1,
                    render: function (data) {
                        return '<div class="user-cell">'
                            + '<div class="user-avatar">' + getInitials(data) + '</div>'
                            + '<span>' + $('<div>').text(data).html() + '</span>'
                            + '</div>';
                    }
                }
            ],
            order: [0, 'desc'],
            drawCallback: function (settings) {
                var api  = this.api();
                var info = api.page.info();
                $('#recordCount').html(
                    '<span class="badge badge-light border">'
                    + info.recordsDisplay.toLocaleString()
                    + ' record' + (info.recordsDisplay !== 1 ? 's' : '')
                    + '</span>'
                );
            }
        });
    }

    $(document).ready(function () {
        datatable();

        $('#btnAdd').on('click', function () {
            Swal.fire({
                title: '<i class="fas fa-calendar-plus text-primary mr-2"></i>Create PPMP',
                html: '<p class="text-muted mb-1">Enter the fiscal year to manage.</p>',
                input: 'number',
                inputValue: <?php echo (int) $current_year; ?>,
                inputAttributes: {
                    min: 2000,
                    max: 2100,
                    step: 1,
                    placeholder: 'e.g. 2025'
                },
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#858796',
                confirmButtonText: '<i class="fas fa-arrow-right mr-1"></i> Continue',
                cancelButtonText: 'Cancel',
                inputValidator: function (value) {
                    if (!value || value.toString().length !== 4) {
                        return 'Please enter a valid 4-digit year.';
                    }
                    if (parseInt(value) < 2000 || parseInt(value) > 2100) {
                        return 'Year must be between 2000 and 2100.';
                    }
                }
            }).then((result) => {
                if (result.value) {
                    window.location.href = "<?php echo base_url('PPMP/ppmpList/'); ?>" + result.value;
                }
            });
        });
    });

    $(document).on('click', '.btnRoutePPMP', function () {
    var ppmpId = $(this).data('id');

    $('#route_ppmp_id').val(ppmpId);
    $('#route_action_type').val('');
    $('#route_to_user_id').val('');
    $('#ppmpRoutingModal').modal('show');
});

$('#route_action_type').on('change', function () {
    var action = $(this).val();

    if (action === 'APPROVE' || action === 'DISAPPROVE' || action === 'MARK_REVIEWED') {
        $('#route_to_user_id').prop('required', false);
    } else {
        $('#route_to_user_id').prop('required', true);
    }
});

    
</script>