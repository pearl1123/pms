<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Purchase Request List</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btnAdd">
            <i class="fas fa-plus fa-sm text-white-50"></i> Create Purchase Request</button>
    </div>

    <hr/>

    <!-- BREADCRUMBS
    ========================================================================================================================================= -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchase Request List</li>
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
            <h6 class="m-0 font-weight-bold text-primary">Purchase Request List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tblPR" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>PR No.</th>
                            <th>SAI No.</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Encoded By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No.</th>
                            <th>PR No.</th>
                            <th>SAI No.</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Encoded By</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody id="prBody">
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
        var tblPR = $('#tblPR').DataTable({
            "responsive": true, 
            "autoWidth": false,
            'serverSide': true,  
            'processing':true,
            'paging': true,
            "lengthMenu": [10, 15, 20],
            "ajax": {
                url : "<?php echo base_url("purchaserequest/getPRList/"); ?>",
                type : 'GET'    
            },
            "language": {
                "emptyTable": "No Results"
            },
            columns: [
                { data: 'id', name: 'pr_id'},
                { data: 'pr_id', name: 'pr_no'},
                { data: 'sai_id', name: 'sai_no'},
                { data: 'office', name: 'office_desc'},
                { data: 'date', name: 'pr_date'},
                { data: 'encoded_by', name: 'encoded_by'},       
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

        $('#prBody').on('click', '#btnUpdate', function () {
            var data = ($(this).parents('tr').hasClass('child') )
                            ? tblPR.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                            : tblPR.row($(this).parents('tr')).data(); // otherwise get original data
            $('#attachmentID').val(data.id);
            $('#attachmentName_update').val(data.name);
            $('#attachmentUpdateModal').modal('show');
        });

        $('#prBody').on('click', '#btnDel', function () {
            var data = ($(this).parents('tr').hasClass('child') )
                        ? tblPR.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                        : tblPR.row($(this).parents('tr')).data(); // otherwise get original data
            var id = data.id;
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
                        url: "<?php echo base_url('Libraries/deleteAttachment'); ?>",
                        async: false,
                        type: "POST",
                        datatype: "json",
                        data: {id:id},
                        success: function(data) {
                            Swal.fire({
                                title: 'Success',
                                html: "<span class = 'text-success'><b>SUCCESS!</b></span> Successfully deleted attachment record.",
                                type: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK!'
                            }).then((result) => {
                                location.reload();
                            });
                        }
                    });
            
                }
            });
        });

    }

    $(document).ready(function() {
        datatable();

        $('#btnAdd').on('click', function(){    
            $('#prAddModal').modal("show"); 
        });
    });
</script>