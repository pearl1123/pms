<script type="text/javascript">
  
$(document).ready(function() {

    <?php if ($this->session->flashdata('success_logout')): ?>

        Swal.fire({
            title: 'Session Expired!',
            text: "You have been idle for 20 minutes. Your login session has expired. Please relogin.",
            type: 'warning',
            confirmButtonColor: '#3c8dbc',
            confirmButtonText: 'Press OK to proceed',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if(result.value == true){
                  window.location='<?php echo base_url('User/logout'); ?>';
            }

        })

    <?php endif; ?>
    
});


</script>
