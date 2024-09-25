
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>GYS Global Service Management System</title>
	<link rel="shortcut icon" href="{{ URL::to('/') }}/grp_bg_logo.png"/>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" integrity="sha512-0S+nbAYis87iX26mmj/+fWt1MmaKCv80H+Mbo+Ne7ES4I6rxswpfnC6PxmLiw33Ywj2ghbtTw0FkLbMWqh4F7Q==" crossorigin="anonymous"/>

    <!-- AdminLTE -->
<!--
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.5/css/adminlte.min.css"
          integrity="sha512-rVZC4rf0Piwtw/LsgwXxKXzWq3L0P6atiQKBNuXYRbg2FoRbSTIY0k2DxuJcs7dk4e/ShtMzglHKBOJxW8EQyQ=="
          crossorigin="anonymous"/>
-->

    <!-- iCheck -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css" integrity="sha512-8vq2g5nHE062j3xor4XxPeZiPjmRDh6wlufQlfC6pdQ/9urJkU07NM0tEREeymP++NczacJ/Q59ul+/K2eYvcg==" crossorigin="anonymous"/>

    <link href='https://fonts.googleapis.com/css?family=Uchen' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,300" rel="stylesheet" type="text/css">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link rel="stylesheet" href="{{ URL::to('/') }}/vendor/bootstrap/dist/css/bootstrap.min.css" crossorigin="anonymous"/>
    <script src="{{ URL::to('/') }}/assets/js/jquery.min.js"></script>
    <script src="{{ URL::to('/') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body class="hold-transition login-page" style="">


<style>.login-card-body .input-group .input-group-text,.register-card-body .input-group .input-group-text{background-color:transparent;border-bottom-right-radius:.25rem;border-left:0;border-top-right-radius:.25rem;color:#777;transition:border-color .15s ease-in-out , box-shadow .15s ease-in-out}.input-group>.input-group-append>.btn, .input-group>.input-group-append>.input-group-text, .input-group>.input-group-prepend:first-child>.btn:not(:first-child), .input-group>.input-group-prepend:first-child>.input-group-text:not(:first-child), .input-group>.input-group-prepend:not(:first-child)>.btn, .input-group>.input-group-prepend:not(:first-child)>.input-group-text {border-top-left-radius:0;border-bottom-left-radius:0}.input-group-text{border-left:none;border-right:none;border-top:none;border-radius:0}.input-group-text{display:flex;align-items:center;padding:.375rem .75rem;font-size:1rem;font-weight:400;line-height:1.5;color:#212529;text-align:center;white-space:nowrap;background-color:#e9ecef;border:1px solid #ced4da;border-radius:.25rem}.input-group-text{display:-ms-flexbox;display:flex;-ms-flex-align:center;align-items:center;padding:.375rem .75rem;margin-bottom:0;font-size:1rem;font-weight:400;line-height:1.5;color:#495057;text-align:center;white-space:nowrap;background-color:#e9ecef;border:1px solid #ced4da;border-radius:.25rem}.input-group-append{margin-left:-1px}.input-group-append,.input-group-prepend{display:-ms-flexbox;display:flex}.carousel-indicators{position:absolute;right:0;bottom:0;left:0;z-index:2;display:flex;justify-content:center;padding:0;margin-right:15%;margin-bottom:1rem;margin-left:15%;list-style:none}.carousel-indicators [data-bs-target]{box-sizing:content-box;flex:0 1 auto;width:30px;height:3px;padding:0;margin-right:3px;margin-left:3px;text-indent:-999px;cursor:pointer;background-color:#fff;background-clip:padding-box;border:0;border-top:10px solid transparent;border-bottom:10px solid transparent;opacity:.5;transition:opacity .6s ease}.carousel-indicators .active{opacity:1}@media (min-width:768px){.col-md-7{-ms-flex:0 0 54.333333%;flex:0 0 54.333333%;max-width:54.333333%}.col-md-5{-ms-flex:0 0 45.666667%;flex:0 0 45.666667%;max-width:45.666667%}}.login-page,.register-page{display:unset}.login-logo,.register-logo{font-size:1.6rem}.input-login,.input-login:focus,.input-login:hover{background:transparent;border-left:none;border-right:none;border-top:none;border-radius:0;padding-left:0;color:#fff}.input-group-text{border-left:none;border-right:none;border-top:none;border-radius:0}.input-login::placeholder{color:#fff;opacity:1}.input-login:-ms-input-placeholder{color:#fff}.input-login::-ms-input-placeholder{color:#fff}.login-card-body .input-group .input-group-text,.register-card-body .input-group .input-group-text{color:#e84f47;border-radius:0;padding-right:2px}input[type="checkbox"]{-ms-transform:scale(2);-moz-transform:scale(2);-webkit-transform:scale(2);-o-transform:scale(2);transform:scale(6);padding:10px}.checkboxtext{font-size:110%;display:inline}.input-login,.input-login:focus,.input-login:hover{font-size:13.5px}.login-box,.register-box{width:420px}.squaredThree label{cursor:pointer;position:absolute;width:20px;height:20px;top:0;border-radius:4px;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.5) , 0 1px 0 rgba(255,255,255,.4);-moz-box-shadow:inset 0 1px 1px rgba(0,0,0,.5) , 0 1px 0 rgba(255,255,255,.4);box-shadow:inset 0 1px 1px rgba(0,0,0,.5) , 0 1px 0 rgba(255,255,255,.4);background:-webkit-linear-gradient(top,#222 0%,#45484d 100%);background:-moz-linear-gradient(top,#222 0%,#45484d 100%);background:-o-linear-gradient(top,#222 0%,#45484d 100%);background:-ms-linear-gradient(top,#222 0%,#45484d 100%);background:linear-gradient(top,#222 0%,#45484d 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#222', endColorstr='#45484d',GradientType=0 )}@media only screen and (max-width:768px){.col-left{display:none!important}}.invalid-feedback{display:block;color:#ffd500;margin-bottom:10px;font-size:18px}.img-slide{height:100%;background-size:cover;background-position:center}button:focus{outline:none!important}</style>
<div class="container-fluid">
	<div class="" style="max-height: 100vh;height: 100vh;
    overflow: hidden;border:0px solid #1176BC;margin:0 -12px 0 -15px;display:block;padding-left:0px;padding-right:0px;">
	<div class="row">
		<div class="col-md-9 col-left" style="padding:0;background:#fff;height:100vh;">
			<div style="overflow:hidden;height: 100vh;padding: 0 0px 0 0;display: flex;margin-top:0%;">
				<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel" style="height:100%;width:100%;">
				  <div class="carousel-indicators">
					{{-- <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button> --}}
					{{-- <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button> --}}
					<button type="button" class="active" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" aria-label="Slide 1"></button>
					<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
					{{-- <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="4" aria-label="Slide 4"></button> --}}
					<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
				  </div>
				  <div class="carousel-inner" style="height:100%;">
					{{-- <div class="carousel-item active" style="height:100%;" data-bs-interval="2000">

					  <div class="img-slide" style="background-image:url({{ URL::to('/') }}/assets/images/slide_1.jpg)"></div>
					</div> --}}
					{{-- <div class="carousel-item" style="height:100%;" data-bs-interval="2000">

					  <div class="img-slide" style="background-image:url({{ URL::to('/') }}/assets/images/slide_2.jpg)"></div>
					</div> --}}
					<div class="carousel-item active" style="height:100%;" data-bs-interval="2000">

						<div class="img-slide" style="background-image:url({{ URL::to('/') }}/assets/images/slide_3.jpg)"></div>
					</div>
					{{-- <div class="carousel-item" style="height:100%;" data-bs-interval="2000">

						<div class="img-slide" style="background-image:url({{ URL::to('/') }}/assets/images/slide_4.jpg)"></div>
					</div> --}}
					<div class="carousel-item" style="height:100%;" data-bs-interval="2000">

						<div class="img-slide" style="background-image:url({{ URL::to('/') }}/assets/images/slide_5.jpg)"></div>
					</div>
					<div class="carousel-item" style="height:100%;" data-bs-interval="2000">

						<div class="img-slide" style="background-image:url({{ URL::to('/') }}/assets/images/slide_6.jpg)"></div>
					</div>
				  </div>

				</div>

			</div>
			{{-- <div style="display:block;height:22vh;width:100%;padding-left:20px;padding-right:20px;">
				<div style="display:block;height:22vh;width:100%;margin-top:0;background-image:url({{ URL::to('/') }}/assets/images/logos.png);background-size:contain;background-position:center;background-repeat:no-repeat"></div>
			</div> --}}
		</div>
		<div class="col-md-3 col-sm-12" style="background-image: url({{ URL::to('/assets/images/bg_side.jpg') }});height:100vh;overflow-y:auto;background-position: center;background-repeat: no-repeat;background-size: cover;">

			<div class="card" style="background:transparent;box-shadow:none;margin-bottom:0px;border:none">
				<div class="card-body login-card-body" style="background:transparent;padding-bottom:0px;">
				<div style="position:absolute;width:100%;height:100%;    background: #2b2b2b;
			opacity: 0.6;z-index:-1;margin: -20px;border-radius:7px"></div>

					<style>.box-input-login{padding:10vh 20px 0 20px}@media (min-width:768px){.box-input-login{padding:54% 20px 0 20px}}</style>
					<div class="box-input-login">
						<div class="text-center">
							<!-- /.login-logo -->
							<img style="height: 100px;" src="{{ URL::to('/') }}/grp_bg_logo.png" alt="GYS Logo" title="GYS Logo" class="img-fluid mx-auto"/>
						</div>


						<form id="form-login" method="post" action="{{ URL::to('/') }}/login" style="    width: 470px;
    max-width: 100%;
    display: block;
    margin: auto;
    padding-top: 20px;">
				<form method="post" action="{{ url('/login') }}">
					@csrf
                    <input id="direct-login" type="hidden" name="direct" value="helpdesk">
					@error('general')
					<span class="error invalid-feedback text-red">{{ $message }}</span>
					@enderror
							<div class="input-group mb-4">
								<input type="text" name="username" value="" placeholder="Username" required class="input-login form-control " style="font-size:14px; padding-left: 20px;">
								<div class="input-group-append">
									<div class="input-group-text"><span class="fas fa-user" style="font-size:14px"></span></div>
								</div>
															</div>

                                                            @error('email')
                                                            <span class="error invalid-feedback">{{ $message }}</span>
                                                            @enderror

							<div class="input-group mb-2">
								<input type="password" name="password" placeholder="Password" required class="input-login form-control " style="font-size:14px; padding-left: 20px;">
								<div class="input-group-append">
									<div class="input-group-text">
										<span class="fas fa-lock" style="font-size:14px"></span>
									</div>
								</div>

							</div>

							<div class="row">
								{{-- <div class="col-12">
									<a style="text-decoration: none;color: #fff;
											float: right;
											font-size: 14px;margin-top:16px" class="pull-right" href="{{ URL::to('/') }}/password/reset">Forgot password?</a>
								</div> --}}
								<br/>



								<div class="col-8 mb-3 d-none">


									<!-- Squared THREE -->
									<div class="squaredThree">
										<input type="checkbox" value="None" id="squaredThree" name="check"/>
										<label for="squaredThree"></label>

									</div>
									<span style="color:#fff;margin-left:10px;font-size:13.5px">Remember Me</span>
								</div>

								<div class="col-12" style="padding: 0 5px;margin-top:30px">
									<button type="submit" class="btn btn-danger btn-block" style="height: 40px;font-size:15px;float:right;width:100%;background-color:#ED1C24;">LOG IN</button>
								</div>

							</div>
						</form>

						<p class="mb-1">



						</p>
											</div>
				</div>
				<!-- /.login-card-body -->
			</div>

		</div>
	</div>
	</div>
</div>


<style>input[type="checkbox"]{visibility:hidden}.squaredThree{display:inline-block;width:20px}.squaredThree label{cursor:pointer;position:relative;width:17px;height:16px;top:2px;left:0;margin-right;5px;border-radius:4px;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.5) , 0 1px 0 rgba(255,255,255,.4);-moz-box-shadow:inset 0 1px 1px rgba(0,0,0,.5) , 0 1px 0 rgba(255,255,255,.4);box-shadow:inset 0 1px 1px rgba(0,0,0,.5) , 0 1px 0 rgba(255,255,255,.4);background:-webkit-linear-gradient(top,#222 0%,#45484d 100%);background:-moz-linear-gradient(top,#222 0%,#45484d 100%);background:-o-linear-gradient(top,#222 0%,#45484d 100%);background:-ms-linear-gradient(top,#222 0%,#45484d 100%);background:linear-gradient(top,#222 0%,#45484d 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#222', endColorstr='#45484d',GradientType=0 )}.squaredThree label:after{-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";filter: alpha(opacity=0);opacity:0;content:'';position:absolute;width:12px;height:7px;background:transparent;top:4px;left:4px;border:3px solid #fcfff4;border-top:none;border-right:none;-webkit-transform:rotate(-45deg);-moz-transform:rotate(-45deg);-o-transform:rotate(-45deg);-ms-transform:rotate(-45deg);transform:rotate(-45deg)}.squaredThree label:hover::after{-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=30)";filter: alpha(opacity=30);opacity:.3}.squaredThree input[type="checkbox"]:checked+label:after{-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";filter: alpha(opacity=100);opacity:1}</style>
<!-- AdminLTE App -->
<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.5/js/adminlte.min.js"
        integrity="sha512-++c7zGcm18AhH83pOIETVReg0dr1Yn8XTRw+0bWSIWAVCAwz1s2PwnSj4z/OOyKlwSXc4RLg3nnjR22q0dhEyA=="
        crossorigin="anonymous"></script>
-->

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Login Redirection Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Where do you want to open after login process,
        Helpdesk Portal or Employee Portal
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-employee">Employee Portal</button>
        <button type="button" class="btn btn-primary btn-helpdesk">Helpdesk Portal</button>
      </div>
    </div>
  </div>
</div>
<script>$('#aaaform-login').submit(function(){event.preventDefault();$('#exampleModalCenter').modal('show')});$('.btn-helpdesk').click(function(){$("#direct-login").val('helpdesk');$('#form-login').trigger('submit');});$('.btn-employee').click(function(){$("#direct-login").val('employee');$('#form-login').trigger('submit');});</script>
</body>
</html>
