@extends('layouts.app')

@section('content')
<style>
.nav .nav-link .nav-text {
    color: #50525f;
}
#kt_wrapper {
	background: #f0f7fd;
}
</style>

<script>
jQuery.fn.rotate = function(degrees) {
    $(this).css({'-webkit-transform' : 'rotate('+ degrees +'deg)',
                 '-moz-transform' : 'rotate('+ degrees +'deg)',
                 '-ms-transform' : 'rotate('+ degrees +'deg)',
                 'transform' : 'rotate('+ degrees +'deg)'});
    return $(this);
};
$(document).ready(function() {
	if (localStorage.getItem('is_back_service') == '1') {
		var target = localStorage.getItem('target');

		$('a[href="#'+target+'"]').trigger('click');
	}

	$('.service-title').html($('.click-category').data('title-click'));

	$('.click-category').on('click', function() {
		var href = $(this).attr('href');
		$('.tab-pane').hide();
		$(href).show();
		$('.service-title').html($(this).data('title-click'));
	})

	if ($('.no-category').data('count-category') == '0') {
		$('.card-category-left').hide();
	}

	@if(Session::has('warning'))
		Swal.fire({title: "Resolved Ticket Need Closed",text: "It's look like you have not close your Resolved Ticket. Please close all your resolved ticket, so you can create a new ticket ",icon: "warning",showCancelButton: true,confirmButtonText: "Check Resolved Ticket",  cancelButtonText: "Close",
		}).then(function(result) {
			if (result.value) {
				window.location = "{{URL('/')}}/list/ticket?status_ticket=Resolved";
			}
		});
	@endif

	var screen_height = screen.height;
	var kt_header_height = $("#kt_header").height();
	var kt_subheader_height = $("#kt_subheader").height();

	$("#sticky-nav").css("height", screen_height-kt_header_height-kt_subheader_height-250);
	//$(".sticky-main").css("height", screen_height-kt_header_height-kt_subheader_height-230);

	localStorage.setItem('is_back_service', '1');

	$(document).on('click', '.nav-link', function(){
		$(".nav-link").removeClass("active");
		$(this).addClass("active");
	});

	var card10 = new KTCard('kt_card_10');
	var rotation = 0;
	$('.toggle-category').click(function() {

		if($('.col-left .card-body-top').is(":visible")) {
			rotation -= 90;
			$('.toggle-category').rotate(rotation);
		} else {
			rotation += 90;
			$('.toggle-category').rotate(rotation);
		}
		$('.col-left .card-body-top').slideToggle('slow');
		//card10.toggle();

	})
	if (window.matchMedia('(max-width: 767px)').matches) {
		$('.category-item-left').on('click', function() {
			if($('.col-left .card-body-top').is(":visible")) {
				rotation -= 90;
				$('.toggle-category').rotate(rotation);
			} else {
				rotation += 90;
				$('.toggle-category').rotate(rotation);
			}
			$('.col-left .card-body-top').slideUp('slow');
			//card10.collapse();
		})
	} else {

	}
});

</script>
<style>
.col-left .card-body-top {

}
.card-header {
    border-bottom: 0px solid #fff;
}
.card-header {
    padding: 2rem 2.25rem 1rem 2.25rem;
}

@media (min-width: 768px){
	.col-left .card-body-top {
		min-height: 500px;
	}
	.toggle-category {
		display:none;
	}
}
</style>
<div class="container-fluid">
    <div class="row">
        <span class="ml-4">You do not have a <strong>Job Title</strong> or <strong>Organization</strong>. Please contact the IT Helpdesk team to fix your data first before creating a ticket.</span>
    </div>
</div>
<style>
.ps__rail-y,.ps__rail-x {
	display:none !important;
}

.card-left ::-webkit-scrollbar {
  width: 19px;
}

.card-left ::-webkit-scrollbar-track {
  background-color: transparent;
}

.card-left ::-webkit-scrollbar-thumb {
  background-color: #f7f7f7;
  border-radius: 20px;
  border: 6px solid transparent;
  background-clip: content-box;
}

.card-left ::-webkit-scrollbar-thumb:hover {
  background-color: #eaeaea;
}
</style>
<script>
    $(document).ready(function () {
        $('.category-item-left').click(function (e) {
            $('.show-services').removeClass('d-none');
        });
    });

	$('#select-division').change(function() {
		const val = $(this).val()
		window.location.href = '?division='+val
	})


</script>
@endsection
