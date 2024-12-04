<style>
.note-editable {
	min-height:150px !important;
}
</style>
<!-- include summernote css/js -->
<!--
<link href="{{URL('/')}}/vendor/summernote/summernote.min.css" rel="stylesheet">
<script src="{{URL('/')}}/vendor/summernote/summernote.min.js"></script>

<script src="{{URL('/')}}/vendor/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
		CKEDITOR.replace('wysiwyg-editor', {
			filebrowserUploadUrl: "{{route('ckeditor.image-upload', ['_token' => csrf_token() ])}}",
			filebrowserUploadMethod: 'form'
		});
    });

//$(document).ready(function() {
  //$('#summernote').summernote('code', '<p><br></p>');
//});
</script>
-->

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label>
                                Requester
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-12 col-md-9 col-sm-12">
                                <div class="radio-inline">
                                    <label class="radio radio-success">
                                    <input required type="radio" class="request_for" name="request_for" value="myself" checked/>
                                    <span></span>My Self</label>
                                    <label class="radio radio-success">
                                    <input required type="radio" class="request_for" name="request_for" value="other" />
                                    <span></span>Other</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row originator">
                            <label>
                                On Behalf
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-12 col-md-9 col-sm-12">
                                <select class="form-control selectpicker select_on_behalf" data-live-search="true" name="requester" required>
									<option value=''>-Select Employee-</option>
                                    @foreach ($originator as $key => $origin)
                                        @if (isset($origin->name))
                                            <option value="{{ $origin->id }}">{{ $origin->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label>
                                Subject
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-12 col-md-9 col-sm-12">
                                <input class="form-control" type="text" name="title" required/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label>
                                Description
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-12 col-md-9 col-sm-12">
                                <textarea id="summernote" name="description" class="form-control" rows="10" required></textarea>
                            </div>
                        </div>
                        @if(!empty($_GET['id']))
							<input type="hidden" name="id" value="{{$ticket_draft->id ?? '0'}}"/>
                        @endif
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

                        <input type="hidden" name="request_management" value="{{$request_management->id}}"/>
                        <div class="form-group row">
                            <div id="get-user-data"></div>
                        </div>
                        <div class="form-group row" hidden>
                            <div class="col-lg-8 col-md-9 col-sm-12">
                                <input class="form-control" type="text" name="service_id" value="{{ $service->id }}"/>
                                <input class="form-control" type="text" name="servicesubcategory_id" value="{{ $category }}"/>
                                <input class="form-control" type="text" name="approval_custom" value="{{ !empty($request_management->approval_user_custom) ? $request_management->approval_user_custom : $approval }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div id="fb-render"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div>
						<input id="submit_type" type="hidden" name="submit_type" value="submit"/>
						<button type="button" id="btn-submit" class="btn btn-success mr-2 btn-submit">Submit</button>
						{{-- <button type="button" id="btn-draft" class="btn btn-warning mr-2 btn-draft">Draft</button> --}}
                        <a href="{!! route('home') !!}" class="btn btn-outline-dark btn-white-line">Cancel</a>
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
			}
		}
	});
}
function iSummernote2(text) {
	editor2 = $('#summernote').summernote(
		"code", text,
		{
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
}
$(document).ready(function() {
	//iSummernote();
	
	$('.modal-reply').hide();
	$('.note-modal').hide();
	$(".close-btn").click(function(e) {
		$('.modal-reply').hide();
		$(".modal-backdrop").remove();
	});
});

</script>
