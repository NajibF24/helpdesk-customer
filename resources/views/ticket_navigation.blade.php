<ul class="nav nav-pills pb-4 pl-2">
    <li class="nav-item">
        @if (request()->query('from') == 'approve_request')
        <a class="nav-link {{ request()->getPathInfo() == '/approve-request' ? 'active' : 'btn btn-secondary text-dark' }}" href="{{url('/approve-request')}}">Ticket</a>
        @else
        <a class="nav-link {{ request()->getPathInfo() == '/ticket-monitoring' ? 'active' : 'btn btn-secondary text-dark' }}" href="{{url('/ticket-monitoring')}}">Ticket</a>
        @endif
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->getPathInfo() == '/goods_issue' ? 'active' : 'btn btn-secondary text-dark' }}" href="{{url('/goods_issue')}}?from={{request()->query('from')}}">Stock Request</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->getPathInfo() == '/goods_receive' ? 'active' : 'btn btn-secondary text-dark' }}" href="{{url('/goods_receive')}}?from={{request()->query('from')}}">Stock Return</a>
      </li>
</ul>