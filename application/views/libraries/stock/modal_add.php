<div class="modal fade" id="stockAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Stock</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("Libraries/saveStock"); ?>" method="post" class="form-horizontal" id="formStockAdd">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="stockItem">Item <span class="text-danger">*</span></label>
                        <select class="form-control" name="item_id" id="stockItem" required>
                            <option value="">-- Select Item --</option>

                            <optgroup label="Library Items">
                                <?php foreach ($items as $item): ?>
                                    <option value="lib_<?php echo $item->item_id; ?>">
                                        <?php echo $item->item_description; ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>

                            <optgroup label="Dietary Items">
                                <?php foreach ($isms_items as $item): ?>
                                    <option value="diet_<?php echo $item->id; ?>">
                                        <?php echo $item->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>

                            <optgroup label="Pharmacy Items">
                                <?php foreach ($pharmacy_items as $item): ?>
                                    <option value="pharm_<?php echo $item->brand_id; ?>">
                                        <?php echo $item->brand_name; ?>
                                        (<?php echo $item->generic_name; ?>
                                        - <?php echo $item->dosage_name; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>

                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="stockUnit">Unit <span class="text-danger">*</span></label>
                        <select class="form-control" name="unit_id" id="stockUnit" required>
                            <option value="">-- Select Unit --</option>
                            <?php foreach ($units as $unit): ?>
                                <option value="<?php echo $unit->unit_id; ?>"><?php echo $unit->unit_code; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="stockOnhand">Stock Onhand <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="stock_onhand" id="stockOnhand" min="0" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
