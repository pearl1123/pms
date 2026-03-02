
<!-- Modal -->
<div class="modal fade" id="modalUpdateNationality" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <div class="modal-header">
        <span class="modal-nationality" id="modalUpdateNationalityHeader" style="font-weight: bold;font-size: 16px;">Update Nationality</span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
     </div>
      
      <form action="<?php echo base_url("Libraries/updateNationality");?>" method="post" class="form-horizontal" id="frmNationalityUpdate">
        
        <?php echo $csrf;?>
        <div class="modal-body">
          <input type="hidden" name="csrf_data" id='csrf_data' class="form-control" value="<?php echo $csrf_ajax['hash']  ?>">
          <input type="hidden" name="nationality_id" id='nationality_id' class="form-control"> 
            <div class="form-group">
                  <label for="GroupCode" class="col-sm-2 control-label">Nationality Description<span class = "text-danger">*</span></label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="nationality_u" name="nationality_u" placeholder="Nationality Description" >
                  </div>
            </div>   
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cancel</button>
            <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
        </div>

     </form>


    </div>
  </div>
</div>
<!-- End of modal -->