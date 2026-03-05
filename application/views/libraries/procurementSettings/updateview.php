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
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Procurement Setting Table</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                
                <table class="table table-bordered" id="tblMode" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Procurement Code</th>
                            <th>Procurement Name</th>
                            <th>Encoded By</th>
                            <th>Action</th>
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

    }
</script>