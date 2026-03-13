<!-- CONTENT WRAPPER -->
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Purchase Request Review</h1>
        <a href="<?php echo base_url('ProcurementStaff'); ?>" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Back to List
        </a>
    </div>

    <hr />

    <!-- BREADCRUMBS -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url('ProcurementStaff'); ?>">Purchase Request Review</a></li>
            <li class="breadcrumb-item active" aria-current="page">Review</li>
        </ol>
    </nav>

    <!-- ALERTS -->
    <?php if ($this->session->flashdata('fail') <> null) { ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">
                <i class="fa fa-times"></i>
            </button>
            <span><?php echo $this->session->flashdata('fail'); ?></span>
        </div>
    <?php } ?>
    <?php if ($this->session->flashdata('success') <> null) { ?>
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">
                <i class="fa fa-times"></i>
            </button>
            <span><?php echo $this->session->flashdata('success'); ?></span>
        </div>
    <?php } ?>

    <!-- PR INFORMATION -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle mr-1"></i> PR Information
            </h6>
            <span id="reviewStatusBadge"></span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="text-xs font-weight-bold text-uppercase text-muted">PR No.</label>
                    <p id="reviewPrNo" class="font-weight-bold mb-0">—</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="text-xs font-weight-bold text-uppercase text-muted">SAI No.</label>
                    <p id="reviewSaiNo" class="font-weight-bold mb-0">—</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="text-xs font-weight-bold text-uppercase text-muted">Procurement Mode</label>
                    <p id="reviewProcName" class="font-weight-bold mb-0">—</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="text-xs font-weight-bold text-uppercase text-muted">Department</label>
                    <p id="reviewOffice" class="font-weight-bold mb-0">—</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="text-xs font-weight-bold text-uppercase text-muted">Requested By</label>
                    <p id="reviewRequestedBy" class="mb-0">—</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="text-xs font-weight-bold text-uppercase text-muted">Designation</label>
                    <p id="reviewDesignation" class="mb-0">—</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="text-xs font-weight-bold text-uppercase text-muted">Encoded By</label>
                    <p id="reviewEncodedBy" class="mb-0">—</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="text-xs font-weight-bold text-uppercase text-muted">Date Sent</label>
                    <p id="reviewDateCreated" class="mb-0">—</p>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="text-xs font-weight-bold text-uppercase text-muted">Remarks</label>
                    <p id="reviewRemarks" class="mb-0">—</p>
                </div>
            </div>
        </div>
    </div>

    <!-- PR ITEMS -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">
                <i class="fas fa-shopping-cart mr-1"></i> PR Items
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
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
                            <td colspan="6" class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin mr-1"></i> Loading...
                            </td>
                        </tr>
                    </tbody>
                    <tfoot id="reviewItemsFoot"></tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ATTACHMENTS -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-paperclip mr-1"></i> Attachments
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Attachment Name</th>
                            <th class="text-center">Required</th>
                            <th>File</th>
                            <th>Remarks</th>
                            <th class="text-center">Upload Status</th>
                            <th>Staff Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="reviewAttachmentsBody">
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin mr-1"></i> Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- STAFF REMARKS + ACTION BUTTONS -->
    <div class="d-flex justify-content-end mb-4" id="actionCard">
        <button type="button" class="btn btn-danger mr-2" id="btnReject">
            <i class="fas fa-times-circle mr-1"></i> Reject
        </button>
        <button type="button" class="btn btn-success" id="btnApprove">
            <i class="fas fa-check-circle mr-1"></i> Approve
        </button>
    </div>

</div>
<!-- END CONTENT WRAPPER -->
</div>
</div>
</div>
</body>

</html>

<!-- SCRIPTS -->
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/jquery/jquery.min.js'); ?>"></script>

<script>
    // Pull pr_id and preq_id from URL
    var urlParts = window.location.href.split('/');
    var pr_id = urlParts[urlParts.length - 2];
    var preq_id = urlParts[urlParts.length - 1];

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

    // Load PR details on page load
    function loadReviewDetail() {
        $.ajax({
            url: "<?php echo base_url('ProcurementStaff/getReviewDetail'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                pr_id: pr_id,
                preq_id: preq_id
            },
            success: function(res) {
                if (!res || !res.pr) {
                    Swal.fire('Error', 'Could not load PR details.', 'error');
                    return;
                }

                var pr = res.pr;

                // PR Info
                $('#reviewPrNo').text(pr.pr_no || '—');
                $('#reviewSaiNo').text(pr.sai_no || '—');
                $('#reviewProcName').text(pr.proc_name || '—');
                $('#reviewOffice').text(pr.office_desc || '—');
                $('#reviewRequestedBy').text(pr.requested_by || '—');
                $('#reviewDesignation').text(pr.designation || '—');
                $('#reviewEncodedBy').text(pr.encoded_by || '—');
                $('#reviewDateCreated').text(formatDate(pr.date_created));
                $('#reviewRemarks').text(pr.remarks || '—');
                $('#reviewStatusBadge').html(statusBadge(pr.status));

                // Hide approve/reject if not pending
                var isPending = (pr.status || '').toLowerCase() === 'pending';
                if (!isPending) {
                    $('#actionCard').hide();
                }

                // Items
                if (res.items && res.items.length > 0) {
                    var itemHtml = '';
                    var grandTotal = 0;
                    $.each(res.items, function(i, item) {
                        grandTotal += parseFloat(item.total_cost || 0);
                        itemHtml += '<tr>' +
                            '<td>' + (i + 1) + '</td>' +
                            '<td>' + (item.item_description || '—') + '</td>' +
                            '<td>' + (item.unit_code || '—') + '</td>' +
                            '<td class="text-right">' + (item.quantity || 0) + '</td>' +
                            '<td class="text-right">' + formatCurrency(item.unit_cost) + '</td>' +
                            '<td class="text-right">' + formatCurrency(item.total_cost) + '</td>' +
                            '</tr>';
                    });
                    $('#reviewItemsBody').html(itemHtml);
                    $('#reviewItemsFoot').html(
                        '<tr class="font-weight-bold bg-light">' +
                        '<td colspan="5" class="text-right">Grand Total:</td>' +
                        '<td class="text-right">' + formatCurrency(grandTotal) + '</td>' +
                        '</tr>');
                } else {
                    $('#reviewItemsBody').html(
                        '<tr><td colspan="6" class="text-center text-muted">No items found.</td></tr>');
                }

                // Attachments
                if (res.attachments && res.attachments.length > 0) {
                    var attachHtml = '';
                    $.each(res.attachments, function(i, att) {
                        var required = att.required == 1 ?
                            '<span class="badge badge-danger">Required</span>' :
                            '<span class="badge badge-secondary">Optional</span>';

                        var fileLink = att.file_name ?
                            '<a href="<?php echo base_url("assets/uploads/pr_attachments/"); ?>' +
                            att.file_name + '" target="_blank" ' +
                            'class="btn btn-sm btn-outline-primary py-0 px-2">' +
                            '<i class="fas fa-eye mr-1"></i>' +
                            (att.original_file_name || att.file_name) + '</a>' :
                            '<span class="text-muted"><i class="fas fa-times-circle text-danger mr-1"></i>Not uploaded</span>';

                        var uploadStatus = att.file_name ?
                            '<span class="badge badge-success">Uploaded</span>' :
                            '<span class="badge badge-secondary">Missing</span>';

                        var isPending = (res.pr.status || '').toLowerCase() === 'pending';

                        // Staff remarks — editable if pending, readonly if already actioned
                        var staffRemarksInput = isPending ?
                            '<textarea class="form-control form-control-sm staffRemarksInput" ' +
                            'rows="2" placeholder="Enter remarks..." ' +
                            'data-attachment-per-id="' + (att.attachment_per_id || '') + '">' +
                            (att.staff_remarks || '') +
                            '</textarea>' :
                            '<p class="mb-0 text-muted small">' + (att.staff_remarks || '—') + '</p>';

                        attachHtml += '<tr>' +
                            '<td>' + (i + 1) + '</td>' +
                            '<td>' + (att.attachment_name || '—') + '</td>' +
                            '<td class="text-center">' + required + '</td>' +
                            '<td>' + fileLink + '</td>' +
                            '<td>' + (att.remarks || '—') + '</td>' +
                            '<td class="text-center">' + uploadStatus + '</td>' +
                            '<td>' + staffRemarksInput + '</td>' +
                            '</tr>';
                    });
                    $('#reviewAttachmentsBody').html(attachHtml);
                } else {
                    $('#reviewAttachmentsBody').html(
                        '<tr><td colspan="7" class="text-center text-muted">No attachments found.</td></tr>');
                }
            },
            error: function(err) {
                console.error(err);
                Swal.fire('Error', 'Failed to load PR details.', 'error');
            }
        });
    }

    // APPROVE
    $('#btnApprove').on('click', function() {
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
                updateStatus('approved');
            }
        });
    });

    // REJECT
    $('#btnReject').on('click', function() {
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
                updateStatus('rejected');
            }
        });
    });

    function updateStatus(status) {

        // Collect per-attachment staff remarks
        var attachmentRemarks = [];
        $('.staffRemarksInput').each(function() {
            attachmentRemarks.push({
                attachment_per_id: $(this).data('attachment-per-id'),
                remarks: $(this).val()
            });
        });

        $.ajax({
            url: "<?php echo base_url('ProcurementStaff/updateReviewStatus'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                pr_id: pr_id,
                preq_id: preq_id,
                status: status,
                attachment_remarks: attachmentRemarks
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        title: status === 'approved' ? 'Approved!' : 'Rejected!',
                        html: "<span class='text-" + (status === 'approved' ? 'success' : 'danger') +
                            "'><b>SUCCESS!</b></span> " + res.message,
                        type: 'success',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location.href = "<?php echo base_url('ProcurementStaff'); ?>";
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

    $(document).ready(function() {
        loadReviewDetail();
    });
</script>

<style>
    .text-xs {
        font-size: 0.70rem;
    }

    .badge {
        font-size: 0.75rem;
    }

    #reviewItemsFoot tr td {
        border-top: 2px solid #dee2e6;
    }
</style>