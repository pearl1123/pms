<!-- CUSTOM STYLE 
================================================================================================ -->
<style type="text/css">
    .datatable_color_row {
        background-color: #00a65a14 !important;
    }

    .select2-container--default .select2-results__option[aria-disabled=true] {
        background-color: #7f8c8d;
        color: white;
    }

    #caredTable td {
        vertical-align: middle;
        text-align: center;
    }

    /* Forces alignment to the top for the text columns and date/time */
    #caredTable td.text-start,
    #caredTable tbody tr td:nth-child(3) {
        vertical-align: top !important;
        text-align: left;
    }

    /* Keep checkmark cells vertically centered */
    #caredTable tbody tr td:nth-child(6),
    #caredTable tbody tr td:nth-child(7),
    #caredTable tbody tr td:nth-child(8),
    #caredTable tbody tr td:nth-child(9),
    #caredTable tbody tr td:nth-child(10) {
        vertical-align: middle !important;
    }

    /* Small padding correction for the single-item list */
    .single-list-item {
        padding-left: 15px;
        margin: 0;
    }

    /* Visual grouping styles */
    #caredTable tbody tr.patient-group-first td:first-child,
    #caredTable tbody tr.patient-group-first td:nth-child(2),
    #caredTable tbody tr.patient-group-first td:nth-child(11),
    #caredTable tbody tr.patient-group-first td:nth-child(12) {
        font-weight: bold;
        background-color: #f9f9f9;
    }

    #caredTable tbody tr.patient-group-first {
        border-top: 2px solid #3c8dbc;
    }

    /* Hide duplicate MRN, Name, Requested By, Performed By for visual grouping */
    #caredTable tbody tr:not(.patient-group-first) td:first-child,
    #caredTable tbody tr:not(.patient-group-first) td:nth-child(2),
    #caredTable tbody tr:not(.patient-group-first) td:nth-child(11),
    #caredTable tbody tr:not(.patient-group-first) td:nth-child(12) {
        color: transparent !important;

        /* 2. Prevents mouse interaction and selecting the hidden text */
        pointer-events: none !important;

        /* 3. If you want to absolutely guarantee no space is taken by the text */
        font-size: 0;
        line-height: 0;
    }

    #caredTable tbody tr td:nth-child(12),
    #caredTable tbody tr td:nth-child(13) {
        /* Align text to the left as requested */
        text-align: left !important;

        /* Ensure content aligns to the top to match the other text columns */
        vertical-align: top !important;
    }
</style>

<div class="content-wrapper" style="min-height: 98px;">

    <!-- HEADER -->
    <section class="content-header">
        <h1>CARED Monitoring</h1>
        <ol class="breadcrumb">
            <li><i class="fa fa-dashboard"></i> WARDS</li>
        </ol>
    </section>

    <section class="content">

        <!-- ALERTS -->
        <?php if ($this->session->flashdata('fail') != null): ?>
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
                <span><?= $this->session->flashdata('fail'); ?></span>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('success') != null): ?>
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
                <span><?= $this->session->flashdata('success'); ?></span>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-xs-12">

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#" data-toggle="tab">Patient List</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="active tab-pane" id="#">

                            <table id="caredTable" class="table table-bordered table-hover w-100">
                                <thead>
                                    <tr>
                                        <th>MRN</th>
                                        <th>Patient Name</th>
                                        <th>Date/Time</th>
                                        <th>Progress Notes</th>
                                        <th>Doctor's Order</th>
                                        <th>Carried</th>
                                        <th>Administered</th>
                                        <th>Requested</th>
                                        <th>Endorsed</th>
                                        <th>Discontinued</th>
                                        <th>Requested By</th>
                                        <th>Performed By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $current_patient_key = null;
                                    ?>
                                    <?php if (!empty($records)): ?>
                                        <?php foreach ($records as $row):
                                            $patient_key = $row['mrn'] . '-' . $row['encounter_id'];
                                            $is_first_order = ($patient_key != $current_patient_key);

                                            if ($is_first_order) {
                                                $current_patient_key = $patient_key;
                                                $row_class = 'patient-group-first';
                                            } else {
                                                $row_class = '';
                                            }
                                        ?>
                                            <tr class="<?= $row_class ?>" data-patient-key="<?= htmlspecialchars($patient_key) ?>">
                                                <td><?= htmlspecialchars($row['mrn'] ?? '') ?></td>
                                                <td class="text-start"><?= htmlspecialchars($row['patient_name'] ?? '') ?></td>
                                                <td><?= !empty($row['date_time']) ? date('h:i A', strtotime($row['date_time'])) : '' ?></td>
                                                <td class="text-start">
                                                    <?php
                                                    if (!empty($row['progress_notes'])) {
                                                        echo '<ul class="single-list-item"><li>' . nl2br(htmlspecialchars($row['progress_notes'] ?? '')) . '</li></ul>';
                                                    } else {
                                                        echo '<em>Special Order</em>';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-start">
                                                    <?php
                                                    if (!empty($row['order'])) {
                                                        echo '<ul class="single-list-item"><li>' . nl2br(htmlspecialchars($row['order'] ?? '')) . '</li></ul>';
                                                    } else {
                                                        echo '<em>No orders</em>';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-center"><?= !empty($row['c']) && $row['c'] != '0' ? '<i class="fa fa-check text-primary"' : '' ?></td>
                                                <td class="text-center"><?= !empty($row['a']) && $row['a'] != '0' ? '<i class="fa fa-check text-primary"' : '' ?></td>
                                                <td class="text-center"><?= !empty($row['r']) && $row['r'] != '0' ? '<i class="fa fa-check text-primary"' : '' ?></td>
                                                <td class="text-center"><?= !empty($row['e']) && $row['e'] != '0' ? '<i class="fa fa-check text-primary"' : '' ?></td>
                                                <td class="text-center"><?= !empty($row['d']) && $row['d'] != '0' ? '<i class="fa fa-check text-primary"' : '' ?></td>
                                                <td><?= htmlspecialchars($row['encoded_by_name'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['modified_by_name'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="12" class="text-center"><em>No records found</em></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>

            </div>
        </div>

    </section>
</div>


<!-- FOOTER -->
<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> 1.0
    </div>
    <strong>Copyright © 2025 <a href="">Management Information Systems Division</a></strong>
</footer>

<div class="control-sidebar-bg"></div>

<script>
    $(document).ready(function() {
        var table = $('#caredTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [
                [0, 'desc'],
                [2, 'desc']
            ],
            columnDefs: [{
                    targets: [2, 3, 4],
                    className: 'text-start'
                },
                {
                    targets: [10, 11],
                    orderable: false
                }
            ],
            language: {
                emptyTable: "No records found",
                zeroRecords: "No matching records found"
            },
            drawCallback: function(settings) {
                // Re-apply visual grouping after sort/page change
                var api = this.api();
                var rows = api.rows({
                    page: 'current'
                }).nodes();
                var lastPatient = null;

                api.column(0, {
                    page: 'current'
                }).data().each(function(group, i) {
                    var currentPatient = $(rows).eq(i).data('patient-key');

                    if (lastPatient !== currentPatient) {
                        $(rows).eq(i).addClass('patient-group-first');
                        lastPatient = currentPatient;
                    } else {
                        $(rows).eq(i).removeClass('patient-group-first');
                    }
                });
            }
        });
    });
</script>