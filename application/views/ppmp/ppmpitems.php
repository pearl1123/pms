<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">PPMP</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btnAdd">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add PPMP Item</button>
    </div>

    <hr/>

    <!-- BREADCRUMBS
    ========================================================================================================================================= -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">PPMP</li>
            <li class="breadcrumb-item active" aria-current="page">PPMP Items</li>
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
            <h6 class="m-0 font-weight-bold text-primary">PPMP Table</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tblPPMP" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>General Description and Objective of the Project to be Procured</th>
                            <th>Type of the Project to be Procured</th>
                            <th>Quantity and Size of the Project to be Procured</th>
                            <th>Recommended Mode of Procurement</th>
                            <th>Source of Funds</th>
                            <th>Estimated Budget / Authorized Budgetary Allocation (PhP)</th>
                            <th>Quantity</th>
                            <th>Unit Cost per Item</th>
                            <th>Encoded By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No.</th>
                            <th>General Description and Objective of the Project to be Procured</th>
                            <th>Type of the Project to be Procured</th>
                            <th>Quantity and Size of the Project to be Procured</th>
                            <th>Recommended Mode of Procurement</th>
                            <th>Source of Funds</th>
                            <th>Estimated Budget / Authorized Budgetary Allocation (PhP)</th>
                            <th>Quantity</th>
                            <th>Unit Cost per Item</th>
                            <th>Encoded By</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody id="ppmpBody">
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
        var tblPPMP = $('#tblPPMP').DataTable({
            "responsive": true, 
            "autoWidth": false,
            'serverSide': true,  
            'processing':true,
            'paging': true,
            "lengthMenu": [10, 15, 20],
            "ajax": {
                url : "<?php echo base_url("Libraries/getPPMPItemList/"); ?>",
                type : 'GET'    
            },
            "language": {
                "emptyTable": "No Results"
            },
            columns: [
                { data: 'id', name: 'id'},
                { data: 'name', name: 'attachment_name'},
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

        $('#ppmpBody').on('click', '#btnUpdate', function () {
            var data = ($(this).parents('tr').hasClass('child') )
                            ? tblPPMP.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                            : tblPPMP.row($(this).parents('tr')).data(); // otherwise get original data
            $('#attachmentID').val(data.id);
            $('#attachmentName_update').val(data.name);
            $('#attachmentUpdateModal').modal('show');
        });

        $('#ppmpBody').on('click', '#btnDel', function () {
            var data = ($(this).parents('tr').hasClass('child') )
                        ? tblPPMP.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                        : tblPPMP.row($(this).parents('tr')).data(); // otherwise get original data
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
            $('#attachmentAddModal').modal("show"); 
        });
    });
</script>