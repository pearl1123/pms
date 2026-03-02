<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link rel="icon" type="image/png" href="<?php echo base_url('assets/img/LCP_logo.png'); ?>">
		<title>Procurement Management Information System | Register</title>

		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/bootstrap5/css/bootstrap.min.css'); ?>">
    	<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/bootswatch/zephyr.min.css'); ?>">
    	<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/font-awesome/css/font-awesome.min.css'); ?>"> 
    	<link rel="stylesheet" href="<?php echo base_url('assets/frameworks/ionicons/css/ionicons.min.css')?>">

		<style>
			.bg {
				animation:slide 10s ease-in-out infinite alternate;
				background-image: linear-gradient(-60deg, #FFFFFF 50%, #0c18c7 50%);
				bottom:0;
				left:-50%;
				opacity:.5;
				position:fixed;
				right:-50%;
				top:0;
				z-index:-1;
			}

			.bg2 {
				animation-direction:alternate-reverse;
				animation-duration:15s;
			}

			.bg3 {
				animation-duration:15s;
			}

			.content {
				background-color:rgba(255,255,255,.8);
				border-radius:.25em;
				box-shadow:0 0 .25em rgba(0,0,0,.25);
				box-sizing:border-box;
				left:50%;
				padding:10vmin;
				position:fixed;
				text-align:center;
				top:50%;
				transform:translate(-50%, -50%);
			}

			@keyframes slide {
				0% {
					transform:translateX(-25%);
				}
				100% {
					transform:translateX(25%);
				}
			}

			.login-card { margin-top: 100px;}
		</style>
	</head>

	<body>
		<div class="bg"></div>
		<div class="bg bg2"></div>
		<div class="bg bg3"></div>

		<div class="container">
			<div class="card login-card mb-3">
				<div class="row g-0">
					<div class="card-body">
						<form action="<?php echo base_url("User/saveRegistration");?>" method="post" id="frmRegister">
							<div class="text-center mb-3">
								<img src="<?php echo base_url('assets/img/LCP_logo.png'); ?>" class="img-fluid mb-3" alt="logo" width="200">
								<h3>PROCUREMENT MANAGEMENT INFORMATION SYSTEM</h3>
							</div>
							<hr/>

							<h5>PERSONAL INFORMATION</h5>
							<div class="form-floating mb-3">
								<input type="text" class="form-control" id="fullname" name="fullname" required>
								<label for="fullname">Full Name</label> 
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-floating mb-3">
										<input type="text" class="form-control" maxlength="11" id="phone_number" name="phone_number" required>
										<label for="phone_number">Phone Number</label>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-floating mb-3">
										<select class="form-select" id="office" name="office">
											<option value="">Select Office/Division</option>
											<?php foreach($offices as $o): ?>
											<option value="<?php echo $o->office_id; ?>" <?php echo set_select('office', $o->office_id); ?>>
												<?php echo htmlspecialchars($o->office_desc); ?>
											</option>
											<?php endforeach; ?>
										</select>
										<label for="floatingPassword">Office</label>
									</div>
								</div>
							</div>

							<h5>ACCOUNT INFORMATION</h5>
							<div class="form-floating mb-3">
								<input type="text" class="form-control" id="email" name="email" required>
								<label for="email">Email</label>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-floating input-group mb-3">
										<input type="password" class="form-control" maxlength="11" id="password" name="password" required>
										<label for="password">Password</label>
										<span class="input-group-text" id="basic-addon2"><i class="fa fa-lock form-control-feedback fa-lg lock" aria-hidden="true"></i></span>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-floating input-group mb-3">
										<input type="password" class="form-control" maxlength="11" id="confirm_pass" name="confirm_pass" required>
										<label for="confirm_pass">Retype Password</label>
										<span class="input-group-text" id="basic-addon2"><i class="fa fa-lock form-control-feedback fa-lg lock" aria-hidden="true"></i></span>
									</div>
								</div>
							</div>

							<div class="form-group has-feedback mb-3" style="text-align: right;">
          						<a href="javascript:void(0)" id="btnShowPass"><span id="lblPass">Show Password</span></a>
        					</div>
							
							<div class="d-grid gap-2">
								<button type="submit" class="btn btn-lg btn-primary">REGISTER</button>
							</div>
						</form>
						<hr/>
						<p>Have an account? Login <a href="<?php echo base_url('User/index');?>">here</a></p>
					</div>
				</div>
			</div>		
		</div>
	</body>
</html>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Other JS Files -->
<script src="<?php echo base_url('assets/frameworks/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/bootstrap5/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/form-validation/formValidation.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/form-validation/bootstrap-formvalidation.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/form-validation/register.js'); ?>"></script>

 <!-- Add this RIGHT BEFORE the closing </body> tag -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    <?php if ($this->session->flashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Registration Successful!',
            html: '<?php echo $this->session->flashdata('success'); ?>',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?php echo base_url('User/index'); ?>";
            }
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('fail')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Registration Failed',
            html: '<?php echo $this->session->flashdata('fail'); ?>',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Try Again'
        });
    <?php endif; ?>
});
</script>

<!-- Your Existing JavaScript -->
<script type="text/javascript">
$(function () {
// 🔒 Show/Hide Password
	$('#btnShowPass').on('click', function(){
		var x = document.getElementById("password");
		var y = document.getElementById("confirm_pass");
		if (x.type === "password") {
			x.type = y.type = "text";
			$('.lock').removeClass('fa-lock').addClass('fa-unlock-alt');
			$('#lblPass').html('Hide Password');
		} else {
			x.type = y.type = "password";
			$('.lock').removeClass('fa-unlock-alt').addClass('fa-lock');
			$('#lblPass').html('Show Password');
		}
	});
});
</script>