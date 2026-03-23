<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bids and Awards Review</h1>

        <!-- <div class="d-flex align-items-center">
            <button type="button" class="btn btn-primary btn-sm mr-2 shadow-sm" data-toggle="modal" data-target="#uploadSignatureModal">
                <i class="fas fa-upload fa-sm text-white-50 mr-1"></i> Upload Signature
            </button> -->

        <span class="badge badge-info px-3 py-2" style="font-size: 0.85rem;">
            <i class="fas fa-file-contract mr-1"></i> BAC Secretariat
        </span>
    </div>
</div>

<div class="modal fade" id="uploadSignatureModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-pen-nib mr-2"></i>Upload BAC Attachment
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="<?php echo base_url('BAC/uploadSignature'); ?>" method="POST" enctype="multipart/form-data">
                <?php echo $csrf; ?>

                <div class="modal-body">
                    <input type="hidden" name="pr_id" id="sig_pr_id">
                    <input type="hidden" name="proc_id" id="sig_proc_id">

                    <div class="alert alert-info py-2 small">
                        <i class="fas fa-info-circle mr-1"></i> Uploading BAC attachment for: <strong><?php echo $fullname; ?></strong>
                    </div>

                    <div class="form-group mt-4">
                        <label class="small font-weight-bold text-gray-700">Select Attachment File</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="signatureFile" name="signature" accept="image/png, image/jpeg" required>
                            <label class="custom-file-label" for="signatureFile">Choose file...</label>
                        </div>
                    </div>

                    <div id="preview-container" class="mt-4 text-center d-none">
                        <div class="small font-weight-bold text-primary mb-2">Attachment Preview:</div>
                        <img id="sig-preview" src="#" alt="Attachment Preview" style="max-height: 100px; border: 2px dashed #d1d3e2; padding: 10px; border-radius: 8px; background-color: #f8f9fc;">
                    </div>
                </div>

                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                        <i class="fas fa-upload mr-1"></i> Upload Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<hr />

<!-- BREADCRUMBS
    ========================================================================================================================================= -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Bids and Awards Review</li>
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
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Submitted</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="countTotal">—</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-contract fa-2x text-gray-300"></i>
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
            <i class="fas fa-list mr-1"></i> Bids and Awards for Review
        </h6>
        <div class="d-flex align-items-center">
            <label class="mr-2 mb-0 text-sm text-gray-600">Filter by Status:</label>
            <select id="statusFilter" class="form-control form-control-sm" style="width: 160px;">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">BAC Approved</option>
                <option value="BAC Rejected">BAC Rejected</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tblBACReview" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>bac_id</th>
                        <th>PR</th>
                        <th>SAI</th>
                        <th>Procurement Mode</th>
                        <th>Department</th>
                        <th>Attachments</th>
                        <th>Encoded By</th>
                        <th>Date Created</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>bac_id</th>
                        <th>PR</th>
                        <th>SAI</th>
                        <th>Procurement Mode</th>
                        <th>Department</th>
                        <th>Attachments</th>
                        <th>Encoded By</th>
                        <th>Date Created</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>
                <tbody id="bacReviewBody"></tbody>
            </table>
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
<!-- =========================================================================================================================================
     REVIEW MODAL — Full BAC details + items + attachments + approve / reject
========================================================================================================================================= -->
<div class="modal fade" id="bacReviewModal" tabindex="-1" role="dialog" aria-labelledby="bacReviewModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bacReviewModalLabel">
                    <i class="fas fa-file-contract mr-2"></i> Bids and Awards Review
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- BAC INFO -->
                <div class="card mb-3">
                    <div class="card-header py-2 bg-light">
                        <strong><i class="fas fa-info-circle mr-1 text-primary"></i> BAC Information</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">BAC No.</label>
                                <p id="reviewBacNo" class="font-weight-bold mb-1">—</p>
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
                                <label class="text-xs font-weight-bold text-uppercase text-muted">Date Created</label>
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
                        <strong><i class="fas fa-boxes mr-1 text-info"></i> BAC Items</strong>
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
                    <div class="card-header py-2 bg-light d-flex align-items-center">
                        <strong><i class="fas fa-paperclip mr-2 text-warning"></i> Attachments</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light text-nowrap">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Attachment Name</th>
                                        <th class="text-center">Required</th>
                                        <th>File</th>
                                        <th>Approved Files</th>
                                        <th class="text-center">Upload Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="reviewAttachmentsBody">
                                    <tr>
                                        <td>1</td>
                                        <td class="text-muted">Technical Specifications</td>
                                        <td class="text-center">
                                            <span class="badge badge-danger">Required</span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> Center Order No_ 661...
                                            </a>
                                        </td>
                                        <td class="text-center">---</td>
                                        <td class="text-center">
                                            <span class="badge badge-success">Uploaded</span>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- SECRETARIAT REMARKS -->
                <div class="card">
                    <div class="card-header py-2 bg-light">
                        <strong><i class="fas fa-comment-alt mr-1 text-secondary"></i> Secretariat Remarks / Notes</strong>
                        <small class="text-danger ml-1">* Required when rejecting</small>
                    </div>
                    <div class="card-body">
                        <textarea id="secretariatRemarks" class="form-control" rows="3"
                            placeholder="Enter remarks or notes for this BAC review..."></textarea>
                    </div>
                </div>

            </div><!-- /.modal-body -->

            <div class="modal-footer">
                <input type="hidden" id="reviewBacId">
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
<div class="modal fade" id="bacAttachmentViewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">
                    <i class="fas fa-paperclip mr-2"></i> Attachments
                    <small id="attachViewBacNo" class="ml-2 font-weight-normal"></small>
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
    var tblBACReview;

    /* -----------------------------------------------------------------------
       HELPERS
    ----------------------------------------------------------------------- */
    function statusBadge(status) {
        var s = (status || '').toLowerCase();
        if (s === 'pending') return '<span class="badge badge-warning px-2 py-1">Pending</span>';
        if (s === 'approved') return '<span class="badge badge-success px-2 py-1">Approved</span>';
        if (s === 'bac approved') return '<span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i>BAC Approved</span>'; // ← dagdag
        if (s === 'rejected') return '<span class="badge badge-danger px-2 py-1">Rejected</span>';
        if (s === 'bac rejected') return '<span class="badge badge-danger px-2 py-1"><i class="fas fa-ban mr-1"></i>BAC Rejected</span>'; // ← dagdag
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

    /* BUILD ATTACHMENT ROWS — Reusable function for modals */
    function buildAttachmentRows(attachments) {
        if (!attachments || attachments.length === 0) {
            return '<tr><td colspan="6" class="text-center text-muted">No attachments found.</td></tr>';
        }

        var html = '';
        $.each(attachments, function(i, att) {
            var required = att.required == 1 ?
                '<span class="badge badge-danger">Required</span>' :
                '<span class="badge badge-secondary">Optional</span>';

            // Pointing to the PR attachments folder
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
            url: "<?php echo base_url('BAC/getReviewSummary'); ?>",
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
       UPDATE REVIEW STATUS
    ----------------------------------------------------------------------- */
    function updateReviewStatus(bac_id, status, remarks, fromModal) {
        $.ajax({
            url: "<?php echo base_url('BAC/updateReviewStatus'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                bac_id: bac_id,
                status: status,
                remarks: remarks
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        title: status === 'approved' ? 'Approved!' : 'Rejected!',
                        html: "<span class='text-" + (status === 'approved' ? 'success' : 'danger') + "'><b>SUCCESS!</b></span> " + res.message,
                        type: 'success',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        if (fromModal) $('#bacReviewModal').modal('hide');
                        tblBACReview.ajax.reload();
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
        tblBACReview = $('#tblBACReview').DataTable({
            "responsive": true,
            "autoWidth": false,
            "serverSide": true,
            "processing": true,
            "ajax": {
                url: "<?php echo base_url('BAC/getReviewList'); ?>",
                type: "GET",
                data: function(d) {
                    d.status_filter = $('#statusFilter').val();
                }
            },
            "language": {
                "emptyTable": "No records found.",
                "processing": '<i class="fas fa-spinner fa-spin mr-1"></i> Loading...'
            },
            columns: [{
                    data: 'bac_id',
                    name: 'bac_id'
                }, // [0] Hidden
                {
                    data: 'bac_no',
                    name: 'bac_no'
                }, // [1] PR
                {
                    data: 'sai_no',
                    name: 'sai_no',
                    defaultContent: '—'
                }, // [2] SAI
                {
                    data: 'proc_name',
                    name: 'proc_name',
                    defaultContent: '—'
                }, // [3] Procurement Mode
                {
                    data: 'office_desc',
                    name: 'office_desc',
                    defaultContent: '—'
                }, // [4] Department
                {
                    data: 'attachment_summary',
                    name: 'attachment_summary',
                    orderable: false
                }, // [5] Status Icon
                {
                    data: 'encoded_by',
                    name: 'encoded_by',
                    defaultContent: '—'
                }, // [6] Personnel
                {
                    data: 'date_created',
                    name: 'date_created',
                    render: function(data) {
                        return formatDate(data);
                    }
                }, // [7] Date
                {
                    data: 'status',
                    name: 'status',
                    render: function(data) {
                        return statusBadge(data);
                    }
                }, // [8] Result (Badge)
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false
                } // [9] Actions
            ],
            columnDefs: [{
                targets: [0],
                visible: false,
                orderable: false
            }],
            order: [
                [7, 'desc']
            ]
        });

        $('#statusFilter').on('change', function() {
            tblBACReview.ajax.reload();
        });

        function getRowData($btn) {
            var $tr = $btn.closest('tr');
            if ($tr.hasClass('child')) $tr = $tr.prev();
            return tblBACReview.row($tr).data();
        }

        /* REVIEW BUTTON */
        $('#bacReviewBody').on('click', '.btnReview', function() {
            var row = getRowData($(this));
            if (!row) return;

            $('#reviewBacId').val(row.bac_id);
            $('#reviewBacNo').text(row.bac_no || '—');
            $('#reviewSaiNo').text(row.sai_no || '—');
            $('#reviewProcName').text(row.proc_name || '—');
            $('#reviewOffice').text(row.office_desc || '—');
            $('#reviewDateCreated').text(formatDate(row.date_created));
            $('#reviewStatus').html(statusBadge(row.status));
            $('#secretariatRemarks').val('');

            // Load Items
            $('#reviewItemsBody').html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i></td></tr>');
            $.ajax({
                url: "<?php echo base_url('BAC/getBACItems'); ?>",
                type: "POST",
                dataType: "json",
                data: {
                    bac_id: row.bac_id
                },
                success: function(items) {
                    var html = '';
                    var grandTotal = 0;
                    if (items.length > 0) {
                        $.each(items, function(i, item) {
                            var total = parseFloat(item.quantity || 0) * parseFloat(item.unit_cost || 0);
                            grandTotal += total;
                            html += '<tr><td>' + (i + 1) + '</td><td>' + item.item_description + '</td><td>' + item.unit_code + '</td><td class="text-right">' + item.quantity + '</td><td class="text-right">' + formatCurrency(item.unit_cost) + '</td><td class="text-right">' + formatCurrency(total) + '</td></tr>';
                        });
                        $('#reviewItemsBody').html(html);
                        $('#reviewItemsFoot').html('<tr><td colspan="5" class="text-right"><b>Grand Total:</b></td><td class="text-right text-primary"><b>' + formatCurrency(grandTotal) + '</b></td></tr>');
                    } else {
                        $('#reviewItemsBody').html('<tr><td colspan="6" class="text-center">No items found.</td></tr>');
                    }
                }
            });

            // Load Attachments
            $('#reviewAttachmentsBody').html('<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i></td></tr>');
            $.ajax({
                url: "<?php echo base_url('BAC/getBACAttachments'); ?>",
                type: "POST",
                dataType: "json",
                data: {
                    bac_id: row.bac_id
                },
                success: function(attachments) {
                    $('#reviewAttachmentsBody').html(buildAttachmentRows(attachments));
                }
            });

            $('#bacReviewModal').modal('show');
        });

        /* ROW APPROVE */
        $('#bacReviewBody').on('click', '.btnApprove', function() {
            var row = getRowData($(this));
            if (!row) return;
            Swal.fire({
                title: 'Approve?',
                type: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes'
            }).then(function(result) {
                if (result.value) updateReviewStatus(row.bac_id, 'BAC Approved', '', false);
            });
        });

        /* ROW REJECT */
        /* ROW REJECT */
        $('#bacReviewBody').on('click', '.btnReject', function() {
            var row = getRowData($(this));
            if (!row) return;
            Swal.fire({
                title: 'Reject?',
                input: 'textarea',
                inputPlaceholder: 'Enter remarks...',
                showCancelButton: true,
                confirmButtonText: 'Yes, Reject'
            }).then(function(result) {
                if (result.value !== undefined) // ← undefined check para kahit walang remarks
                    updateReviewStatus(row.bac_id, 'BAC Rejected', result.value, false); // ← dito
            });
        });

        /* MODAL REJECT BUTTON */
        $('#btnRejectModal').on('click', function() {
            var rem = $('#secretariatRemarks').val();
            if (!rem.trim()) return Swal.fire('Error', 'Remarks required', 'error');
            updateReviewStatus($('#reviewBacId').val(), 'BAC Rejected', rem, true); // ← dito
        });

        /* UPLOAD SIGNATURE/ATTACHMENT MODAL HANDLER */
        $('#bacReviewBody').on('click', '[data-target="#uploadSignatureModal"]', function(e) {
            e.preventDefault();

            var row = getRowData($(this));
            if (row) {
                // Itugma natin sa ID na nasa HTML mo:
                $('#sig_pr_id').val(row.id || row.bac_id); // Ito yung 'pr_id' sa HTML mo

                // Kung may proc_id ka sa row data, ilagay din dito
                if (row.proc_id) {
                    $('#sig_proc_id').val(row.proc_id);
                }

                // I-reset ang file UI
                $('#signatureFile').next('.custom-file-label').html('Choose file...');
                $('#preview-container').addClass('d-none');

                // I-trigger ang modal
                $('#uploadSignatureModal').modal('show');
            }
        });

        /* MODAL BUTTONS */
        $('#btnApproveModal').on('click', function() {
            updateReviewStatus($('#reviewBacId').val(), 'BAC Approved', $('#secretariatRemarks').val(), true);
        });

        $('#btnRejectModal').on('click', function() {
            var rem = $('#secretariatRemarks').val();
            if (!rem.trim()) return Swal.fire('Error', 'Remarks required', 'error');
            updateReviewStatus($('#reviewBacId').val(), 'rejected', rem, true);
        });

    } // end datatable()

    $(document).ready(function() {
        datatable();
        loadSummaryCards();
    });

    // Para lumabas yung filename sa custom-file input
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);

        // Preview image
        const [file] = this.files;
        if (file) {
            $('#preview-container').removeClass('d-none');
            $('#sig-preview').attr('src', URL.createObjectURL(file));
        }
    });

    // Gamit ang Event Delegation para sa dynamic rows
    $(document).on('click', '.btnUploadTrigger', function() {
        // 1. Kunin ang ID mula sa button na pinindot (Specific Row ID)
        var targetBacId = $(this).data('bacid');

        // 2. I-set ang value sa hidden input ng modal
        // Ito ang gagamitin ng PHP sa $_POST['pr_id'] para malaman kung anong row ang i-uupdate
        $('#sig_pr_id').val(targetBacId);

        // 3. Linisin ang modal UI (optional preview reset)
        $('#signatureFile').next('.custom-file-label').html('Choose file...');
        $('#preview-container').addClass('d-none');

        console.log("System: Target PR ID set to " + targetBacId);
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