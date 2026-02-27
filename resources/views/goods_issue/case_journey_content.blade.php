<?php
use App\Helpers\TicketStatusHelper;
use Illuminate\Support\Facades\Auth;
 ?>
<style>
.case-journey i {
	font-size: 1.1rem;
}
</style>

<!--begin::List Widget 9-->
<div class="card case-journey">
    <!--begin::Header-->
    <div class="card-header align-items-center border-0 mt-2 pb-0">
        <h3 class="card-title align-items-start flex-column mb-0">
            <span class="font-weight-bolder text-dark">Case's Journey</span>
        </h3>
    </div>
    <!--end::Header-->
    <?php //START : KODE SAMA HELPDESK DAN PORTAL ?>
    <!--begin::Body-->
    <div class="card-body pt-4">
        <!--begin::Timeline-->
        <div class="timeline timeline-6 mt-3">
            <!--begin::Item-->
            <div class="timeline-item align-items-start">
                <!--begin::Label-->
                <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
                <!--end::Label-->
                <!--begin::Badge-->
                <div class="timeline-badge">
                    <i class="fa fa-genderless text-success icon-xl"></i>
                </div>
                <!--end::Badge-->
                <!--begin::Desc-->
                <div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
                    New
                <i class="text-light-50">
                    by {{$detail->createdByUser->name }}
                </i>
                <br><span>{{ date('M d, Y H:i', strtotime($detail->created_at)) }}</span>
                </div>
                <!--end::Desc-->
            </div>
            <!--end::Item-->

            <?php
                getInventoryManagementCaseJourney($detail,"","","need_html_output_case_journey");
            ?>

            @if (false && count($statuses) > 0)
                @foreach ($statuses as $status)
                <!--begin::Item-->
                <div class="timeline-item align-items-start">
                    <!--begin::Label-->
                    <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg"></div>
                    <!--end::Label-->
                    <!--begin::Badge-->
                    <div class="timeline-badge">
                        <i class="fa fa-genderless text-{{ TicketStatusHelper::status_round_color($status->status) }} icon-xl"></i>
                    </div>
                    <!--end::Badge-->
                    <!--begin::Desc-->
                    <div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">{{ TicketStatusHelper::status_name($status->status) }}
                    <i class="text-light-50">
                        <?php
                            $approval_name = DB::table('users')->where('id', $status->created_by)->first();
                        ?>

                        by {{ $status->status != "new" ? "by " . $approval_name->name : "" }}
                    </i>
                    <br><span>{{ date('M d, Y H:i', strtotime($status->created_at)) }}</span></div>
                    <!--end::Desc-->
                </div>
                <!--end::Item-->
                @endforeach
            @endif
            <?php //end hasrejected?>
        </div>
        <!--end::Timeline-->
    </div>
    <!--end: Card Body-->
</div>
<!--end: List Widget 9-->
