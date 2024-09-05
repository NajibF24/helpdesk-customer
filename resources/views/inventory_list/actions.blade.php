<div class="actioncolumncell"></div>
<a href="{!! route($module.'.edit', [$id]) !!}"  class="edit-button mb-0 btn btn-sm btn-outline-dark btn-white-line" style="font-weight: 500;color: #000000;width: 70px;margin-left:2px;border-radius:10px"><i class="flaticon-eye icon-sm text-dark-75"  style="font-size: 1rem !important;"></i> View</a>
@if(accessv($module,'edit','return'))
@endif
