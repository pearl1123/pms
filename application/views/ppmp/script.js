function getInitials(name) {
    if (!name) return '?';

    var parts = name.trim().split(' ');

    if (parts.length === 1) {
        return parts[0].charAt(0).toUpperCase();
    }

    return (
        parts[0].charAt(0) +
        parts[parts.length - 1].charAt(0)
    ).toUpperCase();
}

function formatPeso(val) {
    var num = parseFloat(val);

    if (isNaN(num)) {
        return '&#8369; 0.00';
    }

    return '&#8369; ' + num.toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function projectTypeBadge(type) {
    if (!type) {
        return '<span class="badge badge-secondary">—</span>';
    }

    var cls = 'badge-secondary';
    var icon = '';

    if (type === 'Goods') {
        cls = 'badge-goods';
        icon = '<i class="fas fa-box fa-xs mr-1"></i>';
    }

    if (type === 'Infrastructure') {
        cls = 'badge-infra';
        icon = '<i class="fas fa-hard-hat fa-xs mr-1"></i>';
    }

    if (type === 'Consulting Services') {
        cls = 'badge-consulting';
        icon = '<i class="fas fa-briefcase fa-xs mr-1"></i>';
    }

    return '<span class="badge ' + cls + ' px-2 py-1">' + icon + type + '</span>';
}

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
            url: BASE_URL + 'PPMP/getPPMPProjectList/' + PPMP_YEAR,
            type: 'GET'
        },
        language: {
            emptyTable:
                "<div class='py-4 text-muted'><i class='fas fa-inbox fa-2x mb-2 d-block'></i>No PPMP projects found.</div>",
            processing:
                "<i class='fas fa-spinner fa-spin mr-2'></i> Loading...",
            search:
                "<i class='fas fa-search mr-1'></i>",
            searchPlaceholder:
                "Search projects..."
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'details-control',
                render: function () {
                    return '<i class="fas fa-plus-circle"></i>';
                }
            },
            { data: 'ppmp_project_id', name: 'ppmp_project_id' },
            { data: 'year', name: 'year' },
            { data: 'general_description', name: 'general_description' },
            { data: 'project_type', name: 'project_type' },
            { data: 'proc_mode', name: 'proc_mode' },
            { data: 'fund_name', name: 'fund_name' },
            { data: 'office_name', name: 'office_name' },
            { data: 'budget', name: 'budget' },
            { data: 'encoded_by', name: 'encoded_by' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        columnDefs: [
            {
                targets: [1, 2],
                visible: false
            },
            {
                targets: 3,
                className: 'desc-column',
                render: function (data) {
                    return '<span class="desc-cell">' +
                        $('<div>').text(data).html() +
                        '</span>';
                }
            },
            {
                targets: 4,
                render: function (data) {
                    return projectTypeBadge(data);
                }
            },
            {
                targets: 7,
                render: function (data) {
                    return '<span class="office-cell">' +
                        $('<div>').text(data || '—').html() +
                        '</span>';
                }
            },
            {
                targets: 8,
                render: function (data) {
                    return '<span class="budget-cell">' + formatPeso(data) + '</span>';
                }
            },
            {
                targets: 9,
                render: function (data) {
                    return '<div class="user-cell">' +
                        '<div class="user-avatar">' + getInitials(data) + '</div>' +
                        '<span>' + $('<div>').text(data || '—').html() + '</span>' +
                        '</div>';
                }
            }
        ],
        order: [[1, 'desc']],
        drawCallback: function () {
            var api = this.api();
            var info = api.page.info();

            $('#recordCount').html(
                '<span class="badge badge-light border">' +
                info.recordsDisplay.toLocaleString() +
                ' project' + (info.recordsDisplay !== 1 ? 's' : '') +
                '</span>'
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
                url: BASE_URL + 'PPMP/getPPMPProjectItems/' + data.ppmp_project_id,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    row.child(
                        formatItemsTable(response.data, data.ppmp_project_id)
                    ).show();

                    tr.addClass('shown');
                    icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
                },
                error: function () {
                    row.child(
                        '<div class="text-danger p-2">Unable to load items.</div>'
                    ).show();
                }
            });
        }
    });
}

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
    }

    html += '</tbody>';
    html += '</table>';
    html += '</div>';

    return html;
}

function buildChildItemRow(item, projectId) {
    var ppmpItemId = item ? item.ppmp_item_id : '';
    var selectedId = item ? item.item_id : '';
    var qty = item ? item.ppmp_quantity : '';
    var unitId = item ? item.unit_id : '';
    var totalCost = item ? parseFloat(item.ppmp_cost || 0) : 0;
    var unitCost = qty > 0 ? totalCost / qty : totalCost;

    var row = '';

    row += '<tr class="child-item-row" data-project-id="' + projectId + '" data-item-id="' + ppmpItemId + '">';

    row += '<td>';
    row += '<input type="hidden" class="child-ppmp-item-id" value="' + ppmpItemId + '">';
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
    row += '<input type="number" step="0.01" min="0" class="form-control form-control-sm child-total-cost-input" value="' + totalCost.toFixed(2) + '" readonly>';
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

function calculateChildRow(row) {
    var qty = parseFloat(row.find('.child-qty-input').val()) || 0;
    var unitCost = parseFloat(row.find('.child-unit-cost-input').val()) || 0;
    var total = qty * unitCost;

    row.find('.child-total-cost-input').val(total.toFixed(2));
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

    if (unitId !== '') {
        row.find('.child-unit-select').val(unitId);
    }

    if (cost > 0) {
        row.find('.child-unit-cost-input').val(cost.toFixed(2));
    }

    calculateChildRow(row);
});

$(document).on('keyup change', '.child-qty-input, .child-unit-cost-input', function () {
    calculateChildRow($(this).closest('.child-item-row'));
});

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
        Swal.fire(
            'Validation Error',
            'Please complete all item fields before saving.',
            'warning'
        );
        return;
    }

    $.ajax({
        url: BASE_URL + 'PPMP/saveInlinePPMPItem',
        type: 'POST',
        dataType: 'json',
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
                url: BASE_URL + 'PPMP/deleteInlinePPMPItem',
                type: 'POST',
                dataType: 'json',
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

$(document).ready(function () {
    datatable();

    $('#btnAdd').on('click', function () {
        $('#ppmpAddModal').modal('show');
    });
});