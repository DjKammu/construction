<div class="col-6 text-left">
@php

  if(!@$url){
	  $url = url()->previous();
	  if(url()->previous() ==  url()->current() && session()->get("url")) {
	    $url = session()->get("url");
	  }
 } 

 if(!@$to){
   $to = '';
}
  
@endphp

<button type="button" class="btn btn-danger mt-0" onclick="return window.location.href='{{ $url }}'">Back {{ $to }}
</button>

@if(request()->filled('url','to'))

<button type="button" class="btn btn-danger mt-0" onclick="return window.location.href='{{ request()->url }}'">Back to {{ request()->to }}
</button>

@endif

</div>

