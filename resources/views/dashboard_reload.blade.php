	<div class="dash-box col-md-2 pl-1 pr-1">
	<?php //validate date mengecek format date betul attau tidak, kalau tidak dianggap tidak diinput?>
		<div class="card ml-2">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Overdue</h3>
		<?php
		$parameters = "&start_date=".($request->query('start_date') ?? "")."&end_date=".($request->query('end_date') ?? "")."&requester=".($request->query('requester') ?? "");
		//fungsi helper on the spot agar kode tidak berulang
		function getQueryDashCount($request,$query) {
			if ($request->query('requester')) {
				//TODO : DISINI HARUS DICEK KEMBALI REQUESTER BERISI USER YG LOGIN DAN BAWAHANNYA,
				//DI LUAR ITU TIDAK BOLEH MASUK
				//yang difilter adalah user sebagai requester atau sebagai creator
				$requester_arr = explode("|", $request->query('requester'));
				$requester_arr2 = array_merge($requester_arr,$requester_arr);
				$arr_param = [];
				for($i=0;$i<count($requester_arr);$i++) {
					$arr_param[] = "?";
				}
				$str_param = implode(",",$arr_param);

				$query->whereRaw(' (created_by_contact IN ('.$str_param.') OR requester IN ('.$str_param.')) ',$requester_arr2);
			} else {
				//tidak ada parameter requester, maka default ke dirinya sendiri
				$query->whereRaw(' (created_by_contact = ? OR requester = ?) ',[Auth::user()->person,Auth::user()->person]);
			}

			if(validateDate($request->query('start_date'))  && validateDate($request->query('end_date'))) {
				$query->whereRaw('DATE(ticket.created_at) BETWEEN ? AND ?', [$request->query('start_date'), $request->query('end_date')]);
			} else if(!validateDate($request->query('start_date'))  && validateDate($request->query('end_date'))) {
				$query->whereRaw('DATE(ticket.created_at) <= ?', [$request->query('end_date')]);
			}
			$count = $query->count();
			return $count;
		}

			$query = DB::table('ticket')
						->whereIn('ticket.status',['Open','Re-Open','On Progress'])
						->whereRaw('due_date < NOW()');

			$count = getQueryDashCount($request,$query);

		  ?>
			<a class="dash-number text-danger" href="{{URL('/')}}/list/ticket?state=overdue{{$parameters}}">{{$count}}</a><?php //old filter &status_ticket=Open, On Progress&ticket_type=overdue   &requester={{implode('|',$array_pl)}}?>
		  </div>
		</div>
	</div>
	<div class="dash-box col-md-2 pl-1 pr-1">
		<div class="card ">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Due Today</h3>
		  <?php
			$query = DB::table('ticket')
						->whereIn('ticket.status',['Open','Re-Open','On Progress'])
						->whereRaw(' ((due_date >= NOW()) and  (CURRENT_DATE = DATE(due_date))) ');

			$count = getQueryDashCount($request,$query);

		  ?>
			<a class="dash-number text-info" href="{{URL('/')}}/list/ticket?state=due_today{{$parameters}}">{{$count}}</a><?php //old filter &status_ticket=Open,On Progress&ticket_type=due_today  &requester={{implode('|',$array_pl)}}?>
		  </div>
		</div>
	</div>
	<div class="dash-box col-md-2 pl-1 pr-1">
		<div class="card ">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Open Tickets</h3>
		  <?php
			$query = DB::table('ticket')
						->whereIn('ticket.status',['Open']);

			$count = getQueryDashCount($request,$query);

						//->whereIn('ticket.requester', $array_pl)
						//->whereBetween('created_at', [$first, $last])
		  ?>
			<a class="dash-number text-info" href="{{URL('/')}}/list/ticket?status_ticket=Open{{$parameters}}">{{$count}}</a> <?php //old filter &requester={{implode('|',$array_pl)}} ?>
		  </div>
		</div>
	</div>
	<div class="dash-box col-md-2 pl-1 pr-1">
		<div class="card ">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">On Progress</h3>
		  <?php
			$query = DB::table('ticket')
						->whereIn('ticket.status',['On Progress']);

			$count = getQueryDashCount($request,$query);

						//->whereIn('ticket.requester', $array_pl)
						//->whereBetween('created_at', [$first, $last])
		  ?>
			{{-- <a class="dash-number text-info" href="{{URL('/')}}/list/ticket?status_ticket=Waiting for User">{{$count}}</a> --}}
			<a class="dash-number text-info" href="{{URL('/')}}/list/ticket?status_ticket=On Progress{{$parameters}}">{{$count}}</a> <?php //old filter &requester={{implode('|',$array_pl)}} ?>
		  </div>
		</div>
	</div>
    <div class="dash-box col-md-2 pl-1 pr-1">
		<div class="card ">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Resolved</h3>
		  <?php
			$query = DB::table('ticket')
						->whereIn('ticket.status',['Resolved']);

			$count = getQueryDashCount($request,$query);

						//->whereIn('ticket.requester', $array_pl)
						//->whereBetween('created_at', [$first, $last])
		  ?>
			<a href="{{URL('/')}}/list/ticket?status_ticket=Resolved{{$parameters}}" class="dash-number text-info">{{$count}}</a> <?php //old filter &requester={{implode('|',$array_pl)}} ?>
		  </div>
		</div>
	</div>
    <div class="dash-box col-md-2 pl-1 pr-1">
		<div class="card">
		  <div class="card-body">
		  <h3 class="dash-title mb-5">Closed</h3>
		  <?php
			$query = DB::table('ticket')
						->whereIn('ticket.status',['Closed']);

			$count = getQueryDashCount($request,$query);

						//->whereIn('ticket.requester', $array_pl)
						//->whereBetween('created_at', [$first, $last])
		  ?>
			<a class="dash-number text-info" href="{{URL('/')}}/list/ticket?status_ticket=Closed{{$parameters}}">{{$count}}</a> <?php //&requester={{implode('|',$array_pl)}} ?>
		  </div>
		</div>
	</div>


@if(false)
<div class="dash-box col-md-2 pl-5 pr-1">
    <div class="card ml-2">
        <div class="card-body">
        <h3 class="dash-title mb-5">Overdue</h3>
        <?php
        $count = DB::table('ticket')
                    ->whereIn('ticket.status',['Submit for Approval','Waiting for User'])
                    ->whereBetween('created_at', [$_GET['start_date'], $_GET['end_date']])
                    ->whereRaw('due_date < CURDATE()')
                    ->count();
        ?>
        <a class="dash-number text-danger" href="{{URL('/')}}/list/ticket?state=overdue&status_ticket=Submit for Approval,Waiting for User&ticket_type=overdue"><?=$count?></a>
        </div>
    </div>
</div>
<div class="dash-box col-md-2 pl-1 pr-1">
    <div class="card ">
        <div class="card-body">
        <h3 class="dash-title mb-5">Due Today</h3>
        <?php
        $count = DB::table('ticket')
                    ->whereIn('ticket.status',['Submit for Approval','Waiting for User'])
                    ->whereBetween('created_at', [$_GET['start_date'], $_GET['end_date']])
                    ->whereRaw('due_date between curdate() and due_date')
                    ->count();
        ?>
        <a class="dash-number text-info" href="{{URL('/')}}/list/ticket?state=due_today&status_ticket=Submit for Approval,Waiting for User&ticket_type=due_today"><?=$count?></a>
        </div>
    </div>
</div>
<div class="dash-box col-md-2 pl-1 pr-1">
    <div class="card ">
        <div class="card-body">
        <h3 class="dash-title mb-5">Open Tickets</h3>
        <?php
        $count = DB::table('ticket')
                    ->whereIn('ticket.status',['Open'])
                    ->whereBetween('created_at', [$_GET['start_date'], $_GET['end_date']])
                    ->count();
        ?>
        <a class="dash-number text-info" href="{{URL('/')}}/list/ticket?status_ticket=Open"><?=$count?></a>
        </div>
    </div>
</div>
<div class="dash-box col-md-2 pl-1 pr-1">
    <div class="card ">
        <div class="card-body">
        <h3 class="dash-title mb-5">On Progress</h3>
        <?php
        $count = DB::table('ticket')
                    ->whereIn('ticket.status',['Waiting for User'])
                    ->whereBetween('created_at', [$_GET['start_date'], $_GET['end_date']])
                    ->count();
        ?>
        <a class="dash-number text-info" href="{{URL('/')}}/list/ticket?status_ticket=Waiting for User"><?=$count?></a>
        </div>
    </div>
</div>
<div class="dash-box col-md-2 pl-1 pr-1">
    <div class="card ">
        <div class="card-body">
        <h3 class="dash-title mb-5">Resolved</h3>
        <?php
        $count = DB::table('ticket')
                    ->whereIn('ticket.status',['Resolved'])
                    ->whereBetween('created_at', [$_GET['start_date'], $_GET['end_date']])
                    ->count();
        ?>
        <a href="{{URL('/')}}/list/ticket?status_ticket=Resolved" class="dash-number text-info"><?=$count?></a>
        </div>
    </div>
</div>
<div class="dash-box col-md-2 pl-5 pr-1">
    <div class="card ml-2">
        <div class="card-body">
        <h3 class="dash-title mb-5">Closed</h3>
        <?php
        $count = DB::table('ticket')
                    ->whereIn('ticket.status',['Closed'])
                    ->whereBetween('created_at', [$_GET['start_date'], $_GET['end_date']])
                    ->count();
        ?>
        <a class="dash-number text-info" href="{{URL('/')}}/list/ticket?status_ticket=unassign_ticket"><?=$count?></a>
        </div>
    </div>
</div>
@endif
