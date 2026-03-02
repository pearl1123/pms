<div class="content-wrapper" style="min-height: 98px;">
  <section class="content-header">
    <h1> Users <small>Manager</small> </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Users</li>
      <li class="active">Manager</li>
    </ol>
  </section>

  <section class="content">
    <?php if ($this->session->flashdata('fail')): ?>
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= $this->session->flashdata('fail'); ?>
      </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('success')): ?>
      <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= $this->session->flashdata('success'); ?>
      </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-xs-12">
        <div class="box box-default">
          <div class="box-header with-border">
            <div class="pull-left">
              <h3 class="box-title"><i class="fa fa-users"></i> List of Users</h3>
            </div>
          </div>

          <div class="box-body">
            <table class="table table-bordered table-hover" id="userTable" style="width: 100%">
              <thead>
                <tr style="background-color: #bdc3c724">
                  <th>ID</th>
                  <th>Full Name</th>
                  <th>Email</th>
                  <th>Group</th>
                  <th>Status</th>
                  <th width="12%">Actions</th>
                </tr>
              </thead>
              <tbody id="userBody"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- modal container -->
<div id="userModalContainer"></div>

<script type="text/javascript">
$(document).ready(function() {
  loadUserTable();
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
    columns: [
      { data: 'id' },
      { data: 'fullname' },
      { data: 'email' },
      { data: 'group_name' },
      { data: 'banned', render: function(data) {
        return `<span class="label label-${data == 1 ? 'danger' : 'success'}">${data == 1 ? 'Inactive' : 'Active'}</span>`;
      }},
      { data: 'actions' }
    ],
    columnDefs: [
      { targets: [0], visible: false, orderable: false }
    ],
    order: [1, 'asc']
  });

  $('#userBody').on('click', '.btnEdit', function () {
    const data = userTable.row($(this).closest('tr')).data();
    $.get("<?= base_url('UserManagement/edit/') ?>" + data.id, function(html) {
      $('#userModalContainer').html(html);
      $('#editUserModal').modal('show');
    });
  });

  $('#userBody').on('click', '.btnDelete', function () {
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
