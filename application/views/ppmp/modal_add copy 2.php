<div class="modal fade" id="ppmpAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header py-2">
                <h5 class="modal-title">Add PPMP Item</h5>
                <button class="close" type="button" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <ul class="nav nav-tabs" id="ppmpTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active"
                                data-toggle="tab"
                                data-target="#singleTab"
                                type="button">
                            Single Item
                        </button>
                    </li>

                    <li class="nav-item">
                        <button class="nav-link"
                                data-toggle="tab"
                                data-target="#multipleTab"
                                type="button">
                            Multiple Items
                        </button>
                    </li>
                </ul>

                <div class="tab-content border border-top-0 p-2">

                    <!-- SINGLE TAB -->
                    <div class="tab-pane fade show active" id="singleTab">

                        <form action="<?php echo base_url('PPMP/savePPMP'); ?>"
                              method="post"
                              id="formPPMPSingle">

                            <input type="hidden" name="ppmp_year" value="<?php echo $year; ?>">
                            <input type="hidden" name="submission_type" value="single">

                            <div id="singleProjectContainer"></div>

                            <button type="button"
                                    class="btn btn-sm btn-secondary mt-2"
                                    id="addSingleProject">
                                + Add Single Project
                            </button>

                        </form>

                    </div>

                    <!-- MULTIPLE TAB -->
                    <div class="tab-pane fade" id="multipleTab">

                        <form action="<?php echo base_url('PPMP/savePPMP'); ?>"
                              method="post"
                              id="formPPMPMultiple">

                            <input type="hidden" name="ppmp_year" value="<?php echo $year; ?>">
                            <input type="hidden" name="submission_type" value="multiple">

                            <div id="multipleProjectContainer"></div>

                            <button type="button"
                                    class="btn btn-sm btn-secondary mt-2"
                                    id="addMultipleProject">
                                + Add Procurement Project
                            </button>

                        </form>

                    </div>

                </div>
            </div>

            <div class="modal-footer py-2">
                <button class="btn btn-sm btn-secondary" type="button" data-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-sm btn-primary" type="button" id="submitActivePPMP">
                    Submit
                </button>
            </div>

        </div>
    </div>
</div>


<!-- PROJECT TEMPLATE -->
<div id="projectTemplate" class="d-none">

    <div class="card mb-2 ppmp-project">

        <div class="card-header py-2 d-flex justify-content-between align-items-center">

            <button type="button"
                    class="btn btn-link p-0 text-left project-toggle">
                <strong class="project-title">Procurement Project</strong>
                <small class="text-muted project-summary">(New Project)</small>
            </button>

            <button type="button"
                    class="btn btn-sm btn-danger remove-project d-none">
                Remove
            </button>

        </div>

        <div class="ppmp-project-collapse collapse">
            <div class="card-body py-2">

                <div class="form-group mb-2">
                    <label class="small mb-1">Procurement Project Description</label>
                    <textarea class="form-control form-control-sm project-description"
                              name="project_description[]"
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
                            <option value="3">Consulting</option>
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
                        <label class="small mb-1">Start</label>
                        <input type="date" class="form-control form-control-sm" name="ppmp_start_proc[]" required>
                    </div>

                    <div class="form-group col-md-3 mb-2">
                        <label class="small mb-1">End</label>
                        <input type="date" class="form-control form-control-sm" name="ppmp_end_proc[]" required>
                    </div>

                    <div class="form-group col-md-3 mb-2">
                        <label class="small mb-1">Delivery</label>
                        <input type="date" class="form-control form-control-sm" name="ppmp_delivery[]" required>
                    </div>

                    <div class="form-group col-md-3 mb-2">
                        <label class="small mb-1">Budget</label>
                        <input type="number" step="0.01" min="1"
                               class="form-control form-control-sm"
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

                <div class="border rounded p-2 bg-light mt-2">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="small">Items</strong>

                        <button type="button"
                                class="btn btn-sm btn-primary add-item">
                            + Add Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0 item-table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 45%;">Item Description</th>
                                    <th style="width: 15%;">Qty</th>
                                    <th style="width: 20%;">Unit</th>
                                    <th style="width: 15%;">Cost</th>
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
                <input type="text"
                       class="form-control form-control-sm"
                       name="item_description[0][]"
                       required>
            </td>

            <td>
                <input type="number"
                       min="1"
                       class="form-control form-control-sm"
                       name="ppmp_quantity[0][]"
                       required>
            </td>

            <td>
                <select class="form-control form-control-sm"
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
                       class="form-control form-control-sm"
                       name="ppmp_cost[0][]"
                       required>
            </td>

            <td class="text-center">
                <button type="button"
                        class="btn btn-sm btn-danger remove-item">
                    ×
                </button>
            </td>
        </tr>
    </tbody>
</table>


<script>
$(document).ready(function () {

    let singleProjectCount = 0;
    let multipleProjectCount = 0;

    function closeAllProjects() {
        $('.ppmp-project-collapse')
            .removeClass('show');

        $('.project-toggle')
            .addClass('collapsed')
            .attr('aria-expanded', 'false');
    }

    function openProject(projectCard) {
        closeAllProjects();

        projectCard.find('.ppmp-project-collapse')
            .addClass('show');

        projectCard.find('.project-toggle')
            .removeClass('collapsed')
            .attr('aria-expanded', 'true');
    }

    function cloneProject(mode) {
        let project = $('#projectTemplate')
            .children()
            .first()
            .clone();

        project.attr('data-mode', mode);

        project.find('input, textarea').val('');
        project.find('select').prop('selectedIndex', 0);
        project.find('input[type="checkbox"]').prop('checked', false);

        project.find('.items-container').empty();
        project.find('.project-summary').text('(New Project)');

        if (mode === 'single') {
            project.find('.project-title').text('Single Procurement');
            project.find('.add-item').remove();
        } else {
            project.find('.project-title').text('Procurement Project');
        }

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

            name = name.replace(/\[\d+\]/, '[' + projectIndex + ']');

            $(this).attr('name', name);
        });
    }

    function reindexProjects(containerSelector) {
        $(containerSelector).find('.ppmp-project').each(function (projectIndex) {

            $(this).find('textarea[name^="project_description"]')
                .attr('name', 'project_description[' + projectIndex + ']');

            $(this).find('select[name^="ppmp_project_type"]')
                .attr('name', 'ppmp_project_type[' + projectIndex + ']');

            $(this).find('select[name^="proc_id"]')
                .attr('name', 'proc_id[' + projectIndex + ']');

            $(this).find('select[name^="fund_id"]')
                .attr('name', 'fund_id[' + projectIndex + ']');

            $(this).find('select[name^="attachment_id"]')
                .attr('name', 'attachment_id[' + projectIndex + ']');

            $(this).find('input[name^="ppmp_start_proc"]')
                .attr('name', 'ppmp_start_proc[' + projectIndex + ']');

            $(this).find('input[name^="ppmp_end_proc"]')
                .attr('name', 'ppmp_end_proc[' + projectIndex + ']');

            $(this).find('input[name^="ppmp_delivery"]')
                .attr('name', 'ppmp_delivery[' + projectIndex + ']');

            $(this).find('input[name^="ppmp_budget"]')
                .attr('name', 'ppmp_budget[' + projectIndex + ']');

            $(this).find('input[name^="ppmp_pre_proc"]')
                .attr('name', 'ppmp_pre_proc[' + projectIndex + ']');

            $(this).find('textarea[name^="ppmp_remarks"]')
                .attr('name', 'ppmp_remarks[' + projectIndex + ']');

            $(this).find('.item-row').each(function () {
                updateItemIndex($(this), projectIndex);
            });

        });
    }

    function updateProjectSummary(projectCard) {
        let desc = projectCard.find('.project-description').val().trim();
        let itemCount = projectCard.find('.item-row').length;

        if (desc.length > 45) {
            desc = desc.substring(0, 45) + '...';
        }

        let summary = desc ? desc : '(New Project)';

        if (itemCount > 0) {
            summary += ' — ' + itemCount + ' item' + (itemCount > 1 ? 's' : '');
        }

        projectCard.find('.project-summary').text(summary);
    }

    function addProject(containerSelector, mode) {
        let project = cloneProject(mode);
        let projectIndex = $(containerSelector).find('.ppmp-project').length;

        let item = cloneItem(projectIndex);
        project.find('.items-container').append(item);

        if (mode === 'single') {
            project.find('.remove-item').addClass('d-none');
        }

        $(containerSelector).append(project);

        reindexProjects(containerSelector);
        openProject(project);
        updateProjectSummary(project);
    }

    $('#addSingleProject').on('click', function () {
        singleProjectCount++;
        addProject('#singleProjectContainer', 'single');
    });

    $('#addMultipleProject').on('click', function () {
        multipleProjectCount++;
        addProject('#multipleProjectContainer', 'multiple');
    });

    $(document).on('click', '.project-toggle', function () {
        let projectCard = $(this).closest('.ppmp-project');
        openProject(projectCard);
    });

    $(document).on('click', '.add-item', function () {
        let projectCard = $(this).closest('.ppmp-project');
        let container = projectCard.closest('form').find('.ppmp-project').parent();
        let projectIndex = container.find('.ppmp-project').index(projectCard);

        let item = cloneItem(projectIndex);

        projectCard.find('.items-container').append(item);

        updateProjectSummary(projectCard);
        openProject(projectCard);
    });

    $(document).on('click', '.remove-item', function () {
        let projectCard = $(this).closest('.ppmp-project');
        let itemCount = projectCard.find('.item-row').length;

        if (itemCount > 1) {
            $(this).closest('.item-row').remove();
        }

        updateProjectSummary(projectCard);
    });

    $(document).on('click', '.remove-project', function () {
        let project = $(this).closest('.ppmp-project');
        let container = project.parent();
        let form = project.closest('form');

        project.remove();

        reindexProjects('#' + container.attr('id'));

        let remainingProjects = form.find('.ppmp-project');

        if (remainingProjects.length > 0) {
            openProject(remainingProjects.last());
        }
    });

    $(document).on('keyup change', '.project-description', function () {
        updateProjectSummary($(this).closest('.ppmp-project'));
    });

    $(document).on('change keyup', '.item-row input, .item-row select', function () {
        updateProjectSummary($(this).closest('.ppmp-project'));
    });

    $('#submitActivePPMP').on('click', function () {
        let activeTab = $('.tab-pane.active');

        if (activeTab.attr('id') === 'singleTab') {
            $('#formPPMPSingle').submit();
        } else {
            $('#formPPMPMultiple').submit();
        }
    });

    $('button[data-toggle="tab"]').on('shown.bs.tab', function () {
        closeAllProjects();

        let activeForm = $('.tab-pane.active').find('form');
        let firstProject = activeForm.find('.ppmp-project').first();

        if (firstProject.length) {
            openProject(firstProject);
        }
    });

    // Initial load
    addProject('#singleProjectContainer', 'single');
    addProject('#multipleProjectContainer', 'multiple');

});
</script>