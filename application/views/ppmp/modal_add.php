<div class="modal fade" id="ppmpAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <form action="<?php echo base_url('PPMP/savePPMP'); ?>" method="post" id="formPPMPAdd">
                <input type="text" name="ppmp_id" value="<?php echo $ppmp_id; ?>">
                <div class="modal-header py-2">
                    <h5 class="modal-title">Add PPMP</h5>
                    <button class="close" type="button" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="ppmp_year" value="<?php echo $year; ?>">

                    <div id="projectContainer"></div>

                    <button type="button" class="btn btn-sm btn-secondary" id="addProject">
                        + Add Procurement Project
                    </button>

                </div>

                <div class="modal-footer py-2">
                    <button class="btn btn-sm btn-secondary" type="button" data-dismiss="modal">
                        Cancel
                    </button>

                    <button class="btn btn-sm btn-primary" type="submit">
                        Submit
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- PROJECT TEMPLATE -->
<div id="projectTemplate" class="d-none">

    <div class="card mb-2 ppmp-project">

        <div class="card-header py-2 d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-link p-0 text-left project-toggle">
                <strong>Procurement Project</strong>
                <small class="text-muted project-summary">(New Project)</small>
            </button>

            <button type="button" class="btn btn-sm btn-danger remove-project d-none">
                Remove
            </button>
        </div>

        <div class="project-body collapse">
            <div class="card-body py-2">

                <div class="form-group mb-2">
                    <label class="small mb-1">Procurement Project Description</label>
                    <textarea class="form-control form-control-sm project-description"
                              name="ppmp_general_description[]"
                              rows="2"
                              required></textarea>
                </div>

                <div class="form-row">

                    <div class="form-group col-md-3 mb-2">
                        <label class="small mb-1">Project Type</label>
                        <select class="form-control form-control-sm" name="ppmp_project_type[]" required>
                            <option value="">Select</option>
                            <option value="1">Goods</option>
                            <option value="2">Infrastructure</option>
                            <option value="3">Consulting Services</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3 mb-2">
                        <label class="small mb-1">Procurement Mode</label>
                        <select class="form-control form-control-sm" name="proc_id[]" required>
                            <option value="">Select</option>
                            <?php foreach($mode as $m){ ?>
                                <option value="<?php echo $m->proc_id; ?>">
                                    <?php echo $m->proc_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group col-md-3 mb-2">
                        <label class="small mb-1">Fund Source</label>
                        <select class="form-control form-control-sm" name="fund_id[]" required>
                            <option value="">Select</option>
                            <?php foreach($source as $s){ ?>
                                <option value="<?php echo $s->fund_id; ?>">
                                    <?php echo $s->fund_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group col-md-3 mb-2">
                        <label class="small mb-1">Attachment</label>
                        <select class="form-control form-control-sm" name="attachment_id[]" required>
                            <option value="">Select</option>
                            <?php foreach($attachment as $a){ ?>
                                <option value="<?php echo $a->attachment_id; ?>">
                                    <?php echo $a->attachment_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group col-md-3 mb-2">
                        <label class="small mb-1">Start Date</label>
                        <input type="date" class="form-control form-control-sm" name="ppmp_start_proc[]" required>
                    </div>

                    <div class="form-group col-md-3 mb-2">
                        <label class="small mb-1">End Date</label>
                        <input type="date" class="form-control form-control-sm" name="ppmp_end_proc[]" required>
                    </div>

                    <div class="form-group col-md-3 mb-2">
                        <label class="small mb-1">Delivery Date</label>
                        <input type="date" class="form-control form-control-sm" name="ppmp_delivery[]" required>
                    </div>

                    <div class="form-group col-md-3 mb-2">
                        <label class="small mb-1">Budget</label>
                        <input type="number"
                               step="0.01"
                               min="1"
                               class="form-control form-control-sm project-budget"
                               name="ppmp_budget[]"
                               required>
                    </div>

                </div>

                <div class="form-group mb-2">
                    <div class="form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               value="1"
                               name="ppmp_pre_proc[]">

                        <label class="form-check-label small">
                            For Pre-Procurement
                        </label>
                    </div>
                </div>

                <div class="form-group mb-2">
                    <label class="small mb-1">Remarks</label>
                    <textarea class="form-control form-control-sm"
                              name="ppmp_remarks[]"
                              rows="2"></textarea>
                </div>

                <div class="border rounded p-2 bg-light">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="small">Items</strong>

                        <button type="button" class="btn btn-sm btn-primary add-item">
                            + Add Line
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 40%;">Item</th>
                                    <th style="width: 12%;">Qty</th>
                                    <th style="width: 18%;">Unit</th>
                                    <th style="width: 15%;">Unit Cost</th>
                                    <th style="width: 15%;">Total Cost</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody class="items-container"></tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>

    </div>

</div>


<!-- ITEM TEMPLATE -->
<table class="d-none">
    <tbody id="itemTemplate">

        <tr class="item-row">

            <td>
                <select class="form-control form-control-sm item-select"
                        name="item_id[0][]"
                        required>
                    <option value="">Select Item</option>

                    <?php foreach($items as $i){ ?>
                        <option value="<?php echo $i->item_id; ?>">
                            <?php echo $i->item_description; ?>
                        </option>
                    <?php } ?>
                </select>
            </td>

            <td>
                <input type="number"
                       min="1"
                       class="form-control form-control-sm qty-input"
                       name="ppmp_quantity[0][]"
                       required>
            </td>

            <td>
                <select class="form-control form-control-sm unit-select"
                        name="unit_id[0][]"
                        required>
                    <option value="">Select</option>

                    <?php foreach($unit as $u){ ?>
                        <option value="<?php echo $u->unit_id; ?>">
                            <?php echo $u->unit_code; ?>
                        </option>
                    <?php } ?>
                </select>
            </td>

            <td>
                <input type="number"
                       step="0.01"
                       min="0.01"
                       class="form-control form-control-sm unit-cost-input"
                       name="ppmp_unit_cost[0][]"
                       required>
            </td>

            <td>
                <input type="number"
                       step="0.01"
                       min="0.01"
                       class="form-control form-control-sm total-cost-input"
                       name="ppmp_cost[0][]"
                       readonly>
            </td>

            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-item">
                    ×
                </button>
            </td>

        </tr>

    </tbody>
</table>


<script>
$(document).ready(function () {

    function closeAllProjects() {
        $('.project-body').removeClass('show');
        $('.project-toggle')
            .addClass('collapsed')
            .attr('aria-expanded', 'false');
    }

    function openProject(projectCard) {
        closeAllProjects();

        projectCard.find('.project-body').addClass('show');

        projectCard.find('.project-toggle')
            .removeClass('collapsed')
            .attr('aria-expanded', 'true');
    }

    function cloneProject() {
        let project = $('#projectTemplate')
            .children()
            .first()
            .clone();

        project.find('input, textarea').val('');
        project.find('select').prop('selectedIndex', 0);
        project.find('input[type="checkbox"]').prop('checked', false);
        project.find('.items-container').empty();
        project.find('.project-summary').text('(New Project)');

        return project;
    }

    function cloneItem(projectIndex) {
        let item = $('#itemTemplate')
            .children()
            .first()
            .clone();

        item.find('input').val('');
        item.find('select').prop('selectedIndex', 0);

        updateItemIndex(item, projectIndex);

        return item;
    }

    function updateItemIndex(itemRow, projectIndex) {
        itemRow.find('[name]').each(function () {
            let name = $(this).attr('name');

            if (name) {
                name = name.replace(/\[\d+\]/, '[' + projectIndex + ']');
                $(this).attr('name', name);
            }
        });
    }

    function reindexProjects() {
        $('#projectContainer .ppmp-project').each(function (projectIndex) {

            $(this).find('[name^="ppmp_general_description"]')
                .attr('name', 'ppmp_general_description[' + projectIndex + ']');

            $(this).find('[name^="ppmp_project_type"]')
                .attr('name', 'ppmp_project_type[' + projectIndex + ']');

            $(this).find('[name^="proc_id"]')
                .attr('name', 'proc_id[' + projectIndex + ']');

            $(this).find('[name^="fund_id"]')
                .attr('name', 'fund_id[' + projectIndex + ']');

            $(this).find('[name^="attachment_id"]')
                .attr('name', 'attachment_id[' + projectIndex + ']');

            $(this).find('[name^="ppmp_start_proc"]')
                .attr('name', 'ppmp_start_proc[' + projectIndex + ']');

            $(this).find('[name^="ppmp_end_proc"]')
                .attr('name', 'ppmp_end_proc[' + projectIndex + ']');

            $(this).find('[name^="ppmp_delivery"]')
                .attr('name', 'ppmp_delivery[' + projectIndex + ']');

            $(this).find('[name^="ppmp_budget"]')
                .attr('name', 'ppmp_budget[' + projectIndex + ']');

            $(this).find('[name^="ppmp_pre_proc"]')
                .attr('name', 'ppmp_pre_proc[' + projectIndex + ']');

            $(this).find('[name^="ppmp_remarks"]')
                .attr('name', 'ppmp_remarks[' + projectIndex + ']');

            $(this).find('.item-row').each(function () {
                updateItemIndex($(this), projectIndex);
            });

        });
    }

    function calculateRow(row) {
        let qty = parseFloat(row.find('.qty-input').val()) || 0;
        let unitCost = parseFloat(row.find('.unit-cost-input').val()) || 0;
        let total = qty * unitCost;

        row.find('.total-cost-input').val(total.toFixed(2));

        updateProjectSummary(row.closest('.ppmp-project'));
    }

    function updateProjectSummary(projectCard) {
        let desc = projectCard.find('.project-description').val().trim();
        let itemCount = projectCard.find('.item-row').length;

        let total = 0;

        projectCard.find('.total-cost-input').each(function () {
            total += parseFloat($(this).val()) || 0;
        });

        projectCard.find('.project-budget').val(total.toFixed(2));

        if (desc.length > 45) {
            desc = desc.substring(0, 45) + '...';
        }

        let summary = desc || '(New Project)';
        summary += ' — ' + itemCount + ' item' + (itemCount > 1 ? 's' : '');
        summary += ' | ₱' + total.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        projectCard.find('.project-summary').text(summary);
    }

    function addProject() {
        let project = cloneProject();
        let projectIndex = $('#projectContainer .ppmp-project').length;

        project.find('.items-container').append(cloneItem(projectIndex));

        $('#projectContainer').append(project);

        reindexProjects();
        openProject(project);
        updateProjectSummary(project);
    }

    $('#addProject').on('click', function () {
        addProject();
    });

    $(document).on('click', '.project-toggle', function () {
        openProject($(this).closest('.ppmp-project'));
    });

    $(document).on('click', '.add-item', function () {
        let projectCard = $(this).closest('.ppmp-project');
        let projectIndex = $('#projectContainer .ppmp-project').index(projectCard);

        projectCard.find('.items-container').append(cloneItem(projectIndex));

        openProject(projectCard);
        updateProjectSummary(projectCard);
    });

    $(document).on('click', '.remove-item', function () {
        let projectCard = $(this).closest('.ppmp-project');

        if (projectCard.find('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
        }

        updateProjectSummary(projectCard);
    });

    $(document).on('click', '.remove-project', function () {
        let project = $(this).closest('.ppmp-project');

        project.remove();
        reindexProjects();

        let lastProject = $('#projectContainer .ppmp-project').last();

        if (lastProject.length) {
            openProject(lastProject);
        }
    });

    $(document).on('change', '.item-select', function () {
        let selected = $(this).find('option:selected');
        let row = $(this).closest('.item-row');

        let unitId = selected.data('unit') || '';
        let cost = selected.data('cost') || '';

        row.find('.unit-select').val(unitId);
        row.find('.unit-cost-input').val(cost);

        calculateRow(row);
        updateProjectSummary($(this).closest('.ppmp-project'));
    });

    $(document).on('keyup change', '.qty-input, .unit-cost-input', function () {
        let row = $(this).closest('.item-row');

        calculateRow(row);
        updateProjectSummary($(this).closest('.ppmp-project'));
    });

    $(document).on('keyup change', '.project-description', function () {
        updateProjectSummary($(this).closest('.ppmp-project'));
    });

    // Initial default project
    addProject();

});
</script>