<style>
.label.label-primary {
    color: #FFFFFF;
    background-color: #3699FF;
}
.text-blue {
    color: blue;
    background-color: #C9F7F5;
}
.text-purple {
    color: purple;
    background-color: #C9F7F5;
}
.label-cyan {
    color: #008080;
    background-color: #00ffff;
}
.label-tomato {
    color: #FFFFFF;
    background-color: #FF6347;
}
.label-lime {
    color: #006400;
    background-color: #00FF00;
}

.label-yellow {
    color: #333300;
    background-color: #FFFF00;
}

.label-purple {
    color: #800080;
    background-color: #D8BFD8;
}

.label-orange {
    color: #000000;
    background-color: #FFA500;
}

.label-white {
    background-color: #FFFFFF;
}
</style>

<style>
.text-blue {
    color: blue;
    background-color: #C9F7F5;
}
.text-purple {
    color: purple;
    background-color: #C9F7F5;
}
</style>
<!--begin::List Widget 9-->
<div class="card card-custom   gutter-b">
	<!--begin::Header-->
	<div class="card-header align-items-center border-0 mt-4">
		<h3 class="card-title align-items-start flex-column">
			<span class="font-weight-bolder text-dark">Ticket Status</span>
		</h3>
	</div>
	<!--end::Header-->
	<!--begin::Body-->
	<div class="card-body pt-4">

                    <!--begin::Item-->
                    <div class="mb-6">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center">
                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Status</span>
                            </div>
                            <!--end::Text-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Desc-->
                        <?php 
                        //var_dump($ticket->request_management);
                        $color = "text-warning";
                        $label = "";
                        if ($ticket->status == "Open" || $ticket->status == "Re-Open") {
                            $color = "text-blue";
                            $label = "label-primary";
                        }
                        if($ticket->status == "Resolved") {
							$color = "text-purple";
                            $label = "label-purple  ";
						}
                        if($ticket->status == "Closed") {
							$color = "text-success";
                            $label = "label-lime";
						}
                        if($ticket->status == "Rejected") {
							$color = "text-danger";
                            $label = "label-tomato";
						}
                        if($ticket->status == "Submit for Approval") {
							//$color = "text-danger";
                            $label = "label-cyan";
						}
                        if($ticket->status == "On Progress") {
							$color = "text-warning";
                            $label = "label-yellow";
						}
                        ?>
                        <?= '<h5 class="label label-lg font-weight-bold '.$label.' label-inline">'.$ticket->status.'</h5>' ?>
                        <!--end::Desc-->
                    </div>
                    <!--end::Item-->
                    
                    <!--begin::Item-->
                    <div class="mb-5">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center">
                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Ticket Number </span>
                            </div>
                            <!--end::Text-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Desc-->
                        <p class="text-dark-50 m-0 pt-2 font-weight-normal">
							<?php 
							echo ticketNumber($ticket->id);
							?>
                        </p>
                        <!--end::Desc-->
                    </div>
                    <!--end::Item-->
                    
                    <!--begin::Item-->
                    <div class="mb-5">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center">
                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Next Approver </span>
                            </div>
                            <!--end::Text-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Desc-->
                        <div class="d-flex">
                            <p class="text-dark-50 m-0 pt-2 font-weight-normal mr-2">
                                <?php 
                                if(!empty($ticket->next_approval_id)) {
                                    $contact = DB::table('contact')
                                                    ->where('id',$ticket->next_approval_id)
                                                    ->first();
                                    echo $contact->name." ";
                                    if($ticket->approval_state == "appoval_support") {
                                        echo "(Approval Support)";
                                    } else {
                                        echo "(Approval User)";
                                    }
                                } else {
                                    echo "-";
                                }
                                ?>
    
                            </p>
                            @if (accessv('edit_next_approver', 'create') && $ticket->status == 'Submit for Approval' )
                            <button class="btn btn-primary btn-sm" id="btn-edit-nextapprover" data-toggle="tooltip" data-placement="top" title="Synchronize Next Approver to Case Journey">
                                <i class="flaticon2-refresh icon-sm pr-0"></i>
                            </button>
                            @endif
                        </div>
                        <!--end::Desc-->
                    </div>
                    <!--end::Item-->                    
                    
                    <!--begin::Item-->
                    <div class="mb-5">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center">
                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Assignment Tier / Agent </span>
                            </div>
                            <!--end::Text-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Desc-->
                        <p class="text-dark-50 m-0 pt-2 font-weight-normal">
							{{ $contact = DB::table('contact')->where('id', $ticket->team_id)->value('name') ?? '-' }}  
							/
							{{$contact = DB::table('contact')->where('id', $ticket->agent_id)->value('name') ?? '-' }}
                        
                        </p>
                        <!--end::Desc-->
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="mb-5">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center">
                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Assign Time</span>
                            </div>
                            <!--end::Text-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Desc-->
                        <p class="text-dark-50 m-0 pt-2 font-weight-normal">
							{{ empty($ticket->assign_time) ? "-" : date('M d, Y H:i', strtotime($ticket->assign_time))}}
                        
                        </p>
                        <!--end::Desc-->
                    </div>
                    <!--end::Item-->

                    <!--begin::Item-->
                    <div class="mb-3">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center">
                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Escalation Date </span>
                            </div>
                            <!--end::Text-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Desc-->
                        <p class="text-dark-50 m-0 pt-2 font-weight-normal">
                            <?php
                                $today = date("Y-m-d H:i:s");
                                $label_due = "label-white";
                                $is_need_label = false;
                                if ($ticket->escalation_date > $today)
                                {
                                    $sDuedate = date("Y-m-d", strtotime($ticket->escalation_date ));
                                    $sToday = date("Y-m-d", strtotime($today ));

                                    if ($sDuedate == $sToday)
                                    {
                                        $label_due = "label-orange";
                                        $is_need_label = true;
                                    }

                                }
                                else
                                {
                                    $label_due = "label-danger";
                                    $is_need_label = true;
                                }
                            ?>
							{{ empty($ticket->escalation_date) ? "-" : date('d M Y H:i', strtotime($ticket->escalation_date))}}
                        </>
                        </p>
                        <!--end::Desc-->
                    </div>
                    <!--end::Item-->
                    
                                        <!--begin::Item-->
                    <div class="mb-3">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center">
                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Due Date </span>
                            </div>
                            <!--end::Text-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Desc-->
                        <p class="text-dark-50 m-0 pt-2 font-weight-normal">
                            <?php
                                $today = date("Y-m-d H:i:s");
                                $label_due = "label-white";
                                $is_need_label = false;
                                if ($ticket->due_date > $today)
                                {
                                    $sDuedate = date("Y-m-d", strtotime($ticket->due_date ));
                                    $sToday = date("Y-m-d", strtotime($today ));

                                    if ($sDuedate == $sToday)
                                    {
                                        $label_due = "label-orange";
                                        $is_need_label = true;
                                    }

                                }
                                else
                                {
                                    $label_due = "label-danger";
                                    $is_need_label = true;
                                }
                            ?>
                            @if($is_need_label)
                            <span class="label label-lg font-weight-bold {{ $label_due }} label-inline">
                            @endif
							{{ empty($ticket->due_date) ? "-" : date('d M Y H:i ', strtotime($ticket->due_date))}}
                            @if($is_need_label)
                            </span>
                            @endif
                        </>
                        </p>
                        <!--end::Desc-->
                    </div>
                    <!--end::Item-->
                    
                    <!--begin::Item-->
                    @if ($ticket->coverage_windows)
                    <div class="mb-5">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center">
                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Coverage Windows</span>
                            </div>
                            <!--end::Text-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Desc-->
                        <!-- <p class="text-dark-50 m-0 pt-2 font-weight-normal">
							{{$ticket->coverage_windows}}
                        </p> -->
                        <button class="mb-0 btn btn-sm btn-white-line2 coverage-detail pt-2"><i class="flaticon-eye icon-sm text-dark-75"></i> Detail</button>
                        <!--end::Desc-->
                    </div>
                    @endif
                    <!--end::Item-->

					<?php 
						//var_dump($ticket);
						if ($ticket->finalclass == "service_request") {
							$rtype =  "Service";
						} elseif ($ticket->finalclass == "problem_request") {
							$rtype =  "Problem";
						} else {
							$rtype =  "Incident";
						}
					?>                    
                    <!--begin::Item-->
                    <div class="mb-3">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center">
                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">{{$rtype}} </span>
                            </div>
                            <!--end::Text-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Desc-->
                        <p class="text-dark-50 m-0 pt-2 font-weight-normal">
							<?php
									$service = DB::table('service')->where('id', $ticket->service_id)->first();
								?>
								@if ($ticket->finalclass == "problem_request")
								{{ $service->name ?? ($ticket->title ?? "") }}
								@else
								{{ $service->name ?? "" }}
								@endif
                        </p>
                        <!--end::Desc-->
                    </div>
                    <!--end::Item-->
                    
                    
					<?php 
					$list_ticket = DB::table('lnktickettoproblem')
										->where('ticket_id',$ticket->id)
										->join('ticket', 'ticket.id', '=', 'lnktickettoproblem.problem_ticket_id')
										->get();
										
					$list_ticket2 = DB::table('lnktickettoproblem')
										->where('problem_ticket_id',$ticket->id)
										->join('ticket', 'ticket.id', '=', 'lnktickettoproblem.ticket_id')
										->get();
					//$parent_ticket = DB::table('ticket')->where('id', $ticket->parent_id)->first();
					?>
					@if($list_ticket->count() || $list_ticket2->count()) 
                    <!--begin::Item-->
                    <div class="mb-5">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center">
                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Related Ticket</span>
                            </div>
                            <!--end::Text-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Desc-->
                        <p class="text-dark-50 m-0 pt-2 font-weight-normal">						
							@foreach($list_ticket as $t) 
								<a target="_blank" href="{{URL('/').'/ticket-monitoring/'.$t->token}}">{{$t->ref}}</a> &nbsp; &nbsp;
							@endforeach
							@foreach($list_ticket2 as $t) 
								<a target="_blank" href="{{URL('/').'/ticket-monitoring/'.$t->token}}">{{$t->ref}}</a> &nbsp; &nbsp;
							@endforeach
                        </p>
                        <!--end::Desc-->
                    </div>
                    @endif
                    <!--end::Item-->
					@if(!empty($_GET['d']))
                    <!--begin::Item-->
                    <div class="mb-5">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center">
                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">SLA Status</span>
                            </div>
                            <!--end::Text-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Desc-->
                        <p class="text-dark-50 m-0 pt-2 font-weight-normal">
							{{$ticket->SLA_status ?? "-"}}
                        </p>
                        <!--end::Desc-->
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="mb-5">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center">
                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="font-weight-bold text-dark-75 text-hover-primary font-size-lg mb-1">Remaining SLA</span>
                            </div>
                            <!--end::Text-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Desc-->
                        <p class="text-dark-50 m-0 pt-2 font-weight-normal">
							<?php 
							if($ticket->remaining_SLA > 0 && 
								($ticket->remaining_SLA_unit == "minutes" || $ticket->remaining_SLA_unit == "Minute")) {
								$hour = floor($ticket->remaining_SLA / 60);
								//echo $hour;
								$minute = $ticket->remaining_SLA % 60;
								echo (($hour > 0) ? ($hour. " hours"):"")." ".(($minute > 0) ? ($minute. " minutes"):"");
							} else {
								?>
								{{$ticket->remaining_SLA  ?? "-"}} {{$ticket->remaining_SLA_unit}}
								<?php
							}
							?>
                        </p>
                        <!--end::Desc-->
                    </div>
                    <!--end::Item-->
                    <pre><?php var_dump($ticket);?></pre>
					@endif

                    

	</div>
	<!--end: Card Body-->
</div>
<!--end: List Widget 9-->


<script>
    $('#btn-edit-nextapprover').click(function() {
        $('#btn-edit-nextapprover').attr('disabled')
        $.ajax({
            type: "POST",
            url: "{{URL('/').'/update_next_approver/'.$ticket->id}}",
            data: {
                _token: '{{csrf_token()}}'
            },
            success: function(data){
                $('#btn-edit-nextapprover').removeAttr('disabled')
                Swal.fire("Confirmation",data.message,"success")
                setTimeout(function() {
                    location.reload();
                },1500);
            },
            error: function(){
                $('#btn-edit-nextapprover').removeAttr('disabled')
            }
        });
    })

    $(document).ready(function() {
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })()
    })
</script>
