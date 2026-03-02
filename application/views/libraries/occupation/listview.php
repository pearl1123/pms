<!-- View: occupation.php -->
<div class="content-wrapper" style="min-height: 98px;">
  <section class="content-header">
    <h1>LIBRARY MANAGEMENT <small>Occupation</small></h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-dashboard"></i> Library Management</li>
      <li class="active">Occupation</li>
    </ol>
  </section>

  <section class="content">
    <?php if ($this->session->flashdata('fail')): ?>
      <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
        <span><?= $this->session->flashdata('fail'); ?></span>
      </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('success')): ?>
      <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
        <span><?= $this->session->flashdata('success'); ?></span>
      </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-xs-12">
        <div class="box box-default">
          <div class="box-header with-border">
            <div class="pull-left">
              <h3 class="box-title"><i class="fa fa-list"></i> List of Occupations</h3>
            </div>
            <div class="pull-right">
              <button class="btn btn-flat btn-primary btn-md" id="btnCreateOccupation">
                <i class="fa fa-plus"></i> Create Occupation Record
              </button>
            </div>
          </div>

          <div class="box-body">
            <table class="table table-bordered table-hover" id="occupationTable" style="width: 100%">
              <thead>
                <tr style="background-color: #bdc3c724">
                  <th>ID</th> 
                  <th>Occupation Description</th>
                  <th>Encoded By</th>
                  <th width="12%">Actions</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<footer class="main-footer">
  <div class="pull-right hidden-xs"><b>Version</b> 1.0</div>
  <strong>Copyright © 2025 <a href="#">Management Information Systems Division</a></strong>
</footer>

<div class="control-sidebar-bg"></div>

<!-- JS Script -->
<script type="text/javascript">
$(document).ready(function () {
  occupationDatatable();

  $('#btnCreateOccupation').on('click', function () {
    $('#occupationAddModal').modal("show");
  });
});

function occupationDatatable() {
  var occupationTable = $('#occupationTable').DataTable({
    responsive: true,
    autoWidth: false,
    serverSide: true,
    processing: true,
    paging: true,
    lengthMenu: [10, 15, 20],
    ajax: {
      url: "<?= base_url('Libraries/getOccupationList'); ?>",
      type: 'GET'
    },
    language: {
      emptyTable: "No Results"
    },
    columns: [
      { data: 'occupation_id' },
      { data: 'occupation_desc' },
      { data: 'fullname' },
      { data: 'actions' }
    ],
    columnDefs: [
      { targets: [0], visible: false, orderable: false },
      { targets: [1, 2, 3], orderable: true }
    ],
    order: [[1, 'asc']]
  });

  // ✅ UPDATE button
  $('#occupationTable').on('click', '#btnUpdateOccupation', function () {
    var data = occupationTable.row($(this).closest('tr')).data();
    $('#occupation_id').val(data.occupation_id);
    $('#occupation_u').val(data.occupation_desc);
    $('#occupationUpdateModal').modal('show');
  });

  // ✅ DELETE button
  $('#occupationTable').on('click', '#btnDelOccupation', function () {
    var data = occupationTable.row($(this).closest('tr')).data();
    var id = data.occupation_id;

    Swal.fire({
      title: 'Delete?',
      html: "<span class='text-danger'><b>WARNING!</b></span> You will not be able to undo this action.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, proceed!'
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: "<?= base_url('Libraries/deleteOccupation'); ?>",
          type: "POST",
          dataType: "json",
          data: { id: id },
          success: function () {
            Swal.fire({
              title: 'Success',
              html: "<span class='text-success'><b>SUCCESS!</b></span> Successfully deleted occupation record.",
              icon: 'success',
              confirmButtonColor: '#3085d6',
              confirmButtonText: 'OK!'
            }).then(() => occupationTable.ajax.reload());
          }
        });
      }
    });
  });
}
</script>
