@extends('layouts.app_approve_email')

@section('content')

<?php
use App\Helpers\TicketStatusHelper;
use Illuminate\Support\Facades\Auth;
 ?>
<style>
.note-editable {
	min-height:100px !important;
}
</style>
<style>
#kt_wrapper {
	background: #f0f7fd;
}

.btn-lg, .btn-group-lg > .btn {
    border-radius: 0.2rem !important;
    font-size: 1.38rem;
}
</style>


<?php
$name = DB::table('users')->where('id', $inventory->created_by)->value('name');

$contact = DB::table('contact')->where('id',$inventory->requestor)->first();
if(!empty($contact->name)) {
	$name = $contact->name;
}
$job_name = "";
$email = "";
if(!empty($contact->job_title)) {
	$job_name = DB::table('job_title')->where('id',$contact->job_title)->value('job_name');
}

?>
<div class="row">
	<div class="col-md-12">
        @php
            // dd($inventory);
        @endphp
		@if ($inventory->status != 'Withdrawn' && $inventory->next_approver_id && Auth::user()->person == $inventory->next_approver_id)
		<h2 class="mt-5 title-desc" style="text-align:center">You need to approve/reject this Inventory Transaction.  </h2>
		@else
		<h2 class="mt-5 title-desc" style="text-align:center">You have done your action (approve/reject) for this inventory transaction. Approve/reject action is unavailable.</h2>
		@endif
		<h2 style="text-align:center">Inventory Transaction Number : {{$inventory->code}}</h2>
		<h2 style="text-align:center">Requester : {{$name}}</h2>
		<h2 style="text-align:center">Subject : {{$inventory->subject}} </h2>
			<div style="    text-align: center;">
					@if ($inventory->status != 'Withdrawn' && $inventory->next_approver_id && Auth::user()->person == $inventory->next_approver_id)
						&nbsp;
						<button style="width: 200px;background-color: #3fb618;border-color: #3fb618;" class="btn btn-success btn-lg mt-7 reasonModalButton" data-id="{{ $inventory->id }}" data-title="{{ $inventory->subject }}" data-status-ticket="Approve">Approve</button>
						&nbsp;
						<button style="width: 200px;background-color: #ff0039;border-color: #ff0039;"  class="btn btn-danger btn-lg ml-2 mt-7 reasonModalButton" data-status-ticket="Reject">Reject</button>
						&nbsp;
					@endif
					@if(false)
						<button style="width: 200px;background-color: #2780e3;border-color: #2780e3;"  class="btn btn-info btn-lg ml-2 mt-7 askMoreInfoButton">Ask More Info</button>
					@endif

			</div>
	</div>
</div>

<div  class="modal-reply3"   id="askMoreInfo">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ask More Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
		<form id="comment-form2" method="post" action="{{URL('/').'/replyComment'}}"  enctype="multipart/form-data">
			@csrf
			<div class="modal-body">
				<input type="hidden" name="id" value="{{$inventory->id}}" />
				<input type="hidden" name="askMoreInfo" value="true" />
				<div class="form-group">
					<label for="exampleTextarea" class="required">Your Message</label>
					<textarea required name="message" id="summernote" class="form-control form-control-solid form-control-lg" rows="3"></textarea>
					<span class="form-text text-muted"></span>
				</div>
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

<style>
.modal-reply,.modal-reply2,.modal-reply3 {
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
				<input type="hidden" name="id" value="{{$inventory->id}}" />
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

<link href="{{URL('/')}}/vendor/summernote/summernote.min.css" rel="stylesheet">
<script src="{{URL('/')}}/vendor/summernote/summernote.min.js"></script>
<!--
<script src="{{URL('/')}}/vendor/ckeditor/ckeditor.js"></script>
-->

<script type="text/javascript">

var editor2;
var editor3;
function sendFile(file, editor, welEditable, el=null) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
      data: data,
      type: "POST",
      url: "{{URL('/')}}/summer_upload",
      cache: false,
      contentType: false,
      processData: false,
      success: function(url) {
        $(el ?? '#summernote').summernote('insertImage', url, "aaa.jpg");
      }
    });
}

function iSummernote() {
	editor2 = $('#summernote,#summernote2').summernote({
		toolbar: [
		  ['style', ['style']],
		  ['font', ['bold', 'underline', 'clear']],
		  ['fontname', ['fontname']],
		  ['color', ['color']],
		  ['para', ['ul', 'ol', 'paragraph']],
		  ['table', ['table']],
		  ['insert', ['link', 'picture', 'video']],
		  ['view', []],
		],
		callbacks: {
			onImageUpload: function(files, editor, welEditable) {
				sendFile(files[0], editor, welEditable);
			}
		}
	});

	$('.reason-textarea').summernote({
		toolbar: [
		  ['style', ['style']],
		  ['font', ['bold', 'underline', 'clear']],
		  ['fontname', ['fontname']],
		  ['color', ['color']],
		  ['para', ['ul', 'ol', 'paragraph']],
		  ['table', ['table']],
		  ['insert', ['link', 'picture', 'video']],
		  ['view', []],
		],
		callbacks: {
			onImageUpload: function(files, editor, welEditable) {
				sendFile(files[0], editor, welEditable, '.reason-textarea');
			}
		}
	});
}
$(document).ready(function() {
	iSummernote();
	console.log("oke");
});

</script>
<script>
$(document).ready(function() {
$('.modal-reply,.modal-reply2,.modal-reply3').hide();
$('.note-modal').hide();
$(".close,.close-btn,.ki-close").click(function(e) {
	$('.modal-reply,.modal-reply2,.modal-reply3').hide();
	$(".modal-backdrop").remove();
});

$(".reply-comment").click(function(e) {
	$('body').append('<div class="modal-backdrop fade show"></div>');
	$('.modal-reply').show();
	$('.note-modal').hide();
	$('#summernote').summernote('code', '<p><br></p>');
});
$(".reasonModalButton").click(function(e) {
	$('body').append('<div class="modal-backdrop fade show"></div>');
	$('.modal-reply2').show();
	$('.note-modal').hide();
	//$('#summernote').summernote('code', '<p><br></p>');
	$('.reason-textarea').summernote('code', '<p><br></p>');
	$('.status-ticket').html($(this).data('status-ticket') + ' Reason');
	$('.status-value').val($(this).data('status-ticket').toLowerCase());
});
$(".askMoreInfoButton").click(function(e) {
	$('body').append('<div class="modal-backdrop fade show"></div>');
	$('.modal-reply3').show();
	$('.note-modal').hide();
	$('#summernote').summernote('code', '<p><br></p>');
});
});
</script>
<script>
// A $( document ).ready() block.

$('#comment-form,#comment-form2').on('submit',(function(e) {
	$(".modal-backdrop").remove();
	KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});
	e.preventDefault();
	var formData = new FormData(this);
	$('#exampleModal').modal('hide')
	$('#askMoreInfo').modal('hide')
	$('.modal-reply2,.modal-reply3,.modal-reply').hide();
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
			iSummernote();
		},
		error: function(data){
			KTApp.unblockPage();
			console.log("error");
			console.log(data);
			iSummernote();
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
				data: "id={{$inventory->id}}&action=solved&message=",
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
    Swal.fire({title: "Confirmation",text: "Are you sure want do escalation for this Inventory Transaction ?",icon: "question",showCancelButton: true,confirmButtonText: "Yes!"
    }).then(function(result) {
        if (result.value) {
			 KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});
			$.ajax({
				type: "POST",
				url: '{{URL("/")}}/ticketAction',
				data: "id={{$inventory->id}}&action=escalate&message=",
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




<div  class="modal-reply2" id="reasonModal" style="overflow: auto;">
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
							<span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Reason <span style="color: red;">*</span></span>
						</div>
						<!--end::Text-->
					</div>
					<!--end::Section-->
					<div class="col-lg-12 col-md-12 col-sm-12">
						<textarea name="reason" class="form-control reason-textarea" rows="10" cols="20"></textarea>
					</div>
					<input class="form-control status-value" type="text" name="status" hidden/>
					<input class="form-control" type="text" name="approval_custom" value="0" hidden/>
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

<script>
    $(".approved").click(function(e) {
		$('.modal-reply2').hide();
		$('.modal-backdrop').hide();
        if ($('.reason-textarea').summernote('isEmpty')){
            Swal.fire('Required Fields', 'Please fill all required fields!', 'error');
            return false;
        }

        Swal.fire({title: "Confirmation",text: "Are you sure want do "+$('.status-value').val()+" for this Inventory Transaction ?",icon: "question",showCancelButton: true,confirmButtonText: "Yes!"
        }).then(function(result) {
            if (result.value) {
                if ($('.status-value').val() == 'approve') {
					KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});
					$.ajax({
						type: "POST",
						url: '{{URL("/")}}/approve-request/ticketActionInventory',
						data: "_token={{csrf_token()}}&id={{$inventory->id}}&type={{ $type }}&action_detail=approved&message="+$('.reason-textarea').val(),
						success: function(data){
							console.log(data);
							$('.reasonModalButton').remove();
							$('.title-desc').html('You have done your action (approve/reject) for this Inventory Transaction. ');
							KTApp.unblockPage();

							var obj = JSON.parse(data);

							if(obj.success) {
								if(obj.warning) {
									<?php
										//ada warning kemungkinan saat agent on leave
										//dari flow grp yang baru
									?>
									Swal.fire("Confirmation",obj.message,"warning")

								} else {
									Swal.fire("Confirmation",obj.message,"success")
									//setTimeout(function() {
										//location.reload();
									//},3000);
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
					const type = '{{ $type }}'
					const url = type == 'issue' ? '{{URL("/")}}/approve-request/{{$inventory->id}}/reject' : '{{URL("/")}}/approve-request/{{$inventory->id}}/reject-goods-receive'
					KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});
					$.ajax({
						type: "POST",
						url: url,
						data: "_token={{csrf_token()}}&id={{$inventory->id}}&action_detail=reject&message="+$('.reason-textarea').val(),
						success: function(data){
							KTApp.unblockPage();

							var obj = data

							if(obj.success) {
								$('.title-desc').html('You have done your action (approve/reject) for this Inventory Transaction. ');
								$('.reasonModalButton').remove();
								Swal.fire("Confirmation",obj.message,"success")

								//setTimeout(function() {
									//location.reload();
								//},3000);
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

</script>

@endsection
