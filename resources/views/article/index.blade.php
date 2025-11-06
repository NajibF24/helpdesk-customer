@extends('layouts.app')

@section('content')
<style>
.subheader {
	display:none;
}
</style>

<script>
	$(document).ready(function() {
		$.ajax({
			type: 'GET',
			url: "{{URL('/')}}/listServer/faq",
			success: function(result) {
				$.each(result.data, function(i, result) {
					$('.content-pdf-'+result.id).html(result.description);
				})
			},
			error: function() {
				alert('Please Refresh This Web');
			}
		});

		$(document).on('click', '.nav-link', function(){
			$(".nav-link").removeClass("active");
			$(".card-title").removeClass("active");
			$(this).addClass("active");
		});

		$(document).on('click', '.card-title', function(){
			$(".nav-link").removeClass("active");
		});
	});
</script>

<!--begin::Section-->
<div class="container-fluid mt-5">
	<!--begin::Card-->
	<div class="card mb-8">
		<!--begin::Body-->
		<div class="card-body p-10">
			@if ($menus != 0)
			<div class="row">
				<div class="col-lg-3">
					<ul class="nav flex-column nav-pills">
						<?php $i=0;?>
						@foreach ($menus as $key => $menu)
						<li class="nav-item mb-2">
							<div class="accordion accordion-light {{ isset($menu['children']) ? 'accordion-toggle-arrow' : '' }}" id="accordionExampleMenu{{$key}}" style="border-style:none;">
								<div class="card">
									<div class="card-header" id="headingOne5">
										@if (isset($menu['children']))
										<?php $target = isset($menu['children']) ? '#target-parent-'.safe_slugify($menu['text']).'' : '#target-'.safe_slugify($menu['text']).''; ?>
										<div class="card-title collapsed" data-toggle="collapse" data-target="{{ $target }}">
										{{ $menu['text'] }}</div>
										@else
										<a class="card-title collapsed" data-toggle="tab" href="#target-{{ safe_slugify($menu['text']) }}">
											{{ $menu['text'] }}
										</a>
										@endif
									</div>
									<div id="target-parent-{{ safe_slugify($menu['text']) }}" class="collapse" data-parent="#accordionExampleMenu{{$key}}">
										<div class="card-body">
											@if (isset($menu['children']))
											<ul class="nav flex-column nav-pills">
												@foreach ($menu['children'] as $child)
												<li class="nav-item mb-2">
													<a class="nav-link" data-toggle="tab" href="#target-{{ safe_slugify($child['text']) }}">
														{{ $child['text'] }}
													</a>
												</li>
												@endforeach
											</ul>
											@endif
										</div>
									</div>
								</div>
							</div>
						</li>
						<?php $i++;?>
						@endforeach
						<li class="nav-item">
						</li>
					</ul>
				</div>
				<div class="col-lg-9">
					<div class="tab-content" id="myTabContent5">
						<?php $j=0;?>
						<?php $n=0;?>
						<?php $p=0;?>
						<?php $q=0;?>

						@foreach ($menus as $menu)
							<?php
								$faqs = DB::table('faq')->where('category_id', $menu['id'])->get();
							?>
							<div class="tab-pane fade show{{$j==0?' active':''}}" id="target-{{ safe_slugify($menu['text']) }}" role="tabpanel" aria-labelledby="home-tab-5">
								@if (count($faqs) > 0 || $j==0)
									@if ($j==0)
										@if ($faqs_search && count($faqs_search) > 0)
											<h1>Search Result: {{count($faqs_search)}}</h1>
											@foreach ($faqs_search as $faq_search)
											<!--begin::Accordion-->
											<div class="accordion accordion-light accordion-light-borderless accordion-svg-toggle" id="accordionExample1" style="border-style:none;">
												<!--begin::Item-->
												<div class="card">
													<!--begin::Header-->
													<div class="card-header">
													<div class="card-title{{$q==0?'':' collapsed'}}" data-toggle="collapse" data-target="#target-faqsearch-{{ safe_slugify($faq_search->title) }}" aria-expanded="{{$q==0?'true':'false'}}" aria-controls="#target-faqsearch-{{ safe_slugify($faq_search->title) }}" role="button">
														<span class="svg-icon svg-icon-primary">
															<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-right.svg-->
															<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																	<polygon points="0 0 24 0 24 24 0 24" />
																	<path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero" />
																	<path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999)" />
																</g>
															</svg>
															<!--end::Svg Icon-->
														</span>
														<div class="card-label text-dark pl-4">{{ $faq_search->title }}</div>
													</div>
													</div>
													<!--end::Header-->
													<div class="text-right" style="position: absolute; right: 0">
														<a href="{{URL('/')}}/download_faq_pdf/{{ $faq_search->id }}" target="_blank">Download PDF</a>
													</div>
													<!--begin::Body-->
													<div id="target-faqsearch-{{ safe_slugify($faq_search->title) }}" class="collapse {{ $q == 0 ? 'show' : ''}}">
														<div class="card-body text-dark-25 font-size-lg pl-8">Category: {{ $faq_search->category_name }}</div>
														<div class="card-body text-dark-50 font-size-lg pl-8">{{ $faq_search->summary }}</div>
														<br>
														<a href="#target-desc-{{ safe_slugify($faq_search->title) }}" class="font-size-lg pl-8 collapsed" data-toggle="collapse" data-target="#target-desc-{{ safe_slugify($faq_search->title) }}" aria-controls="#target-desc-{{ safe_slugify($faq_search->title) }}">Detail</a>
														<div class="card-body text-dark-50 font-size-lg pl-8 collapse content-pdf-{{$faq_search->id}}" id="target-desc-{{ safe_slugify($faq_search->title) }}" data-description="{{$faq_search->description}}">
															{!! $faq_search->description !!}
														</div>
													</div>
													<!--end::Body-->
												</div>
												<!--end::Item-->
											</div>
											<!--end::Accordion-->
											<?php $q++;?>
											@endforeach
										@elseif ($type == "search")
											No Faq Found
										@else
											<h1>Welcome FAQs</h1>
										@endif
									@endif
									@foreach ($faqs as $key => $faq)
										<!--begin::Accordion-->
										<div class="accordion accordion-light accordion-light-borderless accordion-svg-toggle" id="accordionExample{{$key}}" style="border-style:none;">
											<!--begin::Item-->
											<div class="card">
												<!--begin::Header-->
												<div class="card-header" id="headingOne1">
													<div class="card-title{{$n==0?'':' collapsed'}}" data-toggle="collapse" data-target="#target-faq-{{ safe_slugify($faq->title) }}" aria-expanded="{{$n==0?'true':'false'}}" aria-controls="#target-faq-{{ safe_slugify($faq->title) }}" role="button">
														<span class="svg-icon svg-icon-primary">
															<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-right.svg-->
															<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																	<polygon points="0 0 24 0 24 24 0 24" />
																	<path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero" />
																	<path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999)" />
																</g>
															</svg>
															<!--end::Svg Icon-->
														</span>
														<div class="card-label text-dark pl-4">{{ $faq->title }}</div>
													</div>
												</div>
												<!--end::Header-->
												<div class="text-right" style="position: absolute; right: 0">
													<a href="{{URL('/')}}/download_faq_pdf/{{ $faq->id }}" target="_blank">Download PDF</a>
												</div>
												<!--begin::Body-->
												<div id="target-faq-{{ safe_slugify($faq->title) }}" class="collapse {{ $n == 0 ? 'show' : ''}}" aria-labelledby="headingOne1" data-parent="#accordionExample{{$key}}">
													<div class="card-body text-dark-50 font-size-lg pl-12">{{ $faq->summary }}</div>
													<br>
													<a href="#target-desc-{{ safe_slugify($faq->title) }}" class="font-size-lg pl-12 collapsed" data-toggle="collapse" data-target="#target-desc-{{ safe_slugify($faq->title) }}" aria-controls="#target-desc-{{ safe_slugify($faq->title) }}">Detail</a>
													<div class="card-body text-dark-50 font-size-lg pl-12 collapse content-pdf-{{$faq->id}}" id="target-desc-{{ safe_slugify($faq->title) }}" data-description="{{$faq->description}}">
														{!! $faq->description !!}
													</div>
												</div>
												<!--end::Body-->
											</div>
											<!--end::Item-->
										</div>
										<!--end::Accordion-->
										<?php $n++;?>
									@endforeach
								@else
									No Faq For This Category
								@endif
								<!--end::Item-->
							</div>


							<!-- Init Child Content Tab -->
							@if (isset($menu['children']))
								@foreach ($menu['children'] as $child)
								<?php
									$faq_childs = DB::table('faq')->where('category_id', $child['id'])->get();
								?>
								<div class="tab-pane fade show" id="target-{{ safe_slugify($child['text']) }}" role="tabpanel" aria-labelledby="home-tab-5">
									@if (count($faq_childs) > 0)
										@foreach ($faq_childs as $faq_child)
											<!--begin::Accordion-->
											<div class="accordion accordion-light accordion-light-borderless accordion-svg-toggle" id="accordionExample1" style="border-style:none;">
												<!--begin::Item-->
												<div class="card">
													<!--begin::Header-->
													<div class="card-header" id="headingOne1">
														<div class="card-title{{$p==0?'':' collapsed'}}" data-toggle="collapse" data-target="#target-{{ safe_slugify($faq_child->title) }}" aria-expanded="{{$p==0?'true':'false'}}" aria-controls="#target-{{ safe_slugify($faq_child->title) }}" role="button">
															<span class="svg-icon svg-icon-primary">
																<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-right.svg-->
																<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
																	<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																		<polygon points="0 0 24 0 24 24 0 24" />
																		<path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero" />
																		<path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999)" />
																	</g>
																</svg>
																<!--end::Svg Icon-->
															</span>
															<div class="card-label text-dark pl-4">{{ $faq_child->title }}</div>
														</div>
													</div>
													<!--end::Header-->
													<div class="text-right" style="position: absolute; right: 0">
														<a href="{{URL('/')}}/download_faq_pdf/{{ $faq_child->id }}" target="_blank">Download PDF</a>
													</div>
													<!--begin::Body-->
													<div id="target-{{ safe_slugify($faq_child->title) }}" class="collapse {{ $p == 0 ? 'show' : ''}}" aria-labelledby="headingOne1" data-parent="#accordionExample1">
														<div class="card-body text-dark-50 font-size-lg pl-12">{{ $faq_child->summary }}</div>
														<br>
														<a href="#target-desc-{{ safe_slugify($faq_child->title) }}" class="font-size-lg pl-12 collapsed" data-toggle="collapse" data-target="#target-desc-{{ safe_slugify($faq_child->title) }}" aria-controls="#target-desc-{{ safe_slugify($faq_child->title) }}">Detail</a>
														<div class="card-body text-dark-50 font-size-lg pl-12 collapse content-pdf-{{$faq_child->id}}" id="target-desc-{{ safe_slugify($faq_child->title) }}" data-description="{{$faq_child->description}}">
															{!! $faq_child->description !!}
														</div>
													</div>
													<!--end::Body-->
												</div>
												<!--end::Item-->
											</div>
											<!--end::Accordion-->
											<?php $p++;?>
										@endforeach
									@else
										No Faq For This Category
									@endif
									<!--end::Item-->
								</div>
								@endforeach
							@endif

							<?php $j++;?>
						@endforeach
					</div>
				</div>
			</div>
			@else
			<span>No FAQ at this moment</span>
			@endif
		</div>
		<!--end::Body-->
	</div>
	<!--end::Item-->
</div>

<script>
(function() {
  // get element by hash "#id"
  function getByHash(hash) {
    if (!hash || hash === '#') return null;
    return document.getElementById(hash.slice(1));
  }

  // find a tab-link whose hash matches, even if href is a full URL
  function findTabLinkByHash(hash) {
    return $('a[data-toggle="tab"]').filter(function() {
      // this.hash is the part after # even for full URLs
      return this.hash === hash;
    }).first();
  }

  // Show matching tab and/or collapse for a given hash
  function activateFromHash(rawHash, opts) {
    var hash = decodeURIComponent(rawHash || '');
    if (!hash) return;

    var force = opts && opts.force;

    // 1) If hash belongs to a tab-pane, show that tab
    var tabPane = getByHash(hash);
    if (tabPane && tabPane.classList.contains('tab-pane')) {
      var $tabLink = findTabLinkByHash(hash);
      if ($tabLink.length) {
        $tabLink.tab('show');
      }
    }

    // 2) If hash belongs to a collapse, open it, and ensure its parent tab is shown
    var collapseEl = getByHash(hash);
    if (collapseEl && collapseEl.classList.contains('collapse')) {
      var $parentTabPane = $(collapseEl).closest('.tab-pane');
      if ($parentTabPane.length) {
        var paneHash = '#' + $parentTabPane.attr('id');
        var $parentTabLink = findTabLinkByHash(paneHash);
        if ($parentTabLink.length) {
          $parentTabLink.tab('show');
        }
      }

      var $collapse = $(collapseEl);
      if (force || !$collapse.hasClass('show')) {
        $collapse.collapse('show');
      }
    }
  }

  // keep the current ?query but change the hash
  function setHashOnly(idWithoutHash) {
    var base = window.location.origin + window.location.pathname;
    var qs   = window.location.search;
    var newUrl = base + (qs || '') + (idWithoutHash ? '#' + idWithoutHash : '');
    history.replaceState(null, '', newUrl);
  }

  // initial load (handles .../faq?csrt=...#target-it-policy)
  $(function() {
    if (window.location.hash) {
      activateFromHash(window.location.hash, { force: true });
    }
  });

  // if hash changes manually
  window.addEventListener('hashchange', function() {
    activateFromHash(window.location.hash, { force: false });
  });

  // when a tab is shown, update only the hash (even if href is full URL)
  $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function(e) {
    // e.target.hash works even for full URLs
    var targetHash = e.target.hash;
    if (targetHash && targetHash.startsWith('#')) {
      setHashOnly(targetHash.slice(1));
    }
  });

  // when an accordion/collapse opens, update only the hash
  $(document).on('shown.bs.collapse', '.collapse', function(e) {
    var id = e.target.id;
    if (id) setHashOnly(id);
  });
})();
</script>

@endsection
