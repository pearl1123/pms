<div class="modal fade" id="ppmpRoutingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">

        <form method="post" action="<?php echo base_url('PPMP/routePPMP'); ?>">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Route PPMP</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="ppmp_id" id="route_ppmp_id">

                    <div class="form-group">
                        <label>Action</label>
                        <select class="form-control" name="action_type" id="route_action_type" required>
                            <option value="">Select Action</option>
                            <option value="SUBMIT_FOR_REVIEW">Submit for Review / Inputs</option>
                            <option value="RESUBMIT_FOR_REVIEW">Resubmit for Review</option>
                            <option value="RETURN_FOR_REVISION">Return for Revision</option>
                            <option value="MARK_REVIEWED">Mark as Reviewed</option>
                            <option value="SUBMIT_FOR_APPROVAL">Submit for Approval</option>
                            <option value="RESUBMIT_FOR_APPROVAL">Resubmit for Approval</option>
                            <option value="APPROVE">Approve</option>
                            <option value="DISAPPROVE">Disapprove</option>
                        </select>
                    </div>

                    <div class="form-group" id="route_to_group">
                        <label>Forward To</label>
                        <select class="form-control" name="to_user_id" id="route_to_user_id">
                            <option value="">Select User</option>
                            <?php foreach ($routing_users as $u) { ?>
                                <option value="<?php echo $u->id; ?>">
                                    <?php echo $u->fullname; ?>
                                </option>
                            <?php } ?>
                        </select>
                        <small class="text-muted">
                            Required for submit, resubmit, and return actions.
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Remarks / Comments</label>
                        <textarea class="form-control"
                                  name="remarks"
                                  rows="4"
                                  placeholder="Enter review comments, approval notes, return reason, or instructions..."></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Submit Action
                    </button>
                </div>

            </div>

        </form>

    </div>
</div>