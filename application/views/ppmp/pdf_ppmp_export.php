<!DOCTYPE html>
<html>
<head>
    <title>PPMP <?php echo $year; ?></title>

    <style>
        @page {
            size: legal landscape;
            margin: 8mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            color: #000;
            margin: 0;
        }

        .header {
            text-align: center;
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .header h4,
        .header h5 {
            margin: 2px 0;
            font-weight: bold;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .meta td {
            border: none;
            padding: 2px;
            font-size: 8px;
        }

        .ppmp-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .ppmp-table th,
        .ppmp-table td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .ppmp-table th {
            text-align: center;
            font-weight: bold;
            background: #fff;
            font-size: 7px;
        }

        .subhead {
            font-style: italic;
            font-size: 6px;
            font-weight: normal;
        }

        .expense-row td {
            font-weight: bold;
            background: #f2f2f2;
        }

        .data-row td {
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .print-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 8px 12px;
            background: #4e73df;
            color: #fff;
            border: none;
            cursor: pointer;
            z-index: 999;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>

<button class="print-btn" onclick="window.print()">Print / Save as PDF</button>

<div class="header">
    <h5>LUNG CENTER OF THE PHILIPPINES</h5>
    <div>QUEZON AVENUE, QUEZON CITY</div>
    <br>
    <h4>PROJECT PROCUREMENT MANAGEMENT PLAN (PPMP) NO. 1</h4>
    <div>
        &#9744; INDICATIVE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &#9744; FINAL
    </div>
</div>

<table class="meta">
    <tr>
        <td style="width: 12%;"><strong>Fiscal Year :</strong></td>
        <td style="width: 38%;"><?php echo $year; ?></td>
        <td style="width: 15%;"><strong>PPMP ID :</strong></td>
        <td><?php echo $ppmp_id; ?></td>
    </tr>
    <tr>
        <td><strong>End-User or Implementing Unit:</strong></td>
        <td colspan="3">
            <?php
                echo !empty($projects[0]['office_name'])
                    ? $projects[0]['office_name'] . ' (' . $projects[0]['office_abbr'] . ')'
                    : '—';
            ?>
        </td>
    </tr>
</table>

<table class="ppmp-table">
    <thead>
        <tr>
            <th colspan="4">PROCUREMENT PROJECT DETAILS</th>
            <th colspan="4">PROJECTED TIMELINE (MM/YYYY)</th>
            <th colspan="2">FUNDING DETAILS</th>
            <th rowspan="2">ATTACHED<br>SUPPORTING<br>DOCUMENTS</th>
            <th rowspan="2">REMARKS</th>
            <th rowspan="2">QTY</th>
            <th rowspan="2">UNIT COST<br>PER ITEM</th>
            <th rowspan="2">TOTAL</th>
            <th rowspan="2">QTY</th>
            <th rowspan="2">UNIT COST<br>PER ITEM</th>
            <th rowspan="2">TOTAL</th>
            <th rowspan="2">SUB-TOTAL</th>
        </tr>

        <tr>
            <th>General Description and Objective of the Project to be Procured</th>
            <th>Type of the Project to be Procured</th>
            <th>Quantity and Size of the Project to be Procured</th>
            <th>Recommended Mode of Procurement</th>
            <th>Pre-Procurement Conference, if applicable</th>
            <th>Start of Procurement Activity</th>
            <th>End of Procurement Activity</th>
            <th>Expected Delivery / Implementation Period</th>
            <th>Source of Funds</th>
            <th>Estimated Budget / Authorized Budgetary Allocation (PHP)</th>
        </tr>

        <tr>
            <th class="subhead">Column 1</th>
            <th class="subhead">Column 2</th>
            <th class="subhead">Column 3</th>
            <th class="subhead">Column 4</th>
            <th class="subhead">Column 5</th>
            <th class="subhead">Column 6</th>
            <th class="subhead">Column 7</th>
            <th class="subhead">Column 8</th>
            <th class="subhead">Column 9</th>
            <th class="subhead">Column 10</th>
            <th class="subhead">Column 11</th>
            <th class="subhead">Column 12</th>
            <th class="subhead">Column 13</th>
            <th class="subhead">Column 14</th>
            <th class="subhead">Column 15</th>
            <th class="subhead">Column 16</th>
            <th class="subhead">Column 17</th>
            <th class="subhead">Column 18</th>
            <th class="subhead">Column 19</th>
        </tr>
    </thead>

    <tbody>
        <tr class="expense-row">
            <td colspan="19">Maintenance and Other Operating Expenses</td>
        </tr>

        <?php foreach ($projects as $project) { ?>

            <?php
                $items = !empty($project['items']) ? $project['items'] : array(array(
                    'item_description' => '',
                    'ppmp_quantity' => '',
                    'unit_code' => '',
                    'ppmp_cost' => 0
                ));

                $rowspan = count($items);

                switch ((int) $project['ppmp_project_type']) {
                    case 1:
                        $project_type = 'Goods';
                        break;
                    case 2:
                        $project_type = 'Infrastructure';
                        break;
                    case 3:
                        $project_type = 'Services';
                        break;
                    default:
                        $project_type = '—';
                }

                $first = true;
            ?>

            <?php foreach ($items as $item) { ?>

                <?php
                    $qty = (float) $item['ppmp_quantity'];
                    $total_cost = (float) $item['ppmp_cost'];
                    $unit_cost = $qty > 0 ? $total_cost / $qty : $total_cost;
                ?>

                <tr class="data-row">

                    <?php if ($first) { ?>

                        <td rowspan="<?php echo $rowspan; ?>">
                            <?php echo $project['ppmp_general_description']; ?>
                        </td>

                        <td rowspan="<?php echo $rowspan; ?>" class="text-center">
                            <?php echo $project_type; ?>
                        </td>

                        <td rowspan="<?php echo $rowspan; ?>" class="text-center">
                            1 lot
                        </td>

                        <td rowspan="<?php echo $rowspan; ?>" class="text-center">
                            <?php echo !empty($project['proc_code']) ? $project['proc_code'] : $project['proc_name']; ?>
                        </td>

                        <td rowspan="<?php echo $rowspan; ?>" class="text-center">
                            <?php echo ((int) $project['ppmp_pre_proc'] === 1) ? 'Yes' : 'No'; ?>
                        </td>

                        <td rowspan="<?php echo $rowspan; ?>" class="text-center">
                            <?php echo $project['ppmp_start_proc']; ?>
                        </td>

                        <td rowspan="<?php echo $rowspan; ?>" class="text-center">
                            <?php echo $project['ppmp_end_proc']; ?>
                        </td>

                        <td rowspan="<?php echo $rowspan; ?>" class="text-center">
                            <?php echo $project['ppmp_delivery']; ?>
                        </td>

                        <td rowspan="<?php echo $rowspan; ?>" class="text-center">
                            <?php echo $project['fund_name']; ?>
                        </td>

                        <td rowspan="<?php echo $rowspan; ?>" class="text-right">
                            <?php echo number_format($project['ppmp_budget'], 2); ?>
                        </td>

                        <td rowspan="<?php echo $rowspan; ?>" class="text-center">
                            <?php echo $project['ppmp_supporting_docs']; ?>
                        </td>

                        <td rowspan="<?php echo $rowspan; ?>" class="text-center">
                            <?php echo $project['ppmp_remarks']; ?>
                        </td>

                    <?php $first = false; } ?>

                    <td class="text-center">
                        <?php echo $qty; ?>
                    </td>

                    <td class="text-right">
                        <?php echo number_format($unit_cost, 2); ?>
                    </td>

                    <td class="text-right">
                        <?php echo number_format($total_cost, 2); ?>
                    </td>

                    <td class="text-center">
                        <?php echo $qty; ?>
                    </td>

                    <td class="text-right">
                        <?php echo number_format($unit_cost, 2); ?>
                    </td>

                    <td class="text-right">
                        <?php echo number_format($total_cost, 2); ?>
                    </td>

                    <td class="text-right">
                        <?php echo number_format($total_cost, 2); ?>
                    </td>

                </tr>

            <?php } ?>

        <?php } ?>
    </tbody>
</table>

</body>
</html>