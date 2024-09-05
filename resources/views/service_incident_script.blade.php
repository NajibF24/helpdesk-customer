<?php
    if (isset($request_management->form_builder)) {
        $form_builder = DB::table('form_builder')->where('id', $request_management->form_builder)->first();
        $option = DB::table('service_category')
            ->pluck('name', 'id')
            ->toArray();
        $option = [""=>"-Select Service Category-"]+ $option;
    }
?>
<script>
    $(function () {
		@if(!empty($ticket_draft->data_json))
			
			<?php 
			$a = json_decode($ticket_draft->data_json);
			unset($a->description,$a->form_data_json);
			$new_data = json_encode($a);
			?>
			
			//alert('<?=$new_data?>');
			var obj = JSON.parse('<?=$new_data?>'); 
			//for(var i=0;i<obj_json.length;i++) {
				//console.log(obj_json[i]);
			//}
			setTimeout(function() {
				$('input[type="checkbox"]').prop('checked', false);
						for (var prop in obj) {
							if (Object.prototype.hasOwnProperty.call(obj, prop)) {
								// do stuff
								console.log(prop);
								console.log(obj[prop]);
								
								
								//$('#'+prop).val(obj[prop]);
								
			//console.log("yaaa"+$("#text-1617953557806").val());
			
			
								if($('#'+prop).length) {
									console.log("get5");
									$('#'+prop).val(obj[prop]);
									//$('#'+prop).html(obj[prop]);
								}

								if($('input[name="'+prop+'"]').length) {
									if(prop.includes("radio-group")) {
										$('input[name="'+prop+'"]').val([obj[prop]]);
									} else {
										$('input[name="'+prop+'"]').val(obj[prop]);
									}
									console.log("input");
								}
								

								
								if($('textarea[name="'+prop+'"]').length) {
									console.log("get2"+obj[prop]);
									$('textarea[name="'+prop+'"]').html(obj[prop]);
								}
								if($('[name="'+prop+'"]').length) {
									console.log("get3");
									$('[name="'+prop+'"]').val(obj[prop]);
								}
								
								if($('[name="'+prop+'[]"]').length) {
									console.log("get_array");
									//$('[name="'+prop+'"]').val(obj[prop]);
									
									for(var i=0;i<obj[prop].length;i++) {
										console.log(obj[prop][i]);
										$('[value="'+obj[prop][i]+'"]').prop('checked', true);
									}
								}
								if(prop.includes("data_grid")) {
									//if(prop.includes("radio-group")) {
										$('input[name="'+prop+'"]').val([obj[prop]]);
									//} else {
										$('input[name="'+prop+'"]').val(obj[prop]);
									//}
									console.log("input");
								}
								console.log("---------");	
								
							}
						}
						//$('#company-1617953599037').select2('data', {id: '1', text: 'Nabati Group'});
						$('#company-1617953599037').val(1).trigger('change'); // Select the option with a value of '1'		
						//$('#company-1617953599037').trigger('change');
						$('.select2formbuilder').select2();
						//$('#summernote').val('{{$ticket_draft->description}}');
						//$(".summernote").summernote("code", "your text");
						iSummernote2('<?=$ticket_draft->description?>');
						
			},1500);
		
		@else
			setTimeout(function() {
				$('.select2formbuilder').select2();
				iSummernote();
			},1000);
			
		@endif
		
	});
</script>
<script src="{{URL('/')}}/assets/js/form-builder.min.js"></script>
<script src="{{URL('/')}}/assets/js/form-render.min.js"></script>
<script type="text/javascript" src="{{URL('/')}}/vendor/timepicker/jquery.timepicker.js"></script>
<link rel="stylesheet" type="text/css" href="{{URL('/')}}/vendor/timepicker/jquery.timepicker.css" />
<script type="text/javascript" src="{{URL('/')}}/vendor/timepicker/bootstrap-datepicker.js"></script>
<link rel="stylesheet" type="text/css" href="{{URL('/')}}/vendor/timepicker/bootstrap-datepicker.css" />


<script> 
	const getUserDataBtn = document.getElementById("get-user-data"); 
	const fbRender = document.getElementById("fb-render");
	let url = new URL(window.location.href);
	let params = new URLSearchParams(url.search);
	let target = params.get('target');

	localStorage.setItem('target', target);
</script>
<script>
	var formBuilder;
	let fields = [];

	//add time control
	fields.push({
		label: 'Time',
		attrs: {
		type: 'time'
		},
		icon: '<i  class="far fa-clock" style="    color: #5f636f;"></i>'
	});

    <?php 
		$fb_objects = DB::table('form_builder_object')
						->where('type','!=','Data Grid')
						->get();
		foreach($fb_objects as $f) {
			if(!empty($f->type)) {
				$type_control = str_replace(" ","_",strtolower($f->type!="Dropdown"?$f->type:$f->data_source));
			?>  
			fields.push({
			  label: '{{$f->name}}',
			  attrs: {
				type: '{{$type_control}}'
			  },
			  icon: '<i  class="fas fa-cube" style="    color: #5f636f;"></i>'
			});
			<?php 
			}
		}
		$fb_objects_grid = DB::table('form_builder_object')
						->where('type','=','Data Grid')
						->get();
		foreach($fb_objects_grid as $f) {
			if(!empty($f->type)) {
				$type_control = "data_grid".$f->id
			?>  
			fields.push({
			  label: '{{$f->name}}',
			  attrs: {
				type: '{{$type_control}}'
			  },
			  icon: '<i  class="fas fa-border-all" style="    color: #5f636f;"></i>'
			});
			<?php 
			}
		}
    ?>
    <?php $list_data_source = DB::table('form_builder_object')->whereIn('type',['Dropdown'])->pluck('data_source','data_source')->toArray(); 
    $list_data_source = array_unique($list_data_source);

    ?>
    setTimeout(function() {
        // Your code here
        $('.form-actions').hide();
        
        @foreach($list_data_source as $data_source)
            <?php $data_source = str_replace(" ","_",strtolower($data_source));?>
            $.ajax({
                type: "GET",
                url: '{{URL("/")}}/select2list/{{$data_source}}',
                //data: data_input,
                //dataType: 'json',
                success: function(data){
                    var obj = JSON.parse(data);
                    
					for (let i = 0; i < obj.length; i++) {
						console.log("yyyy");
						console.log(obj[i]);
						//text += obj[i] + "<br>";
						$('.target-{{$data_source}}').append("<option value='"+obj[i].id+"'>"+obj[i].text+"</option>");
					}
                    
                    //$('.target-{{$data_source}}').select2({
                        //data: obj
                    //})
                },
                error: function(){console.log("error");}
            });
        @endforeach
    }, 500);
    
    let list_data_source_js = [];
    
    @foreach($list_data_source as $data_source)
        list_data_source_js.push('{{strtolower($data_source)}}');
    @endforeach
    
    
    let templates = {

		//template for time control
		time: function(fieldData) {
			return {
			field: '<input id="'+fieldData.name+'" type="text" class="time form-control"  name="'+fieldData.name+"_"+fieldData.type+"_"+fieldData.label+'"/>',
			onRender: function() {
				$('#'+fieldData.name).timepicker({ 'scrollDefault': 'now', 'timeFormat': 'H:i'  });
			}
			};
		},
		
        @foreach($list_data_source as $data_source)
            <?php $data_source = strtolower($data_source);?>
            {{str_replace(" ","_",$data_source)}}: function(fieldData) {
                return {
                    field: '<span id="'+fieldData.name+'">',
                    onRender: function() {
                        <?php //get object dari type yang dipanggil ?>
                        let content = '<div class="form-group row">\
                                            <select style="width:100%" class="select2formbuilder form-control  '+fieldData.label+' target-'+fieldData.type+' " id="'+fieldData.name+'" name="'+fieldData.name+"_"+fieldData.type+"_"+fieldData.label+'">\
                                                <option value="" selected="selected">-Select '+fieldData.label+'-</option>\
                                            </select>\
                                        </div>';							
                        $(document.getElementById(fieldData.name)).html(content);
                    }
                };
            },
        @endforeach
		<?php 
			foreach($fb_objects_grid as $f) {
				if(!empty($f->type)) {
						$type_control = "data_grid".$f->id
						?>  
						'{{$type_control}}': function(fieldData) {
						return {
						  field: '<span id="'+fieldData.name+'">',
						  onRender: function() {
							  //var target_id = makeid(10);
							  var target_id = fieldData.name.replaceAll(" ","_");
							  //"tESSSS2"+fieldData.type+
							  $(document.getElementById(fieldData.name)).html("<div class='grid"+target_id+"'><div class='table-grid'></div></div>");
							  //var grid_id = fieldData.type.replace("data grid","");
							  //var grid_id = makeid(10);
							  var berkas_header = '{{$f->header ?? ""}}';
							  var berkas_value_json = '<?=$f->value_json?>';
							  var column = <?=$f->columns?>;
							  var rows = <?=$f->rows?>;
							  
							  createDataGrid(target_id,berkas_header,berkas_value_json,column,rows);
							  //$(document.getElementById(fieldData.name)).rateYo({rating: 3.6});
						  }
						};
					  },
				<?php 
				}
			} ?>
    };
			var data_form = new Array(10).fill(0).map(() => new Array(10).fill(0));
			function createDataGrid(target_id,berkas_header,berkas_value_json,column,rows) {
				console.log(rows+"ya");
						setTimeout(function() {
							console.log(rows);
							//$('.create-grid').click();
							console.log("y"+column+rows);
							//gridster = $(".gridster > ul").gridster({
								//widget_margins: [5, 5],
								//widget_base_dimensions: [100, 55],
								//min_cols:$('#column_total').val(),
								//max_cols:$('#column_total').val(),
								//min_rows:$('#row_total').val(),
								//max_rows:$('#row_total').val(),
							//}).data('gridster');
							var str = "";
							//var column = $('#column_total').val();
							//var row = $('#row_total').val();
							str += "<table class='table table-bordered table-grid table-content-grid'>";
							str += "<thead><tr class='heading-table'>";
							for(var j=0;j<column;j++) {
								str += "<th class='cell-element'><div class=' header-grid'  ></div></th>";
							}
							str += "</tr></thead>";
							for(var i=0;i<rows;i++) {
								str += "<tr>";
								for(var j=0;j<column;j++) {
									str += "<td class='cell-element'><ul class='frmb4'  ></ul></td>";
								}
								str += "</tr>";
							}
							str += "</table>";
							//alert(str);
							$(".grid"+target_id+" .table-grid").html(str);

							
							var i = 0;
							var header_array = berkas_header.split("#*#");
							$( '.grid'+target_id+' .header-grid' ).each(function() {
								if (typeof header_array[i] !== "undefined" ) {
									console.log(header_array[i]);
									$( this ).html(header_array[i])
								}
								i++;
							});
							
							var row = 0;
							var col = 0;
							var cellIndex = 0;
							
							var cell;
							var $cell;
											
							var json_string = berkas_value_json;
							var json_data =   JSON.parse(json_string.replaceAll("	",""));//.replaceAll(" ",""))
							var id_el = "",content_html="",content_edit_html="";
							console.log(json_data);
							
							setTimeout(function() {

								for(var m=0;m<json_data.length;m++) {
									for(var k=0;k<json_data[m].length;k++) {
										if(json_data[m][k] != 0) {
											col = k;
											cellIndex = col;
											row = 1;
											if (typeof json_data[m][k].type !== "undefined" ) {
												
												sel_control = json_data[m][k].type;
												data_source = "";
												id_el = "";
												if (typeof json_data[m][k].data_source !== "undefined" ) {
													data_source = json_data[m][k].data_source;
												}
												
												//var element_data = sel_control.split("###");
												//var data_source = element_data[2];
												
												content = conditionalUI(sel_control,row-1,col,data_source,json_data[m][k]);
												var table = $(".grid"+target_id+" .table-content-grid")[0];
												console.log("panggil");
												
												
												cell = table.rows[m+1].cells[k].children; // This is a DOM "TD" element
												athis = $(cell);
												
												athis.html(content.replaceAll("frmb",""));
												
												next = athis.parent();
												while (next.length) {
													next = next.parent('tr').next().children().eq(cellIndex);
													next.html(athis.parent().html());
												}

												var n= 500;
												$( ".form-wrap.form-builder .stage-wrap" ).each(function( index ) {
													$( this ).css('z-index',n--);
												  //console.log( index + ": " + $( this ).text() );
												});
												console.log('dataform');
												console.log(data_form);
												$( ".table-content-grid .frm-holder" ).remove();
												$( ".table-content-grid .field-actions" ).remove();
											}

										}
									}
								}
								
								//set height cell
								var newHeight=50;
								$( ".prev-holder" ).each(function( index ) {
									if(newHeight < $(this).height()) {
										newHeight = $(this).height();
									}
								  //console.log( index + ": " + $( this ).text() );
								});
								
								$( ".droppable2" ).height(newHeight+12);
								
								var pos_row = 0;
								for(var s=0;s<column;s++) {
									<?php //loop kolom ?>
									pos_row = 0;<?php //start dari baris 0 ?>
									$('td:eq('+s+')', $(".grid"+target_id+" .table-content-grid tr")).each(function() {
										
										<?php //loop cell2 di kolom tsb?>
										console.log("kolom"+s);
										//console.log($(this).html());
										$('input', $(this)).each(function() {
											console.log($(this).attr("type"));
											var type = $(this).attr("type");
											if(type == "text" || type == "date" || type == "number") {
												$(this).attr("name","input_"+target_id+"_"+s+"_"+pos_row);
											}
											if(type == "radio") {
												<?php //name harus sama semua utk satu cell ?>
												$(this).attr("name","input_"+target_id+"_"+s+"_"+pos_row);
											}
											if(type == "checkbox") {
												<?php //name harus beda semua utk satu cell ?>
												//var id_checkbox = makeid(10);
												$(this).attr("name","input_"+target_id+"_"+s+"_"+pos_row+"_"+$(this).val().replaceAll(" ","_"));
											}
										});
										$('textarea', $(this)).each(function() {
											console.log('textarea');
											$(this).attr("name","input_"+target_id+"_"+s+"_"+pos_row);
										});
										$('select', $(this)).each(function() {
											console.log('select');
											$(this).attr("name","input_"+target_id+"_"+s+"_"+pos_row);
										});
										pos_row++;
									});
								}
								
							}, 500);
						}, 500);

			}
			function conditionalUI(sel_control,row,col,data_source,json_data_m_k=null) {
				var content = "";
								if(sel_control.includes("Text Field") || sel_control.includes("Text")) {
									data_form[row][col] = {'type' : "Text"};
									content = '<div id="frmb-1621537802249-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537802249" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="text-field form-field" type="text" id="frmb-1621537802249-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537802249-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537802249-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537802249-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Text Field</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder"><div class="formbuilder-text form-group field-text-1621537807103-preview"><label for="text-1621537807103-preview" class="formbuilder-text-label">Text Field</label><input class="form-control" name="text-1621537807103-preview" type="text" id="text-1621537807103-preview"></div></div><div id="frmb-1621537802249-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537802249-fld-1"><div class="form-elements"><div class="form-group required-wrap"><label for="required-frmb-1621537802249-fld-1">Required</label><div class="input-wrap"><input type="checkbox" class="fld-required" name="required" id="required-frmb-1621537802249-fld-1"></div></div><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537802249-fld-1">Label</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537802249-fld-1" contenteditable="true">Text Field</input></div></div><div class="form-group description-wrap" style="display: block"><label for="description-frmb-1621537802249-fld-1">Help Text</label><div class="input-wrap"><input name="description" placeholder="" class="fld-description form-control" id="description-frmb-1621537802249-fld-1" value="" type="text"></div></div><div class="form-group placeholder-wrap" style="display: block"><label for="placeholder-frmb-1621537802249-fld-1">Placeholder</label><div class="input-wrap"><input name="placeholder" placeholder="" class="fld-placeholder form-control" id="placeholder-frmb-1621537802249-fld-1" value="" type="text"></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537802249-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537802249-fld-1" value="form-control" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621537802249-fld-1">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621537802249-fld-1" value="text-1621537807103" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537802249-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537802249-fld-1"> <label for="access-frmb-1621537802249-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537802249-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537802249-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><div class="form-group value-wrap" style="display: undefined"><label for="value-frmb-1621537802249-fld-1">Value</label><div class="input-wrap"><input name="value" placeholder="Value" class="fld-value form-control" id="value-frmb-1621537802249-fld-1" value="" type="text"></div></div><div class="form-group subtype-wrap"><label for="subtype-frmb-1621537802249-fld-1">Type</label><div class="input-wrap"><select id="subtype-frmb-1621537802249-fld-1" name="subtype" class="fld-subtype form-control"><option label="Text Field" value="text">Text Field</option><option label="password" value="password">password</option><option label="email" value="email">email</option><option label="color" value="color">color</option><option label="tel" value="tel">tel</option></select></div></div><div class="form-group maxlength-wrap"><label for="maxlength-frmb-1621537802249-fld-1">Max Length</label><div class="input-wrap"><input type="number" name="maxlength" class="fld-maxlength form-control form-control" id="maxlength-frmb-1621537802249-fld-1"></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
								}

								if(sel_control.includes("Paragraph")) {
									data_form[row][col] = {'type' : "Paragraph"};
									content = '<div id="frmb-1621537627115-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537627115" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="paragraph-field form-field" type="paragraph" id="frmb-1621537627115-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537627115-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537627115-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537627115-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Paragraph</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder" style=""><div class="formbuilder-paragraph form-group field-paragraph-1621537639777-preview"><p class="" name="paragraph-1621537639777-preview" id="paragraph-1621537639777-preview">Paragraph</p></div></div><div id="frmb-1621537627115-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537627115-fld-1" style="display: none;"><div class="form-elements"><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537627115-fld-1">Content</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537627115-fld-1" contenteditable="true">Paragraph</input></div></div><div class="form-group subtype-wrap"><label for="subtype-frmb-1621537627115-fld-1">Type</label><div class="input-wrap"><select id="subtype-frmb-1621537627115-fld-1" name="subtype" class="fld-subtype form-control"><option label="p" value="p">p</option><option label="address" value="address">address</option><option label="blockquote" value="blockquote">blockquote</option><option label="canvas" value="canvas">canvas</option><option label="output" value="output">output</option></select></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537627115-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537627115-fld-1" value="" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537627115-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537627115-fld-1"> <label for="access-frmb-1621537627115-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537627115-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537627115-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
								}
								if(sel_control.includes("Header")) {
									data_form[row][col] = {'type' : "Header"};
									content = '<div id="frmb-1621537667906-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537667906" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="header-field form-field" type="header" id="frmb-1621537667906-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537667906-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537667906-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537667906-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Header</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder"><div class="formbuilder-header form-group field-header-1621537671753-preview"><h1 class="" name="header-1621537671753-preview" id="header-1621537671753-preview">Header</h1></div></div><div id="frmb-1621537667906-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537667906-fld-1"><div class="form-elements"><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537667906-fld-1">Label</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537667906-fld-1" contenteditable="true">Header</input></div></div><div class="form-group subtype-wrap"><label for="subtype-frmb-1621537667906-fld-1">Type</label><div class="input-wrap"><select id="subtype-frmb-1621537667906-fld-1" name="subtype" class="fld-subtype form-control"><option label="h1" value="h1">h1</option><option label="h2" value="h2">h2</option><option label="h3" value="h3">h3</option><option label="h4" value="h4">h4</option><option label="h5" value="h5">h5</option><option label="h6" value="h6">h6</option></select></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537667906-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537667906-fld-1" value="" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537667906-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537667906-fld-1"> <label for="access-frmb-1621537667906-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537667906-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537667906-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
								}
								if(sel_control.includes("Select")) {
									
									var id_opt1 = makeid(10);
									var id_opt2 = makeid(10);

									if (json_data_m_k !== null && typeof json_data_m_k.html !== "undefined" ) {
										<?php //edit ?>
										var id_el = json_data_m_k.id_el;
										data_form[row][col] = {
																'type' : "Select",
																'id_el' : json_data_m_k.id_el
															  };
										content = '<div id="frmb-1621537703850-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537703850" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="select-field form-field" type="select" id="frmb-1621537703850-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537703850-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537703850-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537703850-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Select</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder"><div class="formbuilder-select form-group field-select-1621537707374-preview"><label for="select-1621537707374-preview" class="formbuilder-select-label">Select</label>\
														<select class="form-control select-'+id_el+'" name="select-1621537707374-preview" id="select-1621537707374-preview">\
														'+decodeHtml(json_data_m_k.html)+'\
														</select></div></div><div id="frmb-1621537703850-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537703850-fld-1"><div class="form-elements"><div class="form-group required-wrap"><label for="required-frmb-1621537703850-fld-1">Required</label><div class="input-wrap"><input type="checkbox" class="fld-required" name="required" id="required-frmb-1621537703850-fld-1"></div></div><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537703850-fld-1">Label</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537703850-fld-1" contenteditable="true">Select</input></div></div><div class="form-group description-wrap" style="display: block"><label for="description-frmb-1621537703850-fld-1">Help Text</label><div class="input-wrap"><input name="description" placeholder="" class="fld-description form-control" id="description-frmb-1621537703850-fld-1" value="" type="text"></div></div><div class="form-group placeholder-wrap" style="display: block"><label for="placeholder-frmb-1621537703850-fld-1">Placeholder</label><div class="input-wrap"><input name="placeholder" placeholder="" class="fld-placeholder form-control" id="placeholder-frmb-1621537703850-fld-1" value="" type="text"></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537703850-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537703850-fld-1" value="form-control" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621537703850-fld-1">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621537703850-fld-1" value="select-1621537707374" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537703850-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537703850-fld-1"> <label for="access-frmb-1621537703850-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537703850-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537703850-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><div class="form-group multiple-wrap"><label for="multiple-frmb-1621537703850-fld-1"> </label><div class="input-wrap"><input type="checkbox" class="fld-multiple" name="multiple" id="multiple-frmb-1621537703850-fld-1"> <label for="multiple-frmb-1621537703850-fld-1">Allow Multiple Selections</label></div></div><div class="form-group field-options"><label class="false-label">Options</label><div class="sortable-options-wrap">\
														<ol class="sortable-options ui-sortable id_el-'+id_el+'">\
														'+decodeHtml(json_data_m_k.html_edit)+'\
														</ol><div class="option-actions"><a class="add add-opt">Add Option +</a></div></div></div><a class="close-field">Close</a></div></div></li></ul></div>';										
									} else {
										<?php //create ?>
										var id_el = makeid(10);
										data_form[row][col] = {
																'type' : "Select",
																'id_el' : id_el
															  };
										content = '<div id="frmb-1621537703850-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537703850" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="select-field form-field" type="select" id="frmb-1621537703850-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537703850-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537703850-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537703850-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Select</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder"><div class="formbuilder-select form-group field-select-1621537707374-preview"><label for="select-1621537707374-preview" class="formbuilder-select-label">Select</label>\
														<select class="form-control select-'+id_el+'" name="select-1621537707374-preview" id="select-1621537707374-preview">\
															<option selected="true" value="Option 1" id="select-'+id_opt1+'">Option 1</option>\
															<option value="Option 2" id="select-'+id_opt2+'">Option 2</option>\
														</select></div></div><div id="frmb-1621537703850-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537703850-fld-1"><div class="form-elements"><div class="form-group required-wrap"><label for="required-frmb-1621537703850-fld-1">Required</label><div class="input-wrap"><input type="checkbox" class="fld-required" name="required" id="required-frmb-1621537703850-fld-1"></div></div><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537703850-fld-1">Label</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537703850-fld-1" contenteditable="true">Select</input></div></div><div class="form-group description-wrap" style="display: block"><label for="description-frmb-1621537703850-fld-1">Help Text</label><div class="input-wrap"><input name="description" placeholder="" class="fld-description form-control" id="description-frmb-1621537703850-fld-1" value="" type="text"></div></div><div class="form-group placeholder-wrap" style="display: block"><label for="placeholder-frmb-1621537703850-fld-1">Placeholder</label><div class="input-wrap"><input name="placeholder" placeholder="" class="fld-placeholder form-control" id="placeholder-frmb-1621537703850-fld-1" value="" type="text"></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537703850-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537703850-fld-1" value="form-control" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621537703850-fld-1">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621537703850-fld-1" value="select-1621537707374" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537703850-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537703850-fld-1"> <label for="access-frmb-1621537703850-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537703850-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537703850-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><div class="form-group multiple-wrap"><label for="multiple-frmb-1621537703850-fld-1"> </label><div class="input-wrap"><input type="checkbox" class="fld-multiple" name="multiple" id="multiple-frmb-1621537703850-fld-1"> <label for="multiple-frmb-1621537703850-fld-1">Allow Multiple Selections</label></div></div><div class="form-group field-options"><label class="false-label">Options</label><div class="sortable-options-wrap">\
														<ol class="sortable-options ui-sortable id_el-'+id_el+'">\
															<li class="ui-sortable-handle"><input value="true" type="radio" checked="true" data-attr="selected" class="option-selected option-attr"><input  data-target="'+id_opt1+'" value="Option 1" type="text" placeholder="" data-attr="label" class="option-label option-attr"><input value="Option 1" type="text" placeholder="" data-attr="value" class="option-value option-attr"><a class="remove btn formbuilder-icon-cancel" title="Remove Element"></a></li>\
															<li class="ui-sortable-handle"><input value="false" type="radio" data-attr="selected" class="option-selected option-attr"><input data-target="'+id_opt2+'" value="Option 2" type="text" placeholder="" data-attr="label" class="option-label option-attr"><input value="Option 2" type="text" placeholder="" data-attr="value" class="option-value option-attr"><a class="remove btn formbuilder-icon-cancel" title="Remove Element"></a></li>\
														</ol><div class="option-actions"><a class="add add-opt">Add Option +</a></div></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
									}
								}
								if(sel_control.includes("Radio Group") || sel_control.includes("Radio-group")) {
									
									var id_opt1 = makeid(10);
									var id_opt2 = makeid(10);
									
									if (json_data_m_k !== null && typeof json_data_m_k.html !== "undefined" ) {
										<?php //edit ?>
										var id_el = json_data_m_k.id_el;
										data_form[row][col] = {
																'type' : "Radio-group",
																'id_el' : json_data_m_k.id_el
															  };
										content = '<div id="frmb-1621537735779-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537735779" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="radio-group-field form-field" type="radio-group" id="frmb-1621537735779-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537735779-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537735779-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537735779-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Radio Group</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder" style=""><div class="formbuilder-radio-group form-group field-radio-group-1621537741939-preview"><label for="radio-group-1621537741939-preview" class="formbuilder-radio-group-label">Radio Group</label>\
													<div class="radio-group radio-group-'+id_el+'">\
														'+decodeHtml(json_data_m_k.html)+'\
													</div></div></div><div id="frmb-1621537735779-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537735779-fld-1" style="display: none;"><div class="form-elements"><div class="form-group required-wrap"><label for="required-frmb-1621537735779-fld-1">Required</label><div class="input-wrap"><input type="checkbox" class="fld-required" name="required" id="required-frmb-1621537735779-fld-1"></div></div><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537735779-fld-1">Label</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537735779-fld-1" contenteditable="true">Radio Group</input></div></div><div class="form-group description-wrap" style="display: block"><label for="description-frmb-1621537735779-fld-1">Help Text</label><div class="input-wrap"><input name="description" placeholder="" class="fld-description form-control" id="description-frmb-1621537735779-fld-1" value="" type="text"></div></div><div class="form-group inline-wrap"><label for="inline-frmb-1621537735779-fld-1">Inline</label><div class="input-wrap"><input type="checkbox" class="fld-inline" name="inline" id="inline-frmb-1621537735779-fld-1"> <label for="inline-frmb-1621537735779-fld-1">Display radio inline</label></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537735779-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537735779-fld-1" value="" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621537735779-fld-1">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621537735779-fld-1" value="radio-group-1621537741939" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537735779-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537735779-fld-1"> <label for="access-frmb-1621537735779-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537735779-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537735779-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><div class="form-group other-wrap"><label for="other-frmb-1621537735779-fld-1">Enable "Other"</label><div class="input-wrap"><input type="checkbox" class="fld-other" name="other" id="other-frmb-1621537735779-fld-1"> <label for="other-frmb-1621537735779-fld-1">Let users enter an unlisted option</label></div></div><div class="form-group field-options"><label class="false-label">Options</label><div class="sortable-options-wrap">\
												<ol class="sortable-options ui-sortable id_el-'+id_el+'">\
													'+decodeHtml(json_data_m_k.html_edit)+'\
												</ol><div class="option-actions"><a class="add add-opt">Add Option +</a></div></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
									} else {
										<?php //create ?>
										var id_el = makeid(10);
										data_form[row][col] = {
																'type' : "Radio-group",
																'id_el' : id_el
															  };
										content = '<div id="frmb-1621537735779-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537735779" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="radio-group-field form-field" type="radio-group" id="frmb-1621537735779-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537735779-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537735779-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537735779-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Radio Group</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder" style=""><div class="formbuilder-radio-group form-group field-radio-group-1621537741939-preview"><label for="radio-group-1621537741939-preview" class="formbuilder-radio-group-label">Radio Group</label>\
													<div class="radio-group radio-group-'+id_el+'">\
														<div class="formbuilder-radio">\
															<input name="radio-group-'+id_el+'-preview" class="" id="radio-group-'+id_opt1+'" value="Option 1" type="radio">\
															<label id="label-'+id_opt1+'" for="radio-group-1621537741939-preview-0">Option 1</label>\
														</div>\
														<div class="formbuilder-radio ">\
															<input name="radio-group-'+id_el+'-preview" class="" id="radio-group-'+id_opt2+'" value="Option 2" type="radio">\
															<label id="label-'+id_opt2+'"  for="radio-group-1621537741939-preview-1">Option 2</label>\
														</div>\
													</div></div></div><div id="frmb-1621537735779-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537735779-fld-1" style="display: none;"><div class="form-elements"><div class="form-group required-wrap"><label for="required-frmb-1621537735779-fld-1">Required</label><div class="input-wrap"><input type="checkbox" class="fld-required" name="required" id="required-frmb-1621537735779-fld-1"></div></div><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537735779-fld-1">Label</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537735779-fld-1" contenteditable="true">Radio Group</input></div></div><div class="form-group description-wrap" style="display: block"><label for="description-frmb-1621537735779-fld-1">Help Text</label><div class="input-wrap"><input name="description" placeholder="" class="fld-description form-control" id="description-frmb-1621537735779-fld-1" value="" type="text"></div></div><div class="form-group inline-wrap"><label for="inline-frmb-1621537735779-fld-1">Inline</label><div class="input-wrap"><input type="checkbox" class="fld-inline" name="inline" id="inline-frmb-1621537735779-fld-1"> <label for="inline-frmb-1621537735779-fld-1">Display radio inline</label></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537735779-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537735779-fld-1" value="" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621537735779-fld-1">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621537735779-fld-1" value="radio-group-1621537741939" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537735779-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537735779-fld-1"> <label for="access-frmb-1621537735779-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537735779-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537735779-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><div class="form-group other-wrap"><label for="other-frmb-1621537735779-fld-1">Enable "Other"</label><div class="input-wrap"><input type="checkbox" class="fld-other" name="other" id="other-frmb-1621537735779-fld-1"> <label for="other-frmb-1621537735779-fld-1">Let users enter an unlisted option</label></div></div><div class="form-group field-options"><label class="false-label">Options</label><div class="sortable-options-wrap">\
												<ol class="sortable-options ui-sortable id_el-'+id_el+'">\
													<li class="ui-sortable-handle"><input value="false" type="radio" data-attr="selected" class="option-selected option-attr"><input  data-target="'+id_opt1+'" value="Option 1" type="text" placeholder="" data-attr="label" class="option-label option-attr"><input value="option-1" type="text" placeholder="" data-attr="value" class="option-value option-attr"><a class="remove btn formbuilder-icon-cancel" title="Remove Element"></a></li>\
													<li class="ui-sortable-handle"><input value="false" type="radio" data-attr="selected" class="option-selected option-attr"><input  data-target="'+id_opt2+'" value="Option 2" type="text" placeholder="" data-attr="label" class="option-label option-attr"><input value="option-2" type="text" placeholder="" data-attr="value" class="option-value option-attr"><a class="remove btn formbuilder-icon-cancel" title="Remove Element"></a></li>\
												</ol><div class="option-actions"><a class="add add-opt">Add Option +</a></div></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
									}
								}
								if(sel_control.includes("Checkbox Group") || sel_control.includes("Checkbox-group") ) {
									
									var id_opt1 = makeid(10);

									if (json_data_m_k !== null && typeof json_data_m_k.html !== "undefined" ) {
										<?php //edit ?>
										var id_el = json_data_m_k.id_el;
										console.log("checkb");
										console.log(decodeHtml(json_data_m_k.html_edit));
										data_form[row][col] = {
																'type' : "Checkbox-group",
																'id_el' : json_data_m_k.id_el
															  };
										content = '<div id="frmb-1621531272012-form-wrap" class="form-wrap form-builder " style=""><ul id="frmb-1621531272012" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="checkbox-group-field form-field editing" type="checkbox-group" id="frmb-1621531272012-fld-2"><div class="field-actions"><a type="remove" id="del_frmb-1621531272012-fld-2" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621531272012-fld-2-edit" class="toggle-form btn formbuilder-icon-pencil open" title="Edit"></a><a type="copy" id="frmb-1621531272012-fld-2-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Checkbox Group</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder" style="display: block;"><div class="formbuilder-checkbox-group form-group field-checkbox-group-1621531290438-preview"><label for="checkbox-group-1621531290438-preview" class="formbuilder-checkbox-group-label">Checkbox Group</label>\
													<div class="checkbox-group checkbox-group-'+id_el+'">\
														'+decodeHtml(json_data_m_k.html)+'\
													</div></div></div><div id="frmb-1621531272012-fld-2-holder" class="frm-holder" data-field-id="frmb-1621531272012-fld-2" style="display: none;"><div class="form-elements"><div class="form-group required-wrap"><label for="required-frmb-1621531272012-fld-2">Required</label><div class="input-wrap"><input type="checkbox" class="fld-required" name="required" id="required-frmb-1621531272012-fld-2"></div></div><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621531272012-fld-2">Label</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621531272012-fld-2" contenteditable="true">Checkbox Group</input></div></div><div class="form-group description-wrap" style="display: block"><label for="description-frmb-1621531272012-fld-2">Help Text</label><div class="input-wrap"><input name="description" placeholder="" class="fld-description form-control" id="description-frmb-1621531272012-fld-2" value="" type="text"></div></div><div class="form-group toggle-wrap"><label for="toggle-frmb-1621531272012-fld-2">Toggle</label><div class="input-wrap"><input type="checkbox" class="fld-toggle" name="toggle" id="toggle-frmb-1621531272012-fld-2"></div></div><div class="form-group inline-wrap"><label for="inline-frmb-1621531272012-fld-2">Inline</label><div class="input-wrap"><input type="checkbox" class="fld-inline" name="inline" id="inline-frmb-1621531272012-fld-2"> <label for="inline-frmb-1621531272012-fld-2">Display checkbox inline</label></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621531272012-fld-2">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621531272012-fld-2" value="" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621531272012-fld-2">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621531272012-fld-2" value="checkbox-group-1621531290438" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621531272012-fld-2">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621531272012-fld-2"> <label for="access-frmb-1621531272012-fld-2">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621531272012-fld-2-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621531272012-fld-2-roles-1" class="roles-field"> Administrator</label></div></div></div><div class="form-group other-wrap"><label for="other-frmb-1621531272012-fld-2">Enable "Other"</label><div class="input-wrap"><input type="checkbox" class="fld-other" name="other" id="other-frmb-1621531272012-fld-2"> <label for="other-frmb-1621531272012-fld-2">Let users enter an unlisted option</label></div></div><div class="form-group field-options"><label class="false-label">Options</label><div class="sortable-options-wrap">\
													<ol class="sortable-options ui-sortable id_el-'+id_el+'">\
														'+decodeHtml(json_data_m_k.html_edit)+'\
													</ol><div class="option-actions"><a class="add add-opt">Add Option +</a></div></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
									} else {
										<?php //create ?>
										var id_el = makeid(10);
										data_form[row][col] = {
																'type' : "Checkbox-group",
																'id_el' : id_el
															  };
										content = '<div id="frmb-1621531272012-form-wrap" class="form-wrap form-builder " style=""><ul id="frmb-1621531272012" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="checkbox-group-field form-field editing" type="checkbox-group" id="frmb-1621531272012-fld-2"><div class="field-actions"><a type="remove" id="del_frmb-1621531272012-fld-2" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621531272012-fld-2-edit" class="toggle-form btn formbuilder-icon-pencil open" title="Edit"></a><a type="copy" id="frmb-1621531272012-fld-2-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Checkbox Group</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder" style="display: block;"><div class="formbuilder-checkbox-group form-group field-checkbox-group-1621531290438-preview"><label for="checkbox-group-1621531290438-preview" class="formbuilder-checkbox-group-label">Checkbox Group</label>\
													<div class="checkbox-group checkbox-group-'+id_el+'">\
														<div class="formbuilder-checkbox ">\
															<input name="checkbox-group-1621531290438-preview[]" class="" id="checkbox-group-1621531290438-preview-0" value="option-1" type="checkbox" checked="checked">\
															<label id="label-'+id_opt1+'" for="checkbox-group-1621531290438-preview-0">Option 1</label>\
														</div>\
													</div></div></div><div id="frmb-1621531272012-fld-2-holder" class="frm-holder" data-field-id="frmb-1621531272012-fld-2" style="display: none;"><div class="form-elements"><div class="form-group required-wrap"><label for="required-frmb-1621531272012-fld-2">Required</label><div class="input-wrap"><input type="checkbox" class="fld-required" name="required" id="required-frmb-1621531272012-fld-2"></div></div><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621531272012-fld-2">Label</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621531272012-fld-2" contenteditable="true">Checkbox Group</input></div></div><div class="form-group description-wrap" style="display: block"><label for="description-frmb-1621531272012-fld-2">Help Text</label><div class="input-wrap"><input name="description" placeholder="" class="fld-description form-control" id="description-frmb-1621531272012-fld-2" value="" type="text"></div></div><div class="form-group toggle-wrap"><label for="toggle-frmb-1621531272012-fld-2">Toggle</label><div class="input-wrap"><input type="checkbox" class="fld-toggle" name="toggle" id="toggle-frmb-1621531272012-fld-2"></div></div><div class="form-group inline-wrap"><label for="inline-frmb-1621531272012-fld-2">Inline</label><div class="input-wrap"><input type="checkbox" class="fld-inline" name="inline" id="inline-frmb-1621531272012-fld-2"> <label for="inline-frmb-1621531272012-fld-2">Display checkbox inline</label></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621531272012-fld-2">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621531272012-fld-2" value="" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621531272012-fld-2">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621531272012-fld-2" value="checkbox-group-1621531290438" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621531272012-fld-2">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621531272012-fld-2"> <label for="access-frmb-1621531272012-fld-2">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621531272012-fld-2-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621531272012-fld-2-roles-1" class="roles-field"> Administrator</label></div></div></div><div class="form-group other-wrap"><label for="other-frmb-1621531272012-fld-2">Enable "Other"</label><div class="input-wrap"><input type="checkbox" class="fld-other" name="other" id="other-frmb-1621531272012-fld-2"> <label for="other-frmb-1621531272012-fld-2">Let users enter an unlisted option</label></div></div><div class="form-group field-options"><label class="false-label">Options</label><div class="sortable-options-wrap">\
													<ol class="sortable-options ui-sortable id_el-'+id_el+'">\
														<li class="ui-sortable-handle">\
															<input value="true" type="checkbox" checked="true" data-attr="selected" class="option-selected option-attr">\
															<input  data-target="'+id_opt1+'"  value="Option 1" type="text" placeholder="" data-attr="label" class="option-label option-attr"><input value="option-1" type="text" placeholder="" data-attr="value" class="option-value option-attr"><a class="remove btn formbuilder-icon-cancel" title="Remove Element"></a>\
														</li>\
													</ol><div class="option-actions"><a class="add add-opt">Add Option +</a></div></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
									}
								}
								if(sel_control.includes("File Upload") || sel_control.includes("File")) {
									data_form[row][col] = {'type' : "File"};
									content = '<div id="frmb-1621537836507-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537836507" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="file-field form-field" type="file" id="frmb-1621537836507-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537836507-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537836507-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537836507-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">File Upload</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder"><div class="formbuilder-file form-group field-file-1621537841763-preview"><label for="file-1621537841763-preview" class="formbuilder-file-label">File Upload</label><input class="form-control" name="file-1621537841763-preview" type="file" id="file-1621537841763-preview"></div></div><div id="frmb-1621537836507-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537836507-fld-1"><div class="form-elements"><div class="form-group required-wrap"><label for="required-frmb-1621537836507-fld-1">Required</label><div class="input-wrap"><input type="checkbox" class="fld-required" name="required" id="required-frmb-1621537836507-fld-1"></div></div><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537836507-fld-1">Label</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537836507-fld-1" contenteditable="true">File Upload</input></div></div><div class="form-group description-wrap" style="display: block"><label for="description-frmb-1621537836507-fld-1">Help Text</label><div class="input-wrap"><input name="description" placeholder="" class="fld-description form-control" id="description-frmb-1621537836507-fld-1" value="" type="text"></div></div><div class="form-group placeholder-wrap" style="display: block"><label for="placeholder-frmb-1621537836507-fld-1">Placeholder</label><div class="input-wrap"><input name="placeholder" placeholder="" class="fld-placeholder form-control" id="placeholder-frmb-1621537836507-fld-1" value="" type="text"></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537836507-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537836507-fld-1" value="form-control" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621537836507-fld-1">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621537836507-fld-1" value="file-1621537841763" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537836507-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537836507-fld-1"> <label for="access-frmb-1621537836507-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537836507-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537836507-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><div class="form-group subtype-wrap"><label for="subtype-frmb-1621537836507-fld-1">Type</label><div class="input-wrap"><select id="subtype-frmb-1621537836507-fld-1" name="subtype" class="fld-subtype form-control"><option label="File Upload" value="file">File Upload</option><option label="Fine Uploader" value="fineuploader">Fine Uploader</option></select></div></div><div class="form-group multiple-wrap"><label for="multiple-frmb-1621537836507-fld-1">Multiple Files</label><div class="input-wrap"><input type="checkbox" class="fld-multiple" name="multiple" id="multiple-frmb-1621537836507-fld-1"> <label for="multiple-frmb-1621537836507-fld-1">Allow users to upload multiple files</label></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
								}
								if(sel_control.includes("Text Area") || sel_control.includes("Textarea")) {
									data_form[row][col] = {'type' : "Textarea"};
									content = '<div id="frmb-1621537932382-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537932382" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="textarea-field form-field" type="textarea" id="frmb-1621537932382-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537932382-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537932382-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537932382-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Text Area</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder"><div class="formbuilder-textarea form-group field-textarea-1621537934804-preview"><label for="textarea-1621537934804-preview" class="formbuilder-textarea-label">Text Area</label><textarea class="form-control" name="textarea-1621537934804-preview" type="textarea" id="textarea-1621537934804-preview"></textarea></div></div><div id="frmb-1621537932382-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537932382-fld-1"><div class="form-elements"><div class="form-group required-wrap"><label for="required-frmb-1621537932382-fld-1">Required</label><div class="input-wrap"><input type="checkbox" class="fld-required" name="required" id="required-frmb-1621537932382-fld-1"></div></div><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537932382-fld-1">Label</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537932382-fld-1" contenteditable="true">Text Area</input></div></div><div class="form-group description-wrap" style="display: block"><label for="description-frmb-1621537932382-fld-1">Help Text</label><div class="input-wrap"><input name="description" placeholder="" class="fld-description form-control" id="description-frmb-1621537932382-fld-1" value="" type="text"></div></div><div class="form-group placeholder-wrap" style="display: block"><label for="placeholder-frmb-1621537932382-fld-1">Placeholder</label><div class="input-wrap"><input name="placeholder" placeholder="" class="fld-placeholder form-control" id="placeholder-frmb-1621537932382-fld-1" value="" type="text"></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537932382-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537932382-fld-1" value="form-control" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621537932382-fld-1">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621537932382-fld-1" value="textarea-1621537934804" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537932382-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537932382-fld-1"> <label for="access-frmb-1621537932382-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537932382-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537932382-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><div class="form-group value-wrap" style="display: undefined"><label for="value-frmb-1621537932382-fld-1">Value</label><div class="input-wrap"><input name="value" placeholder="Value" class="fld-value form-control" id="value-frmb-1621537932382-fld-1" value="" type="text"></div></div><div class="form-group subtype-wrap"><label for="subtype-frmb-1621537932382-fld-1">Type</label><div class="input-wrap"><select id="subtype-frmb-1621537932382-fld-1" name="subtype" class="fld-subtype form-control"><option label="Text Area" value="textarea">Text Area</option><option label="tinymce" value="tinymce">tinymce</option><option label="quill" value="quill">quill</option></select></div></div><div class="form-group maxlength-wrap"><label for="maxlength-frmb-1621537932382-fld-1">Max Length</label><div class="input-wrap"><input type="number" name="maxlength" class="fld-maxlength form-control form-control" id="maxlength-frmb-1621537932382-fld-1"></div></div><div class="form-group rows-wrap"><label for="rows-frmb-1621537932382-fld-1">Rows</label><div class="input-wrap"><input type="number" name="rows" class="fld-rows form-control form-control" id="rows-frmb-1621537932382-fld-1"></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
								}
								if(sel_control.includes("Date Field") || sel_control.includes("Date")) {
									data_form[row][col] = {'type' : "Date"};
									content = '<div id="frmb-1621537867801-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537867801" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="date-field form-field" type="date" id="frmb-1621537867801-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537867801-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537867801-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537867801-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Date Field</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder"><div class="formbuilder-date form-group field-date-1621537870649-preview"><label for="date-1621537870649-preview" class="formbuilder-date-label">Date Field</label><input class="form-control" name="date-1621537870649-preview" type="date" id="date-1621537870649-preview"></div></div><div id="frmb-1621537867801-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537867801-fld-1"><div class="form-elements"><div class="form-group required-wrap"><label for="required-frmb-1621537867801-fld-1">Required</label><div class="input-wrap"><input type="checkbox" class="fld-required" name="required" id="required-frmb-1621537867801-fld-1"></div></div><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537867801-fld-1">Label</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537867801-fld-1" contenteditable="true">Date Field</input></div></div><div class="form-group description-wrap" style="display: block"><label for="description-frmb-1621537867801-fld-1">Help Text</label><div class="input-wrap"><input name="description" placeholder="" class="fld-description form-control" id="description-frmb-1621537867801-fld-1" value="" type="text"></div></div><div class="form-group placeholder-wrap" style="display: block"><label for="placeholder-frmb-1621537867801-fld-1">Placeholder</label><div class="input-wrap"><input name="placeholder" placeholder="" class="fld-placeholder form-control" id="placeholder-frmb-1621537867801-fld-1" value="" type="text"></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537867801-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537867801-fld-1" value="form-control" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621537867801-fld-1">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621537867801-fld-1" value="date-1621537870649" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537867801-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537867801-fld-1"> <label for="access-frmb-1621537867801-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537867801-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537867801-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><div class="form-group value-wrap" style="display: undefined"><label for="value-frmb-1621537867801-fld-1">Value</label><div class="input-wrap"><input name="value" placeholder="Value" class="fld-value form-control" id="value-frmb-1621537867801-fld-1" value="" type="text"></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
								}
								if(sel_control.includes("Number")) {
									data_form[row][col] = {'type' : "Number"};
									content = '<div id="frmb-1621537899547-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537899547" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="number-field form-field" type="number" id="frmb-1621537899547-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537899547-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537899547-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537899547-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Number</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder"><div class="formbuilder-number form-group field-number-1621537903607-preview"><label for="number-1621537903607-preview" class="formbuilder-number-label">Number</label><input class="form-control" name="number-1621537903607-preview" type="number" id="number-1621537903607-preview"></div></div><div id="frmb-1621537899547-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537899547-fld-1"><div class="form-elements"><div class="form-group required-wrap"><label for="required-frmb-1621537899547-fld-1">Required</label><div class="input-wrap"><input type="checkbox" class="fld-required" name="required" id="required-frmb-1621537899547-fld-1"></div></div><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537899547-fld-1">Label</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537899547-fld-1" contenteditable="true">Number</input></div></div><div class="form-group description-wrap" style="display: block"><label for="description-frmb-1621537899547-fld-1">Help Text</label><div class="input-wrap"><input name="description" placeholder="" class="fld-description form-control" id="description-frmb-1621537899547-fld-1" value="" type="text"></div></div><div class="form-group placeholder-wrap" style="display: block"><label for="placeholder-frmb-1621537899547-fld-1">Placeholder</label><div class="input-wrap"><input name="placeholder" placeholder="" class="fld-placeholder form-control" id="placeholder-frmb-1621537899547-fld-1" value="" type="text"></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537899547-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537899547-fld-1" value="form-control" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621537899547-fld-1">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621537899547-fld-1" value="number-1621537903607" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537899547-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537899547-fld-1"> <label for="access-frmb-1621537899547-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537899547-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537899547-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><div class="form-group value-wrap" style="display: undefined"><label for="value-frmb-1621537899547-fld-1">Value</label><div class="input-wrap"><input name="value" placeholder="Value" class="fld-value form-control" id="value-frmb-1621537899547-fld-1" value="" type="text"></div></div><div class="form-group min-wrap"><label for="min-frmb-1621537899547-fld-1">min</label><div class="input-wrap"><input type="number" name="min" class="fld-min form-control form-control" id="min-frmb-1621537899547-fld-1"></div></div><div class="form-group max-wrap"><label for="max-frmb-1621537899547-fld-1">max</label><div class="input-wrap"><input type="number" name="max" class="fld-max form-control form-control" id="max-frmb-1621537899547-fld-1"></div></div><div class="form-group step-wrap"><label for="step-frmb-1621537899547-fld-1">step</label><div class="input-wrap"><input type="number" name="step" class="fld-step form-control form-control" id="step-frmb-1621537899547-fld-1"></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
								}
								if(sel_control.includes("Autocomplete")) {
									data_form[row][col] = {
															'type' : "Autocomplete"
														  };
									content = '<div id="frmb-1621537516911-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537516911" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="autocomplete-field form-field" type="autocomplete" id="frmb-1621537516911-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537516911-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537516911-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537516911-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Autocomplete</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder" style=""><div class="formbuilder-autocomplete form-group field-autocomplete-1621537554272-preview"><label for="autocomplete-1621537554272-preview" class="formbuilder-autocomplete-label">Autocomplete</label><input class="form-control" id="autocomplete-1621537554272-preview-input" autocomplete="off"><input class="form-control" name="autocomplete-1621537554272-preview" id="autocomplete-1621537554272-preview" type="hidden"><ul id="autocomplete-1621537554272-preview-list" class="formbuilder-autocomplete-list"><li value="option-1">Option 1</li><li value="option-2">Option 2</li><li value="option-3">Option 3</li></ul></div></div><div id="frmb-1621537516911-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537516911-fld-1" style="display: none;"><div class="form-elements"><div class="form-group required-wrap"><label for="required-frmb-1621537516911-fld-1">Required</label><div class="input-wrap"><input type="checkbox" class="fld-required" name="required" id="required-frmb-1621537516911-fld-1"></div></div><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537516911-fld-1">Label</label><div class="input-wrap"><input name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537516911-fld-1" contenteditable="true">Autocomplete</input></div></div><div class="form-group description-wrap" style="display: block"><label for="description-frmb-1621537516911-fld-1">Help Text</label><div class="input-wrap"><input name="description" placeholder="" class="fld-description form-control" id="description-frmb-1621537516911-fld-1" value="" type="text"></div></div><div class="form-group placeholder-wrap" style="display: block"><label for="placeholder-frmb-1621537516911-fld-1">Placeholder</label><div class="input-wrap"><input name="placeholder" placeholder="" class="fld-placeholder form-control" id="placeholder-frmb-1621537516911-fld-1" value="" type="text"></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537516911-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537516911-fld-1" value="form-control" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621537516911-fld-1">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621537516911-fld-1" value="autocomplete-1621537554272" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537516911-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537516911-fld-1"> <label for="access-frmb-1621537516911-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537516911-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537516911-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><div class="form-group field-options"><label class="false-label">Options</label><div class="sortable-options-wrap"><ol class="sortable-options ui-sortable"><li class="ui-sortable-handle"><input value="true" type="radio" checked="true" data-attr="selected" class="option-selected option-attr"><input value="Option 1" type="text" placeholder="" data-attr="label" class="option-label option-attr"><input value="option-1" type="text" placeholder="" data-attr="value" class="option-value option-attr"><a class="remove btn formbuilder-icon-cancel" title="Remove Element"></a></li><li class="ui-sortable-handle"><input value="false" type="radio" data-attr="selected" class="option-selected option-attr"><input value="Option 2" type="text" placeholder="" data-attr="label" class="option-label option-attr"><input value="option-2" type="text" placeholder="" data-attr="value" class="option-value option-attr"><a class="remove btn formbuilder-icon-cancel" title="Remove Element"></a></li><li class="ui-sortable-handle"><input value="false" type="radio" data-attr="selected" class="option-selected option-attr"><input value="Option 3" type="text" placeholder="" data-attr="label" class="option-label option-attr"><input value="option-3" type="text" placeholder="" data-attr="value" class="option-value option-attr"><a class="remove btn formbuilder-icon-cancel" title="Remove Element"></a></li></ol><div class="option-actions"><a class="add add-opt">Add Option +</a></div></div></div><div class="form-group requireValidOption-wrap"><label for="requireValidOption-frmb-1621537516911-fld-1"> </label><div class="input-wrap"><input type="checkbox" class="fld-requireValidOption" name="requireValidOption" id="requireValidOption-frmb-1621537516911-fld-1"> <label for="requireValidOption-frmb-1621537516911-fld-1">Only accept a pre-defined Option</label></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
								}
								
								if(sel_control.includes("Button")) {
									content = '<div id="frmb-1621537415593-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537415593" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="button-field form-field" type="button" id="frmb-1621537415593-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537415593-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537415593-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537415593-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Button</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder"><div class="formbuilder-button form-group field-button-1621537419457-preview"><button name="button-1621537419457-preview" type="button" class="btn-default btn" id="button-1621537419457-preview">Button</button></div></div><div id="frmb-1621537415593-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537415593-fld-1"><div class="form-elements"><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537415593-fld-1">Label</label><div class="input-wrap"><div name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537415593-fld-1" contenteditable="true">Button</div></div></div><div class="form-group subtype-wrap"><label for="subtype-frmb-1621537415593-fld-1">Type</label><div class="input-wrap"><select id="subtype-frmb-1621537415593-fld-1" name="subtype" class="fld-subtype form-control"><option label="Button" value="button">Button</option><option label="submit" value="submit">submit</option><option label="reset" value="reset">reset</option></select></div></div><div class="form-group style-wrap"><label>Style</label><input value="default" type="hidden" class="btn-style"><div class="btn-group" role="group"><button value="default" type="button" class="btn-xs btn btn-default">Default</button><button value="danger" type="button" class="btn-xs btn btn-danger">Danger</button><button value="info" type="button" class="btn-xs btn btn-info">Info</button><button value="primary" type="button" class="btn-xs btn btn-primary">Primary</button><button value="success" type="button" class="btn-xs btn btn-success">Success</button><button value="warning" type="button" class="btn-xs btn btn-warning">Warning</button></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537415593-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537415593-fld-1" value="" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621537415593-fld-1">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621537415593-fld-1" value="button-1621537419457" type="text"></div></div><div class="form-group value-wrap" style="display: undefined"><label for="value-frmb-1621537415593-fld-1">Value</label><div class="input-wrap"><input name="value" placeholder="Value" class="fld-value form-control" id="value-frmb-1621537415593-fld-1" value="" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537415593-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537415593-fld-1"> <label for="access-frmb-1621537415593-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537415593-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537415593-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><a class="close-field">Close</a></div></div></li></ul></div>';
								}
								
								if(content == "") {
									//object
									console.log(data_source);
									//content = $(ui.draggable).html();
									data_form[row][col] = {
															'type' : "Dropdown",
															'data_source' : data_source
														  };
									//data_form[row][col].tambah = "oke";		  
									
									content = '<div id="frmb-1621537415593-form-wrap" class="form-wrap form-builder "><ul id="frmb-1621537415593" class="frmb stage-wrap pull-left ui-sortable" data-content="Drag a field from the right to this area" style="min-height: 837px;"><li class="dropdown-field form-field" type="button" id="frmb-1621537415593-fld-1"><div class="field-actions"><a type="remove" id="del_frmb-1621537415593-fld-1" class="del-button btn formbuilder-icon-cancel delete-confirm" title="Remove Element"></a><a type="edit" id="frmb-1621537415593-fld-1-edit" class="toggle-form btn formbuilder-icon-pencil" title="Edit"></a><a type="copy" id="frmb-1621537415593-fld-1-copy" class="copy-button btn formbuilder-icon-copy" title="Copy"></a></div><label class="field-label">Dropdown</label><span class="required-asterisk" style=""> *</span><span class="tooltip-element" tooltip="undefined" style="display:none">?</span><div class="prev-holder"><div class="formbuilder-button form-group field-button-1621537419457-preview">\
													<div class="form-group row">\
													<select style="width:100%" class="select2formbuilder  target-'+data_source+'  form-control  ">\
													</select>\
													</div>\
												</div></div><div id="frmb-1621537415593-fld-1-holder" class="frm-holder" data-field-id="frmb-1621537415593-fld-1"><div class="form-elements"><div class="form-group label-wrap" style="display: block"><label for="label-frmb-1621537415593-fld-1">Label</label><div class="input-wrap"><div name="label" placeholder="Label" class="fld-label form-control" id="label-frmb-1621537415593-fld-1" contenteditable="true">Button</div></div></div><div class="form-group subtype-wrap"><label for="subtype-frmb-1621537415593-fld-1">Type</label><div class="input-wrap"><select id="subtype-frmb-1621537415593-fld-1" name="subtype" class="fld-subtype form-control"><option label="Button" value="button">Button</option><option label="submit" value="submit">submit</option><option label="reset" value="reset">reset</option></select></div></div><div class="form-group style-wrap"><label>Style</label><input value="default" type="hidden" class="btn-style"><div class="btn-group" role="group"><button value="default" type="button" class="btn-xs btn btn-default">Default</button><button value="danger" type="button" class="btn-xs btn btn-danger">Danger</button><button value="info" type="button" class="btn-xs btn btn-info">Info</button><button value="primary" type="button" class="btn-xs btn btn-primary">Primary</button><button value="success" type="button" class="btn-xs btn btn-success">Success</button><button value="warning" type="button" class="btn-xs btn btn-warning">Warning</button></div></div><div class="form-group className-wrap" style="display: block"><label for="className-frmb-1621537415593-fld-1">Class</label><div class="input-wrap"><input name="className" placeholder="space separated classes" class="fld-className form-control" id="className-frmb-1621537415593-fld-1" value="" type="text"></div></div><div class="form-group name-wrap" style="display: block"><label for="name-frmb-1621537415593-fld-1">Name</label><div class="input-wrap"><input name="name" placeholder="" class="fld-name form-control" id="name-frmb-1621537415593-fld-1" value="button-1621537419457" type="text"></div></div><div class="form-group value-wrap" style="display: undefined"><label for="value-frmb-1621537415593-fld-1">Value</label><div class="input-wrap"><input name="value" placeholder="Value" class="fld-value form-control" id="value-frmb-1621537415593-fld-1" value="" type="text"></div></div><div class="form-group access-wrap"><label for="access-frmb-1621537415593-fld-1">Access</label><div class="input-wrap"><input type="checkbox" class="fld-access" name="access" id="access-frmb-1621537415593-fld-1"> <label for="access-frmb-1621537415593-fld-1">Limit access to one or more of the following roles:</label><div class="available-roles"><label for="fld-frmb-1621537415593-fld-1-roles-1"><input type="checkbox" name="roles[]" value="1" id="fld-frmb-1621537415593-fld-1-roles-1" class="roles-field"> Administrator</label></div></div></div><a class="close-field">Close</a></div></div></li></ul></div>\
												';
										setTimeout(function() {
											$.ajax({
												type: "GET",
												url: '{{URL("/")}}/select2list/'+data_source.toLowerCase(),
												//data: data_input,
												//dataType: 'json',
												success: function(data){
													console.log(data);
													console.log('yaaaaa'+'.target-'+data_source);
													var obj = JSON.parse(data);
													
													for (let i = 0; i < obj.length; i++) {
														console.log("yyyy");
														console.log(obj[i]);
														//text += obj[i] + "<br>";
														$('.target-'+data_source).append("<option value='"+obj[i].id+"'>"+obj[i].text+"</option>");
													}
													$('.target-'+data_source).select2();
												},
												error: function(){console.log("error");}
											});
										}, 100);

								}
								content.replaceAll("frmb","");
								return content;	
			}
			function makeid(length) {
				var result           = '';
				var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
				var charactersLength = characters.length;
				for ( var i = 0; i < length; i++ ) {
				  result += characters.charAt(Math.floor(Math.random() * 
			 charactersLength));
			   }
			   return result;
			}
			function encodeHtml(str) {
				return str.replaceAll('"', '#$%');  
			}
			function decodeHtml(str) {
				return str.replaceAll('#$%','"');  
			}

    jQuery(function($) {
        setTimeout(function() {
            // Your code here
            $('.form-actions').hide();

			//automatic add -Select- to every select control when rendered
			$('.formbuilder-select select').prepend('<option value="-"  selected="true" >- Select -</option>')

        }, 2000);
		<?php 
		if(!empty($form_builder)) {
			$obj_from_json = json_decode($form_builder->json);
			$new_list_obj = [];
			foreach($obj_from_json as $obj) {
				if(!empty($obj->label)) {
					$obj->label = strip_tags($obj->label ?? "");
					$obj->label = str_replace('\n','',$obj->label);
				}
				$new_list_obj[] = $obj;
			}
			$form_builder->json = json_encode($new_list_obj);
		}
		?>
        @if (isset($form_builder->json))
        const formData = '<?=htmlspecialchars_decode($form_builder->json)?>';
        formBuilder = $(document.getElementById('fb-render')).formRender({dataType: 'json',formData: formData,fields, templates,controlOrder: list_data_source_js});  
        @endif
    });
    // validation form service
    $(function () {
        $("input[type=radio][name=request_for]").on("invalid", function () {
            this.setCustomValidity("Please Choose One.");
        });
        $("input[name=title]").on("invalid", function () {
            this.setCustomValidity("Please enter at least 5 characters.");
        });
    });
    $(document).ready(function() {
		$('.select_on_behalf').removeAttr('required');
		$('.originator').hide()
        $('.request_for').on('click', function() {
            if ($(this).val() == 'myself') {
                $('.originator').hide()
                $('.select_on_behalf').removeAttr('required');
                
            } else {
                $('.originator').show()
                $('.select_on_behalf').attr('required','reuired');
            } 
        })
        $('.selectpicker').on('change', function() {
			console.log("change"+$('.selectpicker').val());
			 $(".target-asset").select2('destroy'); 
			 $('.target-asset').html("<option value=''>-Select Asset-</option>");

			$.ajax({
				type: "GET",
				url: '{{URL("/")}}/select2list/asset?contact_id='+$('.selectpicker').val(),
				//data: data_input,
				//dataType: 'json',
				success: function(data){
					console.log(data);
					console.log('yaaaaa'+'.target-'+data_source);
					var obj = JSON.parse(data);
					
					for (let i = 0; i < obj.length; i++) {
						console.log("yyyy");
						console.log(obj[i]);
						//text += obj[i] + "<br>";
						$('.target-asset').append("<option value='"+obj[i].id+"'>"+obj[i].text+"</option>");
					}
					$('.target-asset').select2();
				},
				error: function(){console.log("error");}
			});
        });
$('#form_submit').on('submit',(function(e) {
	

	
	KTApp.blockPage({overlayColor: '#000000',state: 'primary',message: 'Processing...'});
	e.preventDefault();
	var formData = new FormData(this);
    formData.append('description', $('#summernote').val());
	formData.append('_token', '{{csrf_token()}}');

	console.log("step1___");
	console.log($(this).attr('action'));
	//for (var value of formData.values()) {
	   //console.log(value);
	//}
	//return;
	$.ajax({
		type:'POST',
		url: $(this).attr('action'),
		data:formData,
		cache:false,
		contentType: false,
		processData: false,
		success:function(data){
			console.log("step2");
			KTApp.unblockPage();
			console.log("success");
			console.log(data);
			
			var obj = JSON.parse(data);
			if(obj.success) {

				if(obj.warning) {
					<?php 
						//ada warning kemungkinan saat agent on leave 
						//dari flow grp yang baru
					?>
					Swal.fire("Confirmation",obj.message,"warning")
					setTimeout(function() {
						window.location = obj.redirect;
					},10000);

				} else {

					Swal.fire("Confirmation",obj.message,"success")
					setTimeout(function() {
						window.location = obj.redirect;
					},3000);
					
				}
			} else {
				Swal.fire("Failed",obj.message,"error")
			}
			
		},
		error: function(data){
			console.log("step3");
			KTApp.unblockPage();
			Swal.fire("Failed","Sorry, failed to submit ticket","error")
			console.log("error");
			console.log(data);
			
		}
	});
	console.log("step4");
}));
$('.btn-draft').click(function() {
    Swal.fire({title: "Confirmation",text: "Are you sure want to save as Draft ?",icon: "question",showCancelButton: true,confirmButtonText: "Yes!"
    }).then(function(result) {
        if (result.value) {
			$('#submit_type').val("draft");
			$( "#form_submit" ).submit();
        }
    });
});
var fill = true;
$('.btn-submit').click(function() {
	fill = true;
	$( ':input[required]', $('#form_submit') ).each( function () {

		if($(this).val()) {
			//alert("ada");
			
		} else {
			fill = false;
			//alert("tidak ada");
		}
	});
	if(!fill) {
		<?php //ada yang belum diisi ?>
		 Swal.fire("Please fill all required fields","You have not fill all required fields. Please input the form completely", "warning");
	} else {
		Swal.fire({title: "Confirmation",text: "Please make sure the data you input is right, before you submit this request. Are you sure want to submit this Ticket ? ",icon: "question",showCancelButton: true,confirmButtonText: "Yes!"
		}).then(function(result) {
			if (result.value) {
				$('#submit_type').val("submit");
				$( "#form_submit" ).submit();
			}
		});
	}
});
		//$('.btn-submit').click(function() {
			//// your code here
			////alert(formBuilder.actions.getData('json'));
			//console.log(JSON.stringify(formBuilder.userData));
			//var datastring = $("#form_submit").serialize();
			//var datasubmit = datastring+"&form_data_json="+JSON.stringify(formBuilder.userData);
			//console.log(datasubmit);
			//$.ajax({
				//type: "POST",
				//url: $("#form_submit").attr("action"),
				//data: datasubmit,
				////dataType: "json",
				//success: function(data) {
					//console.log(data);
					////var obj=data;
					//var obj = JSON.parse(data); 
					//window.location = obj.redirect;
					//if(obj.success) {
						//window.location = obj.redirect;
						////console.log("s2");
					//} else {
						//window.location = obj.redirect;
					//}
				//},
				//error: function() {
					//alert('error handling here');
				//}
			//});

		//});
		
		
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
		
		
    })
    
    

</script>
<style>
.table-content-grid{
	width: auto;
}
.cell-element {
	width: fit-content !important;
	padding: 8px !important;
}
.frmb4 {
	padding-left:0 !important;
    width: fit-content !important;
}
.frmb4 li {
	list-style-position:outside	!important;
	
}
.frmb4 .form-wrap.form-builder li {
    list-style-position: outside !important;
    display: block !important;
}
.frmb4 .form-wrap.form-builder .stage-wrap {
    width: auto !important;
    min-height: auto !important;
}
.frmb4 .field-label,.frmb4 .required-asterisk,.frmb4 .formbuilder-text-label,
.frmb4 .formbuilder-radio-group-label,.frmb4 .formbuilder-checkbox-group-label,
.frmb4 .formbuilder-select-label,.frmb4 .formbuilder-date-label,.frmb4 .formbuilder-autocomplete-label,
.frmb4 .formbuilder-textarea-label,.frmb4 .formbuilder-number-label{
	display:none;
}
.frmb4 .form-builder {
    width: fit-content !important;
}
.frmb4 .form-control {
    max-width: 150px;
}
.frmb4 {
	margin-bottom: 0;
}
.frmb4 .form-group {
    margin-bottom: 0px !important;
}
</style>
  <style>
  .table-grid td {padding:0px;}
  .draggable2 { background:red;width: 100px; height: 50px; padding: 0.5em; float: left; margin: 10px 10px 10px 0; }
  .droppable2 { width: 100%; min-height:50px;height: 50px; padding: 0em; float: left; margin: 0px; }
  </style>
