<div class="modal fade" id="ppmpAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add PPMP Item</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                  <ul class="nav nav-tabs" id="myTab" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button class="nav-link active"
                                id="single-tab"
                                data-toggle="tab"
                                data-target="#single"
                                type="button">
                            Single Item
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link"
                                id="multiple-tab"
                                data-toggle="tab"
                                data-target="#multiple"
                                type="button">
                            Multiple Items
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content border border-top-0 p-3">
                    <div class="tab-pane fade show active" id="single">
                        <h4>Single Item Content</h4>
                        <!-- <form action="<?php echo base_url('PPMP/savePPMP'); ?>" method="post" id="formPPMPAdd">
                            <input type="hidden" name="ppmp_year" value="<?php echo $year; ?>">

                            <p class="text-muted font-weight-bold text-uppercase small mb-2">Project Details</p>
                            <div class="form-group">
                                <label>Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="ppmp_general_description" rows="3" required
                                    placeholder="General description and objective of the project..."></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Project Type <span class="text-danger">*</span></label>
                                    <select class="form-control" name="ppmp_project_type" required>
                                        <option value="">Select Project Type</option>
                                        <option value="1">Goods</option>
                                        <option value="2">Infrastructure</option>
                                        <option value="3">Consulting Services</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Procurement Mode <span class="text-danger">*</span></label>
                                    <select class="form-control" name="proc_id" required>
                                        <option value="">Select Procurement Mode</option>
                                        <?php foreach($mode as $m){ ?>
                                            <option value="<?php echo $m->proc_id; ?>"><?php echo $m->proc_name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Quantity <span class="text-danger">*</span></label>
                                    <input type="number" min="1" class="form-control" name="ppmp_quantity" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Unit <span class="text-danger">*</span></label>
                                    <select class="form-control" name="unit_id" required>
                                        <option value="">Select Unit</option>
                                        <?php foreach($unit as $u){ ?>
                                            <option value="<?php echo $u->unit_id; ?>"><?php echo $u->unit_code; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <hr class="my-3">
                            <p class="text-muted font-weight-bold text-uppercase small mb-2">Schedule</p>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="ppmp_start_proc" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="ppmp_end_proc" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Delivery Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="ppmp_delivery" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="ppmp_pre_proc" id="pre_proc">
                                    <label class="form-check-label" for="pre_proc">For Pre-Procurement</label>
                                </div>
                            </div>

                            <hr class="my-3">
                            <p class="text-muted font-weight-bold text-uppercase small mb-2">Financials</p>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Fund Source <span class="text-danger">*</span></label>
                                    <select class="form-control" name="fund_id" required>
                                        <option value="">Select Fund Source</option>
                                        <?php foreach($source as $s){ ?>
                                            <option value="<?php echo $s->fund_id; ?>"><?php echo $s->fund_name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Attachment <span class="text-danger">*</span></label>
                                    <select class="form-control" name="attachment_id" required>
                                        <option value="">Select Attachment</option>
                                        <?php foreach($attachment as $a){ ?>
                                            <option value="<?php echo $a->attachment_id; ?>"><?php echo $a->attachment_name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Budget (PhP) <span class="text-danger">*</span></label>
                                    <input type="number" min="1" step="0.01" class="form-control" name="ppmp_budget" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Cost (PhP) <span class="text-danger">*</span></label>
                                    <input type="number" min="0.01" step="0.01" class="form-control" name="ppmp_cost" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Remarks <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="ppmp_remarks" rows="3" required></textarea>
                            </div>

                        </form> -->
                        <form action="<?php echo base_url('PPMP/savePPMP'); ?>" method="post">
                            <input type="hidden" name="ppmp_year" value="<?php echo $year; ?>">
                            <div id="ppmp-container">
                                <div class="card mb-2 ppmp-item">
                                    <!-- Header -->
                                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                        <button class="btn btn-link p-0 text-left flex-grow-1"
                                                type="button"
                                                data-toggle="collapse"
                                                data-target="#ppmp_1">

                                            <strong>PPMP Entry</strong>
                                            <small class="text-muted entry-summary">
                                                (New Item)
                                            </small>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-danger remove-entry d-none">
                                            Remove
                                        </button>
                                    </div>

                                    <!-- Body -->
                                    <div id="ppmp_1" class="collapse show">
                                        <div class="card-body py-2">

                                            <div class="form-group mb-2">
                                                <label class="small mb-1">
                                                    Description
                                                </label>

                                                <textarea class="form-control form-control-sm desc-field"
                                                        name="ppmp_general_description[]"
                                                        rows="2"
                                                        required></textarea>
                                            </div>

                                            <div class="form-row">

                                                <div class="form-group col-md-3 mb-2">
                                                    <label class="small mb-1">
                                                        Type
                                                    </label>

                                                    <select class="form-control form-control-sm"
                                                            name="ppmp_project_type[]"
                                                            required>

                                                        <option value="">Select</option>
                                                        <option value="1">Goods</option>
                                                        <option value="2">Infrastructure</option>
                                                        <option value="3">Consulting</option>

                                                    </select>
                                                </div>

                                                <div class="form-group col-md-3 mb-2">
                                                    <label class="small mb-1">
                                                        Qty
                                                    </label>

                                                    <input type="number"
                                                        class="form-control form-control-sm"
                                                        name="ppmp_quantity[]"
                                                        required>
                                                </div>

                                                <div class="form-group col-md-3 mb-2">
                                                    <label class="small mb-1">
                                                        Budget
                                                    </label>

                                                    <input type="number"
                                                        class="form-control form-control-sm"
                                                        name="ppmp_budget[]"
                                                        step="0.01"
                                                        required>
                                                </div>

                                                <div class="form-group col-md-3 mb-2">
                                                    <label class="small mb-1">
                                                        Cost
                                                    </label>

                                                    <input type="number"
                                                        class="form-control form-control-sm"
                                                        name="ppmp_cost[]"
                                                        step="0.01"
                                                        required>
                                                </div>

                                            </div>

                                            <div class="form-row">

                                                <div class="form-group col-md-4 mb-2">
                                                    <label class="small mb-1">
                                                        Procurement Mode
                                                    </label>

                                                    <select class="form-control form-control-sm"
                                                            name="proc_id[]"
                                                            required>

                                                        <option value="">Select</option>

                                                        <?php foreach($mode as $m){ ?>
                                                            <option value="<?php echo $m->proc_id; ?>">
                                                                <?php echo $m->proc_name; ?>
                                                            </option>
                                                        <?php } ?>

                                                    </select>
                                                </div>

                                                <div class="form-group col-md-4 mb-2">
                                                    <label class="small mb-1">
                                                        Fund
                                                    </label>

                                                    <select class="form-control form-control-sm"
                                                            name="fund_id[]"
                                                            required>

                                                        <option value="">Select</option>

                                                        <?php foreach($source as $s){ ?>
                                                            <option value="<?php echo $s->fund_id; ?>">
                                                                <?php echo $s->fund_name; ?>
                                                            </option>
                                                        <?php } ?>

                                                    </select>
                                                </div>

                                                <div class="form-group col-md-4 mb-2">
                                                    <label class="small mb-1">
                                                        Unit
                                                    </label>

                                                    <select class="form-control form-control-sm"
                                                            name="unit_id[]"
                                                            required>

                                                        <option value="">Select</option>

                                                        <?php foreach($unit as $u){ ?>
                                                            <option value="<?php echo $u->unit_id; ?>">
                                                                <?php echo $u->unit_code; ?>
                                                            </option>
                                                        <?php } ?>

                                                    </select>
                                                </div>

                                            </div>

                                            <div class="form-row">

                                                <div class="form-group col-md-4 mb-2">
                                                    <label class="small mb-1">
                                                        Start
                                                    </label>

                                                    <input type="date"
                                                        class="form-control form-control-sm"
                                                        name="ppmp_start_proc[]"
                                                        required>
                                                </div>

                                                <div class="form-group col-md-4 mb-2">
                                                    <label class="small mb-1">
                                                        End
                                                    </label>

                                                    <input type="date"
                                                        class="form-control form-control-sm"
                                                        name="ppmp_end_proc[]"
                                                        required>
                                                </div>

                                                <div class="form-group col-md-4 mb-2">
                                                    <label class="small mb-1">
                                                        Delivery
                                                    </label>

                                                    <input type="date"
                                                        class="form-control form-control-sm"
                                                        name="ppmp_delivery[]"
                                                        required>
                                                </div>

                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="small mb-1">
                                                    Remarks
                                                </label>

                                                <textarea class="form-control form-control-sm"
                                                        rows="2"
                                                        name="ppmp_remarks[]"></textarea>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </div>

                            <button type="button"
                                    id="addMore"
                                    class="btn btn-sm btn-secondary">
                                + Add Entry
                            </button>

                            <button type="submit"
                                    class="btn btn-sm btn-primary">
                                Submit
                            </button>

                        </form>
                    </div>

                    <div class="tab-pane fade" id="multiple">
                         <form action="<?php echo base_url('PPMP/savePPMP'); ?>"
      method="post"
      id="formPPMPMultiple">

    <input type="hidden"
           name="ppmp_year"
           value="<?php echo $year; ?>">

    <div id="project-container">

        <!-- PROJECT -->
        <div class="card mb-3 project-card">

            <div class="card-header py-2 d-flex justify-content-between align-items-center">

                <strong>
                    Procurement Project
                </strong>

                <div>
                    <button type="button"
                            class="btn btn-sm btn-danger remove-project d-none">

                        Remove Project
                    </button>
                </div>

            </div>

            <div class="card-body py-2">

                <!-- Project Details -->
                <div class="form-group mb-2">
                    <label class="small">
                        Procurement Project Description
                    </label>

                    <textarea class="form-control form-control-sm"
                              name="project_description[]"
                              rows="2"
                              required></textarea>
                </div>

                <div class="form-row">

                    <div class="form-group col-md-4 mb-2">
                        <label class="small">
                            Project Type
                        </label>

                        <select class="form-control form-control-sm"
                                name="ppmp_project_type[]"
                                required>

                            <option value="">Select</option>
                            <option value="1">Goods</option>
                            <option value="2">Infrastructure</option>
                            <option value="3">Consulting</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 mb-2">
                        <label class="small">
                            Procurement Mode
                        </label>

                        <select class="form-control form-control-sm"
                                name="proc_id[]"
                                required>

                            <option value="">Select</option>

                            <?php foreach($mode as $m){ ?>
                                <option value="<?php echo $m->proc_id; ?>">
                                    <?php echo $m->proc_name; ?>
                                </option>
                            <?php } ?>

                        </select>
                    </div>

                    <div class="form-group col-md-4 mb-2">
                        <label class="small">
                            Fund Source
                        </label>

                        <select class="form-control form-control-sm"
                                name="fund_id[]"
                                required>

                            <option value="">Select</option>

                            <?php foreach($source as $s){ ?>
                                <option value="<?php echo $s->fund_id; ?>">
                                    <?php echo $s->fund_name; ?>
                                </option>
                            <?php } ?>

                        </select>
                    </div>

                </div>

                <!-- Items -->
                <div class="border rounded p-2 bg-light">

                    <div class="d-flex justify-content-between mb-2">
                        <strong>Items</strong>

                        <button type="button"
                                class="btn btn-sm btn-primary add-item">

                            + Add Item
                        </button>
                    </div>

                    <div class="items-container">

                        <div class="item-row border rounded p-2 mb-2 bg-white">

                            <div class="form-row align-items-end">

                                <div class="form-group col-md-5 mb-1">
                                    <label class="small">
                                        Item Description
                                    </label>

                                    <input type="text"
                                           class="form-control form-control-sm"
                                           name="item_description[0][]"
                                           required>
                                </div>

                                <div class="form-group col-md-2 mb-1">
                                    <label class="small">
                                        Qty
                                    </label>

                                    <input type="number"
                                           class="form-control form-control-sm"
                                           name="ppmp_quantity[0][]"
                                           required>
                                </div>

                                <div class="form-group col-md-2 mb-1">
                                    <label class="small">
                                        Unit
                                    </label>

                                    <select class="form-control form-control-sm"
                                            name="unit_id[0][]"
                                            required>

                                        <option value="">
                                            Select
                                        </option>

                                        <?php foreach($unit as $u){ ?>
                                            <option value="<?php echo $u->unit_id; ?>">
                                                <?php echo $u->unit_code; ?>
                                            </option>
                                        <?php } ?>

                                    </select>
                                </div>

                                <div class="form-group col-md-2 mb-1">
                                    <label class="small">
                                        Cost
                                    </label>

                                    <input type="number"
                                           step="0.01"
                                           class="form-control form-control-sm"
                                           name="ppmp_cost[0][]"
                                           required>
                                </div>

                                <div class="form-group col-md-1 mb-1">
                                    <button type="button"
                                            class="btn btn-danger btn-sm remove-item">

                                        ×
                                    </button>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <button type="button"
            id="addProject"
            class="btn btn-secondary btn-sm">

        + Add Procurement Project
    </button>

</form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="submit" form="formPPMPAdd">Submit</button>
            </div>
        </div>
    </div>
</div>
<script>
let count = 1;

$('#addMore').click(function () {

    count++;

    // Collapse ALL current items
    $('.ppmp-item .collapse')
        .removeClass('show')
        .css('height', '');

    // Clone first item
    let clone = $('.ppmp-item:first').clone();

    // Reset form fields
    clone.find('input:not([type="checkbox"])').val('');
    clone.find('textarea').val('');
    clone.find('select').prop('selectedIndex', 0);
    clone.find('input[type="checkbox"]')
         .prop('checked', false);

    // Show remove button
    clone.find('.remove-entry')
         .removeClass('d-none');

    // New collapse ID
    let newId = 'ppmp_' + count;

    // Reset collapse state completely
    clone.find('.collapse')
         .attr('id', newId)
         .removeClass('show')
         .css('height', '');

    // Update button target
    clone.find('[data-target]')
         .attr('data-target', '#' + newId)
         .attr('aria-expanded', 'true');

    // Append clone
    $('#ppmp-container').append(clone);

    // Force newly added item open
    $('#' + newId)
        .addClass('show');
});

$(document).on('click', '.remove-entry', function () {
    $(this).closest('.ppmp-item').remove();
});

$(document).on('keyup', '.desc-field', function () {

    let text = $(this).val().substring(0, 50);

    $(this)
        .closest('.ppmp-item')
        .find('.entry-summary')
        .text(text || '(New Item)');
});

let projectCount = 1;

$('#addProject').click(function () {

    // Force minimize ALL existing projects
    $('.project-collapse')
        .removeClass('show')
        .css('height', '');

    $('.project-toggle')
        .addClass('collapsed')
        .attr('aria-expanded', 'false');

    // Clone first project
    let clone =
        $('.project-card:first')
        .clone();

    // Reset values
    clone.find('input:not([type="checkbox"])')
         .val('');

    clone.find('textarea')
         .val('');

    clone.find('select')
         .prop('selectedIndex', 0);

    clone.find('input[type="checkbox"]')
         .prop('checked', false);

    // Show remove button
    clone.find('.remove-project')
         .removeClass('d-none');

    // Keep only one item row
    clone.find('.item-row:not(:first)')
         .remove();

    clone.find('.item-row:first')
         .find('input')
         .val('');

    clone.find('.item-row:first')
         .find('select')
         .prop('selectedIndex', 0);

    // New collapse ID
    let collapseId =
        'project_' + projectCount;

    // Configure collapse section
    clone.find('.project-collapse')
         .attr('id', collapseId)
         .removeClass('show')
         .addClass('show'); // NEW ONE OPEN

    // Configure toggle
    clone.find('.project-toggle')
         .attr('data-target',
             '#' + collapseId)
         .removeClass('collapsed')
         .attr('aria-expanded', 'true');

    // Append clone
    $('#project-container')
         .append(clone);

    projectCount++;

});

$(document).on('click', '.add-item', function () {

    let projectCard =
        $(this).closest('.project-card');

    let projectIndex =
        $('.project-card')
        .index(projectCard);

    let clone =
        projectCard
        .find('.item-row:first')
        .clone();

    clone.find('input')
         .val('');

    clone.find('select')
         .prop('selectedIndex', 0);

    clone.find('input, select')
         .each(function () {

        let name = $(this).attr('name');

        name = name.replace(/\[\d+\]/,
            '[' + projectIndex + ']');

        $(this).attr('name', name);
    });

    projectCard
        .find('.items-container')
        .append(clone);

});


$(document).on('click',
'.remove-item',
function () {

    let container =
        $(this)
        .closest('.items-container');

    if (container.find('.item-row').length > 1) {

        $(this)
        .closest('.item-row')
        .remove();
    }

});

$(document).on(
    'show.bs.collapse',
    '.project-collapse',
    function () {

        $('.project-collapse')
            .not(this)
            .collapse('hide');
    }
);


$(document).on('click',
'.remove-project',
function () {

    $(this)
    .closest('.project-card')
    .remove();
});
</script>