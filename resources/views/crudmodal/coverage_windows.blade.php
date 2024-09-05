
@extends('layouts.app')

@section('content')

<?php 
$type= "coverage_windows";
?>
<style>
.ui-timepicker-standard {
	z-index: 100000 !important;
}
table, th, td {
	border: 1px solid black;
}
th {
	padding-left: 30px;
	padding-right: 30px;
}
</style>

<section class="content-header" style="margin:10px">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-12">
				<h1>Create Coverage Windows</h1>
			</div>
		</div>
	</div>
</section>

<div class="content px-3">

	@include('adminlte-templates::common.errors')

	<div class="card">
		{!! Form::open(['route' => ['store', $type], 'files' => true]) !!}

		<div class="card-body">
			<div class="form-group col-sm-6">
				{!! Form::label('name', 'Name') !!}
				{!! Form::text('name', (!empty($row->name) ? $row->name : old('name')), 
																['class' => 'form-control  validate-hidden','maxlength' => 255,'maxlength' => 255,
																'data-rule-required' => '',
																'id' => 'name',
																"placeholder" => 'Name',
																'data-msg-required' => 'Required Fields',
				]) !!}
			</div>
			<div class="form-group col-sm-12">
				{!! Form::label('description', 'Description') !!}
				{!! Form::textarea('description', (!empty($row->description) ? $row->description : old('description')), ['class' => 		'form-control  validate-hidden',
					'data-rule-required' => '',
					'id' => 'description',
					"placeholder" => 'Description',
					'data-msg-required' => 'Required Fields',
					'rows'=>3,
				]) !!}	
			</div>
			<div class="content-windows form-group col-sm-12">
				{!! Form::label('ope_hours', 'Open Hours') !!}
				<table>
					<tr>
						<th></th>
						<?php 
							$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday', 'Sunday'];
							foreach($days as $d) {
								?><th><?php echo $d; ?></th><?php
							}
						?>
					</tr>
					<tr>
					<?php
						for($i=0;$i<24;$i++) {
						?>
						<tr class="row-hour">
							<td >
								@if($i>9) 
									{{$i}}:00
								@else
									0{{$i}}:00
								@endif
							</td>
							<?php 
								foreach ($days as $d):
							?>
							<td class="cell-ophour" data-half="0" data-day="{{$d}}" data-hour="{{$i}}" data-minute="00"></td>
							<?php
								endforeach;
							?>
						</tr>
						<tr class="row-hour">
							<td></td>
							<?php 
								foreach ($days as $d): 
							?>
							<td class="cell-ophour" data-half="1" data-day="{{$d}}" data-hour="{{$i}}" data-minute="30"></td>
							<?php
								endforeach;
							?>
						</tr>
						<?php } ?>
					</tr>
				</table>
            </div>
		</div>

		<div class="card-footer">
			{!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
			<a href="{!! route('list', [$type]) !!}" class="btn btn-default">Cancel</a>
		</div>

		{!! Form::close() !!}

	</div>
</div>
@endsection
@section('js')
<style>
.box {
	border: 1px solid #3a87ad;
	background: #3a87ad;
	border-radius:4px;
	
}
</style>

<script>
$( document ).ready(function() {
	$('.cell-ophour').click(function() {
		var hour = $(this).data('hour');
		var minute = $(this).data('minute');
		var day = $(this).data('day');
		var identifier = Math.floor(Math.random() * 10000000) + 1;
		var half_minute = $(this).data('half') === 1 ? twodigit(00) : 30;
		var half_hour = $(this).data('half') === 1 ? twodigit(hour+1) : twodigit(hour);

		if($(this).children().length > 0) {
			// $(this).children().remove();
			alert('halo');
		} else {
			$(this).append("<div class='box' draggable='true' data-day='"+day+"' data-hour='"+hour+"' data-minute='"+minute+"' id='box-"+day+"-"+twodigit(hour)+"-"+minute+"'></div>");
			$("#box-"+day+"-"+twodigit(hour)+"-"+minute+"").html(twodigit(hour)+":"+minute+"-"+half_hour+":"+half_minute);
		}
	});

	$('.box').on("dragstart", function (event) {
		var dt = event.originalEvent.dataTransfer;
		dt.setData('Text', $(this).attr('id'));
	});

	$('table td').on("dragenter dragover drop", function (event) {
		event.preventDefault();

		if (event.type === 'drop') {
			var data = event.originalEvent.dataTransfer.getData('Text',$(this).attr('id'));

			de = $("#box-Monday-00-00").detach();
			de.appendTo($(this));
		};
	});
});

function twodigit(val) {
	if(val<10) {
		return "0"+val;
	} else {
		return val;
	}
}
</script>

<style>
.row-hour {
	height:30px;
}
.container {
    width: 100%;
    height: 100%;
    margin: 0 auto;
    position: relative;
    
}
.A,  .Z {
    position: absolute;
    left: 0;
    width: 100%;
}
.A {
	top: 0;
    height: 100%;
	border-bottom: 5px solid #357ea2;
	background: #3a87ad;
	border-radius:4px;
}

.Z {
    top: 80%;
    height: 20%;
    background-color: none;
    cursor: move;
}
.A div {
    position: absolute;
    top: 0;
    height: 100%;
    text-align: center;
}
.info {
    text-align: center;
    line-height: 2em;
}
</style>
@endsection
