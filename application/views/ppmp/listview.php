<div class="container-fluid">

    <!-- PAGE HEADER
    ========================================================================================================================================= -->
    <div class="d-sm-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clipboard-list mr-2 text-primary"></i>PPMP
            </h1>
            <small class="text-muted">Project Procurement Management Plan &mdash; All Years</small>
        </div>
        <button class="btn btn-primary shadow-sm" id="btnAdd">
            <i class="fas fa-plus fa-sm mr-1"></i> Create PPMP
        </button>
    </div>

    <!-- BREADCRUMBS
    ========================================================================================================================================= -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home fa-sm"></i> Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">PPMP</li>
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
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-2"></i>PPMP List
            </h6>
            <span class="text-muted small" id="recordCount"></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="tblPPMP" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 220px;">Fiscal Year</th>
                            <th>Encoded By</th>
                            <th style="width: 120px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Fiscal Year</th>
                            <th>Encoded By</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </tfoot>
                    <tbody id="ppmpBody"></tbody>
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

<style>
    /* ── Table header ─────────────────────────────────────────────────────── */
    #tblPPMP thead tr th {
        background-color: #4e73df;
        color: #fff;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        vertical-align: middle;
        white-space: nowrap;
        border-color: #3a5fc8;
    }

    /* ── Body rows ────────────────────────────────────────────────────────── */
    #tblPPMP tbody tr td {
        vertical-align: middle;
        font-size: 0.85rem;
        color: #3a3b45;
    }
    #tblPPMP tbody tr:hover td {
        background-color: #eaf0ff;
    }

    /* ── Year cell ────────────────────────────────────────────────────────── */
    .year-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .year-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background-color: #eaf0ff;
        color: #4e73df;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .year-label {
        font-size: 1rem;
        font-weight: 700;
        color: #2e3a8c;
        letter-spacing: 0.02em;
    }
    .year-sub {
        font-size: 0.72rem;
        color: #858796;
    }

    /* ── Encoded by ───────────────────────────────────────────────────────── */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .user-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #4e73df;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        text-transform: uppercase;
    }

    /* ── Action column ────────────────────────────────────────────────────── */
    #tblPPMP td:last-child,
    #tblPPMP th:last-child {
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
    .btn-action-group {
        display: inline-flex;
        gap: 4px;
        align-items: center;
        justify-content: center;
    }
    .btn-action-group .btn {
        padding: 4px 10px;
        font-size: 0.78rem;
        border-radius: 4px;
    }

    /* ── DataTables overrides ─────────────────────────────────────────────── */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #4e73df;
        border-color: #4e73df;
        color: #fff !important;
        border-radius: 4px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #eaf0ff;
        border-color: #eaf0ff;
        color: #4e73df !important;
        border-radius: 4px;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #d1d3e2;
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 0.85rem;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #d1d3e2;
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 0.85rem;
    }
    div.dataTables_processing {
        background: rgba(255,255,255,0.9);
        border: 1px solid #d1d3e2;
        border-radius: 8px;
        padding: 12px 20px;
        font-size: 0.85rem;
        color: #4e73df;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
</style>

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
                url: "<?php echo base_url("PPMP/getPPMPList"); ?>",
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
</script>