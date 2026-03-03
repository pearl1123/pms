<div class="modal fade" id="stockUpdateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stock</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("Libraries/updateStock"); ?>" method="post" class="form-horizontal" id="formStockUpdate">
                <div class="modal-body">
                    <input type="hidden" name="stock_id" id="stockID">
                    <div class="mb-3">
                        <label for="stockItem_update">Item <span class="text-danger">*</span></label>
                        <select class="form-control" name="item_id" id="stockItem_update" required>
                            <option value="">-- Select Item --</option>
                            <?php foreach($items as $item): ?>
                                <option value="<?php echo $item->item_id; ?>"><?php echo $item->item_description; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="stockUnit_update">Unit <span class="text-danger">*</span></label>
                        <select class="form-control" name="unit_id" id="stockUnit_update" required>
                            <option value="">-- Select Unit --</option>
                            <?php foreach($units as $unit): ?>
                                <option value="<?php echo $unit->unit_id; ?>"><?php echo $unit->unit_code; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="stockOnhand_update">Stock Onhand <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="stock_onhand" id="stockOnhand_update" min="0" required>
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