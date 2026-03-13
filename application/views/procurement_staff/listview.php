<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Purchase Request Review</h1>
        <span class="badge badge-info px-3 py-2" style="font-size: 0.85rem;">
            <i class="fas fa-user-tie mr-1"></i> Procurement Staff
        </span>
    </div>

    <hr />

    <!-- BREADCRUMBS
    ========================================================================================================================================= -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchase Request Review</li>
        </ol>
    </nav>

    <!-- ALERTS
    ========================================================================================================================================= -->
    <?php if ($this->session->flashdata('fail') <> null) { ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
            <span><?php echo $this->session->flashdata('fail'); ?></span>
        </div>
    <?php } ?>
    <?php if ($this->session->flashdata('success') <> null) { ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
            <span><?php echo $this->session->flashdata('success'); ?></span>
        </div>
    <?php } ?>

    <!-- SUMMARY CARDS
    ========================================================================================================================================= -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Review</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="countPending">—</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="countApproved">—</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rejected</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="countRejected">—</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sent</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="countTotal">—</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN TABLE
    ========================================================================================================================================= -->
    <div class="shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-1"></i> Purchase Requests for Review
            </h6>
            <div class="d-flex align-items-center">
                <label class="mr-2 mb-0 text-sm text-gray-600">Filter by Status:</label>
                <select id="statusFilter" class="form-control form-control-sm" style="width: 160px;">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tblPRReview" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>preq_id</th>
                            <th>pr_id</th>
                            <th>PR No.</th>
                            <th>SAI No.</th>
                            <th>Procurement Mode</th>
                            <th>Department</th>
                            <th>Attachments</th>
                            <th>Encoded By</th>
                            <th>Date Sent</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>preq_id</th>
                            <th>pr_id</th>
                            <th>PR No.</th>
                            <th>SAI No.</th>
                            <th>Procurement Mode</th>
                            <th>Department</th>
                            <th>Attachments</th>
                            <th>Encoded By</th>
                            <th>Date Sent</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                    <tbody id="prReviewBody"></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- =========================================================================================================================================
     REVIEW MODAL — Full PR details + items + attachments + approve / reject
========================================================================================================================================= -->
<div class="modal fade" id="prReviewModal" tabindex="-1" role="dialog" aria-labelledby="prReviewModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="prReviewModalLabel">
                    <i class="fas fa-file-alt mr-2"></i> Purchase Request Review
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- PR INFO -->
                <div class="card mb-3">
                    <div class="card-header py-2 bg-light">
                        <strong><i class="fas fa-info-circle mr-1 text-primary"></i> PR Information</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">PR No.</label>
                                <p id="reviewPrNo" class="font-weight-bold mb-1">—</p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">SAI No.</label>
                                <p id="reviewSaiNo" class="font-weight-bold mb-1">—</p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">Procurement Mode</label>
                                <p id="reviewProcName" class="font-weight-bold mb-1">—</p>
                            </div>
                            <div class="col-md-3">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">Department</label>
                                <p id="reviewOffice" class="font-weight-bold mb-1">—</p>
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">Requested By</label>
                                <p id="reviewRequestedBy" class="mb-1">—</p>
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">Designation</label>
                                <p id="reviewDesignation" class="mb-1">—</p>
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">Encoded By</label>
                                <p id="reviewEncodedBy" class="mb-1">—</p>
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">Date Sent</label>
                                <p id="reviewDateCreated" class="mb-1">—</p>
                            </div>
                            <div class="col-md-3 mt-2">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">Status</label>
                                <p id="reviewStatus" class="mb-1">—</p>
                            </div>
                            <div class="col-md-9 mt-2">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">Remarks</label>
                                <p id="reviewRemarks" class="mb-1">—</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ITEMS TABLE -->
                <div class="card mb-3">
                    <div class="card-header py-2 bg-light">
                        <strong><i class="fas fa-shopping-cart mr-1 text-info"></i> PR Items</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Item Description</th>
                                        <th>Unit</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Unit Cost</th>
                                        <th class="text-right">Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody id="reviewItemsBody">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No items found.</td>
                                    </tr>
                                </tbody>
                                <tfoot id="reviewItemsFoot"></tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ATTACHMENTS TABLE -->
                <div class="card mb-3">
                    <div class="card-header py-2 bg-light">
                        <strong><i class="fas fa-paperclip mr-1 text-warning"></i> Attachments</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Attachment Name</th>
                                        <th class="text-center">Required</th>
                                        <th>File</th>
                                        <th>Remarks</th>
                                        <th class="text-center">Upload Status</th>
                                    </tr>
                                </thead>
                                <tbody id="reviewAttachmentsBody">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No attachments found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- STAFF REMARKS -->
                <div class="card">
                    <div class="card-header py-2 bg-light">
                        <strong><i class="fas fa-comment-alt mr-1 text-secondary"></i> Staff Remarks / Notes</strong>
                        <small class="text-danger ml-1">* Required when rejecting</small>
                    </div>
                    <div class="card-body">
                        <textarea id="staffRemarks" class="form-control" rows="3"
                            placeholder="Enter remarks or notes for this PR review..."></textarea>
                    </div>
                </div>

            </div><!-- /.modal-body -->

            <div class="modal-footer">
                <input type="hidden" id="reviewPrId">
                <input type="hidden" id="reviewPreqId">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
                <button type="button" class="btn btn-danger" id="btnRejectModal">
                    <i class="fas fa-times-circle mr-1"></i> Reject
                </button>
                <button type="button" class="btn btn-success" id="btnApproveModal">
                    <i class="fas fa-check-circle mr-1"></i> Approve
                </button>
            </div>

        </div>
    </div>
</div>

<!-- =========================================================================================================================================
     ATTACHMENTS QUICK-VIEW MODAL — opened via View Attachments button in row
========================================================================================================================================= -->
<div class="modal fade" id="prAttachmentViewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">
                    <i class="fas fa-paperclip mr-2"></i> Attachments
                    <small id="attachViewPrNo" class="ml-2 font-weight-normal"></small>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Attachment Name</th>
                                <th class="text-center">Required</th>
                                <th>File</th>
                                <th>Remarks</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="attachViewBody">
                            <tr>
                                <td colspan="6" class="text-center text-muted">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
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
    var tblPRReview;

    /* -----------------------------------------------------------------------
       HELPERS
    ----------------------------------------------------------------------- */
    function statusBadge(status) {
        var s = (status || '').toLowerCase();
        if (s === 'pending') return '<span class="badge badge-warning  px-2 py-1">Pending</span>';
        if (s === 'approved') return '<span class="badge badge-success  px-2 py-1">Approved</span>';
        if (s === 'rejected') return '<span class="badge badge-danger   px-2 py-1">Rejected</span>';
        return '<span class="badge badge-secondary px-2 py-1">' + (status || '—') + '</span>';
    }

    function formatDate(dateStr) {
        if (!dateStr) return '—';
        var d = new Date(dateStr);
        return d.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: '2-digit'
        });
    }

    function formatCurrency(val) {
        return '&#8369; ' + parseFloat(val || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    /* -----------------------------------------------------------------------
       BUILD ATTACHMENT ROWS — reused in both modals
    ----------------------------------------------------------------------- */
    function buildAttachmentRows(attachments) {
        if (!attachments || attachments.length === 0) {
            return '<tr><td colspan="6" class="text-center text-muted">No attachments found.</td></tr>';
        }

        var html = '';
        $.each(attachments, function(i, att) {
            var required = att.required == 1 ?
                '<span class="badge badge-danger">Required</span>' :
                '<span class="badge badge-secondary">Optional</span>';

            var fileLink = att.file_name ?
                '<a href="<?php echo base_url("assets/uploads/pr_attachments/"); ?>' +
                att.file_name + '" target="_blank" ' +
                'class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:0.78rem;">' +
                '<i class="fas fa-eye mr-1"></i>' +
                (att.original_file_name || att.file_name) + '</a>' :
                '<span class="text-muted">' +
                '<i class="fas fa-times-circle text-danger mr-1"></i>Not uploaded</span>';

            var uploadStatus = att.file_name ?
                '<span class="badge badge-success">Uploaded</span>' :
                '<span class="badge badge-secondary">Missing</span>';

            html += '<tr>' +
                '<td>' + (i + 1) + '</td>' +
                '<td>' + (att.attachment_name || '—') + '</td>' +
                '<td class="text-center">' + required + '</td>' +
                '<td>' + fileLink + '</td>' +
                '<td>' + (att.remarks || '—') + '</td>' +
                '<td class="text-center">' + uploadStatus + '</td>' +
                '</tr>';
        });
        return html;
    }

    /* -----------------------------------------------------------------------
       SUMMARY CARDS
    ----------------------------------------------------------------------- */
    function loadSummaryCards() {
        $.ajax({
            url: "<?php echo base_url('ProcurementStaff/getReviewSummary'); ?>",
            type: "GET",
            dataType: "json",
            success: function(res) {
                $('#countPending').text(res.pending || 0);
                $('#countApproved').text(res.approved || 0);
                $('#countRejected').text(res.rejected || 0);
                $('#countTotal').text(res.total || 0);
            }
        });
    }

    /* -----------------------------------------------------------------------
       UPDATE REVIEW STATUS — shared by row buttons and modal buttons
       fromModal = true  → close the review modal after success
    ----------------------------------------------------------------------- */
    function updateReviewStatus(pr_id, preq_id, status, remarks, fromModal) {
        $.ajax({
            url: "<?php echo base_url('ProcurementStaff/updateReviewStatus'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                pr_id: pr_id,
                preq_id: preq_id,
                status: status,
                remarks: remarks
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        title: status === 'approved' ? 'Approved!' : 'Rejected!',
                        html: "<span class='text-" +
                            (status === 'approved' ? 'success' : 'danger') +
                            "'><b>SUCCESS!</b></span> " + res.message,
                        type: 'success',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        if (fromModal) $('#prReviewModal').modal('hide');
                        tblPRReview.ajax.reload();
                        loadSummaryCards();
                    });
                } else {
                    Swal.fire('Error', res.message || 'An error occurred.', 'error');
                }
            },
            error: function(err) {
                console.error(err);
                Swal.fire('Error', 'An unexpected error occurred.', 'error');
            }
        });
    }

    /* -----------------------------------------------------------------------
       DATATABLE
    ----------------------------------------------------------------------- */
    function datatable() {

        tblPRReview = $('#tblPRReview').DataTable({
            "responsive": true,
            "autoWidth": false,
            "serverSide": true,
            "processing": true,
            "paging": true,
            "lengthMenu": [10, 15, 20],
            "ajax": {
                url: "<?php echo base_url('ProcurementStaff/getReviewList'); ?>",
                type: "GET",
                data: function(d) {
                    d.status_filter = $('#statusFilter').val();
                }
            },
            "language": {
                "emptyTable": "No Purchase Requests found.",
                "processing": '<i class="fas fa-spinner fa-spin mr-1"></i> Loading...'
            },
            columns: [{
                    data: 'preq_id',
                    name: 'preq_id'
                }, // [0] hidden
                {
                    data: 'pr_id',
                    name: 'pr_id'
                }, // [1] hidden
                {
                    data: 'pr_no',
                    name: 'pr_no'
                }, // [2]
                {
                    data: 'sai_no',
                    name: 'sai_no',
                    defaultContent: '—'
                }, // [3]
                {
                    data: 'proc_name',
                    name: 'proc_name',
                    defaultContent: '—'
                }, // [4]
                {
                    data: 'office_desc',
                    name: 'office_desc',
                    defaultContent: '—'
                }, // [5]
                { // [6] attachments pill summary
                    data: 'attachment_summary',
                    name: 'attachment_summary',
                    orderable: false,
                    defaultContent: '<span class="text-muted">—</span>'
                },
                {
                    data: 'encoded_by',
                    name: 'encoded_by',
                    defaultContent: '—'
                }, // [7]
                { // [8] date
                    data: 'date_created',
                    name: 'date_created',
                    render: function(data) {
                        return formatDate(data);
                    }
                },
                { // [9] status
                    data: 'status',
                    name: 'status',
                    render: function(data) {
                        return statusBadge(data);
                    }
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false
                } // [10]
            ],
            columnDefs: [{
                targets: [0, 1],
                visible: false,
                orderable: false
            }],
            order: [
                [8, 'desc']
            ]
        });

        /* Re-draw table when status filter changes */
        $('#statusFilter').on('change', function() {
            tblPRReview.ajax.reload();
        });

        /* ---- helper: get DataTable row data (handles responsive child rows) ---- */
        function getRowData($btn) {
            var $tr = $btn.closest('tr');
            if ($tr.hasClass('child')) $tr = $tr.prev();
            return tblPRReview.row($tr).data();
        }

        /* ====================================================================
           REVIEW BUTTON — opens full Review Modal with PR info + items + attachments
        ==================================================================== */
        $('#prReviewBody').on('click', '.btnReview', function() {
            var row = tblPRReview.row($(this).closest('tr')).data();
            if (!row) {
                row = tblPRReview.row($(this).closest('tr').prev()).data();
            }

            // Redirect to the review page instead of opening a modal
            window.location.href = "<?php echo base_url('ProcurementStaff/review'); ?>/" +
                row.pr_id + '/' + row.preq_id;
        });

        /* ====================================================================
           APPROVE BUTTON (row action) — quick approve without opening the modal
        ==================================================================== */
        $('#prReviewBody').on('click', '.btnApprove', function() {
            var row = getRowData($(this));
            if (!row) return;

            Swal.fire({
                title: 'Approve this PR?',
                html: 'You are about to approve <b>PR No. ' + row.pr_no + '</b>.',
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Approve!'
            }).then(function(result) {
                if (result.value) {
                    updateReviewStatus(row.pr_id, row.preq_id, 'approved', '', false);
                }
            });
        });

        /* ====================================================================
           REJECT BUTTON (row action) — prompts for remarks via SweetAlert input
        ==================================================================== */
        $('#prReviewBody').on('click', '.btnReject', function() {
            var row = getRowData($(this));
            if (!row) return;

            Swal.fire({
                title: 'Reject this PR?',
                html: 'Please provide a reason for rejecting <b>PR No. ' + row.pr_no + '</b>:',
                input: 'textarea',
                inputPlaceholder: 'Enter reason here...',
                inputAttributes: {
                    'aria-label': 'Rejection reason'
                },
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Reject!',
                preConfirm: function(val) {
                    if (!val || !val.trim()) {
                        Swal.showValidationMessage('Remarks are required when rejecting a PR.');
                    }
                    return val;
                }
            }).then(function(result) {
                if (result.value) {
                    updateReviewStatus(row.pr_id, row.preq_id, 'rejected', result.value, false);
                }
            });
        });

        /* ====================================================================
           VIEW ATTACHMENTS BUTTON (row action) — opens lightweight attachment modal
        ==================================================================== */
        $('#prReviewBody').on('click', '.btnViewAttachments', function() {
            var row = getRowData($(this));
            if (!row) return;

            $('#attachViewPrNo').text('— PR No. ' + row.pr_no);
            $('#attachViewBody').html(
                '<tr><td colspan="6" class="text-center text-muted">' +
                '<i class="fas fa-spinner fa-spin mr-1"></i>Loading...</td></tr>');

            $('#prAttachmentViewModal').modal('show');

            $.ajax({
                url: "<?php echo base_url('ProcurementStaff/getPRAttachmentsByPr'); ?>",
                type: "POST",
                dataType: "json",
                data: {
                    pr_id: row.pr_id
                },
                success: function(res) {
                    $('#attachViewBody').html(buildAttachmentRows(res));
                },
                error: function(err) {
                    console.error(err);
                    $('#attachViewBody').html(
                        '<tr><td colspan="6" class="text-center text-danger">' +
                        'Failed to load attachments.</td></tr>');
                }
            });
        });

        /* ====================================================================
           APPROVE / REJECT from inside the Review Modal
        ==================================================================== */
        $('#btnApproveModal').on('click', function() {
            var pr_id = $('#reviewPrId').val();
            var preq_id = $('#reviewPreqId').val();
            var remarks = $('#staffRemarks').val();

            Swal.fire({
                title: 'Approve this PR?',
                html: "This will mark the Purchase Request as <b class='text-success'>Approved</b>.",
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Approve!'
            }).then(function(result) {
                if (result.value) {
                    updateReviewStatus(pr_id, preq_id, 'approved', remarks, true);
                }
            });
        });

        $('#btnRejectModal').on('click', function() {
            var pr_id = $('#reviewPrId').val();
            var preq_id = $('#reviewPreqId').val();
            var remarks = $('#staffRemarks').val();

            if (!remarks.trim()) {
                Swal.fire('Remarks Required',
                    'Please enter a reason for rejecting this PR before submitting.',
                    'warning');
                $('#staffRemarks').focus();
                return;
            }

            Swal.fire({
                title: 'Reject this PR?',
                html: "This will mark the Purchase Request as <b class='text-danger'>Rejected</b>.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Reject!'
            }).then(function(result) {
                if (result.value) {
                    updateReviewStatus(pr_id, preq_id, 'rejected', remarks, true);
                }
            });
        });

    } // end datatable()

    /* -----------------------------------------------------------------------
       INIT
    ----------------------------------------------------------------------- */
    $(document).ready(function() {
        datatable();
        loadSummaryCards();
    });
</script>

<style>
    /* Card hover lift */
    .card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    /* Badge text */
    .badge {
        font-size: 0.75rem;
        letter-spacing: 0.02em;
    }

    /* Tiny label */
    .text-xs {
        font-size: 0.70rem;
    }


    /* Grand total row separator */
    #reviewItemsFoot tr td {
        border-top: 2px solid #dee2e6;
    }

    /* Modal close button */
    .modal-header .close {
        opacity: 0.85;
    }

    .modal-header .close:hover {
        opacity: 1;
    }
</style>