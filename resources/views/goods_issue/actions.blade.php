<div class="actioncolumncell"></div>
@if(accessv($module,'edit','return'))
<a href="{!! route($module.'.show', [$id]) !!}" target="_blank" class="edit-button mb-0 btn btn-sm btn-outline-dark btn-white-line" style="font-weight: 500;color: #000000;width: 70px;margin-left:2px;border-radius:10px"><i class="flaticon-eye icon-sm text-dark-75" style="font-size: 1rem !important;"></i> Open</a>
@endif
