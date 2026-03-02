<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="<?php echo base_url('assets/img/LCP_logo.png'); ?>">
    <title>LCP-EMR| Register</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url('assets/frameworks/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/frameworks/font-awesome/css/font-awesome.min.css'); ?>"> 
    <link rel="stylesheet" href="<?php echo base_url('assets/frameworks/ionicons/css/ionicons.min.css')?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/frameworks/adminlte/css/adminlte.min.css')?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  </head>

  <body>

<div class="container mt-5">
    <h2>Update Account</h2>

    <?php if ($this->session->flashdata('fail')): ?>
        <div class="alert alert-danger"><?php echo $this->session->flashdata('fail'); ?></div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
    <?php endif; ?>

    <form action="<?php echo base_url('User/update'); ?>" method="post">
        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo set_value('fullname', $user['fullname']); ?>">
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo set_value('phone_number', $user['phone_number']); ?>">
        </div>

        <div class="form-group">
            <label for="office">Office/Division</label>
            <select class="form-control" id="office" name="office">
                <option value="">Select Office/Division</option>
                <?php foreach ($offices as $office): ?>
                    <option value="<?php echo $office['id']; ?>" <?php echo set_select('office', $office['id'], ($user['office_id'] == $office['id'])); ?>>
                        <?php echo $office['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo set_value('email', $user['email']); ?>">
        </div>

        <div class="form-group">
            <label for="password">New Password (Leave blank if not changing)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <div class="form-group">
            <label for="confirm_pass">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_pass" name="confirm_pass">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-secondary">Back</a>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('#password, #confirm_pass').on('keyup', function(){
            if ($('#password').val() != $('#confirm_pass').val()) {
                $('#confirm_pass').css('border', '2px solid red');
            } else {
                $('#confirm_pass').css('border', '2px solid green');
            }
        });
    });
</script>

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





