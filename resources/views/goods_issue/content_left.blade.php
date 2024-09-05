<!--begin::List Widget 8-->
<div class="card card-custom gutter-b h-100">
    <div class="card-body">
        <!--begin::Top-->
        <div class="d-flex">
            <!--begin::Pic-->
            <div class="flex-shrink-0 mr-7">
                <div class="symbol symbol-50 symbol-lg-90 symbol-light-primary">
                    <span class="font-size-h3 symbol-label font-weight-boldest">{{acronym($detail->contactRequestor->name ?? "")}}</span>
                </div>
            </div>
            <!--end::Pic-->
            <!--begin: Info-->
            <div class="flex-grow-1">
                <!--begin::Title-->
                <div class="d-flex align-items-center justify-content-between flex-wrap mt-2">
                    <!--begin::User-->
                    <div class="mr-3">
                        <!--begin::Name-->
                        <h2>{{ @$detail->subject }}</h2>
                        <span class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3">


                            {{ @$detail->contactRequestor->name }}


                        <i class="flaticon2-correct text-success icon-md ml-2"></i></span>
                        <!--end::Name-->
                        <!--begin::Contacts-->
                        <div class="d-flex flex-wrap my-2">

                            <span class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
                            <span class="flaticon2-chronometer icon-md icon-gray-500 mr-1"></span>
                            {{date('d M Y H:i', strtotime($detail->created_at))}}</span>
                            <span class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
                            <span class="svg-icon svg-icon-md svg-icon-gray-500 mr-1">
                                <!--begin::Svg Icon | path:assets/media/svg/icons/General/Lock.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <mask fill="white">
                                            <use xlink:href="#path-1"></use>
                                        </mask>
                                        <g></g>
                                        <path d="M7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C14.7614237,3 17,5.23857625 17,8 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 Z M12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L15,10 L15,8 C15,6.34314575 13.6568542,5 12,5 Z" fill="#000000"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>{{@$detail->contactRequestor->jobTitle->job_name}}</span>
                            <span class="text-muted text-hover-primary font-weight-bold">
                            <span class="svg-icon svg-icon-md svg-icon-gray-500 mr-1">
                                <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Mail-notification.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <path d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000"></path>
                                        <circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5" r="2.5"></circle>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>{{$detail->contactRequestor->email ?? ""}}</span>
                        </div>
                        <!--end::Contacts-->
                    </div>
                    <!--begin::User-->
                    <!--begin::Actions-->
                    <div class="my-lg-0 my-1" hidden>
                        <a href="#" class="btn btn-sm btn-white-line3"><span class="far fa-star"></span></a>
                        <a href="#" class="btn btn-sm btn-white-line3 ml-1 mr-2"><span class="far fa-trash-alt"></span></a>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Title-->
                <!--begin::Content-->
                <div class="d-none align-items-center flex-wrap justify-content-between">
                    <!--begin::Description-->
                    <div class="flex-grow-1  text-dark-75 py-2 py-lg-2 mr-5">
                        Hi, team <br/><br/>
                    <?= $detail->description ?>
                        <br/><br/>
                        Regards,<br/>
                        {{ @$detail->contactRequestor->name }}
                    </div>
                    <!--end::Description-->

                </div>
                <!--end::Content-->
            </div>
            <!--end::Info-->
        </div>
        <div>
            <div class="row mt-4">
                <br/>
                <!--begin::Item-->
                <div class="mb-2  col-md-4">
                    <span style="font-weight: 600" class="text-dark-75 text-hover-primary font-size-lg mb-1">Type</span>
                    <br> {{@$detail->inventoryType->label}} <br> <br>
                    <span style="font-weight: 600" class="text-dark-75 text-hover-primary font-size-lg mb-1">Description</span>
                    <br> {!! @$detail->description !!}
                </div>
                <!--end::Item-->
                <?php
                $form_builder = json_decode($detail->form_builder_json);
                //var_dump($form_builder);
                //echo "<br/><br/>";

                $data_json = json_decode($detail->data_json);
                //var_dump($data_json);
                //echo "</pre>";
                if(!empty($form_builder)) {
                    foreach($form_builder as $f) {
                        //var_dump($f);
                        if(str_contains($f->type, 'data_grid')) {
                            //echo "MASUK";
                            if(!empty($f->header)) {
                            ?>
                            <!--begin::Item-->
                            <div class="mb-2 col-md-12">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Text-->
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1"><?=$f->label?></span>
                                    </div>
                                    <!--end::Text-->
                                </div>
                                <!--end::Section-->
                                <!--begin::Desc-->
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <?php
                                            $headers = explode("#*#",$f->header);
                                            ?>
                                            @foreach($headers as $h)
                                            <th>
                                                {{$h}}
                                            </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        for($r=0;$r<$f->rows;$r++) {
                                        ?>
                                        <tr>
                                            <?php $k = 0;
                                            foreach($headers as $h) {

                                                $target_key = "input_".$f->name."_".$k."_".$r;
                                                ?>
                                                <td>
                                                    <?php
                                                    foreach($data_json as $key=>$value) {
                                                        if($key == $target_key) {
                                                            //control selain checkbox
                                                            echo $value;
                                                        } else {
                                                            if(str_contains($key, $target_key)) {
                                                                //control checkbox, lebih dari 1 value
                                                                echo "<div><i class='fas fa-check' style='font-size: 11px;margin-right: 5px;color: #868686;'></i>".$value."</div>";
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                            <?php
                                            $k++;
                                            } ?>

                                        </tr>
                                        <?php
                                        } ?>
                                    </tbody>
                                </table>
                                <?php

                                ?>
                                <!--end::Desc-->
                            </div>
                            <!--end::Item-->
                            <?php
                            }
                        } else {
                            ?>
                            <!--begin::Item-->
                            <div class="mb-2  col-md-4">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Text-->
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span style="font-weight: bold" class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1"><?=$f->label?></span>
                                    </div>
                                    <!--end::Text-->
                                </div>
                                <!--end::Section-->
                                <!--begin::Desc-->
                                <?php
                                foreach($data_json ?? [] as $key=>$value) {
                                    //echo $key;
                                    $a = explode("_",$key); //misal : location-1617930142092_location_Address
                                    if(!empty($f->name)) {
                                        if($a[0] == $f->name) {
                                            //echo "SAMA";
                                            //var_dump($value);
                                            //echo $f->type;

                                            $retval =  getObjectValue($f->type,$value);
                                            if(is_array($retval)) {
                                                echo "<ul>";
                                                foreach($retval as $val) {
                                                    if(!is_array($val)) {
                                                        echo "<li>$val</li>";
                                                    }
                                                }
                                                echo "</ul>";
                                            } else {
                                                if(is_string($retval)) {
                                                    echo $retval;
                                                }
                                            }

                                        }
                                    }
                                }
                                ?>
                                <p class="text-dark-50 m-0 pt-5 font-weight-normal">{{ "" }}</p>
                                <!--end::Desc-->
                            </div>
                            <!--end::Item-->
                            <?php
                        }
                    }
                }
                ?>
            </div>
            <?php
            //cek last approver
            $last_approver = DB::table('goods_issue_approvals')->where('goods_issue_id', $detail->id)->latest('created_at')->first();
            // dd($request_management);
            $last_approver_id = $last_approver->approval_id ?? -9234;
            // dd([$next_approver, Auth::user()->person, $is_alr_first_support_custom]);
            ?>

            @if (@$next_approver->id == Auth::user()->person)
                &nbsp;
                <button class="btn btn-sm btn-white-line2 mt-7" id="btn-approve" data-id="{{ $detail->id }}" data-title="{{ $detail->title }}" data-status-ticket="Approve"><i class="fa fa-check"></i> Approve</button>
                &nbsp;
                <button class="btn btn-sm btn-white-line2 mt-7 reason-modal" data-toggle="modal" data-target="#rejectModal"><i class="fa fa-ban"></i> Reject</button>
                &nbsp;
            @endif
			<button  class="mb-0 btn btn-sm btn-white-line2 reply-comment mt-7"  data-toggle="modal" data-target="#exampleModal" style=""><i class="flaticon2-reply-1 icon-sm text-dark-75"></i> Reply</button>

            @if(!empty($detail->files))
                <!--begin::Item-->
                <div class="mb-2">
                    <!--begin::Section-->
                    <div class="d-flex align-items-center">
                        <!--begin::Text-->
                        <div class="d-flex flex-column flex-grow-1">
                            <span style="font-weight: bold" class="text-dark-75 text-hover-primary font-size-lg mb-1">Attachments</span>
                        </div>
                        <!--end::Text-->
                    </div>
                    <!--end::Section-->
                    <!--begin::Desc-->

                    <div class="d-flex flex-column mb-5 align-items-start">
                        <div>
                        <?php $files = explode(",",$detail->files);
                        ?>
                        @foreach($files as $f)
                            @if(is_image($f))
                            @else
                                <?php
                                $a = explode("/",$f);
                                $t = substr($a[count($a)-1],6);
                                ?>
                                <a class="file-attach" target="_blank" href="{{$f}}">{{$t}}</a>
                                <br/>
                            @endif
                        @endforeach
                        @foreach($files as $f)
                            @if(is_image($f))
                                <a target="_blank" href="{{$f}}">
                                <div class="symbol symbol-50 symbol-lg-50 symbol-light-primary ml-1 p-1" style="border:1px solid #d4d4d4;">
                                    <img src="{{$f}}">
                                </div>
                                </a>
                            @else
                            @endif
                        @endforeach
                        </div>
                    </div>
                    <!--end::Desc-->
                </div>
                <!--end::Item-->
            @endif
            <!--end::Info-->
        </div>

    </div>
</div>

@push('modal')
<div class="custom-modal fade modal-reply-reason" id="approveModal" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title status-ticket" id="exampleModalLabel">Approve</h5>
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
						<textarea name="reason" class="form-control form-control-solid form-control-lg reason-textarea summernote-2" rows="7" maxlength="50"></textarea>
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

<div class="custom-modal fade modal-reply-reason" id="rejectModal" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title status-ticket" id="exampleModalLabel">Reject</h5>
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
						<textarea name="reason" class="form-control form-control-solid form-control-lg reason-textarea reject-reason summernote-2" rows="7" maxlength="50"></textarea>
					</div>
					
					<input class="form-control status-value" type="hidden" name="status"/>
					<input class="form-control" type="hidden" name="approval_custom" value="0"/>
				</div>
				<!--end::Item-->
			</div>
			<div class="modal-footer">
				{!! Form::submit('Submit', ['class' => 'btn btn-primary mr-2 rejected']) !!}
				<button type="button" class="close-btn btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>   

<div  class="modal-reply" >
    <div class="modal-dialog modal-lg modal-dialog-centered" style="min-width: 1000px" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Reply Comment</h5>
            <button type="button" class="close close-btn" data-dismiss="modal" aria-label="Close">&times;</button>
        </div>
            <form id="comment-form" method="post" action="{{URL('/inventory/comment')}}?type=goods_issue"  enctype="multipart/form-data">
                @csrf
                <div class="modal-body pt-4 pb-0">
                    <div class="row">
                        <div class="col-md-7 mb-0">


                            <input type="hidden" name="id" value="{{$detail->id}}" />
                            <div class="form-group mb-0">
                                <label for="exampleTextarea">Your Message <span class="text-danger">*</span></label>
                                <textarea name="message" id="summernote" class="form-control form-control-solid form-control-lg" rows="8"></textarea>
                                <span class="form-text text-muted"></span>
                            </div>
                        </div>
                        <div class="col-md-5 mb-0">
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
                            .swal2-container {
                                z-index:2000001 !important;
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
                            $list_contact = getInventoryManagementCaseJourney($detail);
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

                                <span class="form-text text-muted">All people inputed here will get notified for this ticket. Requester not need to include because requester always get notified.</span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal-footer">
                <button type="button" class="close-btn btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary submit-message">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush



<script>
$( document ).ready(function() {
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
    
    $('#comment-form').on('submit',(function(e) {
        $(".modal-backdrop").remove();
        $('.modal-reply').hide();
        showLoading()
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
                hideLoading()
                console.log("success");
                console.log(data);

                var obj = JSON.parse(data);
                if(obj.success) {

                    Swal.fire("Confirmation",obj.message,"success")
                    $('.messages').html(obj.content);

                } else {
                    Swal.fire("Failed",obj.message,"error")
                }
                $('#summernote').summernote();
            },
            error: function(data){
                $('.modal-reply').hide();
                hideLoading()
                console.log("error");
                console.log(data);
                $('#summernote').summernote();
            }
        });
    }));

    $(".reply-comment").click(function(e) {
        $('body').append('<div class="modal-backdrop fade show"></div>');
        $('.modal-reply').show();
        $('#summernote').summernote('code', '<p><br></p>');
        $('.note-modal').hide();
    });

    $(".close-btn").click(function(e) {
        $('.modal-reply').hide();
        $(".modal-backdrop").remove();
    });

    $('.summernote-2').summernote();
    $('.note-modal').hide();

    $('#btn-approve').on('click', function() {
        $('#approveModal').modal('show');
    })

    $('.approved').on('click', function() {
        $('#approveModal').modal('hide');

        Swal.fire({title: "Confirmation",text: "Are you sure want to approve ?",icon: "question",showCancelButton: true,confirmButtonText: "Yes!"
        }).then(function(result) {
            if(result.value) {
                $.ajax({
                    url: '{{ url("approve-request/ticketActionInventory") }}?id={{$detail->id}}&type=issue&action_detail=approved',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        message: $('.reason-textarea').val()
                    },
                    dataType: 'json',
                    success: (res) => {
                        Swal.fire(res.success ? 'Updated' : 'Info', res.message, res.success ? 'success' : 'info')
                        setTimeout(() => {
                            window.location.reload()
                        }, 850);
                    },
                    error: () => alert('Something went wrong')
                });
            }
		});
    });

    $('.rejected').on('click', function() {
        $('#rejectModal').modal('hide');

        Swal.fire({title: "Confirmation",text: "Are you sure want to Reject ?",icon: "question",showCancelButton: true,confirmButtonText: "Yes!"
        }).then(function(result) {
            if(result.value) {
                $.ajax({
                    url: '{{ route("approve_request.reject_inventory", $detail->id) }}',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        message: $('.reject-reason').val()
                    },
                    dataType: 'json',
                    success: (res) => {
                        
                        if(res.success) {
                            Swal.fire("Updated", res.message, "success")
                        } else {
                            Swal.fire("Failed", res.message, "error")
                        }

                        setTimeout(() => {
                            window.location.reload()
                        }, 850);
                    },
                    error: () => alert('Something went wrong')
                });
            }
		});
    });

	$(".activities").click(function(e) {
		if ($('.content-activities').css('display') == 'none') {
			$('.content-activities').slideDown();
			$(this).addClass('btn-press');
		} else {
			$('.content-activities').slideUp();
			$(this).removeClass('btn-press');
		}
	});

	$(".askMoreInfoButton").click(function(e) {
		$('body').append('<div class="modal-backdrop fade show"></div>');
		$('.modal-reply3').show();
		$('.note-modal').hide();
		$('#summernote').summernote('code', '<p><br></p>');
	});

	$('#comment-form2').on('submit',(function(e) {
		$(".modal-backdrop").remove();
		$('.modal-reply3').hide();
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
				$('.modal-reply3').hide();
				KTApp.unblockPage();
				console.log("success");
				console.log(data);

				var obj = JSON.parse(data);
				if(obj.success) {

					Swal.fire("Confirmation",obj.message,"success")
					$('.messages').html(obj.content);
					//setTimeout(function() {
						// location.reload();
					//},3000);

				} else {
					Swal.fire("Failed",obj.message,"error")
				}
				$('#summernote').summernote('code', '<p><br></p>');
			},
			error: function(data){
				$('.modal-reply3').hide();
				KTApp.unblockPage();
				console.log("error");
				console.log(data);
				$('#summernote').summernote('code', '<p><br></p>');
			}
		});
	}));
});
</script>
<style>
.btn-press {
	background: linear-gradient(180deg, #e0e0e0 0%, #e8e8e8 100%) !important;
}
.content-activities .timeline.timeline-6 .timeline-item .timeline-label {
    width: 150px;
    text-align: right;
    padding-right: 15px;
}
.content-activities .timeline.timeline-6:before {
    left: 151px;
}

.modal-reply3, .modal-reply {
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
