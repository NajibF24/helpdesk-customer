@extends('layouts.app')

@section('content')

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-warning">
				{{$message}}
			</div>
		</div>
	</div>
</div>

<script>
let url = new URL(window.location.href);
let params = new URLSearchParams(url.search);
let target = params.get('target');

localStorage.setItem('target', target);
</script>

@endsection
