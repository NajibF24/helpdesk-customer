<?php 
Session::flash('message_expire', 'Your page session is expired. Please reinput your username password.'); 
?>

<script>
	window.location = '<?=Request::url()?>';
</script>
