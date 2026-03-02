<!-- Occupation Add Modal -->
<div class="modal fade" id="occupationAddModal" tabindex="-1" role="dialog" aria-labelledby="occupationAddModalLabel" data-backdrop="static" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <span class="modal-title" id="occupationAddModalHeader" style="font-weight: bold; font-size: 16px;">Create Occupation Record</span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="<?= base_url('Libraries/saveOccupation'); ?>" method="post" class="form-horizontal" id="frmOccupationAdd">
        <?= $csrf; ?>
        <div class="modal-body">
          <input type="hidden" name="csrf_data" id="csrf_data" class="form-control" value="<?= $csrf_ajax['hash']; ?>">
          <div class="form-group">
            <label for="occupation" class="col-sm-3 control-label">Occupation Description<span class="text-danger">*</span></label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="occupation" name="occupation" placeholder="Occupation Description" required>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">
            <i class="fa fa-times" aria-hidden="true"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary btn-flat">
            <i class="fa fa-floppy-o" aria-hidden="true"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End of Occupation Add Modal -->
