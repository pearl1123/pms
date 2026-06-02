<div class="container-fluid">

    <!-- PAGE HEADER
    ========================================================================================================================================= -->
    <div class="d-sm-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">PPMP <span class="badge badge-primary"><?php echo $year; ?></span></h1>
            <small class="text-muted">Project Procurement Management Plan &mdash; <?php echo $year; ?></small>
        </div>
        <button class="btn btn-primary shadow-sm" id="btnAdd">
            <i class="fas fa-plus fa-sm mr-1"></i> Add PPMP Item
        </button>
    </div>

    <!-- BREADCRUMBS
    ========================================================================================================================================= -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home fa-sm"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url('PPMP'); ?>">PPMP</a></li>
            <li class="breadcrumb-item active" aria-current="page">PPMP Items &mdash; <?php echo $year; ?></li>
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
                <i class="fas fa-table mr-2"></i>PPMP Items for <?php echo $year; ?>
            </h6>
            <span class="text-muted small" id="recordCount"></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="tblPPMP" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="d-none">No.</th>
                            <th class="d-none">Year</th>
                            <th>Description &amp; Objective</th>
                            <th style="width:120px;">Project Type</th>
                            <th style="width:130px;">Quantity / Size</th>
                            <th style="width:140px;">Procurement Mode</th>
                            <th style="width:130px;">Fund Source</th>
                            <th style="width:130px;">Budget (PhP)</th>
                            <th style="width:120px;">Encoded By</th>
                            <th style="width:100px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="d-none">No.</th>
                            <th class="d-none">Year</th>
                            <th>Description &amp; Objective</th>
                            <th>Project Type</th>
                            <th>Quantity / Size</th>
                            <th>Procurement Mode</th>
                            <th>Fund Source</th>
                            <th>Budget (PhP)</th>
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

    /* ── Description cell ─────────────────────────────────────────────────── */
    #tblPPMP .desc-cell {
        max-width: 280px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #tblPPMP .desc-cell:hover {
        white-space: normal;
        overflow: visible;
        word-break: break-word;
    }

    /* ── Badges ───────────────────────────────────────────────────────────── */
    .badge-goods        { background-color: #1cc88a; color: #fff; }
    .badge-infra        { background-color: #f6c23e; color: #5a4000; }
    .badge-consulting   { background-color: #36b9cc; color: #fff; }

    /* ── Budget cell ──────────────────────────────────────────────────────── */
    .budget-cell {
        font-weight: 600;
        color: #1a7a4a;
        white-space: nowrap;
    }

    /* ── Encoded by ───────────────────────────────────────────────────────── */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .user-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: #4e73df;
        color: #fff;
        font-size: 0.7rem;
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
        padding: 4px 8px;
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

    /* ── Helper: format number as Philippine Peso ────────────────────────── */
    function formatPeso(val) {
        var num = parseFloat(val);
        if (isNaN(num)) return val;
        return '&#8369; ' + num.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    /* ── Helper: project type badge ──────────────────────────────────────── */
    function projectTypeBadge(type) {
        if (!type) return '<span class="badge badge-secondary">—</span>';
        var cls = 'badge-secondary';
        var icon = '';
        if (type === 'Goods')               { cls = 'badge-goods';      icon = '<i class="fas fa-box fa-xs mr-1"></i>'; }
        if (type === 'Infrastructure')      { cls = 'badge-infra';      icon = '<i class="fas fa-hard-hat fa-xs mr-1"></i>'; }
        if (type === 'Consulting Services') { cls = 'badge-consulting'; icon = '<i class="fas fa-briefcase fa-xs mr-1"></i>'; }
        return '<span class="badge ' + cls + ' px-2 py-1">' + icon + type + '</span>';
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
                url: "<?php echo base_url("PPMP/getPPMPItemList/" . $year); ?>",
                type: 'GET'
            },
            "language": {
                "emptyTable":   "<div class='py-4 text-muted'><i class='fas fa-inbox fa-2x mb-2 d-block'></i>No PPMP items found for <?php echo $year; ?>.</div>",
                "processing":   "<i class='fas fa-spinner fa-spin mr-2'></i> Loading...",
                "search":       "<i class='fas fa-search mr-1'></i>",
                "searchPlaceholder": "Search items..."
            },
            columns: [
                { data: 'id',                  name: 'id'                  },
                { data: 'year',                name: 'year'                },
                { data: 'general_description', name: 'general_description' },
                { data: 'project_type',        name: 'project_type'        },
                { data: 'quantity',            name: 'quantity'            },
                { data: 'proc_mode',           name: 'proc_mode'           },
                { data: 'fund_name',           name: 'fund_name'           },
                { data: 'cost',                name: 'cost'                },
                { data: 'encoded_by',          name: 'encoded_by'          },
                { data: 'actions',             name: 'actions', orderable: false, searchable: false }
            ],
            columnDefs: [
                { targets: [0, 1], visible: false, orderable: false },
                {
                    targets: 2,
                    render: function (data) {
                        return '<span class="desc-cell" title="' + $('<div>').text(data).html() + '">' + $('<div>').text(data).html() + '</span>';
                    }
                },
                {
                    targets: 3,
                    render: function (data) {
                        return projectTypeBadge(data);
                    }
                },
                {
                    targets: 7,
                    render: function (data) {
                        return '<span class="budget-cell">' + formatPeso(data) + '</span>';
                    }
                },
                {
                    targets: 8,
                    render: function (data) {
                        return '<div class="user-cell">'
                            + '<div class="user-avatar">' + getInitials(data) + '</div>'
                            + '<span>' + $('<div>').text(data).html() + '</span>'
                            + '</div>';
                    }
                }
            ],
            order: [1, 'desc'],
            drawCallback: function (settings) {
                var api  = this.api();
                var info = api.page.info();
                $('#recordCount').html(
                    '<span class="badge badge-light border">'
                    + info.recordsDisplay.toLocaleString()
                    + ' item' + (info.recordsDisplay !== 1 ? 's' : '')
                    + '</span>'
                );
            }
        });

        /* ── Edit button ─────────────────────────────────────────────────── */
        $('#ppmpBody').on('click', '#btnUpdate', function () {
            var row  = $(this).closest('tr');
            var data = row.hasClass('child')
                ? tblPPMP.row(row.prev('tr')).data()
                : tblPPMP.row(row).data();

            /* TODO: populate edit modal fields with data and show it */
            // $('#editPPMPModal').modal('show');
        });

        /* ── Delete button ───────────────────────────────────────────────── */
        $('#ppmpBody').on('click', '#btnDel', function () {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Delete this item?',
                html: "<span class='text-danger'><b>WARNING!</b></span> This action cannot be undone.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "<?php echo base_url('PPMP/deletePPMPItem'); ?>",
                        type: "POST",
                        dataType: "json",
                        data: { id: id },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    html: "<span class='text-success'><b>SUCCESS!</b></span> PPMP item has been deleted.",
                                    type: 'success',
                                    confirmButtonColor: '#1cc88a',
                                    confirmButtonText: 'OK'
                                }).then(() => { location.reload(); });
                            } else {
                                Swal.fire('Error', response.error || 'Failed to delete item.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                        }
                    });
                }
            });
        });
    }

    $(document).ready(function () {
        datatable();

        $('#btnAdd').on('click', function () {
            $('#ppmpAddModal').modal('show');
        });
    });
</script>