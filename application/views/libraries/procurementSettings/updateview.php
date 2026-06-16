<style>
    .step-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 10px;
        cursor: grab;
        transition: .2s;
    }

    .step-card:hover {
        background: #f8f9fa;
    }

    .drop-zone {
        min-height: 400px;
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 15px;
        background: #fafafa;
    }

    .target-zone {
        background: #f8fbff;
    }

    .sortable-ghost {
        opacity: .4;
    }

    .sortable-chosen {
        background: #e9f3ff;
    }

    .empty-message {
        pointer-events: none;
    }
</style>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Update Procurement Settings</h1>
    </div>

    <hr/>

    <!-- BREADCRUMBS
    ========================================================================================================================================= -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Configuration</li>
            <li class="breadcrumb-item"><a href="<?php echo base_url("Libraries/procurementSettings"); ?>">Procurement Setting</a></li>
            <li class="breadcrumb-item active" aria-current="page">Update Procurement Setting</li>
        </ol>
    </nav>

    <!-- ALERTS
    ========================================================================================================================================= -->
    <?php if ($this->session->flashdata('fail') <> null){ ?>
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
            <span>
                <?php echo $this->session->flashdata('fail'); ?>
            </span>
        </div>
    <?php } ?>

    <?php if ($this->session->flashdata('success') <> null){ ?>
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="fa fa-times" aria-hidden="true"></i>
            </button>
            <span>
                <?php echo $this->session->flashdata('success'); ?>
            </span>
        </div>
    <?php } ?>

    <!-- MAIN CONTENT
    ========================================================================================================================================= -->
        <input  id = "proc_id" name = "proc_id" value = "<?php echo $proc_id; ?>" hidden>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Procurement Setting Table</h6>
            </div>
            <div class="card-body">
                <!-- Step Ordering -->
                <!-- Workflow Builder -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Procurement Workflow Steps</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                <!-- LEFT: Available Steps -->
                                <div class="col-md-5">
                                    <h6 class="mb-3">Available Steps</h6>

                                    <div id="available-steps" class="drop-zone">
                                        <?php 
                                            $curSetIds = array_column($curSet, 'proc_step_id');
                                            foreach($steps as $s){ 
                                                if (!in_array($s->proc_step_id, $curSetIds) ){ ?>
                                                    <div class="step-card" data-id="<?php echo $s->proc_step_id; ?>">
                                                        <i class="bi bi-grip-vertical me-2"></i>
                                                        <?php echo $s->step_description; ?>
                                                    </div>
                                        <?php } } ?>

                                    </div>
                                </div>

                                <!-- RIGHT: Ordered Workflow -->
                                <div class="col-md-7">
                                    <h6 class="mb-3">Workflow Sequence</h6>
                                    <div id="workflow-steps" class="drop-zone target-zone">
                                        <?php if(count($curSet) == 0){ ?>
                                                Drag steps here and arrange in order
                                        <?php } else { 
                                            foreach($curSet as $c){ ?>
                                            <div class="step-card" data-id="<?php echo $c->proc_step_id; ?>" draggable="true" style="">
                                                <span class="badge bg-primary me-2"><?php echo $c->order; ?></span>
                                                <i class="bi bi-grip-vertical me-2"></i>
                                                <?php echo $c->step_description; ?>
                                            </div>
                                        <?php } }  ?>
                                    </div>

                                    <button id="save-order" class="btn btn-primary mt-3">Save Workflow</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<!-- CLOSING TAGS
========================================================================================================================================= -->
</div>
</div>
</div>
</body>
</html>

<!-- SCRIPTS
========================================================================================================================================= -->
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/datatables/dataTables.bootstrap4.min.js'); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const availableSteps = document.getElementById('available-steps');
        const workflowSteps = document.getElementById('workflow-steps');

        new Sortable(availableSteps, {
            group: {
            name: 'workflow',
            pull: true,
            put: true
            },
            sort: false,
            animation: 150,
            onAdd: function () {
            resetStep();
            },
        });

        new Sortable(workflowSteps, {
            group: {
            name: 'workflow',
            pull: true,
            put: true
            },
            animation: 150,

            onAdd: function () {
            removeEmptyMessage();
            updateStepNumbers();
            },

            onSort: function () {
            updateStepNumbers();
            },

            onRemove: function () {
            checkIfEmpty();
            }
        });

        function removeEmptyMessage() {
            const empty =
            workflowSteps.querySelector('.empty-message');

            if (empty) {
            empty.remove();
            }
        }

        function checkIfEmpty() {
            if (
            workflowSteps.querySelectorAll('.step-card').length === 0
            ) {
            workflowSteps.innerHTML = `
                <div class="text-muted text-center p-4 empty-message">
                Drag steps here and arrange in order
                </div>
            `;
            }
        }

        function updateStepNumbers() {
            const items =
            workflowSteps.querySelectorAll('.step-card');

            items.forEach((item, index) => {
            const text =
                item.innerText.replace(/^(\d+[\s.:-]*)+/, '');

            item.innerHTML = `
                <span class="badge bg-primary me-2">
                ${index + 1}
                </span>
                <i class="bi bi-grip-vertical me-2"></i>
                ${text}
            `;
            });
        }

        function resetStep(){
            const items = availableSteps.querySelectorAll('.step-card');

            items.forEach((item, index) => {
            const text =
                item.innerText.replace(/^(\d+[\s.:-]*)+/, '');

            item.innerHTML = `
                <i class="bi bi-grip-vertical me-2"></i>
                ${text}
            `;
            });
        }

        document
            .getElementById('save-order')
            .addEventListener('click', function () {

            const workflow =
                [...workflowSteps.querySelectorAll('.step-card')]
                .map((step, index) => ({
                order: index + 1,
                step: step.innerText.trim(),
                id: step.getAttribute('data-id')
                }));

            swal.fire({
                title: 'Save Workflow?',
                text: 'This will overwrite the existing sequence and will take effect on the next approved PR.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                console.log(result);
                console.log(workflow);
                if (result.value) {
                    $.ajax({
                        url: '<?php echo base_url("Libraries/saveProcurementWorkflow/") . $proc_id; ?>',
                        method: 'POST',
                        data: { workflow: JSON.stringify(workflow)},
                        success: function (response) {
                            swal.fire('Saved!', 'The workflow sequence has been saved.', 'success');
                        },
                        error: function () {
                            swal.fire('Error!', 'There was an error saving the workflow.', 'error');
                        }
                    });
                }
            });
        });
    }); 
</script>