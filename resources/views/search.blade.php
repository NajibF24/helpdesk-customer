@extends('layouts.app')
@section('content')

<?php use Illuminate\Support\Facades\DB;?>

<div class="card card-custom gutter-b" style="min-width: 100%;box-shadow: none;margin-top: -50px;">
	<div class="card-body" style="background: #f3f6f9;padding: 0.2rem 0.7rem;">
        <?php
            $count = count($data) > 1 ? 'categories' : 'category';
            $data_count = count($data) > 0 ? 'Result found '.count($data). ' ' .$count : 'Result Not Found';
        ?>
        <h3 class="card-label">{{ $data_count }}</h3>
        @foreach ($data as $key => $table)
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-title">
                        <?php
                            $count_table = count($table) > 1 ? 'items' : 'item';
                        ?>
                        <h3 class="card-label">{{ str_replace("_", " ", strtoupper($key)) }} ( {{ count($table) }} {{ $count_table }} )</h3>
                    </div>
                </div>
                <div class="card-body">
                    @foreach ($table as $detail)
					<?php 
					$mode_key = $key;
					if($key == 'ticket') {
						$ticket = DB::table('ticket')->where('id',$detail->id)->first();
						if(empty($ticket->status)) {
							$mode_key = 'ticket-monitoring/'.$detail->token;
						}
						else if($ticket->status == 'Submit for Approval') {
							$mode_key = 'approve-request/'.$detail->token;
						}
						else {
							$mode_key = 'ticket-monitoring/'.$detail->token;
						}
					}

                    if($key == 'ticket_draft') {
						$mode_key = 'create/draft?id='.$detail->id;
					}
					?>
					
                    <div class="row">
                        <div class="col-md-8">
                            <div class="font-weight-bolder font-size-lg text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                        <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                        <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                    </g>
                                </svg>

                                <a href="{{URL('/')}}/{{$mode_key}}" target="_blank" class="ml-4">{{$detail->title ?? $detail->name}}</a>
                                @if ($key == 'ticket')
                                <span class="text-muted ml-2">{{ isset($detail->ref) ? '#'.$detail->ref : ''}}</span>
                                @endif

                                @if ($key == 'asset')
                                <span class="text-muted ml-2">{{ isset($detail->asset_number) ? '#'.$detail->asset_number : ''}}</span>
                                @endif
                            </div>
                            @if ($key == 'ticket' || $key == 'asset' || $key == 'ticket_draft')
                            <?php $user = DB::table('users')->where('id', $detail->created_by)->first(); ?>
                            <div>
                                <label class="text-muted">From:</label> <span>{{ $key == 'asset' ? $detail->created_by : $user->name }}</span>
                                <label class="text-muted ml-4">Created:</label> <span>{{ date('d M Y', strtotime($detail->created_at)) }}</span>
                            </div>
                            @elseif ($key == 'faq')
                            <div>
                                <span>{{ $detail->summary ?? "-" }}</span>
                            </div>
                            @endif
                        </div>
                        @if ($key == 'ticket' || $key == 'ticket_draft')
                        <div class="col-md-4">
                            <?php
                            $agent = DB::table('contact')->where('id', $detail->agent_id)->first();
                            ?>
                            <div>Agent: {{$agent->name ?? '-'}}</div>
                            <div>Status: {{$detail->status ?? '-'}}</div>
                            <!-- <div>Group:</div> -->
                            @if ($detail->next_approval_id)
                            <?php
                            $approver = DB::table('contact')->where('id', $detail->next_approval_id)->first();
                            ?>
                            <div>Next Approval: {{$approver->name ?? '-'}}</div>
                            @endif
                        </div>
                        @endif

                        @if ($key == 'employee' || $key == 'team')
                        <div class="col-md-4">
                            <div>Type: {{ $detail->type ?? '-' }}</div>
                            <div>Status {{ $detail->status ?? '-' }}</div>
                        </div>
                        @endif

                        @if ($key == 'service')
                        <div class="col-md-4">
                            <div>Type: {{ $detail->request_type ?? '-' }}</div>
                        </div>
                        @endif

                        @if ($key == 'asset')
                        <div class="col-md-4">
                            <div>Type: {{ str_replace('_', ' ', ucwords($detail->finalclass)) }}</div>
                        </div>
                        @endif
                    </div>
                    <div class="separator separator-dashed mt-8 mb-5"></div>
                    
                    @endforeach
                </div>
            </div>
            <!--end::Card-->
            <br>
        @endforeach
    </div>
</div>

@endsection
