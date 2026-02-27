<ul class="nav nav-pills pb-4 pl-2">
    <li class="nav-item">
      <a class="nav-link {{ request()->getPathInfo() == '/approve-request' ? 'active' : 'btn btn-secondary text-dark' }}" href="{{url('/approve-request')}}">Ticket</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->getPathInfo() == '/goods_issue_approve_request' ? 'active' : 'btn btn-secondary text-dark' }}" href="{{url('/goods_issue_approve_request')}}">Stock Request</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->getPathInfo() == '/goods_receive_approve_request' ? 'active' : 'btn btn-secondary text-dark' }}" href="{{url('/goods_receive_approve_request')}}">Stock Return</a>
      </li>
</ul>