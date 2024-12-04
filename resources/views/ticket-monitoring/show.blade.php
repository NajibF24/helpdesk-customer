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
.rating {
    display: inline-flex;
    margin-top: -10px;
    flex-direction: row-reverse
}

.rating>input {
    display: none
}

.rating>label {
    position: relative;
    width: 75px;
    font-size: 35px;
    color: #2566cc;
    cursor: pointer
}

.rating>label::before {
    content: "\2605";
    position: absolute;
    opacity: 0
}

.rating>label:hover:before,
.rating>label:hover~label:before {
    opacity: 1 !important
}

.rating>input:checked~label:before {
    opacity: 1
}

.rating:hover>input:checked~label:before {
    opacity: 0.4
}

.swal-height {
	width: 80vh;
	overflow-y: hidden !important; height: auto!important;
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
.modal-reply,.modal-reopen {
	z-index:1000000 !important;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1050;
    display: none;
    width: 100%;
    height: 100%;
    overflow-y: hidden;
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
							<span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">
								Reason
								<span class="text-danger required-reason">*</span>
							</span>
						</div>
						<!--end::Text-->
					</div>
					<!--end::Section-->
					<div class="col-lg-12 col-md-12 col-sm-12">
						<textarea name="reason" id="summernote-2" class="form-control form-control-solid form-control-lg reason-textarea" rows="7" maxlength="50"></textarea>
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

<div class="modal modal-reply3" id="askMoreInfo">
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

<div class="modal modal-rating">
	<div class="modal-dialog modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"></h5>
				<button type="button" class="close close-modal close-rating" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
			</div>
			<div class="modal-body pt-4">
				<div class="card-body text-center">
					<img src=" https://i.imgur.com/d2dKtI7.png" height="100" width="100">
					<div class="comment-box text-center">
						<h4>Are you sure want to mark this ticket as Closed ?</h4>
						<span>Please, give us rate to our service.</span>
						<input type="hidden" name="id" value="{{$ticket->id}}"/><br><br>
						<div class="rating rating-value">
							<input type="radio" name="rating" value="5" id="5" data-id=5 class="p-4"><label for="5">
								<span style="top: 10%">☆</span>
								<br>
								<small class="small" style="font-size: 14px;">Excellent</small>
							</label>
							<input type="radio" name="rating" value="4" id="4" data-id=4><label for="4">☆
								<br>
								<small class="small" style="font-size: 14px;">Very Good</small>
							</label>
							<input type="radio" name="rating" value="3" id="3" data-id=3><label for="3">☆
								<br>
								<small class="small" style="font-size: 14px;">Good</small>
							</label>
							<input type="radio" name="rating" value="2" id="2" data-id=2><label for="2">☆
								<br>
								<small class="small" style="font-size: 14px;">Not Good</small>
							</label>
							<input type="radio" name="rating" value="1" id="1" data-id=1><label for="1">☆
								<br>
								<small class="small" style="font-size: 14px;">Bad</small>
							</label>
						</div>
						<div class="comment-area">
							<textarea class="form-control comment-value" placeholder="What is your ticket view?" rows="4" name="comment" required></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary font-weight-bold rating-submit">Yes</button>
                <button type="button" class="btn btn-light-primary font-weight-bold close-rating" data-dismiss="modal">No</button>
            </div>
		</div>
	</div>
</div>

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
								<label for="exampleTextarea">Your Message <span class="text-danger">*</span></label>
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

<div  class="modal-reopen" id="reopenModal" data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title status-ticket" id="exampleModalLabel">Reopen Ticket Reason</h5>
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
							<span class="ml-4 mb-3 text-dark-75 text-hover-primary font-size-lg mb-1">Please describe the reason, why this ticket need to be Reopen </span>
						</div>
						<!--end::Text-->
					</div>
					<!--end::Section-->
					<div class="col-lg-12 col-md-12 col-sm-12">
						<textarea id="reason-reopen" class="form-control reason-textarea" rows="10" cols="20"></textarea>
					</div>
					<div class="col-xs-12 text-right">
						<p class="note-input">Input Minimal <span id="maxContentPost">10</span> Characters</p>
					</div>
					<input class="form-control status-value" type="text" name="status" hidden/>
					<input class="form-control" type="text" name="approval_custom" value="0" hidden/>
				</div>
				<!--end::Item-->
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary mr-2 reopen-ticket submit-solve" disabled>Submit</button>
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

var editor2;
function sendFile(file, editor, welEditable, el) {
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
		$(el ?? '#summernote').summernote('insertImage', url, "file.jpg");
      }
    });
}

function iSummernote() {
	editor2 = $('#summernote').summernote({
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
function iSummernoteReopen() {
	editor2 = $('#reason-reopen').summernote({
		placeholder: 'Leave a comment ...',
		callbacks: {
			onImageUpload: function(files, editor, welEditable) {
				sendFile(files[0], editor, welEditable, '#reason-reopen');
			},
			onKeydown: function (e) {
				var t = e.currentTarget.innerText;
				if (t.trim().length >= 10) {
					$('.submit-solve').removeAttr('disabled');
				} else {
					$('.submit-solve').attr('disabled','disabled');
				}
			},
			onKeyup: function (e) {
				var t = e.currentTarget.innerText;

				if (t.trim().length >= 10) {
					$('#maxContentPost').text("");
					// $('.note-input').hide();
				} else {
					$('#maxContentPost').text(10 - t.trim().length);
					$('.note-input').show();
				}
			},
			onPaste: function (e) {
				// var t = e.currentTarget.innerText;
				// var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
				// e.preventDefault();
				// var maxPaste = bufferText.length;
				// if(t.length + bufferText.length >= 10){
				// 	maxPaste = 10 - t.length;
				// }
				// if(maxPaste > 0){
				// 	document.execCommand('insertText', false, bufferText.substring(0, maxPaste));
				// }
				// $('#maxContentPost').text(10 - t.length);
			}
		},

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
		//callbacks: {

		//}
	});
}
$(document).ready(function() {
	iSummernote();
	
	iSummernoteReopen();
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

$('.modal-rating').hide();
$('.modal-reply').hide();
$('.modal-reopen').hide();
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
				x[0].style.backgroundColor = "#375471";

				if (minute == '30') {
					if (i > hour) {
						y = document.getElementsByClassName("cell-day-"+day+":"+twodigit(i)+":00");
						y[0].style.backgroundColor = "#375471";

						if (end_minute == '30') {
							z = document.getElementsByClassName("cell-day-"+day+":"+twodigit(i)+":"+twodigit(end_minute));
						} else {
							z = document.getElementsByClassName("cell-day-"+day+":"+twodigit(i)+":30");
						}

						if (end_hour === i) {
							z[0].style.backgroundColor = "white";
						} else {
							z[0].style.backgroundColor = "#375471";
						}
					} else {
						y = document.getElementsByClassName("cell-day-"+day+":"+twodigit(i)+":30");
						y[0].style.backgroundColor = "#375471";
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
						y[0].style.backgroundColor = "#375471";
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
					x[0].style.backgroundColor = "#375471";
					y[0].style.backgroundColor = "#375471";
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

$(".close-btn").click(function(e) {
	$('.modal-reply').hide();
	$('#askMoreInfo').hide();
	$(".modal-backdrop").remove();
});
$(".reply-comment").click(function(e) {
	$('body').append('<div class="modal-backdrop fade show"></div>');
	$('.modal-reply').show();
	$('.note-modal').hide();
	$('#summernote').summernote('code', '<p><br></p>');
});
$('.rating-closed').click(function(e) {
	$('body').append('<div class="modal-backdrop fade show"></div>');
	$('.modal-rating').modal('show');
})
$(".close-rating").click(function(e) {
	$('.modal-rating').hide();
	$(".modal-backdrop").remove();
});
$('.reopen').click(function(e) {
	$('body').append('<div class="modal-backdrop fade show"></div>');
	$('.modal-reopen').modal('show');
})
$(".askMoreInfoButton").click(function(e) {
	$('body').append('<div class="modal-backdrop fade show"></div>');
	$('.modal-reply3').show();
	$('.note-modal').hide();
	$('#summernote-1').summernote();
});
</script>
<script>
// A $( document ).ready() block.
$(document).ready(function() {
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

$('.closed-rating').click(function(e) {
	alert('Please Write Your Ticket Review');
});


$(".reopen-ticket").click(function(e) {
	$('.modal-reopen').hide();
	$(".modal-backdrop").remove();

	Swal.fire({
		 text: "Are you sure want to Reopen this Ticket ?",
		showCancelButton: true,
		confirmButtonText: "Yes!",
	}).then(function(result) {
		if (result.value) {
			KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});
			$.ajax({
				type: "POST",
				url: '{{URL("/")}}/ticketAction',
				data: {
					"_token": '{{csrf_token()}}',
					id: '{{ $ticket->id }}',
					message: $('#reason-reopen').val(),
					action: "reopen"
				},
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


var form = document.createElement('div');
form.innerHTML = `
<div class="card-body text-center">
					<img src=" https://i.imgur.com/d2dKtI7.png" height="100" width="100">
					<div class="comment-box text-center">
						<h4>Are you sure want to mark this ticket as Closed ?</h4>
						<span>Please, give us rate to our service.</span>
						<input type="hidden" name="id" value="{{$ticket->id}}"/><br><br>
						<div class="rating rating-value">
							<input type="radio" name="rating" value="5" id="5" data-id=5><label for="5">☆</label>
							<input type="radio" name="rating" value="4" id="4" data-id=4><label for="4">☆</label>
							<input type="radio" name="rating" value="3" id="3" data-id=3><label for="3">☆</label>
							<input type="radio" name="rating" value="2" id="2" data-id=2><label for="2">☆</label>
							<input type="radio" name="rating" value="1" id="1" data-id=1><label for="1">☆</label>
						</div>
						<div class="comment-area">
							<textarea class="form-control comment-value" placeholder="What is your ticket view?" rows="4" name="comment" required></textarea>
						</div>
					</div>
				</div>`;

	$(".close-ticket").click(function(e) {
		Swal.fire({
			// title: "Confirmation",
			// text: "Are you sure want to mark this ticket as Closed ?",
			// icon: "question",
			heightAuto: false,
			customClass: 'swal-height',
			html: form,
			showCancelButton: true,
			confirmButtonText: "Yes!",
			preConfirm: () => {
				if ($('input[type=radio]:checked').length == 0) {
					Swal.showValidationMessage('Please give us rating star');
				} else if ($('.comment-value').val() == "") {
					Swal.showValidationMessage('Please write comment');
				}
			}
		}).then(function(result) {
			if (result.value) {
				KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});
				$.ajax({
					type: "POST",
					url: '{{URL("/")}}/ticketAction',
					data: {
						id: "{{$ticket->id}}",
						action: "closed",
						message: $('.comment-value').val(),
						rating: $('input[type=radio]:checked').data('id')
					},
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

	$(".rating-submit").click(function(e) {
		if ($('input[type=radio]:checked').length == 0) {
			alert('Please give us rating star');
		} else if ($('.comment-value').val() == "") {
			alert('Please write comment');
		} else {
			$('.modal-rating').hide();
			$(".modal-backdrop").remove();

			KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});

			$.ajax({
				type: "POST",
				url: '{{URL("/")}}/ticketAction',
				//dataType: 'json',
				data: {
					id: "{{$ticket->id}}",
					action: "closed",
					message: $('.comment-value').val(),
					rating: $('input[type=radio]:checked').data('id')
				},
				success: function(data){
					KTApp.unblockPage();
					console.log(data);
					var obj = JSON.parse(data);
					if(obj.success) {
						Swal.fire("Confirmation",obj.message,"success").then(function(result) {
							if (result.value || result.isDismissed) {
								location.reload();
							}
						})
						// setTimeout(function() {
						// 	location.reload();
						// },3000);
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
	})

	$('#summernote-2').summernote({
		placeholder: 'Leave a reason ...'
	});

	$('.reason-modal').on('click', function() {
		$('.modal-reply-reason').modal('show');
		$('.status-ticket').html($(this).data('status-ticket') + ' Reason');
		$('.status-value').val($(this).data('status-ticket').toLowerCase());

		if ($(this).data('status-ticket') == 'Reject' || $(this).data('status-ticket') == 'Delete') {
			$('.reason-textarea').attr('required', '');
			$('.box-manual-assignment').hide();
			$('.required-reason').removeAttr('required');
		} else {
			<?php //approve?>
			$('.box-manual-assignment').show();
			$('.reason-textarea').removeAttr('required');
			$('.required-hide').hide();
			$('.required-reason').attr('hidden', '');
		}
	})

	$('.approved').on('click', function() {
		if ($('.status-value').val() != 'approve' && $('.reason-textarea').val() == "") {
			alert('Please write your reason');
		} else {
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
								"_token": "{{csrf_token()}}",
								id: "{{$ticket->id}}",
								action_detail: "approved",
								message: $('.reason-textarea').val(),
							},
							success: function(data){
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
					} else if ($('.status-value').val() == 'reject') {
						$.ajax({
							type: "POST",
							url: '{{URL("/")}}/approve-request/ticket_reject',
							data: {
								"_token": "{{csrf_token()}}",
								id: "{{$ticket->id}}",
								message: $('.reason-textarea').val(),
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
						$.ajax({
							type: "POST",
							url: '{{URL("/")}}/approve-request/ticket_delete',
							data: {
								"_token": "{{csrf_token()}}",
								id: "{{$ticket->id}}",
								message: $('.reason-textarea').val(),
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
});
</script>
@endsection
