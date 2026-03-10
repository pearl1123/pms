<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Purchase Request List</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btnAdd">
            <i class="fas fa-plus fa-sm text-white-50"></i> Create Purchase Request</button>
    </div>

    <hr />

    <!-- BREADCRUMBS
    ========================================================================================================================================= -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchase Request List</li>
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
            <h6 class="m-0 font-weight-bold text-primary">Purchase Request List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tblPR" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>PR No.</th>
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
                            <th>PR No.</th>
                            <th>SAI No.</th>
                            <th>Procurement Mode</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Encoded By</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody id="prBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php $this->load->view('PurchaseRequest/modal_add'); ?>
    <?php $this->load->view('PurchaseRequest/modal_item'); ?>
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
    var tblPR;

    function datatable() {
        var tblPR = $('#tblPR').DataTable({
            "responsive": true,
            "autoWidth": false,
            'serverSide': true,
            'processing': true,
            'paging': true,
            "lengthMenu": [10, 15, 20],
            "ajax": {
                url: "<?php echo base_url("purchaserequest/getPRList/"); ?>",
                type: 'GET'
            },
            "language": {
                "emptyTable": "No Results"
            },
            columns: [{
                    data: 'id',
                    name: 'pr_id'
                },
                {
                    data: 'pr_no',
                    name: 'pr_no'
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
                // { data: 'pr_date', name: 'pr_date'},
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

        $('#prBody').on('click', '#btnUpdate', function() {
            var data = ($(this).parents('tr').hasClass('child')) ?
                tblPR.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                :
                tblPR.row($(this).parents('tr')).data(); // otherwise get original data

            const $prUpdateModal = $('#prUpdateModal');
            $.ajax({
                url: "<?php echo base_url('PurchaseRequest/getPR'); ?>",
                type: "POST",
                dataType: "json",
                data: {
                    pr_id: data.id
                },
                success: function(res) {
                    $prUpdateModal.find('#prId').val(res.pr_id || '');
                    $prUpdateModal.find('#prNumber').val(res.pr_no || '');
                    $prUpdateModal.find('#saiNumber').val(res.sai_no || '');
                    $prUpdateModal.find('#prQuantity').val(res.quantity || '').trigger('input');
                    $prUpdateModal.find('#prUnitCost').val(res.unit_cost || '').trigger('input');
                    $prUpdateModal.find('#prRemarks').val(res.remarks || '');
                    $prUpdateModal.find('#prRequestedBy').val(res.requested_by || '');
                    $prUpdateModal.find('#prDesignation').val(res.designation || '');

                    $.when(
                        initOfficeSelect2($prUpdateModal, res.office_id),
                        initStockSelect2($prUpdateModal, res.stock_id),
                        initProcurementModeSelect2($prUpdateModal, res.stock_id),
                        initProcurementModeSelect2($prUpdateModal, res.proc_id),
                    ).done(function() {
                        $prUpdateModal.modal('show');
                    });
                },
                error: function(err) {
                    console.error(err);
                }
            })
        });

        $('#prBody').on('click', '#btnDel', function() {
            var data = ($(this).parents('tr').hasClass('child')) ?
                tblPR.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                :
                tblPR.row($(this).parents('tr')).data(); // otherwise get original data
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
                        url: "<?php echo base_url('PurchaseRequest/deletePR'); ?>",
                        async: false,
                        type: "POST",
                        datatype: "json",
                        data: {
                            pr_id: id
                        },
                        success: function(data) {
                            Swal.fire({
                                title: 'Success',
                                html: "<span class = 'text-success'><b>SUCCESS!</b></span> Successfully deleted Purchase Request.",
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

        $('#tblPR').on('click', '#btnAttachment', function() {

            var pr_id = $(this).data('prid'); // read from button

            const $modal = $('#prAttachmentModal');

            $modal.find('form')[0].reset();
            $modal.find('#pr_id').val(pr_id);

            $modal.modal('show');

        });

        $(document).on('click', '#btnAddItem', function() {
            const prId = $(this).data('prid');
            const $modal = $('#prAddItem');
            $modal.find('form')[0].reset();
            $modal.find('input[name="pr_id"]').val(prId);
            initStockSelect2AddItem($modal);
            $modal.modal('show');
        });

    }

    function initAttachmentSelect2($modal) {
        return $modal.find('#prAttachment').select2({
            dropdownParent: $modal, // Important inside modal
            width: '100%',
            placeholder: "-- Select Attachment --",
            allowClear: true
        });
    }

    $(document).ready(function() {

        datatable();

        $('#btnAdd').on('click', function() {
            const $modal = $('#prAddModal');
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
        const $prAddItem = $('#prAddItem');
        const $qty = $prAddItem.find("#prQuantity");
        const $unit = $prAddItem.find("#prUnitCost");
        const $total = $prAddItem.find("#prTotalCost");

        function calculateTotalCost() {
            const prQty = parseFloat($qty.val()) || 0;
            const prCost = parseFloat($unit.val()) || 0;
            const total = prQty * prCost;
            $total.val(total.toFixed(2));
        }

        $qty.add($unit).on("input change", calculateTotalCost);

        $prAddItem.on('show.bs.modal', function() {
            $(this).find("form")[0].reset();
            $total.val('0.00');
        });

    });

    function initOfficeSelect2($modal, selectedId = null) {
        const $select = $modal.find('#prOffice');

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
        const $select = $modal.find('#prStock');

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

        // Destroy existing Select2 if already initialized
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        let deferred = $.Deferred();

        // If a selected ID is provided, load it first
        if (selectedId) {
            $.post(
                "<?php echo base_url('PurchaseRequest/getProcurementModeById'); ?>", {
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
                    url: "<?php echo base_url('PurchaseRequest/getProcurementModeList'); ?>",
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
        const $select = $modal.find('#prStock');

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
        /* match your input height */
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    /* Text inside main box */
    .select2-container--bootstrap-4 .select2-selection__rendered {
        line-height: 28px;
        /* vertically center text */
    }

    /* Arrow on the right */
    .select2-container--bootstrap-4 .select2-selection__arrow {
        height: 36px;
        right: 10px;
    }

    /* The dropdown search box */
    .select2-container--bootstrap-4 .select2-search--dropdown .select2-search__field {
        height: 38px;
        /* match your inputs */
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border: 1px solid #ced4da;
        /* border-radius: 0.375rem; */
        width: 100%;
        /* make sure it fills dropdown */
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
        /* match Bootstrap input padding */
        font-size: 1rem;
        line-height: 1.5;
        cursor: pointer;
    }

    /* Hover effect */
    .select2-container--bootstrap-4 .select2-results__option--highlighted {
        background-color: #f8f9fa;
        /* light gray like Bootstrap hover */
        color: #212529;
        /* default text color */
    }

    /* Selected option */
    .select2-container--bootstrap-4 .select2-results__option[aria-selected="true"] {
        background-color: #e9ecef;
        /* slightly darker gray */
        color: #212529;
    }

    /* Optional: smooth transition */
    .select2-container--bootstrap-4 .select2-results__option {
        transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
    }
</style>