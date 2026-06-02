<div class="modal fade" id="ppmpAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add PPMP Item</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url('PPMP/savePPMP'); ?>" method="post" id="formPPMPAdd">
                    <input type="hidden" name="ppmp_year" value="<?php echo $year; ?>">

                    <p class="text-muted font-weight-bold text-uppercase small mb-2">Project Details</p>
                    <div class="form-group">
                        <label>Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="ppmp_general_description" rows="3" required
                            placeholder="General description and objective of the project..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Project Type <span class="text-danger">*</span></label>
                            <select class="form-control" name="ppmp_project_type" required>
                                <option value="">Select Project Type</option>
                                <option value="1">Goods</option>
                                <option value="2">Infrastructure</option>
                                <option value="3">Consulting Services</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Procurement Mode <span class="text-danger">*</span></label>
                            <select class="form-control" name="proc_id" required>
                                <option value="">Select Procurement Mode</option>
                                <?php foreach($mode as $m){ ?>
                                    <option value="<?php echo $m->proc_id; ?>"><?php echo $m->proc_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Quantity <span class="text-danger">*</span></label>
                            <input type="number" min="1" class="form-control" name="ppmp_quantity" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Unit <span class="text-danger">*</span></label>
                            <select class="form-control" name="unit_id" required>
                                <option value="">Select Unit</option>
                                <?php foreach($unit as $u){ ?>
                                    <option value="<?php echo $u->unit_id; ?>"><?php echo $u->unit_code; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <hr class="my-3">
                    <p class="text-muted font-weight-bold text-uppercase small mb-2">Schedule</p>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="ppmp_start_proc" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="ppmp_end_proc" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Delivery Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="ppmp_delivery" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="ppmp_pre_proc" id="pre_proc">
                            <label class="form-check-label" for="pre_proc">For Pre-Procurement</label>
                        </div>
                    </div>

                    <hr class="my-3">
                    <p class="text-muted font-weight-bold text-uppercase small mb-2">Financials</p>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Fund Source <span class="text-danger">*</span></label>
                            <select class="form-control" name="fund_id" required>
                                <option value="">Select Fund Source</option>
                                <?php foreach($source as $s){ ?>
                                    <option value="<?php echo $s->fund_id; ?>"><?php echo $s->fund_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Attachment <span class="text-danger">*</span></label>
                            <select class="form-control" name="attachment_id" required>
                                <option value="">Select Attachment</option>
                                <?php foreach($attachment as $a){ ?>
                                    <option value="<?php echo $a->attachment_id; ?>"><?php echo $a->attachment_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Budget (PhP) <span class="text-danger">*</span></label>
                            <input type="number" min="1" step="0.01" class="form-control" name="ppmp_budget" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Cost (PhP) <span class="text-danger">*</span></label>
                            <input type="number" min="0.01" step="0.01" class="form-control" name="ppmp_cost" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Remarks <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="ppmp_remarks" rows="3" required></textarea>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="submit" form="formPPMPAdd">Submit</button>
            </div>
        </div>
    </div>
</div>