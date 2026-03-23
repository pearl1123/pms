
                <!-- Footer -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright &copy; PMIS Developed by MISD 2026</span>
                        </div>
                    </div>
                </footer>
                <!-- End of Footer -->

            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->
<a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary btn-logout">Logout</a>
                </div>
            </div>
        </div>
    </div>


<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/jquery-easing/jquery.easing.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/sbadmin/js/sb-admin-2.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/sbadmin/vendor/chart.js/Chart.min.js');?>"></script>

<!-- <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script> -->
<script src="<?php echo base_url('assets/js/slimscroll.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/fastclick.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/adminlte.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/icheck.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/bootstrap-datepicker.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/sweetalert2.js'); ?>"></script>

<!-- Datatables -->
<script src="<?php echo base_url('assets/js/responsive.bootstrap4.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/buttons.bootstrap4.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/jzip.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/buttons.html5.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/buttons.print.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/pdfmake.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/vs_fonts.js') ?>"></script>
<script src="<?php echo base_url('assets/js/formValidation.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/bootstrap-formvalidation.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/select2.full.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/moment.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/daterangepicker.js'); ?>"></script>

<script type="text/javascript">
  $(function() {
    $('.select2').css('width', '100%').select2({
      placeholder: '-- SELECT --',
      allowClear: true
    });

    var url = window.location;
    // for sidebar menu entirely but not cover treeview
    $('ul.sidebar-menu a').filter(function() {
      return this.href == url;
    }).parent().addClass('active');

    // for treeview
    $('ul.treeview-menu a').filter(function() {
      return this.href == url;
    }).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');
  });

  $('#btnLogout').on('click', function() {
    swal.fire({
      title: "Are you sure you want to logout?",
      text: "Any unsaved changes will be discarded!",
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, Confirm Logout!",
      cancelButtonText: "No, cancel!",
      reverseButtons: true
    }).then((result) => {
      console.log(result);
      if (result.dismiss == 'cancel') {
        swal.fire({
          text: "Logout cancelled!",
          type: "error"
        });
      } else {
        swal.fire({
          text: "Logout confirmed!",
          type: "success",
          showConfirmButton: false,
        });
        setTimeout(logout, 1500)

      }
    });

  });

  $('.btn-logout').on('click', function () {
    swal.fire({
      text: "Logout confirmed!",
      type: "success",
      showConfirmButton: false,
    });
    setTimeout(logout, 1500)
  });

  function logout() {
    window.location.href = '<?php echo base_url('User/logout'); ?>';
  }

  document.addEventListener("DOMContentLoaded", function() {
    const labels = document.querySelectorAll('.btn-group-toggle label');

    labels.forEach(label => {
      label.addEventListener("click", function() {
        const input = this.querySelector('input');
        const selectedUI = input.value;

        Swal.fire({
          title: 'Are you sure?',
          text: "Change UI to " + (selectedUI === "1" ? "Table-Based" : "Card-Based"),
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: 'Yes, change it',
          cancelButtonText: 'Cancel',
        }).then((result) => {
          if (!result.isConfirmed) return;

          // Save the preference via AJAX
          $.ajax({
            url: "<?= base_url('User/changeUIPreference'); ?>",
            type: "POST",
            dataType: "json",
            data: {
              ui: selectedUI
            },
            success: function(data) {
              if (!data.success) {
                Swal.fire('Error', data.message || 'Failed to update UI.', 'error');
                return;
              }

              // Preference saved successfully
              Swal.fire('Updated!', 'Your UI preference has been saved.', 'success')
                .then(() => {

                  const patientId = <?php echo json_encode($pData->patient_id ?? $patientID ?? ''); ?>;
                  const baseProfile = "<?= base_url('PatientRegistration/patientProfile/') ?>";
                  const baseProfileRev = "<?= base_url('PatientRegistration/patientProfileRev/') ?>";

                  // 1️⃣ If a patient is currently loaded, redirect immediately
                  if (patientId) {
                    window.location.href = (selectedUI === "1") ?
                      baseProfile + patientId :
                      baseProfileRev + patientId;
                    return;
                  }

                  // 2️⃣ Update all patient links in listview dynamically
                  document.querySelectorAll("a.patient-link").forEach(a => {
                    const pid = a.getAttribute("data-patient-id");
                    if (!pid) return;
                    a.href = (selectedUI === "1") ? baseProfile + pid : baseProfileRev + pid;
                  });
                });
            },
            error: function(xhr, status, error) {
              console.error("AJAX Error:", error);
              Swal.fire('Error', 'Something went wrong while saving.', 'error');
            }
          });
        });
      });
    });
  });
</script>

</body>

</html>