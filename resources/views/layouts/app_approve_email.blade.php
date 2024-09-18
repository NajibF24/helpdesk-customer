
<!DOCTYPE html>
<html lang="en">
	<head><base href="">
		<meta charset="utf-8" />
		<title>GYS Service Management System - Employee Portal</title>
		<meta name="description" content="Updates and statistics" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<link rel="canonical" href="https://keenthemes.com/metronic" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Page Vendors Styles(used by this page)-->
		<link href="{{URL('/')}}/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
		<!--end::Page Vendors Styles-->
		<!--begin::Global Theme Styles(used by all pages)-->

		<link href="{{URL('/')}}/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />

		<link href="{{URL('/')}}/assets/plugins/custom/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css" />
		<link href="{{URL('/')}}/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />

		<link href="{{URL('/')}}/assets/plugins/custom/jstree/jstree.bundle.css" rel="stylesheet" type="text/css" />

		<link href="{{URL('/')}}/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />

		<!--end::Global Theme Styles-->
		<!--begin::Layout Themes(used by all pages)-->
		<!--end::Layout Themes-->
		<link rel="shortcut icon" href="{{URL('/')}}/grp_bg_logo.png" />


		<!--end::Demo Panel-->
		<script>var HOST_URL = "https://preview.keenthemes.com/metronic/theme/html/tools/preview";</script>
		<!--begin::Global Config(global config for global JS scripts)-->
		<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#6993FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#E1E9FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script>
		<!--end::Global Config-->
		<!--begin::Global Theme Bundle(used by all pages)-->
		<script src="{{URL('/')}}/assets/plugins/global/plugins.bundle.js"></script>
		<script src="{{URL('/')}}/assets/plugins/custom/prismjs/prismjs.bundle.js"></script>

        <script src="{{URL('/')}}/assets/js/scripts.bundle.js"></script>

		<!--end::Global Theme Bundle-->

		<!-- select2 -->
		<link rel="stylesheet" href="{{URL('/')}}/vendor/select2/select2.min.css">
		<script src="{{URL('/')}}/vendor/select2/select2.min.js"></script>

		<script>
		$(document).ready(function() {
			$('.select2').select2();
		});
		</script>
		<style>
		.select2-selection {
			height: 35px !important;
		}
		.select2-container--default .select2-selection--single .select2-selection__arrow {
			height: 40px;
		}
		.select2-container--default .select2-selection--single .select2-selection__rendered {
			line-height: 15px;
		}
		.select2-container--default .select2-selection--single {
			border: 1px solid #bbbdc7;
		}
		#kt_wrapper {
			background: #f0f7fd;

		}

		#kt_datatable td {
			white-space: nowrap;
			overflow: hidden;
			height: 10px;
		}
		#kt_datatable th {
			padding-top: 0.8rem;
			padding-bottom: 0.8rem;
		}
		#kt_datatable td {
			padding-top: 0.8rem;
			padding-bottom: 0.8rem;
		}
		#kt_datatable table{
			table-layout:fixed;
		}



	.btn-white-line3 {
		width: auto;
		margin-left: 10px;
		cursor: pointer;
		color: #181C32;
		background-color: transparent;
		border-color: #181C32;
		background: linear-gradient(180deg, #fff 0%, #f5f7f9 100%);
		border: 1px solid #cfd7df !important;
		-moz-box-shadow: 0 1px 0 0 rgba(24,50,71,0.05) !important;
		-webkit-box-shadow: 0 1px 0 0 rgba(24,50,71,0.05) !important;
		box-shadow: 0 1px 0 0 rgba(24,50,71,0.05) !important;
		font-weight: 600;
		color: #444a6d;
		outline: none !important;
		vertical-align: middle;
		border-radius:3px;
	}
	.btn-white-line2 {

		width: auto;
		margin-left: 10px;
		cursor: pointer;
		color: #181C32;
		background-color: transparent;
		border-color: #181C32;
		background: linear-gradient(180deg, #fff 0%, #f5f7f9 100%);
		border: 1px solid #cfd7df !important;
		-moz-box-shadow: 0 1px 0 0 rgba(24,50,71,0.05) !important;
		-webkit-box-shadow: 0 1px 0 0 rgba(24,50,71,0.05) !important;
		box-shadow: 0 1px 0 0 rgba(24,50,71,0.05) !important;
		font-weight: 600;
		color: #444a6d;
		outline: none !important;
		vertical-align: middle;
		border-radius:3px;
		font-weight: 500;color: #000000;margin-left:2px;border-radius:10px
	}
	.btn-white-line2 i{
		font-size: 1rem !important;
	}
		</style>
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="quick-panel-right demo-panel-right offcanvas-right header-mobile-fixed aside-enabled aside-static page-loading"

	>
@yield('content')
</html>
