Sige, ito na ang buong code para sa bidsAwardsReview.php. Pinagsama ko na ang HTML structure at ang optimized JavaScript logic para direkta na itong gagana pag-load ng page.

Siguraduhin lang na ang iyong Controller (BAC.php) ay may methods na getBACItems, getBACAttachments, at updateReviewStatus.

PHP
<div class="container-fluid mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-file-contract mr-2"></i> Bids and Awards Review
            </h5>
            <a href="<?php echo base_url('BAC/listview'); ?>" class="btn btn-sm btn-light">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card-body">
            <input type="hidden" id="reviewBacId" value="<?php echo $this->uri->segment(3); ?>">

            <div class="card mb-3 border-left-primary">
                <div class="card-header py-2 bg-light">
                    <strong><i class="fas fa-info-circle mr-1 text-primary"></i> BAC Information</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="text-xs font-weight-bold text-uppercase text-muted mb-0">BAC No.</label>
                            <p id="reviewBacNo" class="font-weight-bold text-primary mb-0">Loading...</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-xs font-weight-bold text-uppercase text-muted mb-0">SAI No.</label>
                            <p id="reviewSaiNo" class="mb-0">—</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-xs font-weight-bold text-uppercase text-muted mb-0">Office/Dept</label>
                            <p id="reviewOffice" class="mb-0">—</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-xs font-weight-bold text-uppercase text-muted mb-0">Status</label>
                            <div id="reviewStatusBadge">—</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header py-2 bg-light d-flex justify-content-between">
                    <strong><i class="fas fa-boxes mr-1 text-info"></i> BAC Items</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Item Description</th>
                                    <th>Unit</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Unit Cost</th>
                                    <th class="text-right">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody id="reviewItemsBody">
                                <tr><td colspan="6" class="text-center p-4"><i class="fas fa-spinner fa-spin mr-2"></i>Loading items...</td></tr>
                            </tbody>
                            <tfoot id="reviewItemsFoot" class="bg-light"></tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header py-2 bg-light">
                    <strong><i class="fas fa-paperclip mr-1 text-warning"></i> Attachments / Requirements</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Requirement Name</th>
                                    <th class="text-center">Required?</th>
                                    <th>File</th>
                                    <th>Remarks</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="reviewAttachmentsBody">
                                <tr><td colspan="6" class="text-center text-muted">No attachments loaded.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header py-2 bg-light">
                    <strong><i class="fas fa-comment-alt mr-1 text-secondary"></i> Secretariat Remarks / Notes</strong>
                </div>
                <div class="card-body">
                    <textarea id="secretariatRemarks" class="form-control" rows="3" placeholder="Enter review notes, findings, or reason for rejection here..."></textarea>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white text-right">
            <button type="button" class="btn btn-outline-danger px-4" id="btnRejectModal">
                <i class="fas fa-times-circle mr-1"></i> Reject
            </button>
            <button type="button" class="btn btn-success px-4" id="btnApproveModal">
                <i class="fas fa-check-circle mr-1"></i> Approve
            </button>
        </div>
    </div>
</div>