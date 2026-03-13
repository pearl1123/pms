<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bids and Awards List</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btnAdd">
            <i class="fas fa-plus fa-sm text-white-50"></i> Create Bids and Awards</button>
    </div>

    <hr />

    <!-- BREADCRUMBS
    ========================================================================================================================================= -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Bids and Awards List</li>
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
            <h6 class="m-0 font-weight-bold text-primary">Bids and Awards List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tblBAC" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>BAC No.</th>
                            <th>SAI No.</th>
                            <th>Procurement Mode</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Encoded By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No.</th>
                            <th>BAC No.</th>
                            <th>SAI No.</th>
                            <th>Procurement Mode</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Encoded By</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody id="bacBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php $this->load->view('BAC/modal_add'); ?>
    <?php $this->load->view('BAC/modal_update'); ?>
    <?php $this->load->view('BAC/BACattachment'); ?>
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
    var tblBAC;

    function datatable() {
        var tblBAC = $('#tblBAC').DataTable({
            "responsive": true,
            "autoWidth": false,
            'serverSide': true,
            'processing': true,
            'paging': true,
            "lengthMenu": [10, 15, 20],
            "ajax": {
                url: "<?php echo base_url("BAC/getBACList/"); ?>",
                type: 'GET'
            },
            "language": {
                "emptyTable": "No Results"
            },
            columns: [{
                    data: 'id',
                    name: 'bac_id'
                },
                {
                    data: 'bac_no',
                    name: 'bac_no'
                },
                {
                    data: 'sai_no',
                    name: 'sai_no'
                },
                {
                    data: 'proc_name',
                    name: 'proc_name'
                },
                {
                    data: 'office_desc',
                    name: 'office_desc'
                },
                {
                    data: 'date_created',
                    name: 'date_created',
                    render: function(data, type, row) {
                        if (!data) return '';

                        let date = new Date(data);
                        return date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: '2-digit'
                        });
                    }
                },
                {
                    data: 'encoded_by',
                    name: 'encoded_by'
                },
                {
                    data: 'actions',
                    name: 'actions'
                },
            ],
            'columnDefs': [{
                    'targets': [1, 2, 3],
                    'visible': true,
                    'orderable': true
                },
                {
                    'targets': [0],
                    'visible': false,
                    'orderable': false
                }
            ],
            order: [1, 'desc']
        });

        $('#bacBody').on('click', '#btnUpdate', function() {
            var data = ($(this).parents('tr').hasClass('child')) ?
                tblBAC.row($(this).parents().prev('tr')).data()
                :
                tblBAC.row($(this).parents('tr')).data();

            const $bacUpdateModal = $('#bacUpdateModal');
            $.ajax({
                url: "<?php echo base_url('BAC/getBAC'); ?>",
                type: "POST",
                dataType: "json",
                data: {
                    bac_id: data.id
                },
                success: function(res) {
                    $bacUpdateModal.find('#bacId').val(res.bac_id || '');
                    $bacUpdateModal.find('#bacNumber').val(res.bac_no || '');
                    $bacUpdateModal.find('#saiNumber').val(res.sai_no || '');
                    $bacUpdateModal.find('#bacQuantity').val(res.quantity || '').trigger('input');
                    $bacUpdateModal.find('#bacUnitCost').val(res.unit_cost || '').trigger('input');
                    $bacUpdateModal.find('#bacRemarks').val(res.remarks || '');
                    $bacUpdateModal.find('#bacRequestedBy').val(res.requested_by || '');
                    $bacUpdateModal.find('#bacDesignation').val(res.designation || '');

                    $.when(
                        initOfficeSelect2($bacUpdateModal, res.office_id),
                        initStockSelect2($bacUpdateModal, res.stock_id),
                        initProcurementModeSelect2($bacUpdateModal, res.stock_id),
                        initProcurementModeSelect2($bacUpdateModal, res.proc_id),
                    ).done(function() {
                        $bacUpdateModal.modal('show');
                    });
                },
                error: function(err) {
                    console.error(err);
                }
            })
        });

        $('#bacBody').on('click', '#btnDel', function() {
            var data = ($(this).parents('tr').hasClass('child')) ?
                tblBAC.row($(this).parents().prev('tr')).data()
                :
                tblBAC.row($(this).parents('tr')).data();
            var id = data.id;
            Swal.fire({
                title: 'Delete?',
                html: "<span class = 'text-danger'><b>WARNING!</b></span> You will not be able to undo this action.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "<?php echo base_url('BAC/deleteBAC'); ?>",
                        async: false,
                        type: "POST",
                        datatype: "json",
                        data: {
                            bac_id: id
                        },
                        success: function(data) {
                            Swal.fire({
                                title: 'Success',
                                html: "<span class = 'text-success'><b>SUCCESS!</b></span> Successfully deleted Bids and Awards record.",
                                type: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK!'
                            }).then((result) => {
                                location.reload();
                            });
                        }
                    });

                }
            });
        });

        $('#tblBAC').on('click', '#btnAttachment', function() {

            var bac_id = $(this).data('bacid');

            const $modal = $('#bacAttachmentModal');

            $modal.find('form')[0].reset();
            $modal.find('#bac_id').val(bac_id);

            $modal.modal('show');

        });

        $(document).on('click', '#btnAddItem', function() {
            const bacId = $(this).data('bacid');
            const $modal = $('#bacAddItem');
            $modal.find('form')[0].reset();
            $modal.find('input[name="bac_id"]').val(bacId);
            initStockSelect2AddItem($modal);
            $modal.modal('show');
        });

    }

    function initAttachmentSelect2($modal) {
        return $modal.find('#bacAttachment').select2({
            dropdownParent: $modal,
            width: '100%',
            placeholder: "-- Select Attachment --",
            allowClear: true
        });
    }

    $(document).ready(function() {

        datatable();

        $('#btnAdd').on('click', function() {
            const $modal = $('#bacAddModal');
            const $form = $modal.find("form");

            if ($form.length > 0 && $form[0]) {
                $form[0].reset();
            }

            $form.find('select').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).val(null).trigger('change');
                }
            });

            $.when(
                initOfficeSelect2($modal),
                initStockSelect2($modal),
                initAttachmentSelect2($modal),
                initProcurementModeSelect2($modal)
            ).done(function() {
                $modal.modal("show");
            });
        });

        // MODAL ITEM
        const $bacAddItem = $('#bacAddItem');
        const $qty = $bacAddItem.find("#bacQuantity");
        const $unit = $bacAddItem.find("#bacUnitCost");
        const $total = $bacAddItem.find("#bacTotalCost");

        function calculateTotalCost() {
            const bacQty = parseFloat($qty.val()) || 0;
            const bacCost = parseFloat($unit.val()) || 0;
            const total = bacQty * bacCost;
            $total.val(total.toFixed(2));
        }

        $qty.add($unit).on("input change", calculateTotalCost);

        $bacAddItem.on('show.bs.modal', function() {
            $(this).find("form")[0].reset();
            $total.val('0.00');
        });

    });

    function initOfficeSelect2($modal, selectedId = null) {
        const $select = $modal.find('#bacOffice');

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        let deferred = $.Deferred();

        if (selectedId) {
            $.post(
                "<?php echo base_url('Libraries/getOfficeById'); ?>", {
                    office_id: selectedId
                },
                function(data) {
                    if (data) {
                        $select.empty();
                        const option = new Option(data.office_desc, data.office_id, true, true);
                        $select.append(option);
                    }
                    setupSelect2();
                    deferred.resolve();
                },
                'json'
            );
        } else {
            setupSelect2();
            deferred.resolve();
        }

        function setupSelect2() {
            $select.select2({
                dropdownParent: $modal,
                placeholder: "Search office...",
                width: '100%',
                minimumInputLength: 0,
                theme: 'bootstrap-4',
                ajax: {
                    url: "<?php echo base_url('Libraries/getOfficeList'); ?>",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: {
                                value: params.term || ''
                            },
                            start: 0,
                            length: 10,
                            draw: 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            })
                        };
                    }
                }
            });
        }

        return deferred.promise();
    }

    function initStockSelect2($modal, selectedId = null) {
        const $select = $modal.find('#bacStock');

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        let deferred = $.Deferred();

        if (selectedId) {
            $.post(
                "<?php echo base_url('Libraries/getStockById'); ?>", {
                    stock_id: selectedId
                },
                function(data) {
                    if (data) {
                        $select.empty();
                        const option = new Option(data.text, data.id, true, true);
                        $select.append(option);
                    }
                    setupSelect2();
                    deferred.resolve();
                },
                'json'
            );
        } else {
            setupSelect2();
            deferred.resolve();
        }

        function setupSelect2() {
            $select.select2({
                dropdownParent: $modal,
                placeholder: "Search stock...",
                width: '100%',
                minimumInputLength: 0,
                theme: 'bootstrap-4',
                ajax: {
                    url: "<?php echo base_url('Libraries/getStockList'); ?>",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: {
                                value: params.term || ''
                            },
                            start: 0,
                            length: 10,
                            draw: 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.item_description +
                                        " (" + item.unit_code +
                                        ") - Stock: " + item.stock_onhand
                                };
                            })
                        };
                    }
                }
            });
        }

        return deferred.promise();

    }

    function initProcurementModeSelect2($modal, selectedId = null) {
        const $select = $modal.find('#proc_id');

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        let deferred = $.Deferred();

        if (selectedId) {
            $.post(
                "<?php echo base_url('BAC/getProcurementModeById'); ?>", {
                    proc_id: selectedId
                },
                function(data) {
                    if (data) {
                        $select.empty();
                        const option = new Option(data.proc_name, data.proc_id, true, true);
                        $select.append(option);
                    }
                    setupSelect2();
                    deferred.resolve();
                },
                'json'
            );
        } else {
            setupSelect2();
            deferred.resolve();
        }

        function setupSelect2() {
            $select.select2({
                dropdownParent: $modal,
                placeholder: "Search procurement mode...",
                width: '100%',
                minimumInputLength: 0,
                theme: 'bootstrap-4',
                ajax: {
                    url: "<?php echo base_url('BAC/getProcurementModeList'); ?>",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.proc_id,
                                    text: item.proc_name
                                };
                            })
                        };
                    }
                }
            });
        }

        return deferred.promise();
    }

    function initStockSelect2AddItem($modal, selectedId = null) {
        const $select = $modal.find('#bacStock');

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        $select.select2({
            dropdownParent: $modal,
            placeholder: "Search item...",
            width: '100%',
            minimumInputLength: 0,
            theme: 'bootstrap-4',
            ajax: {
                url: "<?php echo base_url('Libraries/getStockList'); ?>",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term || '',
                        start: 0,
                        length: 10
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.data.map(item => ({
                            id: item.id,
                            text: item.item_description + " (" + item.unit_code + ") - Stock: " + item.stock_onhand
                        }))
                    };
                }
            }
        });

        if (selectedId) {
            $.post("<?php echo base_url('Libraries/getStockById'); ?>", {
                stock_id: selectedId
            }, function(data) {
                if (data) {
                    const option = new Option(data.text, data.id, true, true);
                    $select.append(option).trigger('change');
                }
            }, 'json');
        }
    }
</script>

<style>
    /* Main Select2 box */
    .select2-container--bootstrap-4 .select2-selection {
        height: 38px;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    /* Text inside main box */
    .select2-container--bootstrap-4 .select2-selection__rendered {
        line-height: 28px;
    }

    /* Arrow on the right */
    .select2-container--bootstrap-4 .select2-selection__arrow {
        height: 36px;
        right: 10px;
    }

    /* The dropdown search box */
    .select2-container--bootstrap-4 .select2-search--dropdown .select2-search__field {
        height: 38px;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ced4da;
        width: 100%;
        box-sizing: border-box;
    }

    /* Dropdown search box focus */
    .select2-container--bootstrap-4 .select2-search--dropdown .select2-search__field:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        outline: 0;
    }

    /* Dropdown options default */
    .select2-container--bootstrap-4 .select2-results__option {
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        cursor: pointer;
    }

    /* Hover effect */
    .select2-container--bootstrap-4 .select2-results__option--highlighted {
        background-color: #f8f9fa;
        color: #212529;
    }

    /* Selected option */
    .select2-container--bootstrap-4 .select2-results__option[aria-selected="true"] {
        background-color: #e9ecef;
        color: #212529;
    }

    /* Optional: smooth transition */
    .select2-container--bootstrap-4 .select2-results__option {
        transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
    }
</style>