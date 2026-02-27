<script>
function on_click_approve_link(agent) {
	window.location = '{{URL('/').'/approve-request'}}';
}
</script>

<style>
.flaticon-menu {
	margin-right:10px;
}
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item > .menu-heading .menu-text, .aside-menu .menu-nav > .menu-item .menu-submenu .menu-item > .menu-link .menu-text {
    color: #ccccd2;
}
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-active > .menu-heading .menu-bullet.menu-bullet-dot > span, .aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-active > .menu-link .menu-bullet.menu-bullet-dot > span {
    background-color: #ffffff;
}
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-here > .menu-heading .menu-bullet.menu-bullet-line > span, .aside-menu .menu-nav > .menu-item .menu-submenu .menu-item.menu-item-here > .menu-link .menu-bullet.menu-bullet-line > span {
    background-color: #ffffff;
}
.aside-menu .menu-nav > .menu-item .menu-submenu .menu-item > .menu-heading .menu-bullet.menu-bullet-dot > span, .aside-menu .menu-nav > .menu-item .menu-submenu .menu-item > .menu-link .menu-bullet.menu-bullet-dot > span {
    background-color: #ffffff;
}
.aside-menu .menu-nav > .menu-item > .menu-heading .menu-arrow, .aside-menu .menu-nav > .menu-item > .menu-link .menu-arrow {
    color: #fdfdfd;
}
.text-cyan {
	color: #bbdcfd !important;
}
</style>


<style>
@media all and (min-width: 992px) {
	.navi-hover .dropdown-menu{ display: none; }
	.navi-hover:hover .nav-link{   }
	.navi-hover:hover .dropdown-menu{ display: block; }
	.navi-hover .dropdown-menu{ margin-top:-70px; }
}
.dropright .dropdown-toggle::after {
    display: none;

}
.dropdown1 {
	background: #12344d;
}
.dropdown1 .navi.navi-hover .navi-item .navi-link:hover {
    background: #27628e;
}
.dropdown1 .navi .navi-item .navi-link .navi-text {
    color: #aedcfd !important;
}
.dropdown1 .navi .navi-item .navi-link:hover {

}
/*
.dropdown1 .navi .navi-item .navi-link.active .navi-icon i {
    color: #aedcfd !important;
}
.dropdown1 .navi .navi-item .navi-link .navi-icon i {
    color: #aedcfd !important;
}
  */
.dropdown1 .navi .navi-item .navi-link .navi-icon i {
    font-size:1.3rem;
    margin-right:10px;
}
.dropdown1  .navi .navi-header {
	padding: 0.1rem 1.5rem 0.8rem 1.5rem;
    color: #bbdcfd;

}
.dropdown1  .navi .navi-header {
	font-weight:500 !important;
	font-size: 1.1rem;
}
.dropdown1 .navi .navi-item .navi-link .navi-text {
    font-size: 1rem;
}
.label.label-inline {
    padding: 0.15rem 0.35rem;
}
</style>

<?php $first_level_organization = DB::table('organization_level')->value('name');?>
<?php

$path = app('request')->path();
$url_administration = in_array($path, ['list/organization_level', 'list/organization', 'list/company', 'users', 'authorization']);
$url_config = in_array($path, ['list/location', 'list/job_title', 'list/position', 'list/sla', 'list/holiday', 'list/request_type', 'list/request_management']);
$url_contact = in_array($path, ['list/employee', 'list/person', 'list/team']);
$url_form_builder = in_array($path, ['list/form_builder_object', 'list/form_builder']);
$url_service = in_array($path, ['list/service_category', 'list/service', 'list/incident', 'list/coverage_windows']);
$url_incident = in_array($path, ['list/incident_request', 'list/service_request']);
$url_problem = in_array($path, ['list/problem', 'list/faq', 'list/faq_category', 'list/knowledgebase_category', 'list/knowledge']);

?>

<?php

use Illuminate\Support\Facades\Auth;

$agents = array();

if (Auth::user()->person) {
	$contact = DB::table('contact')->where('id', Auth::user()->person)->first();
	if ($contact->job_title) {
		$job_title = DB::table('job_title')->where('id', $contact->job_title)->first();

		if (is_null($job_title->parent)) {
			$job_title2 = DB::table('job_title')->where('parent', $job_title->id)->get();
			foreach($job_title2 as $job2) {
				$agents[] = DB::table('contact')->where('job_title', $job2->id)->first();
			}
		} else {
			$job_title3 = DB::table('job_title')->where('parent', $job_title->id)->get();
			foreach($job_title3 as $job3) {
				$agents[] = DB::table('contact')->where('job_title', $job3->id)->first();
			}
		}
	}
}
?>

<ul class="menu-nav">
	<li class="menu-item {{ Request::is('home') ? 'menu-item-active' : '' }}" aria-haspopup="true">
		<a href="{{URL('/')}}/home" class="menu-link">
			<span class="flaticon2-protection flaticon-menu icon-lg text-neon">
			</span>
			<span class="menu-text {{ Request::is('home') ? 'text-muted' : '' }}">Dashboard</span>
		</a>
	</li>

	@if(true || accessv('asset_management','list','return'))
	<li class="menu-item menu-item-submenu dropright navi-hover navi-active {{ Request::is('request-service/service-catalog/2') ? 'menu-item-active' : '' }}" aria-haspopup="true" data-menu-toggle="hover">
		<a class="menu-link" href="{{ URL('/').'/request-service/service-catalog/2' }}">
			<span class="flaticon2-cube flaticon-menu icon-lg text-neon d-none">
			</span>
			<span class="svg-icon  svg-icon-warning menu-icon svg-icon-2x">
				<!--begin::Svg Icon | path:assets/media/svg/icons/Design/Sketch.svg-->
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<rect x="0" y="0" width="24" height="24" />
						<polygon fill="#000000" opacity="0.8" points="5 3 19 3 23 8 1 8" />
						<polygon fill="#000000" points="23 8 12 20 1 8" />
					</g>
				</svg>
				<!--end::Svg Icon-->
			</span>
			<span class="menu-text {{ Request::is('request-service/service-catalog/2') ? 'text-muted' : '' }}">Request Service</span>
		</a>
	</li>
	@endif
	@if(true || accessv('asset_management','list','return'))
	<li class="menu-item menu-item-submenu dropright navi-hover navi-active {{ Request::is('request-incident/incident-catalog/2') ? 'menu-item-active' : '' }}" aria-haspopup="true" data-menu-toggle="hover">
		<a class="menu-link" href="{{ URL('/').'/request-incident/incident-catalog/2' }}">
			<span class="flaticon2-cube flaticon-menu icon-lg text-neon d-none">
			</span>
			<span class="svg-icon  svg-icon-danger menu-icon svg-icon-2x">
				<!--begin::Svg Icon | path:assets/media/svg/icons/General/Thunder-move.svg-->
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<rect x="0" y="0" width="24" height="24" />
						<path d="M16.3740377,19.9389434 L22.2226499,11.1660251 C22.4524142,10.8213786 22.3592838,10.3557266 22.0146373,10.1259623 C21.8914367,10.0438285 21.7466809,10 21.5986122,10 L17,10 L17,4.47708173 C17,4.06286817 16.6642136,3.72708173 16.25,3.72708173 C15.9992351,3.72708173 15.7650616,3.85240758 15.6259623,4.06105658 L9.7773501,12.8339749 C9.54758575,13.1786214 9.64071616,13.6442734 9.98536267,13.8740377 C10.1085633,13.9561715 10.2533191,14 10.4013878,14 L15,14 L15,19.5229183 C15,19.9371318 15.3357864,20.2729183 15.75,20.2729183 C16.0007649,20.2729183 16.2349384,20.1475924 16.3740377,19.9389434 Z" fill="#000000" />
						<path d="M4.5,5 L9.5,5 C10.3284271,5 11,5.67157288 11,6.5 C11,7.32842712 10.3284271,8 9.5,8 L4.5,8 C3.67157288,8 3,7.32842712 3,6.5 C3,5.67157288 3.67157288,5 4.5,5 Z M4.5,17 L9.5,17 C10.3284271,17 11,17.6715729 11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L4.5,20 C3.67157288,20 3,19.3284271 3,18.5 C3,17.6715729 3.67157288,17 4.5,17 Z M2.5,11 L6.5,11 C7.32842712,11 8,11.6715729 8,12.5 C8,13.3284271 7.32842712,14 6.5,14 L2.5,14 C1.67157288,14 1,13.3284271 1,12.5 C1,11.6715729 1.67157288,11 2.5,11 Z" fill="#000000" opacity="1" />
					</g>
				</svg>
				<!--end::Svg Icon-->
			</span>
			<span class="menu-text {{ Request::is('request-incident/incident-catalog/2') ? 'text-muted' : '' }}">Request Incident</span>
		</a>
	</li>
	@endif
	@if(true || accessv('asset_management','list','return'))
	<li class="menu-item menu-item-submenu dropright navi-hover navi-active {{ Request::is('approve-request') ? 'menu-item-active' : '' }}" aria-haspopup="true" data-menu-toggle="hover">
		<a class="menu-link" href="javascript:;" onclick="on_click_approve_link()">
			<span class="flaticon2-cube flaticon-menu icon-lg text-neon d-none">
			</span>
			<span class="svg-icon  svg-icon-warning menu-icon svg-icon-2x">
				<!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Communication\Clipboard-check.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<rect x="0" y="0" width="24" height="24"/>
						<path style="" d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.7"/>
						<path style="" d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z" fill="#000000"/>
						<path style="" d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
					</g>
				</svg><!--end::Svg Icon-->
			</span>
			<span class="menu-text {{ Request::is('approve-request') ? 'text-muted' : '' }}">Approve Request</span>
		</a>
	</li>
	@endif
	@if(true || accessv('asset_management','list','return'))
	<li class="menu-item menu-item-submenu dropright navi-hover navi-active {{ Request::is('ticket-monitoring') ? 'menu-item-active' : '' }}" aria-haspopup="true" data-menu-toggle="hover">
		<a class="menu-link" href="{{ URL('/').'/ticket-monitoring' }}">
			<span class="flaticon2-cube flaticon-menu icon-lg text-neon d-none">
			</span>
			<span class="svg-icon  svg-icon-danger menu-icon svg-icon-2x">
				<!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Communication\Urgent-mail.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<rect x="0" y="0" width="24" height="24"/>
						<path style="" d="M12.7037037,14 L15.6666667,10 L13.4444444,10 L13.4444444,6 L9,12 L11.2222222,12 L11.2222222,14 L6,14 C5.44771525,14 5,13.5522847 5,13 L5,3 C5,2.44771525 5.44771525,2 6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,13 C19,13.5522847 18.5522847,14 18,14 L12.7037037,14 Z" fill="#000000" opacity="1"/>
						<path style="" d="M9.80428954,10.9142091 L9,12 L11.2222222,12 L11.2222222,16 L15.6666667,10 L15.4615385,10 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 L9.80428954,10.9142091 Z" fill="#000000"/>
					</g>
				</svg><!--end::Svg Icon-->
			</span>
			<span class="menu-text {{ Request::is('ticket-monitoring') ? 'text-muted' : '' }}">Tracking Your Ticket</span>
		</a>
	</li>
	@endif
	@if(true || accessv('asset_management','list','return'))
	<li class="menu-item menu-item-submenu dropright navi-hover navi-active d-none {{ Request::is('myDraft') ? 'menu-item-active' : '' }}" aria-haspopup="true" data-menu-toggle="hover">
		<a class="menu-link" href="{{ URL('/').'/myDraft' }}">
			<span class="flaticon2-cube flaticon-menu icon-lg text-neon d-none">
			</span>
			<span class="svg-icon  svg-icon-info menu-icon svg-icon-2x">
				<!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Communication\Urgent-mail.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<rect x="0" y="0" width="24" height="24"/>
						<path style="" d="M12.7037037,14 L15.6666667,10 L13.4444444,10 L13.4444444,6 L9,12 L11.2222222,12 L11.2222222,14 L6,14 C5.44771525,14 5,13.5522847 5,13 L5,3 C5,2.44771525 5.44771525,2 6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,13 C19,13.5522847 18.5522847,14 18,14 L12.7037037,14 Z" fill="#000000" opacity="1"/>
						<path style="" d="M9.80428954,10.9142091 L9,12 L11.2222222,12 L11.2222222,16 L15.6666667,10 L15.4615385,10 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 L9.80428954,10.9142091 Z" fill="#000000"/>
					</g>
				</svg><!--end::Svg Icon-->
			</span>
			<span class="menu-text {{ Request::is('myDraft') ? 'text-muted' : '' }}">Draft Request</span>
		</a>
	</li>
	@endif
	@if(true || accessv('asset_management','list','return'))
	<li class="menu-item menu-item-submenu dropright navi-hover navi-active {{ Request::is('faq') ? 'menu-item-active' : '' }}" aria-haspopup="true" data-menu-toggle="hover">
		<a class="menu-link" href="{{ URL('/').'/faq' }}">
			<span class="flaticon2-cube flaticon-menu icon-lg text-neon d-none">
			</span>
			<span class="svg-icon  svg-icon-warning menu-icon svg-icon-2x">
				<!--begin::Svg Icon | path:assets/media/svg/icons/Home/Mirror.svg-->
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<rect x="0" y="0" width="24" height="24" />
						<path style="" d="M13,17.0484323 L13,18 L14,18 C15.1045695,18 16,18.8954305 16,20 L8,20 C8,18.8954305 8.8954305,18 10,18 L11,18 L11,17.0482312 C6.89844817,16.5925472 3.58685702,13.3691811 3.07555009,9.22038742 C3.00799634,8.67224972 3.3975866,8.17313318 3.94572429,8.10557943 C4.49386199,8.03802567 4.99297853,8.42761593 5.06053229,8.97575363 C5.4896663,12.4577884 8.46049164,15.1035129 12.0008191,15.1035129 C15.577644,15.1035129 18.5681939,12.4043008 18.9524872,8.87772126 C19.0123158,8.32868667 19.505897,7.93210686 20.0549316,7.99193546 C20.6039661,8.05176407 21.000546,8.54534521 20.9407173,9.09437981 C20.4824216,13.3000638 17.1471597,16.5885839 13,17.0484323 Z" fill="#000000" fill-rule="nonzero" />
						<path style="" d="M12,14 C8.6862915,14 6,11.3137085 6,8 C6,4.6862915 8.6862915,2 12,2 C15.3137085,2 18,4.6862915 18,8 C18,11.3137085 15.3137085,14 12,14 Z M8.81595773,7.80077353 C8.79067542,7.43921955 8.47708263,7.16661749 8.11552864,7.19189981 C7.75397465,7.21718213 7.4813726,7.53077492 7.50665492,7.89232891 C7.62279197,9.55316612 8.39667037,10.8635466 9.79502238,11.7671393 C10.099435,11.9638458 10.5056723,11.8765328 10.7023788,11.5721203 C10.8990854,11.2677077 10.8117724,10.8614704 10.5073598,10.6647638 C9.4559885,9.98538454 8.90327706,9.04949813 8.81595773,7.80077353 Z" fill="#000000" opacity="1" />
					</g>
				</svg>
				<!--end::Svg Icon-->
			</span>
			<span class="menu-text {{ Request::is('faq') ? 'text-muted' : '' }}">FAQ &amp; Tutorials</span>
		</a>
	</li>
	@endif


</ul>
