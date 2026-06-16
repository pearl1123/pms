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
        <div class="card-body">
            <div class="table-responsive">
                <!-- <table class="table table-bordered table-hover mb-0" id="tblPPMP" width="100%" cellspacing="0">
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
                            <th width = "30%">Description &amp; Objective</th>
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
                </table> -->

                <table class="table table-bordered table-hover mb-0" id="tblPPMP" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width:30px;"></th>
                            <th class="d-none">Project ID</th>
                            <th class="d-none">Year</th>
                            <th>Description &amp; Objective</th>
                            <th style="width:120px;">Project Type</th>
                            <th style="width:140px;">Procurement Mode</th>
                            <th style="width:130px;">Fund Source</th>
                            <th style="width:180px;">Office</th>
                            <th style="width:130px;">Budget (PhP)</th>
                            <th style="width:120px;">Encoded By</th>
                            <th style="width:100px;" class="text-center">Action</th>
                        </tr>
                    </thead>
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
        max-width: 350px;
        white-space: normal !important;
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
        display: block;
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
    .details-control {
        cursor: pointer;
        text-align: center;
        color: #4e73df;
    }

    .desc-cell {
        max-width: 380px;
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: break-word;
        display: block;
    }

    .child-table {
        background: #f8f9fc;
        margin: 0;
    }

    .child-table th {
        font-size: 0.75rem;
        background: #eaecf4;
        color: #4e73df;
        text-transform: uppercase;
    }

    .child-table td {
        font-size: 0.82rem;
        vertical-align: middle;
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

    var PPMP_ITEM_OPTIONS = `
        <option value="">Select Item</option>
        <?php foreach($items as $i){ ?>
            <option value="<?php echo $i->item_id; ?>">
                <?php echo htmlspecialchars($i->item_description, ENT_QUOTES); ?>
            </option>
        <?php } ?>
    `;

    var PPMP_UNIT_OPTIONS = `
        <option value="">Select</option>
        <?php foreach($unit as $u){ ?>
            <option value="<?php echo $u->unit_id; ?>">
                <?php echo htmlspecialchars($u->unit_code, ENT_QUOTES); ?>
            </option>
        <?php } ?>
    `;

    function datatable() {
        var tblPPMP = $('#tblPPMP').DataTable({
            responsive: true,
            autoWidth: false,
            serverSide: true,
            processing: true,
            paging: true,
            lengthMenu: [10, 15, 20, 50],
            pageLength: 10,
            ajax: {
                url: "<?php echo base_url('PPMP/getPPMPProjectList/' . $year); ?>",
                type: "GET"
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "details-control",
                    render: function () {
                        return '<i class="fas fa-plus-circle"></i>';
                    }
                },
                { data: "ppmp_project_id", name: "ppmp_project_id" },
                { data: "year", name: "year" },
                { data: "general_description", name: "general_description" },
                { data: "project_type", name: "project_type" },
                { data: "proc_mode", name: "proc_mode" },
                { data: "fund_name", name: "fund_name" },
                { data: 'office_name', name: 'office_name' },
                { data: "budget", name: "budget" },
                { data: "encoded_by", name: "encoded_by" },
                { data: "actions", name: "actions", orderable: false, searchable: false }
            ],
            columnDefs: [
                { targets: [1, 2], visible: false },
                {
                    targets: 3,
                    className: "desc-column",
                    render: function (data) {
                        return '<span class="desc-cell">' + $('<div>').text(data).html() + '</span>';
                    }
                },
                {
                    targets: 4,
                    render: function (data) {
                        return projectTypeBadge(data);
                    }
                },
                {
                    targets: 8, // budget
                    render: function(data){
                        return '<span class="budget-cell">' + formatPeso(data) + '</span>';
                    }
                },
                {
                    targets: 9, // encoded by
                    render: function(data){
                        return '<div class="user-cell">'
                            + '<div class="user-avatar">' + getInitials(data) + '</div>'
                            + '<span>' + $('<div>').text(data).html() + '</span>'
                            + '</div>';
                    }
                }
            ],
            order: [[1, "desc"]],
            drawCallback: function () {
                var api = this.api();
                var info = api.page.info();

                $('#recordCount').html(
                    '<span class="badge badge-light border">'
                    + info.recordsDisplay.toLocaleString()
                    + ' project' + (info.recordsDisplay !== 1 ? 's' : '')
                    + '</span>'
                );
            }
        });

        $('#tblPPMP tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = tblPPMP.row(tr);
            var icon = $(this).find('i');
            var data = row.data();

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
            } else {
                $.ajax({
                    url: "<?php echo base_url('PPMP/getPPMPProjectItems'); ?>/" + data.ppmp_project_id,
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        row.child(
                            formatItemsTable(response.data, data.ppmp_project_id)
                        ).show();

                        tr.addClass('shown');
                        icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
                    },
                    error: function () {
                        row.child('<div class="text-danger p-2">Unable to load items.</div>').show();
                    }
                });
            }
        });
    }

    $(document).on('click', '.btnChildAddItem', function () {
        var projectId = $(this).data('project-id');
        var wrapper = $(this).closest('.child-items-wrapper');
        var tbody = wrapper.find('tbody');

        tbody.append(buildChildItemRow(null, projectId));
    });

    $(document).on('change', '.child-item-select', function () {
        var selected = $(this).find('option:selected');
        var row = $(this).closest('.child-item-row');

        var unitId = selected.data('unit') || '';
        var cost = parseFloat(selected.data('cost')) || 0;

        row.find('.child-unit-select').val(unitId);
        row.find('.child-unit-cost-input').val(cost.toFixed(2));

        calculateChildRow(row);
    });

    $(document).on('keyup change', '.child-qty-input, .child-unit-cost-input', function () {
        calculateChildRow($(this).closest('.child-item-row'));
    });

    function calculateChildRow(row) {
        var qty = parseFloat(row.find('.child-qty-input').val()) || 0;
        var unitCost = parseFloat(row.find('.child-unit-cost-input').val()) || 0;
        var total = qty * unitCost;

        row.find('.child-total-cost-input').val(total.toFixed(2));
    }

    $(document).on('click', '.btnChildSaveItem', function () {
        var row = $(this).closest('.child-item-row');

        var data = {
            ppmp_item_id: row.find('.child-ppmp-item-id').val(),
            ppmp_project_id: row.data('project-id'),
            item_id: row.find('.child-item-select').val(),
            ppmp_quantity: row.find('.child-qty-input').val(),
            unit_id: row.find('.child-unit-select').val(),
            ppmp_cost: row.find('.child-total-cost-input').val()
        };

        if (!data.item_id || !data.ppmp_quantity || !data.unit_id || !data.ppmp_cost) {
            Swal.fire('Validation Error', 'Please complete all item fields before saving.', 'warning');
            return;
        }

        $.ajax({
            url: "<?php echo base_url('PPMP/saveInlinePPMPItem'); ?>",
            type: "POST",
            dataType: "json",
            data: data,
            beforeSend: function () {
                row.find('.btnChildSaveItem')
                    .prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i>');
            },
            success: function (res) {
                if (res.success) {
                    row.find('.child-ppmp-item-id').val(res.ppmp_item_id);
                    row.attr('data-item-id', res.ppmp_item_id);

                    Swal.fire({
                        title: 'Saved!',
                        text: 'Item has been saved.',
                        type: 'success',
                        timer: 1200,
                        showConfirmButton: false
                    });

                    $('#tblPPMP').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire('Error', res.message || 'Unable to save item.', 'error');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire('Error', 'Something went wrong while saving the item.', 'error');
            },
            complete: function () {
                row.find('.btnChildSaveItem')
                    .prop('disabled', false)
                    .html('<i class="fas fa-save"></i>');
            }
        });
    });

    $(document).on('click', '.btnChildDeleteItem', function () {
        var row = $(this).closest('.child-item-row');
        var itemId = row.find('.child-ppmp-item-id').val();

        if (!itemId) {
            row.remove();
            return;
        }

        Swal.fire({
            title: 'Delete this item?',
            text: 'This item will be removed from the project.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "<?php echo base_url('PPMP/deleteInlinePPMPItem'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: {
                        ppmp_item_id: itemId
                    },
                    success: function (res) {
                        if (res.success) {
                            row.remove();

                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Item has been deleted.',
                                type: 'success',
                                timer: 1200,
                                showConfirmButton: false
                            });

                            $('#tblPPMP').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'Unable to delete item.', 'error');
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        Swal.fire('Error', 'Something went wrong while deleting item.', 'error');
                    }
                });
            }
        });
    });

    function formatItemsTable(items, projectId) {
        var html = '';

        html += '<div class="p-2 bg-light border rounded child-items-wrapper" data-project-id="' + projectId + '">';
        html += '<div class="d-flex justify-content-between align-items-center mb-2">';
        html += '<strong class="small">Project Items</strong>';
        html += '<button type="button" class="btn btn-sm btn-primary btnChildAddItem" data-project-id="' + projectId + '">';
        html += '<i class="fas fa-plus fa-xs mr-1"></i>Add Item';
        html += '</button>';
        html += '</div>';

        html += '<table class="table table-sm table-bordered child-table mb-0">';
        html += '<thead>';
        html += '<tr>';
        html += '<th style="width:35%;">Item</th>';
        html += '<th style="width:10%;">Qty</th>';
        html += '<th style="width:15%;">Unit</th>';
        html += '<th style="width:15%;">Unit Cost</th>';
        html += '<th style="width:15%;">Total Cost</th>';
        html += '<th style="width:10%;" class="text-center">Action</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';

        if (items && items.length > 0) {
            $.each(items, function (i, item) {
                html += buildChildItemRow(item, projectId);
            });
        } else {
            html += buildChildItemRow(null, projectId);
        }

        html += '</tbody>';
        html += '</table>';
        html += '</div>';

        return html;
    }

    function buildChildItemRow(item, projectId) {
        var itemId      = item ? item.ppmp_item_id : '';
        var selectedId  = item ? item.item_id : '';
        var qty         = item ? item.ppmp_quantity : '';
        var unitId      = item ? item.unit_id : '';
        var cost        = item ? parseFloat(item.ppmp_cost || 0) : 0;

        var unitCost = qty > 0 ? cost / qty : cost;

        var row = '';

        row += '<tr class="child-item-row" data-project-id="' + projectId + '" data-item-id="' + itemId + '">';

        row += '<td>';
        row += '<input type="hidden" class="child-ppmp-item-id" value="' + itemId + '">';
        row += '<select class="form-control form-control-sm child-item-select">';
        row += PPMP_ITEM_OPTIONS;
        row += '</select>';
        row += '</td>';

        row += '<td>';
        row += '<input type="number" min="1" class="form-control form-control-sm child-qty-input" value="' + qty + '">';
        row += '</td>';

        row += '<td>';
        row += '<select class="form-control form-control-sm child-unit-select">';
        row += PPMP_UNIT_OPTIONS;
        row += '</select>';
        row += '</td>';

        row += '<td>';
        row += '<input type="number" step="0.01" min="0.01" class="form-control form-control-sm child-unit-cost-input" value="' + unitCost.toFixed(2) + '">';
        row += '</td>';

        row += '<td>';
        row += '<input type="number" step="0.01" min="0" class="form-control form-control-sm child-total-cost-input" value="' + cost.toFixed(2) + '" readonly>';
        row += '</td>';

        row += '<td class="text-center">';
        row += '<button type="button" class="btn btn-sm btn-success btnChildSaveItem mr-1" title="Save Item">';
        row += '<i class="fas fa-save"></i>';
        row += '</button>';
        row += '<button type="button" class="btn btn-sm btn-danger btnChildDeleteItem" title="Delete Item">';
        row += '<i class="fas fa-trash"></i>';
        row += '</button>';
        row += '</td>';

        row += '</tr>';

        var $row = $(row);

        $row.find('.child-item-select').val(selectedId);
        $row.find('.child-unit-select').val(unitId);

        return $('<div>').append($row).html();
    }

    $(document).ready(function () {
        datatable();

        $('#btnAdd').on('click', function () {
            $('#ppmpAddModal').modal('show');
        });
    });

    function clearEditModal() {
        $('#formEditPPMPProject')[0].reset();
        $('#editItemsContainer').empty();
        $('#deletedItemsContainer').empty();
        $('#edit_ppmp_budget').val('0.00');
    }

    function cloneEditItemRow() {
        let row = $('#editItemTemplate')
            .children()
            .first()
            .clone();

        row.find('input').val('');
        row.find('select').prop('selectedIndex', 0);
        row.find('.edit-total-cost-input').val('0.00');

        return row;
    }

    function calculateEditRow(row) {
        let qty = parseFloat(row.find('.edit-qty-input').val()) || 0;
        let unitCost = parseFloat(row.find('.edit-unit-cost-input').val()) || 0;
        let total = qty * unitCost;

        row.find('.edit-total-cost-input').val(total.toFixed(2));

        calculateEditBudget();
    }

    function calculateEditBudget() {
        let totalBudget = 0;

        $('#editItemsContainer .edit-total-cost-input').each(function () {
            totalBudget += parseFloat($(this).val()) || 0;
        });

        $('#edit_ppmp_budget').val(totalBudget.toFixed(2));
    }

    function addEditItemRow(item) {
        let row = cloneEditItemRow();

        if (item) {
            row.find('.edit-ppmp-item-id').val(item.ppmp_item_id);
            row.find('.edit-item-select').val(item.item_id);
            row.find('.edit-qty-input').val(item.ppmp_quantity);
            row.find('.edit-unit-select').val(item.unit_id);
            row.find('.edit-unit-cost-input').val(item.ppmp_unit_cost || item.ppmp_cost);
            row.find('.edit-total-cost-input').val(parseFloat(item.ppmp_cost || 0).toFixed(2));
        }

        $('#editItemsContainer').append(row);
        calculateEditRow(row);
    }

    $(document).on('click', '.btnUpdateProject', function () {
        let projectId = $(this).data('id');

        clearEditModal();

        $.ajax({
            url: "<?php echo base_url('PPMP/getPPMPProjectForEdit'); ?>/" + projectId,
            type: "GET",
            dataType: "json",
            success: function (res) {
                if (!res.success) {
                    Swal.fire('Error', res.message || 'Unable to load project.', 'error');
                    return;
                }

                let p = res.project;

                $('#edit_ppmp_project_id').val(p.ppmp_project_id);
                $('#edit_ppmp_id').val(p.ppmp_id);
                $('#edit_ppmp_year').val(p.ppmp_year);
                $('#edit_ppmp_general_description').val(p.ppmp_general_description);
                $('#edit_ppmp_project_type').val(p.ppmp_project_type);
                $('#edit_proc_id').val(p.proc_id);
                $('#edit_fund_id').val(p.fund_id);
                $('#edit_attachment_id').val(p.attachment_id);
                $('#edit_ppmp_start_proc').val(p.ppmp_start_proc);
                $('#edit_ppmp_end_proc').val(p.ppmp_end_proc);
                $('#edit_ppmp_delivery').val(p.ppmp_delivery);
                $('#edit_ppmp_budget').val(parseFloat(p.ppmp_budget || 0).toFixed(2));
                $('#edit_ppmp_remarks').val(p.ppmp_remarks);

                $('#edit_ppmp_pre_proc').prop('checked', parseInt(p.ppmp_pre_proc) === 1);

                if (res.items && res.items.length > 0) {
                    $.each(res.items, function (i, item) {
                        addEditItemRow(item);
                    });
                } else {
                    addEditItemRow(null);
                }

                calculateEditBudget();

                $('#editPPMPProjectModal').modal('show');
            },
            error: function () {
                Swal.fire('Error', 'Something went wrong while loading the project.', 'error');
            }
        });
    });

    $(document)
    .off('click', '#btnEditAddItem')
    .on('click', '#btnEditAddItem', function (e) {
        e.preventDefault();
        e.stopPropagation();

        addEditItemRow(null);
        calculateEditBudget();
    });

    $(document).on('change', '.edit-item-select', function () {
        let selected = $(this).find('option:selected');
        let row = $(this).closest('.edit-item-row');

        let unitId = selected.data('unit') || '';
        let cost = selected.data('cost') || '';

        row.find('.edit-unit-select').val(unitId);
        row.find('.edit-unit-cost-input').val(cost);

        calculateEditRow(row);
    });

    $(document).on('keyup change', '.edit-qty-input, .edit-unit-cost-input', function () {
        calculateEditRow($(this).closest('.edit-item-row'));
    });

    $(document).on('click', '.btnRemoveEditItem', function () {
        let row = $(this).closest('.edit-item-row');
        let itemId = row.find('.edit-ppmp-item-id').val();

        if ($('#editItemsContainer .edit-item-row').length <= 1) {
            Swal.fire('Notice', 'At least one item is required.', 'info');
            return;
        }

        if (itemId) {
            $('#deletedItemsContainer').append(
                '<input type="hidden" name="deleted_item_ids[]" value="' + itemId + '">'
            );
        }

        row.remove();
        calculateEditBudget();
    });

    
</script>