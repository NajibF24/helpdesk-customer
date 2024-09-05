
<style>
.highcharts-figure, .highcharts-data-table table {
    min-width: 320px;
    max-width: 660px;
    margin: 1em auto;
}

.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #EBEBEB;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
}
.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}
.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}
.highcharts-data-table tr:hover {
    background: #f1f7ff;
}
.highcharts-credits {
    display:none;
}
</style>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<!--
<script src="https://code.highcharts.com/modules/exporting.js"></script>
-->
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<div class="dash-box col-md-4 pl-7 pr-1 pt-2">
    <!--begin::Card-->
    <div class="card card-custom gutter-b">
        <div class="card-body" >
                <h3 class="dash-title mb-5">Incident Ticket</h3>
                <!--start::Chart-->
                <figure class="highcharts-figure">
                    <div id="container-chart"></div>
                </figure>
                <!--end::Chart-->
        </div>
    </div>
    <!--end::Card-->
</div>
<div class="dash-box col-md-4 pl-3 pr-1 pt-2">
    <!--begin::Card-->
    <div class="card card-custom gutter-b">
        <div class="card-body" >
                <h3 class="dash-title mb-5">Service Ticket</h3>
                <!--start::Chart-->
                <figure class="highcharts-figure">
                    <div id="container-chart2"></div>
                </figure>
                <!--end::Chart-->
        </div>
    </div>
    <!--end::Card-->
</div>
<div class="dash-box col-md-4 pl-3 pr-1 pt-2">
    <!--begin::Card-->
    <div class="card card-custom gutter-b">
        <div class="card-body">
        <h3 class="dash-title mb-5">All Tickets</h3>
        <?php
            $count = DB::table('ticket')
                        ->whereIn('ticket.status',['Open', 'Waiting for User', 'Resolved', 'Closed'])
                        ->whereBetween('created_at', [$_GET['start_date'], $_GET['end_date']])
                        ->count();
        ?>
        <div class="dash-number-large text-center" style="
            font-size: 96px;
            font-style: bol;
            padding-bottom: 153px;
            padding-top: 153px;
            font-weight: bold;
        "><?=   $count?></div>
        </div>
        </div>
    <!--end::Card-->
</div>


<?php
//INCIDENT
$list_priority = DB::table('sla_priority')->get();
$list_status = ['Open', 'Waiting for User', 'Resolved', 'Closed'];
$arr_priority =[];
$total = DB::table('ticket')->whereIn('ticket.status',$list_status)//['Submit for Approval','Rejected','Waiting for User','Open',])
                            ->where('finalclass','incident_management')
                            ->whereBetween('created_at', [$_GET['start_date'], $_GET['end_date']])
                            ->count();

foreach($list_status as $p) {
//foreach($list_priority as $p) {
    $count = DB::table('ticket')
                                //->whereIn('ticket.status',['Submit for Approval','Rejected','Waiting for User','Open',])
                                //->where('priority',$p->priority)
                                ->where('status',$p)
                                ->where('finalclass','incident_management')
                                ->whereBetween('created_at', [$_GET['start_date'], $_GET['end_date']])
                                ->count();
    if($count) {
        //$arr_priority[] = ['name'=>$p->priority,'y'=>$count
                                ////,'drilldown'=>$p->priority
                            //];
        $arr_priority[] = ['name'=>$p,'y'=>$count
                                //,'drilldown'=>$p->priority
                            ];
    }
}
//var_dump($arr_priority);
//echo json_encode($arr_priority);
?>
<script>
var total_ticket = <?=$total?>;
var data_json1 = <?=json_encode($arr_priority)?>;
makechart('container-chart',total_ticket,data_json1);
</script>

<?php
//SERVICE
$list_priority = DB::table('sla_priority')->get();
$list_status = ['new','Submit for Approval','Rejected','Waiting for User','Open','On Progress'
                    //,'Resolve','Close','Draft'
                ];
$arr_priority =[];
$total = DB::table('ticket')->whereIn('ticket.status',$list_status)//['Submit for Approval','Rejected','Waiting for User','Open',])
                            ->whereBetween('created_at', [$_GET['start_date'], $_GET['end_date']])
                            ->where('finalclass','service_request')->count();

foreach($list_status as $p) {
//foreach($list_priority as $p) {
    $count = DB::table('ticket')
                                //->whereIn('ticket.status',['Submit for Approval','Rejected','Waiting for User','Open',])
                                //->where('priority',$p->priority)
                                ->where('status',$p)
                                ->where('finalclass','service_request')
                                ->whereBetween('created_at', [$_GET['start_date'], $_GET['end_date']])
                                ->count();
    if($count) {
        //$arr_priority[] = ['name'=>$p->priority,'y'=>$count
                                ////,'drilldown'=>$p->priority
                            //];
        $arr_priority[] = ['name'=>$p,'y'=>$count
                                //,'drilldown'=>$p->priority
                            ];
    }
}
//var_dump($arr_priority);
//echo json_encode($arr_priority);
?>
<script>
total_ticket = <?=$total?>;
data_json1 = <?=json_encode($arr_priority)?>;
makechart('container-chart2',total_ticket,data_json1);
</script>

<?php
//PROBLEM
$list_priority = DB::table('sla_priority')->get();
$list_status = ['new','Submit for Approval','Rejected','Waiting for User','Open','On Progress'
                    //,'Resolve','Close','Draft'
                ];
$arr_priority =[];
$total = DB::table('ticket')->whereIn('ticket.status',$list_status)//['Submit for Approval','Rejected','Waiting for User','Open',])
                            ->whereBetween('created_at', [$_GET['start_date'], $_GET['end_date']])
                            ->where('finalclass','problem')->count();

foreach($list_status as $p) {
//foreach($list_priority as $p) {
    $count = DB::table('ticket')
                                //->whereIn('ticket.status',['Submit for Approval','Rejected','Waiting for User','Open',])
                                //->where('priority',$p->priority)
                                ->where('status',$p)
                                ->whereBetween('created_at', [$_GET['start_date'], $_GET['end_date']])
                                ->where('finalclass','problem')
                                ->count();
    if($count) {
        //$arr_priority[] = ['name'=>$p->priority,'y'=>$count
                                ////,'drilldown'=>$p->priority
                            //];
        $arr_priority[] = ['name'=>$p,'y'=>$count
                                //,'drilldown'=>$p->priority
                            ];
    }
}
//var_dump($arr_priority);
//echo json_encode($arr_priority);
?>
<script>
total_ticket = <?=$total?>  ;
data_json1 = <?=json_encode($arr_priority)?>;
makechart('container-chart3',total_ticket,data_json1);
</script>
