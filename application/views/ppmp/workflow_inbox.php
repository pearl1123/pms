<div class="container-fluid">

    <h1 class="h3 mb-3 text-gray-800">My PPMP Inbox</h1>

    <div class="card shadow">
        <div class="card-body">

            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Office</th>
                        <th>Status</th>
                        <th>Last Action</th>
                        <th>Remarks</th>
                        <th>Date</th>
                        <th width="160">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($inbox as $i) { ?>
                        <tr>
                            <td><?php echo $i->ppmp_year; ?></td>
                            <td><?php echo $i->office_abbr; ?></td>
                            <td><?php echo $i->current_status; ?></td>
                            <td><?php echo $i->last_action; ?></td>
                            <td><?php echo nl2br($i->last_remarks); ?></td>
                            <td><?php echo $i->date_modified; ?></td>
                            <td>
                                <a href="<?php echo base_url('PPMP/ppmpList/' . $i->ppmp_year . '/' . $i->ppmp_id); ?>"
                                   class="btn btn-sm btn-primary">
                                    Open
                                </a>

                                <a href="<?php echo base_url('PPMP/submissionHistory/' . $i->ppmp_id); ?>"
                                   class="btn btn-sm btn-info">
                                    History
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        </div>
    </div>

</div>