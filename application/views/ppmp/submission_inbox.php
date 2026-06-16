<div class="container-fluid">

    <h1 class="h3 mb-3 text-gray-800">PPMP Submission Inbox</h1>

    <?php if ($this->session->flashdata('success')) { ?>
        <div class="alert alert-success">
            <?php echo $this->session->flashdata('success'); ?>
        </div>
    <?php } ?>

    <?php if ($this->session->flashdata('fail')) { ?>
        <div class="alert alert-danger">
            <?php echo $this->session->flashdata('fail'); ?>
        </div>
    <?php } ?>

    <div class="card shadow">
        <div class="card-header">
            <strong>Pending / Routed PPMPs</strong>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Office</th>
                        <th>Submitted By</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Date Submitted</th>
                        <th width="240">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($submissions as $s) { ?>

                        <?php
                            $type = $s->submission_type == 1 ? 'For Review / Inputs' : 'For Approval';

                            $statusMap = [
                                1 => 'Pending',
                                2 => 'Returned',
                                3 => 'Reviewed',
                                4 => 'Approved',
                                5 => 'Disapproved',
                                6 => 'Cancelled'
                            ];

                            $status = isset($statusMap[$s->status]) ? $statusMap[$s->status] : 'Unknown';
                        ?>

                        <tr>
                            <td><?php echo $s->ppmp_year; ?></td>
                            <td><?php echo $s->office_abbr; ?></td>
                            <td><?php echo $s->submitted_by_name; ?></td>
                            <td><?php echo $type; ?></td>
                            <td><?php echo $status; ?></td>
                            <td><?php echo $s->remarks; ?></td>
                            <td><?php echo $s->date_submitted; ?></td>
                            <td>
                                <?php if ($s->status == 1) { ?>
                                    <form method="post"
                                          action="<?php echo base_url('PPMP/actOnPPMPSubmission'); ?>">

                                        <input type="hidden" name="submission_id"
                                               value="<?php echo $s->submission_id; ?>">

                                        <div class="form-group mb-1">
                                            <textarea class="form-control form-control-sm"
                                                      name="action_remarks"
                                                      rows="2"
                                                      placeholder="Action remarks"></textarea>
                                        </div>

                                        <button name="action" value="3"
                                                class="btn btn-sm btn-info">
                                            Reviewed
                                        </button>

                                        <button name="action" value="4"
                                                class="btn btn-sm btn-success">
                                            Approve
                                        </button>

                                        <button name="action" value="2"
                                                class="btn btn-sm btn-warning">
                                            Return
                                        </button>

                                        <button name="action" value="5"
                                                class="btn btn-sm btn-danger">
                                            Disapprove
                                        </button>

                                    </form>
                                <?php } else { ?>
                                    <span class="text-muted">Action completed</span>
                                <?php } ?>
                            </td>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>

        </div>
    </div>

</div>