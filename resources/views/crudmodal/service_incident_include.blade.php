@extends('layouts.app')

@section('content')
<style>
.note-editable {
	min-height:100px !important;
}
</style>
<?php use App\Helpers\TicketStatusHelper; ?>
<!-- Modal -->
<div  class="modal-reply" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Reply Comment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="close-btn">&times;</span>
        </button>
      </div>
		<form id="comment-form" method="post" action="{{URL('/').'/replyComment'}}"  enctype="multipart/form-data">
			@csrf
			<div class="modal-body">
				<input type="hidden" name="id" value="{{$ticket->id}}" />
				<div class="form-group">
					<label for="exampleTextarea">Your Message</label>
					<textarea name="message" id="summernote" class="form-control form-control-solid form-control-lg" rows="7"></textarea>
					<span class="form-text text-muted"></span>
				</div>
				<style>
				.image-preview-input {
					position: relative;
					overflow: hidden;
					margin: 0px;
					margin-left: -5px;
					background-color: #fff;
					border-color: #ccc;
				    border-top-left-radius: 0;
					border-bottom-left-radius: 0;
				}
				.image-preview-input input[type=file] {
					position: absolute;
					top: 0;
					right: 0;
					margin: 0;
					padding: 0;
					font-size: 20px;
					cursor: pointer;
					opacity: 0;
					filter: alpha(opacity=0);
				}
				.image-preview-input-title {
					margin-left:2px;
				}
				.image-preview-clear {
				    border-radius:0 !important;
					position: relative;
					margin: 0px;
					background-color: #fff;
					border-color: #ccc;
				}
				.img-prev {
					max-height:100px !important;
				}
				.add-more-file {
					background-color: #fff;
					border-color: #ccc;
				}
				.image-preview-filename {
					height:38px;
				}
				</style>
				<div class="box-files-upload">
					<div class="form-group">
						<label for="exampleTextarea">Upload File/s</label>
							<!-- image-preview-filename input [CUT FROM HERE]-->
							<div class="input-group image-preview">
								<input type="text" class="form-control image-preview-filename" disabled="disabled"> <!-- don't give a name === doesn't send on POST/GET -->
								<span class="input-group-btn">
									<!-- image-preview-clear button -->
									<button id="image-preview-clear" type="button" class="btn image-preview-clear" style="display:none;">
										<span class="glyphicon glyphicon-remove"></span> Clear
									</button>
									<!-- image-preview-input -->
									<div class="btn image-preview-input">
										<span class="glyphicon glyphicon-folder-open"></span>
										<span class="image-preview-input-title">Browse</span>
										<input type="file" accept222="image/png, image/jpeg, image/gif" name="file"/> <!-- rename it -->
									</div>
								</span>
							</div><!-- /input-group image-preview [TO HERE]-->
					</div>
				</div>
				<button type="button" class="btn add-more-file btn-sm" style="display:block">Add More File</button>
				<span class="form-text text-muted">Max 3 file attachments. Total 5MB</span>
			</div>


			<div class="modal-footer">
			<button type="button" class="close-btn btn btn-secondary" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-primary">Send Message</button>
			</div>
		</form>
    </div>
  </div>
</div>

<?php
$name = DB::table('users')->where('id', $ticket->created_by)->value('name');

$contact = DB::table('contact')->where('id',$ticket->requester)->first();
if(!empty($contact->name)) {
	$name = $contact->name;
}
$job_name = "";
$email = "";
if(!empty($contact->job_title)) {
	$job_name = DB::table('job_title')->where('id',$contact->job_title)->value('job_name');
}

?>
<!--begin::Container-->
<div class="container-fluid container-detail-ticket">
    <!--begin::Row-->
    <div class="row">
        <div class="col-xl-8">
			<?php $mode = "ticket_monitoring"; ?>
			@include('content_ticket_left')
            <div class="messages">
			@include('comments')
            </div>
        </div>
        <div class="col-xl-4">
			@include('content_ticket')

			@include('case_journey_content')
        </div>

    </div>
    <!--end::Row-->
</div>
<!--end::Container-->

<div class="modal fade modal-reply-reason" id="reasonModal" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title status-ticket" id="exampleModalLabel">Reason Reject</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i aria-hidden="true" class="ki ki-close"></i>
				</button>
			</div>
			<div class="modal-body">
				<!--begin::Item-->
				<div class="mb-10 reason">
					<!--begin::Section-->
					<div class="d-flex align-items-center">
						<!--begin::Text-->
						<div class="d-flex flex-column flex-grow-1">
							<span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Reason</span>
						</div>
						<!--end::Text-->
					</div>
					<!--end::Section-->
					<div class="col-lg-12 col-md-12 col-sm-12">
						<textarea name="reason" class="form-control reason-textarea" data-provide="markdown" rows="10" cols="20"></textarea>
					</div>
					<input class="form-control status-value" type="hidden" name="status"/>
					<input class="form-control" type="hidden" name="approval_custom" value="0"/>
				</div>
				<!--end::Item-->
			</div>
			<div class="modal-footer">
				{!! Form::submit('Submit', ['class' => 'btn btn-primary mr-2 approved']) !!}
				<button type="button" class="close-btn btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<link href="{{URL('/')}}/vendor/summernote/summernote.min.css" rel="stylesheet">
<script src="{{URL('/')}}/vendor/summernote/summernote.min.js"></script>
<!--
<script src="{{URL('/')}}/vendor/ckeditor/ckeditor.js"></script>
-->
<script type="text/javascript">
    //$(document).ready(function () {
        //$('#ckeditor2').ckeditor();
		//CKEDITOR.replace('wysiwyg-editor', {
			//filebrowserUploadUrl: "{{route('ckeditor.image-upload', ['_token' => csrf_token() ])}}",
			//filebrowserUploadMethod: 'form'
		//});
    //});
$('.modal-reply-reason').hide();

$(document).ready(function() {
	$('#summernote').summernote('code', '<p><br></p>');

	$('.reason-modal').on('click', function() {
		$('.modal-reply-reason').modal('show');
		$('.status-ticket').html($(this).data('status-ticket') + ' Reason');
		$('.status-value').val($(this).data('status-ticket').toLowerCase());
	})

	$('.approved').on('click', function() {
		console.log("TH1")
		$('.modal-reply-reason').modal('hide');

        Swal.fire({title: "Confirmation",text: "Are you sure want do "+$('.status-value').val()+" for this ticket ?",icon: "question",showCancelButton: true,confirmButtonText: "Yes!"
        }).then(function(result) {
            if (result.value) {
                if ($('.status-value').val() == 'approve') {
					KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});
					$.ajax({
						type: "POST",
						url: '{{URL("/")}}/approve-request/ticketAction',						
						data: {
							"_token": '{{csrf_token()}}',
							id: '{{ $ticket->id }}',
							action_detail: 'approved',
							message: $('.reason-textarea').val()
						},
						success: function(data){
							KTApp.unblockPage();

							var obj = JSON.parse(data);

							if(obj.success) {

								if(obj.warning) {
									console.log("TH2")
									<?php 
										//ada warning kemungkinan saat agent on leave 
										//dari flow grp yang baru
									?>
									Swal.fire("Confirmation",obj.message,"warning")
									setTimeout(function() {
										location.reload();
									},10000);

								} else {
									console.log("TH3")
									Swal.fire("Confirmation", obj.message, "success")
									setTimeout(function() {
										location.reload();
									},3000);

								}
							} else {
								Swal.fire("Failed",obj.message,"error")
							}

						},
						error: function(){
							KTApp.unblockPage();
							Swal.fire("Failed","Sorry, your action is failed","error")
						}
					});
				} else {
					$.ajax({
						type: "POST",
						url: '{{URL("/")}}/approve-request/ticket_reject',
						data: {
							"_token": '{{csrf_token()}}',
							id: '{{ $ticket->id }}',
							message: $('.reason-textarea').val()
						},
						success: function(data){
							KTApp.unblockPage();

							var obj = JSON.parse(data);

							if(obj.success) {
								Swal.fire("Confirmation", obj.message, "success")
								setTimeout(function() {
									location.reload();
								},3000);
							} else {
								Swal.fire("Failed",obj.message,"error")
							}
						},
						error: function(){
							KTApp.unblockPage();
							Swal.fire("Failed","Sorry, your action is failed","error")
						}
					});
				}
            }
        });
    });
});
</script>
<style>
.modal-reply {
	z-index:1000000 !important;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1050;
    display: none;
    width: 100%;
    height: 100%;
    overflow: hidden;
    outline: 0;
}
</style>
<script>
$('.modal-reply').hide();
$('.note-modal').hide();
$(".close-btn").click(function(e) {
	$('.modal-reply').hide();
	$(".modal-backdrop").remove();
});
$(".reply-comment").click(function(e) {
	$('body').append('<div class="modal-backdrop fade show"></div>');
	$('.modal-reply').show();
	$('.note-modal').hide();
	$('#summernote').summernote('code', '<p><br></p>');
});
$('#comment-form').on('submit',(function(e) {
	$(".modal-backdrop").remove();
	$('.modal-reply').hide();
	KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});
	e.preventDefault();
	var formData = new FormData(this);
	$('#exampleModal').modal('hide')

	$.ajax({
		type:'POST',
		url: $(this).attr('action'),
		data:formData,
		cache:false,
		contentType: false,
		processData: false,
		success:function(data){
			$('#input-group-2').empty();
			$('#input-group-2').remove();
			$('#input-group-3').empty();
			$('#input-group-3').remove();
			$(".image-preview-filename").val("");
			$('.modal-reply').hide();
			KTApp.unblockPage();
			console.log("success");
			console.log(data);

			var obj = JSON.parse(data);
			if(obj.success) {

				Swal.fire("Confirmation",obj.message,"success")
				$('.messages').html(obj.content);
				//setTimeout(function() {
					//location.reload();
				//},3000);

			} else {
				Swal.fire("Failed",obj.message,"error")
			}
			$('#summernote').summernote('code', '<p><br></p>');
		},
		error: function(data){
			$('.modal-reply').hide();
			KTApp.unblockPage();
			console.log("error");
			console.log(data);
			$('#summernote').summernote('code', '<p><br></p>');
		}
	});
}));

$(".add-more-file").click(function(e) {
	var idx = "2";
	if($('#file2').length) {
		idx = 3;
		$(this).hide();
	}

	$(".box-files-upload").append('<div class="form-group">\
										\
											<div id="input-group-'+idx+'" class="input-group image-preview">\
												<input type="text" class="form-control image-preview-filename" disabled="disabled"> \
												<span class="input-group-btn">\
													<button type="button" class="btn image-preview-clear" style="display:none;">\
														<span class="glyphicon glyphicon-remove"></span> Clear\
													</button>\
													<div class="btn image-preview-input">\
														<span class="glyphicon glyphicon-folder-open"></span>\
														<span class="image-preview-input-title">Browse</span>\
														<input id="file'+idx+'" name="file'+idx+'" type="file" accept222="image/png, image/jpeg, image/gif" />\
													</div>\
												</span>\
											</div>\
									</div>');
});


$(".solved").click(function(e) {
    Swal.fire({title: "Confirmation",text: "Are you sure want to mark this ticket as Solved ?",icon: "question",showCancelButton: true,confirmButtonText: "Yes!"
    }).then(function(result) {
        if (result.value) {
			 KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});
			$.ajax({
				type: "POST",
				url: '{{URL("/")}}/ticketAction',
				data: "id={{$ticket->id}}&action=solved&message=",
				//dataType: 'json',
				success: function(data){
					KTApp.unblockPage();
					console.log(data);
					var obj = JSON.parse(data);
					if(obj.success) {
						Swal.fire("Confirmation",obj.message,"success")
						setTimeout(function() {
							location.reload();
						},3000);
					} else {
						Swal.fire("Failed",obj.message,"error")
					}
				},
				error: function(){
					KTApp.unblockPage();
					console.log("error");
					Swal.fire("Failed","Sorry, your action is failed","error")

				}
			});
        }
    });
});
$(".on_progress").click(function(e) {
    Swal.fire({title: "Confirmation",text: "Are you sure want to start this case and mark this ticket as On Progress ?",icon: "question",showCancelButton: true,confirmButtonText: "Yes!"
    }).then(function(result) {
        if (result.value) {
			 KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});
			$.ajax({
				type: "POST",
				url: '{{URL("/")}}/ticketAction',
				data: "id={{$ticket->id}}&action=on_progress&message=",
				//dataType: 'json',
				success: function(data){
					KTApp.unblockPage();
					console.log(data);
					var obj = JSON.parse(data);
					if(obj.success) {
						Swal.fire("Confirmation",obj.message,"success")
						setTimeout(function() {
							location.reload();
						},3000);
					} else {
						Swal.fire("Failed",obj.message,"error")
					}
				},
				error: function(){
					KTApp.unblockPage();
					console.log("error");
					Swal.fire("Failed","Sorry, your action is failed","error")

				}
			});
        }
    });
});
$(".escalate").click(function(e) {
    Swal.fire({title: "Confirmation",text: "Are you sure want do escalation for this ticket ?",icon: "question",showCancelButton: true,confirmButtonText: "Yes!"
    }).then(function(result) {
        if (result.value) {
			 KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});
			$.ajax({
				type: "POST",
				url: '{{URL("/")}}/ticketAction',
				data: "id={{$ticket->id}}&action=escalate&message=",
				//dataType: 'json',
				success: function(data){
					KTApp.unblockPage();
					console.log(data);
					var obj = JSON.parse(data);
					if(obj.success) {
						Swal.fire("Confirmation",obj.message,"success")
						setTimeout(function() {
							location.reload();
						},3000);
					} else {
						Swal.fire("Failed",obj.message,"error")
					}

				},
				error: function(){
					KTApp.unblockPage();
					console.log("error");
					Swal.fire("Failed","Sorry, your action is failed","error")


				}
			});
        }
    });
});
</script>

@endsection

