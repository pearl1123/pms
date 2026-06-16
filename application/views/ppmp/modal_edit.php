<div class="modal fade" id="editPPMPProjectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <form id="formEditPPMPProject"
      method="POST"
      action="<?php echo base_url('PPMP/updatePPMPProject'); ?>">

                <div class="modal-header py-2">
                    <h5 class="modal-title">Edit PPMP Project</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <input type="text" name="ppmp_project_id" id="edit_ppmp_project_id">
                    <input type="text" name="ppmp_id" id="edit_ppmp_id">
                    <input type="text" name="ppmp_year" id="edit_ppmp_year">

                    <div id="deletedItemsContainer"></div>

                    <div class="form-group mb-2">
                        <label class="small mb-1">Procurement Project Description</label>
                        <textarea class="form-control form-control-sm"
                                  name="ppmp_general_description"
                                  id="edit_ppmp_general_description"
                                  rows="2"
                                  required></textarea>
                    </div>

                    <div class="form-row">

                        <div class="form-group col-md-3 mb-2">
                            <label class="small mb-1">Project Type</label>
                            <select class="form-control form-control-sm" name="ppmp_project_type" id="edit_ppmp_project_type" required>
                                <option value="">Select</option>
                                <option value="1">Goods</option>
                                <option value="2">Infrastructure</option>
                                <option value="3">Consulting Services</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3 mb-2">
                            <label class="small mb-1">Procurement Mode</label>
                            <select class="form-control form-control-sm" name="proc_id" id="edit_proc_id" required>
                                <option value="">Select</option>
                                <?php foreach($mode as $m){ ?>
                                    <option value="<?php echo $m->proc_id; ?>">
                                        <?php echo $m->proc_name; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group col-md-3 mb-2">
                            <label class="small mb-1">Fund Source</label>
                            <select class="form-control form-control-sm" name="fund_id" id="edit_fund_id" required>
                                <option value="">Select</option>
                                <?php foreach($source as $s){ ?>
                                    <option value="<?php echo $s->fund_id; ?>">
                                        <?php echo $s->fund_name; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group col-md-3 mb-2">
                            <label class="small mb-1">Attachment</label>
                            <select class="form-control form-control-sm" name="attachment_id" id="edit_attachment_id" required>
                                <option value="">Select</option>
                                <?php foreach($attachment as $a){ ?>
                                    <option value="<?php echo $a->attachment_id; ?>">
                                        <?php echo $a->attachment_name; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>

                    <div class="form-row">

                        <div class="form-group col-md-3 mb-2">
                            <label class="small mb-1">Start Date</label>
                            <input type="date" class="form-control form-control-sm" name="ppmp_start_proc" id="edit_ppmp_start_proc" required>
                        </div>

                        <div class="form-group col-md-3 mb-2">
                            <label class="small mb-1">End Date</label>
                            <input type="date" class="form-control form-control-sm" name="ppmp_end_proc" id="edit_ppmp_end_proc" required>
                        </div>

                        <div class="form-group col-md-3 mb-2">
                            <label class="small mb-1">Delivery Date</label>
                            <input type="date" class="form-control form-control-sm" name="ppmp_delivery" id="edit_ppmp_delivery" required>
                        </div>

                        <div class="form-group col-md-3 mb-2">
                            <label class="small mb-1">Budget</label>
                            <input type="number"
                                   step="0.01"
                                   class="form-control form-control-sm"
                                   name="ppmp_budget"
                                   id="edit_ppmp_budget"
                                   readonly>
                        </div>

                    </div>

                    <div class="form-group mb-2">
                        <div class="form-check">
                            <input type="checkbox"
                                   class="form-check-input"
                                   name="ppmp_pre_proc"
                                   value="1"
                                   id="edit_ppmp_pre_proc">
                            <label class="form-check-label small" for="edit_ppmp_pre_proc">
                                For Pre-Procurement
                            </label>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="small mb-1">Remarks</label>
                        <textarea class="form-control form-control-sm"
                                  name="ppmp_remarks"
                                  id="edit_ppmp_remarks"
                                  rows="2"></textarea>
                    </div>

                    <div class="border rounded p-2 bg-light mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="small">Items</strong>

                            <button type="button" class="btn btn-sm btn-primary" id="btnEditAddItem">
                                + Add Item
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 38%;">Item</th>
                                        <th style="width: 12%;">Qty</th>
                                        <th style="width: 17%;">Unit</th>
                                        <th style="width: 15%;">Unit Cost</th>
                                        <th style="width: 15%;">Total Cost</th>
                                        <th style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="editItemsContainer"></tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        Save Changes
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<table class="d-none">
    <tbody id="editItemTemplate">
        <tr class="edit-item-row">
            <td>
                <input type="hidden" class="edit-ppmp-item-id" name="ppmp_item_id[]" value="">

                <select class="form-control form-control-sm edit-item-select"
                        name="item_id[]"
                        required>
                    <option value="">Select Item</option>
                    <?php foreach($items as $i){ ?>
                        <option value="<?php echo $i->item_id; ?>">
                            <?php echo $i->item_description; ?>
                        </option>
                    <?php } ?>
                </select>
            </td>

            <td>
                <input type="number"
                       min="1"
                       class="form-control form-control-sm edit-qty-input"
                       name="ppmp_quantity[]"
                       required>
            </td>

            <td>
                <select class="form-control form-control-sm edit-unit-select"
                        name="unit_id[]"
                        required>
                    <option value="">Select</option>
                    <?php foreach($unit as $u){ ?>
                        <option value="<?php echo $u->unit_id; ?>">
                            <?php echo $u->unit_code; ?>
                        </option>
                    <?php } ?>
                </select>
            </td>

            <td>
                <input type="number"
                       step="0.01"
                       min="0.01"
                       class="form-control form-control-sm edit-unit-cost-input"
                       name="ppmp_unit_cost[]"
                       required>
            </td>

            <td>
                <input type="number"
                       step="0.01"
                       min="0"
                       class="form-control form-control-sm edit-total-cost-input"
                       name="ppmp_cost[]"
                       readonly>
            </td>

            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger btnRemoveEditItem">
                    ×
                </button>
            </td>
        </tr>
    </tbody>
</table>