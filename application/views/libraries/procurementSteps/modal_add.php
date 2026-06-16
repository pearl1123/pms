<div class="modal fade" id="stepAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Procurement Step</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("Libraries/saveProcurementStep");?>" method="post" class="form-horizontal" id="procStep">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="stepDescription">Procurement Step Description <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="step_description" id="stepDescription">
                        <input type="text" class="form-control" name="stepId" id="stepId">
                    </div>
                    <div class="mb-3" style ="max-height: 500px; overflow-y: auto;">
                        <table class="table table-bordered" id="tblMode" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Attachment</th>
                                    <th>Is Required</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>Attachment</th>
                                    <th>Is Required</th>
                                </tr>
                            </tfoot>
                            <tbody id="modeBody">
                                <?php 
                                    foreach($attachment as $a){
                                        $check = "";
                                ?>
                                    <tr>
                                        <td align = "center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="<?php echo $a->attachment_id; ?>" name = "docs[]" id = "docs[]">
                                            </div>
                                        </td>
                                        <td style="width: 90%"><?= $a->attachment_name ?></td>
                                        <td align = "center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="isRequired_<?= $a->attachment_id ?>" name="isRequired_<?= $a->attachment_id ?>">
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
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