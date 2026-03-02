  <div class="content-wrapper" style="min-height: 98px;">
    <section class="content-header">
      <h1>
        LIBRARY MANAGEMENT <small>Nationality</small>
      </h1>
      <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> Library Management</li>
        <li class="active"> Nationality</li>
      </ol>

    </section>


    <!-- Main content -->
    <section class="content">
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
  
        <div class="row">
            <div class="col-xs-12">
              <div class="box box-default">
                <div class="box-header with-border">
                  <div class="pull-left">
                    <h3 class="box-nationality"><i class="fa fa-list" aria-hidden="true"></i> List of Nationalities</h3>
                  </div>
                  <div class="pull-right">
                    <a class="btn btn-flat btn-primary btn-md" id = "btnCreateNationality" title="Create New Nationality Record" ><i class="fa fa-plus" aria-hidden="true"></i> Create Nationality Record</a>
                  </div>
                </div>

                <div class="box-body">
                  <table class="table table-bordered table-hover" id="nationalityTable" style="width: 100%">
                        <thead >
                            <tr style="background-color: #bdc3c724" >
                                <th align="center">ID</th> 
                                <th align="center">Nationality Description</th>
                                <th align="center">Encoded By</th>
                                <th width="12%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id = "nationalityBody"></tbody>
                    </table>
                </div>
              </div>
            </div>
        </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0
    </div>
    <strong>Copyright © 2025 <a href="">Management Information Systems Division</a></strong> 
  </footer>
  
  <div class="control-sidebar-bg"></div>

</div>

</body>
</html>

<script type="text/javascript">
$(document).ready(function() {

  datatable();
  $('#btnCreateNationality').on('click', function(){    
      $('#nationalityAddModal').modal("show"); 
  });

});

function datatable(){
  var nationalityTable = $('#nationalityTable').DataTable( {
      "responsive": true, 
      "autoWidth": false,
      'serverSide': true,  
      'processing':true,
      'paging': true,
      "lengthMenu": [10, 15, 20], // Number of records per page options
      "ajax": {
        url : "<?php echo base_url("Libraries/getNationalityList/"); ?>",
        type : 'GET'
      },
      "language": {
        "emptyTable": "No Results"
      },
      columns: [
        { data: 'id', name: 'id'},
        { data: 'nationality_desc', name: 'nationality_desc'  },
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

    $('#nationalityBody').on('click', '#btnUpdate', function () {
        var data = ($(this).parents('tr').hasClass('child') )
                        ? nationalityTable.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                        : nationalityTable.row($(this).parents('tr')).data(); // otherwise get original data
        $('#nationality_id').val(data.id);
        $('#nationality_u').val(data.nationality_desc);
        $('#modalUpdateNationality').modal('show');
    });

    $('#nationalityBody').on('click', '#btnDel', function () {
        var data = ($(this).parents('tr').hasClass('child') )
                        ? nationalityTable.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                        : nationalityTable.row($(this).parents('tr')).data(); // otherwise get original data
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
                        url: "<?php echo base_url('Libraries/deleteNationality'); ?>",
                        async: false,
                        type: "POST",
                        datatype: "json",
                        data: {id:id},
                        success: function(data) {
                            Swal.fire({
                                title: 'Success',
                                html: "<span class = 'text-success'><b>SUCCESS!</b></span> Successfully deleted nationality record.",
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
</script>