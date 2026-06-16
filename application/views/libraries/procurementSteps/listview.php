<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Procurement Steps</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btnAdd">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Procurement Step</button>
    </div>

    <hr/>

    <!-- BREADCRUMBS
    ========================================================================================================================================= -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Configuration</li>
            <li class="breadcrumb-item active" aria-current="page">Procurement Steps</li>
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
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Procurement Steps Table</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tblStep" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Procurement Step Description</th>
                            <th>List of Attachments</th>
                            <th style="width: 8%;">Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No.</th>
                            <th>Procurement Step Description</th>
                            <th>List of Attachments</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody id="stepBody">
                    </tbody>
                </table>
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

<script>
    function datatable(){
        var tblStep = $('#tblStep').DataTable({
            "responsive": true, 
            "autoWidth": false,
            'serverSide': true,  
            'processing':true,
            'paging': true,
            "lengthMenu": [10, 15, 20],
            "ajax": {
                url : "<?php echo base_url("Libraries/getProcurementStepsList/"); ?>",
                type : 'GET'    
            },
            "language": {
                "emptyTable": "No Results"
            },
            columns: [
                { data: 'proc_step_id', name: 'id'},
                { data: 'step', name: 'step'},
                { data: 'attachments', name: 'attachments'},      
                { data: 'actions', name: 'actions'},   
            ],
            'columnDefs': [
                {
                    'targets': [1,2,3],
                    'visible': true,
                    'orderable': true
                },
                {   
                    'targets': [0],
                    'visible': false,
                    'orderable': false
                }        
            ],
            order: [1,'desc']
        });

        $('#stepBody').on('click', '#btnDel', function () {
            var data = ($(this).parents('tr').hasClass('child') )
                        ? tblStep.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                        : tblStep.row($(this).parents('tr')).data(); // otherwise get original data
            var id = data.proc_step_id; 
            Swal.fire({
                title: 'Delete?',
                html: "<span class = 'text-danger'><b>WARNING!</b></span> You will not be able to undo this action.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "<?php echo base_url('Libraries/deleteProcurementStep'); ?>",
                        async: false,
                        type: "POST",
                        datatype: "json",
                        data: {id:id},
                        success: function(data) {
                            if (data.status === false) {
                                Swal.fire({
                                    title: 'Error',
                                    html: "<span class = 'text-danger'><b>FAILED!</b></span> Failed to delete procurement step record.",
                                    type: 'error',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK!'
                                }).then((result) => {
                                    location.reload();
                                });
                            } else {   
                                Swal.fire({
                                    title: 'Success',
                                    html: "<span class = 'text-success'><b>SUCCESS!</b></span> Successfully deleted procurement step record.",
                                    type: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK!'
                                }).then((result) => {
                                    location.reload();
                                }); 
                            }
                             $("#procStep")[0].reset();
                        }
                    });
            
                }
            });
        });

        $('#stepBody').on('click', '#btnUpdate', function () {
            var data = ($(this).parents('tr').hasClass('child') )
                        ? tblStep.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                        : tblStep.row($(this).parents('tr')).data(); // otherwise get original data
            var id = data.proc_step_id; 

            //retrieve procurement step details
            $.ajax({
                url: "<?php echo base_url('Libraries/getProcurementStepDetails'); ?>",
                async: false,
                type: "POST",
                dataType: "json",
                data: {id:id},
                success: function(data) {
                    console.log(data);

                    if(data.status == true){
                        data = data.data;
                        console.log(data);
                        $('#stepDescription').val(data.step_details['step_description']);
                        $('#stepId').val(data.step_details['proc_step_id']);
                        //set attachments
                        $('#modeBody input[type="checkbox"]').prop('checked', false); 
                        data.attachments.forEach(function(attachment) {
                            $('#modeBody input[type="checkbox"][value="' + attachment.attachment_id + '"]').prop('checked', true); // Check the checkboxes that are in the attachments array
                            if(attachment.is_required == 1){
                                $('#isRequired_' + attachment.attachment_id).prop('checked', true); // Check the is_required checkbox if it's required
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            html: "<span class = 'text-danger'><b>FAILED!</b></span> Failed to retrieve procurement step details.",
                            type: 'error',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK!'
                        }).then((result) => {
                            location.reload();
                        });
                         return;
                    }
                    
                    $('#stepAddModal').modal("show");
                }
            });
        });

    }

    $(document).ready(function() {
        datatable();
        $('#btnAdd').on('click', function(){    
            $('#stepAddModal').modal("show"); 
        });
    });
</script>