<div class="content-wrapper" style="min-height: 98px;">
    <section class="content-header">
      <h1>
       Permissions <small>Manager</small>
      </h1>
      <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> Permissions</li>
        <li class="active"> Manager</li>
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
                    <h3 class="box-title"><i class="fa fa-list" aria-hidden="true"></i> List of Permissions</h3>
                  </div>
                  <div class="pull-right">
                    <a class="btn btn-flat btn-primary btn-md" id = "btnCreatePermission" title="Create New Permission" ><i class="fa fa-plus" aria-hidden="true"></i> Create Permission</a>
                  </div>
                </div>

                <div class="box-body">
                  <table class="table table-bordered table-hover" id="permissionTable" style="width: 100%">
                        <thead>
                            <tr style="background-color: #bdc3c724" >
                                <th align="center">ID</th> 
                                <th align="center">Name</th>
                                <th align="center">Definition</th>
                                <th width="12%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id = "permissionBody"></tbody>
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
  
  <div class="control-sidebar-bg abc"></div>

</div>

</body>
</html>

<script type="text/javascript">
$(document).ready(function() {

  datatable();

  $('#btnCreatePermission').on('click', function(){    
      $('#permissionAddModal').modal("show"); 
  });

});

function datatable(){
  var permissionTable = $('#permissionTable').DataTable( {
      "responsive": true, 
      "searching": true,
      "autoWidth": false,
      'serverSide': true,  
      'processing':true,
      'paging': true,
      "lengthMenu": [10, 15, 20], // Number of records per page options
      "ajax": {
        url : "<?php echo base_url("Libraries/getAllPermissions/"); ?>",
        type : 'GET'
      },
      "language": {
        "emptyTable": "No Results"
      },
      columns: [
        { data: 'id', name: 'id'},
        { data: 'name', name: 'name'  },
        { data: 'definition', name: 'definition'  },   
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

    $('#permissionBody').on('click', '.btnUpdate', function () {
        var data = ($(this).parents('tr').hasClass('child') )
                        ? permissionTable.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                        : permissionTable.row($(this).parents('tr')).data(); // otherwise get original data
        $('#id').val(data.id);
        $('#name_u').val(data.name);
        $('#definition_u').val(data.definition);
        $('#modalUpdatePermission').modal('show');
    });

	$('#permissionBody').on('click', '.btnPermission', function () {
		var data = ($(this).parents('tr').hasClass('child'))
						? permissionTable.row($(this).parents().prev('tr')).data()
						: permissionTable.row($(this).parents('tr')).data();
		$('#rowPermissionId').html(data.id);
        $('#rowPermissionName').html(data.name);
        $('#rowPermissionDefinition').html(data.definition);
		$('#permissionSetModal').modal('show');
	});

    $('#permissionBody').on('click', '.btnDel', function () {
        var data = ($(this).parents('tr').hasClass('child') )
                        ? permissionTable.row($(this).parents().prev('tr')).data() // if tr is a child, then get the parents
                        : permissionTable.row($(this).parents('tr')).data(); // otherwise get original data
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
                        url: "<?php echo base_url('Libraries/deletePermission'); ?>",
                        async: false,
                        type: "POST",
                        datatype: "json",
                        data: {id:id},
                        success: function(data) {
                            Swal.fire({
                                title: 'Success',
                                html: "<span class = 'text-success'><b>SUCCESS!</b></span> Successfully deleted permission.",
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

