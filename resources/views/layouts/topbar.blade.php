									@if(Request::is('approve-request') || Request::is('ticket-monitoring') || Request::is('myDraft'))
										<div class="row" style="    width: 100%;">
											<div class="col-md-12 mt-2">
												<div class="mt-2" style="display: inline-block;">
												 <label class="checkbox2 path ml-3">
														<input type="checkbox">
														<svg viewBox="0 0 21 21">
															<path d="M5,10.75 L8.5,14.25 L19.4,2.3 C18.8333333,1.43333333 18.0333333,1 17,1 L4,1 C2.35,1 1,2.35 1,4 L1,17 C1,18.65 2.35,20 4,20 L17,20 C18.65,20 20,18.65 20,17 L20,7.99769186"></path>
														</svg>
													</label>
												<span class="ml-3 mr-3">Select All</span> | 
												<div class="dropdown ml-3" style="display: inline-block;">
												  <span class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													Sort by <span class="current-sort" style="font-weight:600" >Date Created</span>
												  </span>
												  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
													<a class="dropdown-item" href="#">Date Created</a>
													<a class="dropdown-item" href="#">Date Updated</a>
													<a class="dropdown-item" href="#">ID</a>
												  </div>
												</div>
												</div>
												<div class="" style="display: inline-block;float:right">
													<span  >
														<span class="mt-1 mr-3">Export </span> | 
														<span class="mt-1">Showing 1 - 4 of 4</span>
													</span>
													
													<div class="ml-2 btn-group btn-group-toggle" data-toggle="buttons" style="height: 32px;">
													  <label class=" btn btn btn-sm btn-outline-dark btn-white-line btn-sm">
														<i class="flaticon2-left-arrow icon-sm" style="text-align: center;margin: auto;display: block;margin-top: 3px;"></i>
													  </label>
													  <label class=" btn btn btn-sm btn-outline-dark btn-white-line btn-sm" >
														<i class="flaticon2-right-arrow icon-sm"  style="text-align: center;margin: auto;display: block;margin-top: 3px;"></i>
													  </label>
													</div>
													
												</div>
											</div>
										</div>
<style>
.checkbox2 {
  --background: #fff;
  --border: #D1D6EE;
  --border-hover: #BBC1E1;
  --border-active: #7d8388;
  --tick: #fff;
  position: relative;
  margin-bottom:2px;
  vertical-align: middle;
}
.checkbox2 input,
.checkbox2 svg {
  width: 17px;
  height: 17px;
  display: block;
}
.checkbox2 input {
  -webkit-appearance: none;
  -moz-appearance: none;
  position: relative;
  outline: none;
  background: var(--background);
  border: none;
  margin: 0;
  padding: 0;
  cursor: pointer;
  border-radius: 4px;
  transition: box-shadow 0.3s;
  box-shadow: inset 0 0 0 var(--s, 1px) var(--b, var(--border));
}
.checkbox2 input:hover {
  --s: 2px;
  --b: var(--border-hover);
}
.checkbox2 input:checked {
  --b: var(--border-active);
}
.checkbox2 svg {
  pointer-events: none;
  fill: none;
  stroke-width: 2px;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke: var(--stroke, var(--border-active));
  position: absolute;
  top: 0;
  left: 0;
  width: 17px;
  height: 17px;
  transform: scale(var(--scale, 1)) translateZ(0);
}
.checkbox2.path input:checked {
  --s: 2px;
  transition-delay: 0.4s;
}
.checkbox2.path input:checked + svg {
  --a: 16.1 86.12;
  --o: 102.22;
}
.checkbox2.path svg {
  stroke-dasharray: var(--a, 86.12);
  stroke-dashoffset: var(--o, 86.12);
  transition: stroke-dasharray 0.6s, stroke-dashoffset 0.6s;
}
.checkbox2.bounce {
  --stroke: var(--tick);
}
.checkbox2.bounce input:checked {
  --s: 11px;
}
.checkbox2.bounce input:checked + svg {
  -webkit-animation: bounce 0.4s linear forwards 0.2s;
          animation: bounce 0.4s linear forwards 0.2s;
}
.checkbox2.bounce svg {
  --scale: 0;
}

@-webkit-keyframes bounce {
  50% {
    transform: scale(1.2);
  }
  75% {
    transform: scale(0.9);
  }
  100% {
    transform: scale(1);
  }
}

@keyframes bounce {
  50% {
    transform: scale(1.2);
  }
  75% {
    transform: scale(0.9);
  }
  100% {
    transform: scale(1);
  }
}

</style>
									@endif
									
