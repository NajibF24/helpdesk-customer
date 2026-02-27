
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
			@include('email_content_ticket_left')
        </div>
        <div class="col-xl-4">
			@include('content_ticket')			
        </div>
    
    </div>
    <!--end::Row-->
</div>
<!--end::Container-->
