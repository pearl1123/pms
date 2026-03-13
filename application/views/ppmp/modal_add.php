<div class="modal fade" id="ppmpAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add PPMP Item</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div> 
            <div class="modal-body">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800"></h1>
                    <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btnAddItem">
                        <i class="fas fa-plus fa-sm text-white-50"></i> Add Item</button>
                </div>
                <table class="table table-responsive table-bordered" id="tblPPMPItem">
                    <form action="<?php echo base_url("Libraries/savePPMP");?>" method="post" class="form-horizontal" id="formPPMPAdd">
                        <tbody>
                            <tr>
                                <td>
                                    <div class="mb-3">
                                        <label for="generalDescription">Description<span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="ppmp_general_description" id="generalDescription" rows="5"></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <label for="projectType">Project Type<span class="text-danger">*</span></label>
                                        <select class="form-control" name="ppmp_project_type" id="project_type">
                                            <option>Select Project Type</option>
                                            <option value="1">Goods</option>
                                            <option value="2">Infrastructure</option>
                                            <option value="3">Consulting Services</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <label for="quantity">Quantity<span class="text-danger">*</span></label>
                                        <input type="number" min="1" class="form-control" name="ppmp_quantity" id="quantity">
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <label for="unit_type">Unit<span class="text-danger">*</span></label>
                                        <select class="form-control" name="unit_id" id="unit_type">
                                            <option>Select Unit</option>
                                            <?php  foreach($unit as $u){ ?>
                                                <option value=<?php echo $u->unit_id; ?>><?php echo $u->unit_code; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <label for="mode_type">Mode<span class="text-danger">*</span></label>
                                        <select class="form-control" name="proc_id" id="mode_type">
                                            <option>Select Procurement Mode</option>
                                            <?php foreach($mode as $m){ ?>
                                                <option value=<?php echo $m->proc_id; ?>><?php echo $m->proc_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" name="ppmp_pre_proc" id="pre_proc">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                For Pre-Proc
                                            </label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <label for="start_date">Start Date<span class="text-danger">*</span></label>
                                        <input type="date" min="1" class="form-control" name="ppmp_start_proc" id="start_date">
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <label for="end_date">End Date<span class="text-danger">*</span></label>
                                        <input type="date" min="1" class="form-control" name="ppmp_end_proc" id="end_date">
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <label for="delivery_date">Delivery Date<span class="text-danger">*</span></label>
                                        <input type="date" min="1" class="form-control" name="ppmp_delivery" id="delivery_date">
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <label for="attachment">Attachment<span class="text-danger">*</span></label>
                                        <select class="form-control" name="attachment_id" id="attachment">
                                            <option>Select Attachment</option>
                                            <?php foreach($attachment as $a){ ?>
                                                <option value=<?php echo $a->attachment_id; ?>><?php echo $a->attachment_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <label for="fund_source">Fund Source<span class="text-danger">*</span></label>
                                        <select class="form-control" name="fund_id" id="fund_source">
                                            <option>Select Project Type</option>
                                            <?php foreach($source as $s){ ?>
                                                <option value=<?php echo $s->fund_id; ?>><?php echo $s->fund_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <label for="budget">Budget<span class="text-danger">*</span></label>
                                        <input type="number" min="1" class="form-control" name="ppmp_budget" id="budget">
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <label for="cost">Cost<span class="text-danger">*</span></label>
                                        <input type="number" min="0.01" class="form-control" name="ppmp_cost" id="cost">
                                    </div>
                                </td>
                                
                                
                                <td>
                                    <div class="mb-3">
                                        <label for="remarks">Remarks <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="ppmp_remarks" id="remarks" rows="5"></textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </form>
                </table>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="submit">Submit</button>
            </div>
        </div>
    </div>
</div>

