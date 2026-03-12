<div class="container-fluid">

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">User Management</h1>
    <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="btnAdd">
      <i class="fas fa-plus fa-sm text-white-50"></i> Add User</button>
  </div>

  <hr />

  <!-- BREADCRUMBS
    ========================================================================================================================================= -->
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">User Management</li>
    </ol>
  </nav>

  <!-- ALERTS
    ========================================================================================================================================= -->
  <?php if ($this->session->flashdata('fail') <> null) { ?>
    <div class="alert alert-danger">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <i class="fa fa-times" aria-hidden="true"></i>
      </button>
      <span>
        <?php echo $this->session->flashdata('fail'); ?>
      </span>
    </div>
  <?php } ?>
  <?php if ($this->session->flashdata('success') <> null) { ?>
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
      <h6 class="m-0 font-weight-bold text-primary">Users Table</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="userTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Group</th>
              <th>Status</th>
              <th width="12%">Actions</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Group</th>
              <th>Status</th>
              <th width="12%">Actions</th>
            </tr>
          </tfoot>
          <tbody id="userBody">
          </tbody>
        </table>
      </div>
    </div>
  </div>

 <?php $this->load->view('user/AddUser'); ?>
  <div id="userModalContainer"></div>

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

<script type="text/javascript">
  $(document).ready(function() {
    loadUserTable();

    $('#btnAdd').on('click', function() {
      $('#userAddModal').modal("show");
    });
  });

  function loadUserTable() {
    const userTable = $('#userTable').DataTable({
      responsive: true,
      autoWidth: false,
      serverSide: true,
      processing: true,
      paging: true,
      lengthMenu: [10, 15, 20],
      ajax: {
        url: "<?= base_url('UserManagement/getUsersAjax') ?>",
        type: 'GET'
      },
      language: {
        emptyTable: "No Users Found"
      },
      columns: [{
          data: 'id'
        },
        {
          data: 'fullname'
        },
        {
          data: 'email'
        },
        {
          data: 'group_name'
        },
        {
          data: 'banned',
          render: function(data) {
            return `<span class="label label-${data == 1 ? 'danger' : 'success'}">${data == 1 ? 'Inactive' : 'Active'}</span>`;
          }
        },
        {
          data: 'actions'
        }
      ],
      columnDefs: [{
        targets: [0],
        visible: false,
        orderable: false
      }],
      order: [1, 'asc']
    });

    $('#userBody').on('click', '.btnEdit', function() {
      const data = userTable.row($(this).closest('tr')).data();
      $.get("<?= base_url('UserManagement/edit/') ?>" + data.id, function(html) {
        $('#userModalContainer').html(html);
        $('#editUserModal').modal('show');
      });
    });

    $('#userBody').on('click', '.btnDelete', function() {
      const data = userTable.row($(this).closest('tr')).data();
      Swal.fire({
        title: 'Delete User?',
        html: "<span class='text-danger'><b>WARNING:</b></span> This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.value) {
          $.post("<?= base_url('UserManagement/delete/') ?>" + data.id, function() {
            Swal.fire('Deleted!', 'User has been removed.', 'success').then(() => {
              location.reload();
            });
          });
        }
      });
    });
  }
</script>