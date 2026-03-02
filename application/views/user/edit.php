<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/img/LCP_logo.png'); ?>">
    <title>LCP-EMR| Update Account</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url('assets/frameworks/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/frameworks/font-awesome/css/font-awesome.min.css'); ?>"> 
    <link rel="stylesheet" href="<?php echo base_url('assets/frameworks/ionicons/css/ionicons.min.css')?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/frameworks/adminlte/css/adminlte.min.css')?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  </head>

<body class="hold-transition update-page" style="overflow-y:hidden; max-width:100%; background-repeat:no-repeat; background-image:url(<?= base_url() ?>assets/img/LCP_home.jpg);background-size: 100% 115%;">
  <div class="update-box" style="padding-bottom: 2em; width:600px !important;">
	  <div class="update-logo">
		  <div class="col-lg-12" style="padding-top: .4em">
        <img src="<?php echo base_url('assets/img/LCP_logo.png'); ?>" alt="logo" width="120" >
        <br>
        <span style='font-size: 20px;color:rgb(4, 38, 71);text-align: center;font-weight: bold;font-family: "Century Gothic","Apple Gothic",AppleGothic,"URW Gothic L","Avant Garde",Futura,sans-serif;'> LUNG CENTER OF THE PHILIPPINES</span>
        <span style="color:rgb(4, 5, 7)"><h3><u>Electronic Medical Record</u></h3></span>
		  </div>
	  </div>

    <div class="update-box-body">
      <p class="login-box-msg">Update Account</p>
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

          <?php if ($userdata !== null): ?>
    <!-- Full Name -->
    <div class="form-group">
        <label for="fullname">Full Name</label>
        <input type="text" name="fullname" id="fullname" class="form-control" value="<?= set_value('fullname', $this->session->userdata('fullname')); ?>">
    </div>

    <!-- Phone Number -->
    <div class="form-group">
        <label for="phone_number">Phone Number</label>
        <input type="text" name="phone_number" id="phone_number" class="form-control" value="<?= set_value('phone_number', $this->session->userdata('phone_number')); ?>">
    </div>

    <!-- Office/Division -->
    <div class="form-group">
        <label for="office">Office/Division</label>
        <input type="text" name="office" id="office" class="form-control" value="<?= set_value('office', $this->session->userdata('office')); ?>">
    </div>

    <!-- Email -->
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control" value="<?= set_value('email', $this->session->userdata('email')); ?>">
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control" value="<?= set_value('password', $this->session->userdata('pass')); ?>">
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        User data not found.
    </div>
<?php endif; ?>

      <center><a href="javascript:history.back()" class="text-center">Back</a></center>
    </div>
  </div>
</body>
</html>

<script src="<?php echo base_url('assets/frameworks/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/form-validation/formValidation.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/form-validation/bootstrap-formvalidation.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/form-validation/register.js'); ?>"></script>
<script src="<?php echo base_url('assets/form-validation/document.js'); ?>"></script> 
<script type = "text/javascript">
  $(function () {
    $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
 
     $('#btnShowPass').on('click',function(){
        var x = document.getElementById("password");
        var y = document.getElementById("confirm_pass");
        if (x.type === "password") {
          x.type = "text";
          y.type = "text";
          $('.lock').removeClass('fa-lock');
          $('.lock').addClass('fa-unlock-alt');
          $('#lblPass').html('Hide Password');
        }else {
          x.type = "password";
          y.type = "password";
          $('.lock').removeClass('fa-unlock-alt');
          $('.lock').addClass('fa-lock');
          $('#lblPass').html('Show Password');
        }

    });

  });



<script type="text/javascript">
$(document).ready(function() {
    $('.select2').css('width','100%').select2({
        placeholder: '-- SELECT --',
        allowClear:true
    });

    $('#is_unknown').on('change',function(){
      if($('#is_unknown').is(":checked")){
        $('#office').attr('disabled','disabled');
      } else {
        $('#office').attr('disabled','disabled');
      }
    });

});
</script>

<script type="text/javascript">
    var csrf_ajax = {
        name: '<?= $csrf_ajax['name']; ?>',
        hash: '<?= $csrf_ajax['hash']; ?>'
    };

    // Example of using the CSRF token in an AJAX request
    $.ajax({
        url: '/user/edit',
        type: 'POST',
        data: {
            [csrf_ajax.name]: csrf_ajax.hash,
            // Other data fields
        },
        success: function(response) {
            console.log(response);
        }
    });
</script>

