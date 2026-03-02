<!-- Modal: Update Occupation -->
<div class="modal fade" id="occupationUpdateModal" tabindex="-1" role="dialog" aria-labelledby="modalUpdateOccupationLabel" data-backdrop="static" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <span class="modal-title" id="modalUpdateOccupationHeader" style="font-weight: bold; font-size: 16px;">Update Occupation</span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="<?= base_url('Libraries/updateOccupation'); ?>" method="post" class="form-horizontal" id="frmOccupationUpdate">
        <?= $csrf; ?>
        <div class="modal-body">
          <input type="hidden" name="csrf_data" id="csrf_data" class="form-control" value="<?= $csrf_ajax['hash']; ?>">
          <input type="hidden" name="occupation_id" id="occupation_id" class="form-control">

          <div class="form-group">
            <label for="occupation_u" class="col-sm-2 control-label">Occupation<span class="text-danger">*</span></label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="occupation_u" name="occupation_u" placeholder="Occupation Description" required>
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
<!-- End of Update Occupation Modal -->
