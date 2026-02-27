<script>
$( document ).ready(function() {
	// cek if has local storage expand
	$('.brand-toggle').css('left','235px');
	$('.logo-image').css('margin-left','0px');
	$('.aside-enabled .header.header-fixed').css('left','240px');
	$('.dropdown1').addClass('d-none');
	$('body').removeClass('aside-minimize');
	$('body').removeClass('aside-minimize-hoverable');
	$('.icon-arr').removeClass('flaticon2-right-arrow');
	$('.icon-arr').addClass('flaticon2-left-arrow');
	setTimeout(function() {
		$('.brand-toggle').removeClass('active');
		$('.side-logo').show();
	}, 2000);

	localStorage.setItem('is_collapsed', "0");

	$('.side-logo').hide();
	$("body").on('click', '.brand-toggle', function(){
		if($(this).hasClass('active')) {
			$('.side-logo').hide();
			$('.brand-toggle').css('left','60px');
			$('.logo-image').css('margin-left','-20px');
			$('.aside-enabled .header.header-fixed').css('left','70px');
			$('.dropdown1').removeClass('d-none');
			$('.icon-arr').addClass('flaticon2-right-arrow');
			$('.icon-arr').removeClass('flaticon2-left-arrow');

			localStorage.setItem('is_collapsed', "1");
		} else {
			$('.side-logo').show();
			$('.brand-toggle').css('left','235px');
			$('.logo-image').css('margin-left','0px');
			$('.aside-enabled .header.header-fixed').css('left','240px');
			$('.dropdown1').addClass('d-none');
			$('.icon-arr').removeClass('flaticon2-right-arrow');
			$('.icon-arr').addClass('flaticon2-left-arrow');

			localStorage.setItem('is_collapsed', "0");
		}
	});

	$( ".aside-menu-wrapper" ).hover(function() {
		if (localStorage.getItem('is_collapsed') == "1") {
			$('.side-logo').show();
			$('.brand-toggle').css('left','255px');
			$('.logo-image').css('margin-left','0px');
			$('.icon-arr').removeClass('flaticon2-right-arrow');
			$('.icon-arr').addClass('flaticon2-left-arrow');
		}
	}, function() {
		if (localStorage.getItem('is_collapsed') == "1") {
			$('.side-logo').hide();
			$('.brand-toggle').css('left','60px');
			$('.logo-image').css('margin-left','-20px');
			$('.icon-arr').addClass('flaticon2-right-arrow');
			$('.icon-arr').removeClass('flaticon2-left-arrow');
		}
	});
});
</script>
<style>
.brand {
    padding-right:0px;
}
</style>
<style>

.aside-menu .menu-nav > .menu-item > .menu-heading .menu-text, .aside-menu .menu-nav > .menu-item > .menu-link .menu-text {
    color: #dddee8;
}
.aside-menu .menu-nav > .menu-section .menu-text {
    color: #d0d0d0;
}
.brand {
    background-color: #12344d;#e82f24;
}
.aside {
    background-color: #12344d;#e82f24;
}
.aside-menu {
    background-color: #12344d;#e82f24;
}
.aside-menu .menu-nav > .menu-item.menu-item-open > .menu-link {
    background-color: #12344d;
}
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-here > .menu-heading, .aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-here > .menu-link {
    background-color: #12344d;
}
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-active > .menu-heading, .aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-active > .menu-link {
    background-color: #12344d;
}
</style>

				<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
					<!--begin::Brand-->
					<div class="brand flex-column-auto" id="kt_brand">
						<img alt="Logo" class="logo-image" src="{{URL('/')}}/assets/images/logo_nabati.png" style="margin-left:-20px;height: 22px;" />

							<span class="side-logo" style="font-size: 15px;
    color: white;
    font-weight: 500;
    display: block;
    position: absolute;
    left: 70px;">NGS System</span>
						<!--end::Logo-->
						<!--begin::Toggle-->
						<button class="brand-toggle btn btn-sm px-0 active" id="kt_aside_toggle" style="position: absolute;
    top: 50px;
    background: #fff;
    color: #000;
    border-radius: 50px;
    height: 26px;
    padding: 0px;
    width: 26px;
    left: 60px;
    -moz-box-shadow: 0 0 3px rgba(0,0,0,0.15);
    -webkit-box-shadow: 0 0 3px rgba(0,0,0,0.15);
    box-shadow: 0 0 3px rgba(0,0,0,0.15);
    border: 0.5px solid #d2cfcf;">

							<span style="font-size: 18px; line-height: 1;"><i style="font-size: 12px;margin-left: 2.5px;" class="icon-arr flaticon2-right-arrow"></i></span>
							<span class="svg-icon svg-icon svg-icon-sm" style="display:none;margin-left: 2px;color:#000">
								<!--begin::Svg Icon | path:{{URL('/')}}/template1/dist/assets/media/svg/icons/Navigation/Angle-double-left.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<polygon points="0 0 24 0 24 24 0 24" />
										<path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
										<path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
									</g>
								</svg>
								<!--end::Svg Icon-->
							</span>
						</button>
						<!--end::Toolbar-->
					</div>
					<!--end::Brand-->
					<!--begin::Aside Menu-->
					<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper" style="overflow: unset;">
						<!--begin::Menu Container-->
						<div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
							<!--begin::Menu Nav-->
							@include('layouts.menu')
							<!--end::Menu Nav-->
						</div>
						<!--end::Menu Container-->
					</div>
					<!--end::Aside Menu-->
				</div>
				<!--end::Aside-->
