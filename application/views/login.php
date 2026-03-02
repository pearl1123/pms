<html>
	<head>
    	<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<link rel="icon" type="image/png" href="<?php echo base_url('assets/img/LCP_logo.png'); ?>">
    	<title>Procurement Management Information System | Login</title>

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

			.login-card { margin-top: 250px;}
		</style>
  	</head>

	<body>
		<div class="bg"></div>
		<div class="bg bg2"></div>
		<div class="bg bg3"></div>

		<div class="container">
			
			<div class="card login-card mb-3">
				<div class="row g-0">
					<div class="col-md-6">
						<img src="<?= base_url() ?>assets/img/LCP_home.jpg" class="img-fluid rounded-start" alt="Logo" style="height: 100%">
					</div>
					<div class="col-md-6">
						<div class="card-body">
							<form action="<?php echo base_url("User/index");?>" method="post">
								<div class="text-center mb-3">
									<img src="<?php echo base_url('assets/img/LCP_logo.png'); ?>" class="img-fluid mb-3" alt="logo" width="200">
									<h3>PROCUREMENT MANAGEMENT INFORMATION SYSTEM</h3>
								</div>
								<div class="form-floating mb-3">
									<input type="email" class="form-control" id="email" name="email" required>
									<label for="floatingInput">Email address</label>
								</div>
								<div class="form-floating mb-3">
									<input type="password" class="form-control" id="password" name="password" required>
									<label for="floatingPassword">Password</label>
								</div>
								<div class="d-grid gap-2">
									<button type="submit" class="btn btn-lg btn-primary">LOGIN</button>
								</div>
							</form>
							<hr/>
							<p>No account? Contact your administrator <a href="<?php echo base_url('User/register');?>">here</a></p>
						</div>
					</div>
				</div>
			</div>

		</div>
	</body>
</html>

<script src="<?php echo base_url('assets/frameworks/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/frameworks/bootstrap5/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/icheck/js/icheck.min.js'); ?>"></script>

<script type = "text/javascript">
  $(function () {
    $('#btnShowPass').on('click',function(){
        var x = document.getElementById("password");
        if (x.type === "password") {
          x.type = "text";
          $('.lock').removeClass('fa-lock');
          $('.lock').addClass('fa-unlock-alt');
          $('#lblPass').html('Hide Password');
        }else {
          x.type = "password";
          $('.lock').removeClass('fa-unlock-alt');
          $('.lock').addClass('fa-lock');
          $('#lblPass').html('Show Password');
        }
    });
  });

   document.addEventListener("DOMContentLoaded", function() {
          var demo1 = new BVAmbient({
            selector: "#ambient",
            fps: 60,
            max_transition_speed: 12000,
            min_transition_speed: 8000,
            particle_number: 30,
            particle_maxwidth: 60,
            particle_minwidth: 10,
            particle_radius: 50,
            particle_opacity: true,
            particle_colision_change: true,
            particle_background: "#0c18c7",
            refresh_onfocus: true,
            particle_image: {
              image: false,
              src: ""
            },
            responsive: [
                {
                  breakpoint: 768,
                  settings: {
                    particle_number: "15"
                  }
                },
                {
                  breakpoint: 480,
                  settings: {
                    particle_number: "10"
                  }
                }
            ]
          });
    });
    </script>
