
<style>
.content {
	padding-top:0 !important;
}
table.dataTable thead th, table.dataTable thead td {
    border-bottom: none !important;
}
th,td {
	vertical-align: middle !important;
}
.table th {
	line-height: 2 !important;
}
.table-responsive {
    min-height: .01%;
    overflow-x: scroll;
}
.table {
	background:#fff;

}
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #ffffff;
}
thead {
	background:#f5f7f9;
}
tbody tr:first-child td {
	border-top:1px solid #cfd7df !important;
}
tr th {
	border-top:1px solid #cfd7df !important;
}
tbody tr td {
	border-right:none !important
}
tr {
	border-right:1px solid #cfd7df !important;
	border-left:1px solid #cfd7df !important;
}
tr td:first-child,tr th:first-child {
	border-left:1px solid #cfd7df !important;
}
tr th {
	border-right:1px solid #cfd7df !important;
	white-space:nowrap;
}

</style>


    @include('flash::message')

    <div class="table-responsive pl-3 pr-3" style="display: block">

    <?php
        $identifier =  rand(pow(10, 12-1), pow(10, 12)-1);
        $id_table = "table-".$identifier;
    ?>
    <style>
    th {
            font-weight:600 !important;
    }
    </style>

        <table class="table table-striped table-bordered" id="goods-receive-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Request No</th>
                    <th>Requestor Name</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

<script>
$(document).ready( function () {
    const datatable = $('#goods-receive-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ str_contains(request()->url(), 'approve-request') ? route('goods_receive.index', ['from' => 'approve-request']) : route('goods_receive.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'code', name: 'code' },
            { data: 'created_by_user.name', name: 'created_by_user.name', defaultContent: '' },
            { data: 'subject', name: 'subject' },
            { data: 'inventory_type.label', name: 'inventory_type.title', defaultContent: '' },
            { data: 'status_label', name: 'status' },
            { data: 'created_at', name: 'created_at', render: (data, type, row) => row.created_date},
            { data: 'action', name: 'action', orderable: false, sortable: false },
        ]
	});

    $('#goods-receive-table tbody').on('click', 'tr', function() {
        $(this).children("td").last().children()[1].click()
        // window.open($(this).children("td").last().children, '_blank');
    })

    datatable.on( 'draw.dt', function () {
        var PageInfo = $('.dataTable').DataTable().page.info();
        datatable.column(0, { page: 'current' }).nodes().each( function (cell, i) {
            cell.innerHTML = i + 1 + PageInfo.start;
        } );
    } );
 });
</script>

<style>
#goods-receive-table {
    width: 100% !important
}
tfoot input {
	width:100%;
	border-radius: 2px;
	border: 1px solid #e2e7f1;
	box-shadow: none;

}
tfoot input:focus {

  border: 1px solid #e2e7f1;
}
</style>
