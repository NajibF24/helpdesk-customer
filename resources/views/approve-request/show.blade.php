@extends('layouts.app')

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
</style>


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
<div class="container-fluid">
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
				<input type="hidden" name="id" value="{{$ticket->id}}" />
				<input type="hidden" name="askMoreInfo" value="true" />
				<div class="form-group">
					<label for="exampleTextarea">Your Message</label>
					<textarea name="message" id="summernote-1" class="form-control form-control-solid form-control-lg" rows="3"></textarea>
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

@if ($ticket->coverage_windows)
<?php
$coverage = DB::table('coverage_windows')->where('id', $ticket->coverage_windows)->first();
$hours = explode(", ", $coverage->coverage_hours);
?>
<!-- Modal Coverage -->
<div  class="modal modal-coverage" >
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Coverage Windows</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
		</div>
		<div class="modal-body">
			<table class="table-center">
				<tr style="text-align:center">
					<?php
					$days = ['', 'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
					$days_number = [0,1,2,3,4,5,6,7];
					foreach($days as $d) {
						?><th><?php echo $d; ?></th><?php
					}
					?>
				</tr>
				<?php
				for($i=0;$i<24;$i++) {
				?>
				<tr class="row-hour">
					<td>
						@if($i>9)
							{{$i}}:00
						@else
							0{{$i}}:00
						@endif
					</td>
					<?php
						foreach ($days_number as $d):
						?>
						@if($i>9)
						<td class="cell-ophour cell-day-{{$d}}:{{$i}}:00" data-day="{{$d}}" data-hour="{{$i}}" data-minute="00">
						@else
						<td class="cell-ophour cell-day-{{$d}}:0{{$i}}:00" data-day="{{$d}}" data-hour="{{$i}}" data-minute="00">
						@endif
						<?php
							//echo $list_hours[$i].'-'.$hour_end.' '.$priority;
						?></td><?php
						endforeach;
					?>
				</tr>
				<tr class="row-hour">
					<td>
						@if($i>9)
							{{$i}}:30
						@else
							0{{$i}}:30
						@endif
					</td>
					<?php
						foreach ($days_number as $d):

						?>
						@if($i>9)
						<td class="cell-ophour cell-day-{{$d}}:{{$i}}:30" data-day="{{$d}}" data-hour="{{$i}}" data-minute="30">
						@else
						<td class="cell-ophour cell-day-{{$d}}:0{{$i}}:30" data-day="{{$d}}" data-hour="{{$i}}" data-minute="30">
						@endif
						<?php
							//echo $list_hours[$i].'-'.$hour_end.' '.$priority;
						?></td><?php
						endforeach;
					?>
				</tr>
				<?php } ?>
			</table>
		</div>
		<div class="modal-footer" hidden>
			<button type="button" class="close-btn btn btn-secondary" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-primary" disabled>Send Message</button>
		</div>
    </div>
  </div>
</div>
@endif

<style>
.table-center {
  margin-left: auto;
  margin-right: auto;
}
table, th, td {
	border: 1px solid black;
}
th {
	padding-left: 30px;
	padding-right: 30px;
}
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
.modal-body {
	height:auto;
	max-height:500px;
	overflow-y:auto;
}
.ps__rail-y,.ps__rail-x {
	display:none !important;
}

.modal-reply ::-webkit-scrollbar {
  width: 19px;
}

.modal-reply ::-webkit-scrollbar-track {
  background-color: transparent;
}

.modal-reply ::-webkit-scrollbar-thumb {
  background-color: #f7f7f7;
  border-radius: 20px;
  border: 6px solid transparent;
  background-clip: content-box;
}

.modal-reply ::-webkit-scrollbar-thumb:hover {
  background-color: #eaeaea;
}

</style>
<!-- Modal -->
<div  class="modal-reply" >
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Reply Comment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="close-btn">&times;</span>
        </button>
      </div>
		<form id="comment-form" method="post" action="{{URL('/').'/replyComment'}}"  enctype="multipart/form-data">
			@csrf
			<div class="modal-body pt-4 pb-0">
				<div class="row">
					<div class="col-md-6 mb-0">

						<input type="hidden" name="id" value="{{$ticket->id}}" />
						<div class="form-group mb-4">
							<label for="exampleTextarea">Your Message</label>
							<textarea name="message" id="summernote" class="form-control form-control-solid form-control-lg" rows="7"></textarea>
							<span class="form-text text-muted"></span>
						</div>

					</div>
					<div class="col-md-6 mb-0">
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


						<style>
						.select2-selection__rendered {
							height: auto;
							display: block !important;
						}
						.select2-selection--multiple {
							height: auto !important;
						}
						.select2-dropdown,
						.select2-results,
						.select2-dropdown--below {
							z-index:2000000 !important;
						}
						.select2-container--default.select2-container--focus .select2-selection--multiple {
							border: solid #cccccc 1px;
							outline: 0;
						}
						.select2-container--default .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
							font-size: 0.9rem;
						}
						</style>
						<?php
						$list_contact = getContactCaseJourney($ticket);
						//var_dump($list_contact); ?>
						<div class="form-group">
							<label for="exampleTextarea">Notification Tags</label>
							 <select class="form-control " id="kt_select2_112" multiple name="notif[]" style="width:100%;height:auto;z-index:20000">
								@foreach($list_contact as $contact)
								  <option selected="selected" value="{{$contact->id}}">{{$contact->name.",".$contact->email}}</option>
								@endforeach
							 </select>

								<script>
								var data_target = 'employee2';
								// Initialization
								jQuery(document).ready(function() {
								  $('#kt_select2_112').select2({
								   placeholder: "Add employee get notified",
								   tags: false,
										//multiple: true,
										tokenSeparators: [',', ' '],
										//minimumInputLength: 2,
										//minimumResultsForSearch: 10,
										//ajax: {
											//url: '{{URL("/")}}/select2list/'+data_target,
											//dataType: "json",
											//type: "GET",
											//data: function (params) {

												//var queryParameters = {
													//term: params.term
												//}
												//return queryParameters;
											//},
											//processResults: function (data) {

												//console.log(data);
												//return {
													//results: $.map(data, function (item) {
														//return {
															//text: item.text,
															//id: item.id
														//}
													//})
												//};
											//}
										//}

								  });





								});
								</script>

							<span class="form-text text-muted">All people inputed here will get notified for this ticket.</span>
						</div>

					</div>
				</div>

			</div>


			<div class="modal-footer">
			<button type="button" class="close-btn btn btn-secondary" data-dismiss="modal">Close</button>
			<button type="submit" class="btn btn-primary submit-message" disabled>Send Message</button>
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
function sendFile(file, editor, welEditable) {
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
		  console.log(url);
        //editor.insertImage(welEditable, url);
        //editor.insertImage(welEditable, url);
        $('#summernote').summernote('insertImage', url, "aaa.jpg");

      }
    });
}

function iSummernote() {
	editor2 = $('#summernote,#summernote-1').summernote({
		toolbar: [
		  ['style', ['style']],
		  ['font', ['bold', 'underline', 'clear']],
		  ['fontname', ['fontname']],
		  ['color', ['color']],
		  ['para', ['ul', 'ol', 'paragraph']],
		  ['table', ['table']],
		  ['insert', ['link', 'video']],
		  ['view', []],
		],
		callbacks: {
			onImageUpload: function(files, editor, welEditable) {
				sendFile(files[0], editor, welEditable);
			},
			onKeydown: function (e) {
				var t = e.currentTarget.innerText;
				if (t.trim().length > 0) {
					$('.submit-message').removeAttr('disabled');
				} else if (t.trim().length  == 0) {
					$('.submit-message').attr('disabled','disabled');
				}
			},
			onKeyup: function (e) {
				var t = e.currentTarget.innerText;

				if (t.trim().length > 0) {
					$('.submit-message').removeAttr('disabled');
				} else if (t.trim().length  == 0) {
					$('.submit-message').attr('disabled','disabled');
				}
			},
			onPaste: function (e) {}
		}
	});
}
$(document).ready(function() {
	iSummernote();
	
});

</script>
<script>
function twodigit(val) {
	if(val<10) {
		return "0"+val;
	} else {
		return val;
	}
}

$(document).ready(function() {

$('.modal-reply,.modal-reply2,.modal-reply3').hide();
$('.note-modal').hide();
$('.modal-coverage').hide();

@if (isset($hours))
setTimeout(function() {
	var x;
	var y;
	var day = "";
	var hour = "";
	var minute = "";
	@foreach ($hours as $hour)
		@if ($hour != '')
			@if (strpos($hour, " - ") > 0)
			<?php
				$c = explode(" - ", $hour);
				$d = explode(":", $c[0]);
				$e = explode(":", $c[1]);

				// explode start hour
				$day = $d[0];
				$hour = $d[1];

				$minute = $d[2];
				$total_start = 60*$hour + $minute;

				// explode end hour
				$end_hour = $e[0];
				$end_minute = $e[1];
				$total_end = 60*$end_hour + $end_minute;
				$range_minute = $total_end-$total_start;
				$range_hour = $range_minute / 60;
			?>
			day = {{$day}};
			hour = {{$hour}};
			minute = {{$minute}};

			end_hour = {{$end_hour}};
			end_minute = {{$end_minute}};

			for (let i = hour; i <= end_hour; i++) {
				// document.getElementsByClassName(".cell-day-"+i+":"+twodigit(hour)+":"+twodigit(minute)).style.backgroundColor = 'gray';
				x = document.getElementsByClassName("cell-day-"+day+":"+twodigit(i)+":"+twodigit(minute));
				x[0].style.backgroundColor = "blue";

				if (minute == '30') {
					if (i > hour) {
						y = document.getElementsByClassName("cell-day-"+day+":"+twodigit(i)+":00");
						y[0].style.backgroundColor = "#ebeff3";

						if (end_minute == '30') {
							z = document.getElementsByClassName("cell-day-"+day+":"+twodigit(i)+":"+twodigit(end_minute));
						} else {
							z = document.getElementsByClassName("cell-day-"+day+":"+twodigit(i)+":30");
						}

						if (end_hour === i) {
							z[0].style.backgroundColor = "white";
						} else {
							z[0].style.backgroundColor = "#ebeff3";
						}
					} else {
						y = document.getElementsByClassName("cell-day-"+day+":"+twodigit(i)+":30");
						y[0].style.backgroundColor = "#ebeff3";
					}
				} else {
					if (end_minute == '30') {
						y = document.getElementsByClassName("cell-day-"+day+":"+twodigit(i)+":"+twodigit(end_minute));
					} else {
						y = document.getElementsByClassName("cell-day-"+day+":"+twodigit(i)+":30");
					}

					if (end_hour === i) {
						y[0].style.backgroundColor = "white";
					} else {
						y[0].style.backgroundColor = "#ebeff3";
					}
				}
			}
			@else
				<?php
				$c = explode(":",$hour);

				if(count($c) >= 3) {
					$day = $c[0];
					$hour = $c[1];
					$minute = $c[2];
					?>
					day = {{$day}};
					hour = {{$hour}};
					minute = {{$minute}};

					x = document.getElementsByClassName("cell-day-"+day+":"+twodigit(hour)+":"+twodigit(minute));
					y = document.getElementsByClassName("cell-day-"+day+":"+twodigit(hour)+":"+twodigit(end_minute));
					x[0].style.backgroundColor = "#ebeff3";
					y[0].style.backgroundColor = "#ebeff3";
					<?php
				}
				?>
			@endif
		@endif
	@endforeach
}, 4000);
@endif

$('.coverage-detail').on('click', function() {
	$('.modal-coverage').modal('show');
})

$(".close,.close-btn,.ki-close").click(function(e) {
	$('.modal-reply,.modal-reply2,.modal-reply3').hide();
	$(".modal-backdrop").remove();
});

$(".reply-comment").click(function(e) {
	$('body').append('<div class="modal-backdrop fade show"></div>');
	$('.modal-reply').show();
	$('.note-modal').hide();
	$('#summernote').summernote('code', '<p><br></p>');
	$('.note-toolbar .note-insert').children().eq(1).remove()
});
$(".reasonModalButton").click(function(e) {
	$('body').append('<div class="modal-backdrop fade show"></div>');
	$('.modal-reply2').show();
	$('.note-modal').hide();
	//$('#summernote').summernote('code', '<p><br></p>');
	$('.status-ticket').html($(this).data('status-ticket') + ' Reason');
	$('.status-value').val($(this).data('status-ticket').toLowerCase());
	$('.reason-textarea').val("");

	if ($(this).data('status-ticket') == 'Reject' || $(this).data('status-ticket') == 'Delete') {
		$('.reason-textarea').attr('required', '');
		$('.box-manual-assignment').hide();
	} else {
		<?php //approve?>
		$('.box-manual-assignment').show();
		$('.reason-textarea').removeAttr('required');
	}

	$('.note-toolbar .note-insert').children().eq(1).remove()
});
$(".askMoreInfoButton").click(function(e) {
	$('body').append('<div class="modal-backdrop fade show"></div>');
	$('.modal-reply3').show();
	$('.note-modal').hide();
	$('#summernote-1').summernote();
	$('.note-toolbar .note-insert').children().eq(1).remove()
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
							<span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">
								Reason
								<span class="text-danger required-reason">*</span>
							</span>
						</div>
						<!--end::Text-->
					</div>
					<!--end::Section-->
					<div class="col-lg-12 col-md-12 col-sm-12">
						<textarea name="reason" class="form-control reason-textarea" rows="5" cols="20"></textarea>
					</div>
					<?php
					$contact_case_journey = getContactCaseJourney($ticket,"include self","not_include_request_management_notif","","need_list_contact_not_unique");
					//var_dump($contact_case_journey);


					$last_approver = end($contact_case_journey);

					$is_really_last_approver = checkReallyLastApprover($contact_case_journey, $ticket->id, $last_approver);

					//var_dump($last_approver);
					//var_dump(Auth::user()->id);
					if(isset($last_approver->id) && $last_approver->id == Auth::user()->person && $is_really_last_approver) {
					?>
					<div class="box-manual-assignment col-md-12 form-group mt-6">
						<?php
						$req_management = DB::table('request_management')->where('id',$ticket->request_management)->first();
						$assignment_type_list = explode(",",$req_management->assignment_type);
						$assignment_tier_list = explode(",",$req_management->assignment_tier);
						//
						$index_array_next_tier = 0;//current tier mulai dari 1, index mulai dari 0
						$message_escalation = "";
						if(!empty($assignment_type_list[$index_array_next_tier])
							&& !empty($assignment_tier_list[$index_array_next_tier]))
						{
							if($assignment_type_list[$index_array_next_tier] == 4) {
								////manual

								//$option = DB::table('contact')
												//->where('contact.status', '=', 'Active')
												//->whereNull('contact.deleted_at')
												//->selectRaw('contact.id,CONCAT(contact.name, " - ", IFNULL(job_title.job_name,"Not Set")) AS name')->where('type','Employee')->leftJoin('job_title', 'job_title.id', '=', 'contact.job_title')->pluck('name','contact.id')->toArray();

								//SHOW ONE EMPLOYEE AGENT
								$option = DB::table('contact')
                                    ->where('contact.id', $assignment_tier_list[$index_array_next_tier])
									->where(function($q) {
										$q->where('leave.id', null)
										->orWhere('leave.start_date', '>', \Carbon\Carbon::now())
										->orWhere('leave.end_date', '<', \Carbon\Carbon::now());
									})
                                    ->join('company', 'company.id', '=', 'contact.company')
                                    ->selectRaw('contact.id,
                                        CONCAT(contact.name,
                                            \' - \' || COALESCE(job_title.job_name, \'Not Set\') ||
                                            \' - \' || COALESCE(company.name, \'Not Set\')  ||
											\' - \' || COALESCE(coverage_windows.name, \'Not Set\')
                                        ) AS name')
                                    ->leftJoin('job_title', 'job_title.id', '=', 'contact.job_title')
									->leftJoin('coverage_windows', 'coverage_windows.id', '=', 'contact.coverage_windows')
									->leftJoin('leave', 'leave.employee', '=', 'contact.id')
                                    ->pluck('name', 'contact.id')
                                    ->toArray();


								$name = DB::table('contact')->where('id',$assignment_tier_list[$index_array_next_tier])->value('name');

								$next_tier_string = "If select box is not set, this ticket will be assign via Manual Assignment to $name";
							} else {
								////selain manual
								////hanya tim di next tier yang tampil
								//$option = DB::table('contact')
												//->join('lnkemployeetoteam', 'lnkemployeetoteam.employee_id', '=', 'contact.id')
												//->where('lnkemployeetoteam.team_id',$assignment_tier_list[$index_array_next_tier])
												//->where('contact.status', '=', 'Active')
												//->whereNull('contact.deleted_at')
												//->selectRaw('contact.id,CONCAT(contact.name, " - ", IFNULL(job_title.job_name,"Not Set")) AS name')->where('type','Employee')->leftJoin('job_title', 'job_title.id', '=', 'contact.job_title')->pluck('name','contact.id')->toArray();

								//$message_escalation = "Employee that is shown here is employee team in tier ".($index_array_next_tier+1);

								//selain manual
								//hanya tim di next tier yang tampil
								$option = DB::table('contact')
                                ->join('lnkemployeetoteam', 'lnkemployeetoteam.employee_id', '=', 'contact.id')
                                ->where('lnkemployeetoteam.team_id', $assignment_tier_list[$index_array_next_tier])
                                ->where('contact.status', '=', 'Active')
                                ->whereNull('contact.deleted_at')
                                ->where('type', 'Employee')
								->where(function($q) {
									$q->where('leave.id', null)
									->orWhere('leave.start_date', '>', \Carbon\Carbon::now())
									->orWhere('leave.end_date', '<', \Carbon\Carbon::now());
								})
                                ->leftJoin('job_title', 'job_title.id', '=', 'contact.job_title')
								->leftJoin('coverage_windows', 'coverage_windows.id', '=', 'contact.coverage_windows')
								->leftJoin('leave', 'leave.employee', '=', 'contact.id')
                                ->selectRaw('contact.id,
                                    contact.name ||
									\' - \' || COALESCE(job_title.job_name, \'Not Set\')  ||
									\' - \' || COALESCE(coverage_windows.name, \'Not Set\')
									AS name')
                                ->pluck('name', 'contact.id')
                                ->toArray();

								$message_escalation = "Employee that is shown here is employee team in tier ".($index_array_next_tier+1);

								$name = DB::table('contact')->where('id',$assignment_tier_list[$index_array_next_tier])->value('name');

								$type_assigment = DB::table('assignment_type')->where('id',$assignment_type_list[$index_array_next_tier])->value('name');

								$next_tier_string = "If select box is not set, this ticket will be assign via $type_assigment to Team ".str_replace("Team","",$name)."";
							}
						}
						else {
							////next tier not available
							////SHOW ALL EMPLOYEE
							//$option = DB::table('contact')
											//->where('contact.status', '=', 'Active')
											//->whereNull('contact.deleted_at')
											//->selectRaw('contact.id,CONCAT(contact.name, " - ", IFNULL(job_title.job_name,"Not Set")) AS name')->where('type','Employee')->leftJoin('job_title', 'job_title.id', '=', 'contact.job_title')->pluck('name','contact.id')->toArray();

                                            $option = DB::table('contact')
                                            ->where('contact.status', '=', 'Active')
                                            ->whereNull('contact.deleted_at')
                                            ->join('company', 'company.id', '=', 'contact.company')
                                            ->whereIn('contact.company', get_company_role())
                                            ->selectRaw('contact.id,
                                                contact.name ||
												\' - \' || COALESCE(job_title.job_name, \'Not Set\') ||
												\' - \' || COALESCE(company.name, \'Not Set\')  ||
												\' - \' || COALESCE(coverage_windows.name, \'Not Set\')
												AS name')
                                            ->where('type', 'Employee')
                                            ->where('is_agent', 1)
											->where(function($q) {
												$q->where('leave.id', null)
												->orWhere('leave.start_date', '>', \Carbon\Carbon::now())
												->orWhere('leave.end_date', '<', \Carbon\Carbon::now());
											})
                                            ->leftJoin('job_title', 'job_title.id', '=', 'contact.job_title')
											->leftJoin('coverage_windows', 'coverage_windows.id', '=', 'contact.coverage_windows')
											->leftJoin('leave', 'leave.employee', '=', 'contact.id')
                                            ->pluck('name', 'contact.id')
                                            ->toArray();


							$next_tier_string = "You have to select Employee in select box because there is no Employee that is set in Request Management.";
						}

						$option = [''=>'-Select Agent'] + $option ;

						?>
						<b>{!! Form::label('agent_id', 'Manual Assignment (Optional):') !!}</b>
						{!! Form::select('agent_id', $option, null, ['class' => 'agent_id select2 form-control', 'style'=>'width:100%']) !!}
						<p class="mt-2">Choose agent to assign mannually if not follow automatic assignment by system. {{$message_escalation}}</p>

						@if($ticket->finalclass != 'problem_request')
						<p style='color: #525050;'><b><?=$next_tier_string?></b></p>
						@endif
					</div>
					<?php } ?>
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
	$('.reason-textarea').summernote('code', '<p><br></p>');
    $(".approved").click(function(e) {
		console.log($('.status-value').val());
		if ($('.status-value').val() != 'approve' && $('.reason-textarea').val() == "") {
			alert('Please write your reason');
		} else {
			$('.modal-reply2').hide();
			$('.modal-backdrop').hide();

			Swal.fire({title: "Confirmation",text: "Are you sure want do "+$('.status-value').val()+" for this ticket ?",icon: "question",showCancelButton: true,confirmButtonText: "Yes!"
			}).then(function(result) {
				if (result.value) {
					if ($('.status-value').val() == 'approve') {
						KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});
						let params = {
							"_token": '{{csrf_token()}}',
							id: '{{ $ticket->id }}',
							action_detail: 'approved',
							message: $('.reason-textarea').val()
						}

						if($('.agent_id').length && ($('.agent_id').val() > 0)) {
							params.agent_id = $('.agent_id').val()
						} 

						//var params = "_token={{csrf_token()}}&id={{$ticket->id}}&action_detail=approved&message="+$('.reason-textarea').val();
						$.ajax({
							type: "POST",
							url: '{{URL("/")}}/approve-request/ticketAction',
							data: params,
							success: function(data){
								console.log(data);
								KTApp.unblockPage();

								var obj = JSON.parse(data);

								if(obj.success) {

									if(obj.warning) {
										<?php 
											//ada warning kemungkinan saat agent on leave 
											//dari flow grp yang baru
										?>
										Swal.fire("Confirmation",obj.message,"warning")
										setTimeout(function() {
											location.reload();
										},10000);

									} else {

										Swal.fire("Confirmation",obj.message,"success")
										if(data.includes("Pending On-Leave")) {
											setTimeout(function() {
												location.reload();
											},10000);
										} else {
											setTimeout(function() {
												location.reload();
											},3000);
										}
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
					} else if ($('.status-value').val() == 'reject') {
						KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});

						$.ajax({
							type: "POST",
							url: '{{URL("/")}}/approve-request/ticket_reject',
							data: {
								"_token": "{{csrf_token()}}",
								id: "{{$ticket->id}}",
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
					} else {
						KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});

						$.ajax({
							type: "POST",
							url: '{{URL("/")}}/approve-request/ticket_delete',
							data: {
								"_token": "{{csrf_token()}}",
								id: "{{$ticket->id}}",
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
		}
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
