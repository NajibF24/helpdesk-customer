<?php 
$identifier =  rand(pow(10, 12-1), pow(10, 12)-1);
?>

<div class="modal fade" id="modal-{{$identifier}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
	<div class="modal-content modal-add-ajax">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Create {{t($type)}}</h5>
				<button type="button" class="btn-close close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body ">
				@include('adminlte-templates::common.errors')

					{!! Form::open(['route' => ['store_modal', $type, "modal" ], 'files' => true]) !!}
<script>
$( document ).ready(function() {
	$(".tab-content-other").hide();
});
</script>
					<div class="card-body">
								@include('crudmodal.menutab')
								<div class="row content-tab-home mt-3">
										
								@include('crudmodal.create_fields')
								<!-- Submit Field -->
								</div>
					</div>

					<div class="card-footer">
						<button type="button" data-select-target="{{$select_target}}" class="modal-submit btn btn-primary float-right" data-target="modal-{{$identifier}}">
							Add
						</button>
						<button type="button" class="btn btn-secondary float-right" data-dismiss="modal" data-bs-dismiss="modal" style="margin-right:10px;">Cancel</button>
					</div>

					{!! Form::close() !!}



			</div>
			<div class="modal-footer">

			</div>

	</div>
  </div>
</div>

<script>
$( document ).ready(function() {
    $('#modal-{{$identifier}}').modal('show')
});

</script>
