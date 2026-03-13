<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Procurememnt Mode</h1>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btnAdd">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Procurement Mode</button>
    </div>

    <hr/>

    <!-- BREADCRUMBS
    ========================================================================================================================================= -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Libraries</li>
            <li class="breadcrumb-item active" aria-current="page">Procurement Mode</li>
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
            <h6 class="m-0 font-weight-bold text-primary">Procurement Mode Table</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tblMode" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Procurement Code</th>
                            <th>Procurement Name</th>
                            <th style="width: 10%;">Encoded By</th>
                            <th style="width: 8%;">Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No.</th>
                            <th>Procurement Code</th>
                            <th>Procurement Name</th>
                            <th>Encoded By</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody id="modeBody">
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
        var tblMode = $('#tblMode').DataTable({
            "responsive": true, 
            "autoWidth": false,
            'serverSide': true,  
            'processing':true,
            'paging': true,
            "lengthMenu": [10, 15, 20],
            "ajax": {
                url : "<?php echo base_url("Libraries/getModeList/"); ?>",
                type : 'GET'    
            },
            "language": {
                "emptyTable": "No Results"
            },
            columns: [
                { data: 'id', name: 'id'},
                { data: 'code', name: 'proc_code'},
                { data: 'name', name: 'proc_name'},
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

        $('#modeBody').on('click', '#btnUpdate', function () {
            var data = ($(this).parents('tr').hasClass('child') )
                            ? tblMode.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                            : tblMode.row($(this).parents('tr')).data(); // otherwise get original data
            $('#modeID').val(data.id);
            $('#modeCode_update').val(data.code);
			$('#modeName_update').val(data.name);
            $('#modeUpdateModal').modal('show');
        });

        $('#modeBody').on('click', '#btnDel', function () {
            var data = ($(this).parents('tr').hasClass('child') )
                        ? tblMode.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                        : tblMode.row($(this).parents('tr')).data(); // otherwise get original data
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
                        url: "<?php echo base_url('Libraries/deleteMode'); ?>",
                        async: false,
                        type: "POST",
                        datatype: "json",
                        data: {id:id},
                        success: function(data) {
                            Swal.fire({
                                title: 'Success',
                                html: "<span class = 'text-success'><b>SUCCESS!</b></span> Successfully deleted procurement mode record.",
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
            $('#modeAddModal').modal("show"); 
        });
    });
</script>