@extends('layouts.app')

@section('content')

<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">

			</div>
		</div>
	</div>

<script>
	Swal.fire({title: "Resolved Ticket Need Closed",text: "It's look like you have not close your Resolved Ticket. Please close all your resolved ticket, so you can create a new ticket ",icon: "warning",showCancelButton: true,confirmButtonText: "Yes!"
	}).then(function(result) {
		if (result.value) {
		}
	});

</script>
@endsection
