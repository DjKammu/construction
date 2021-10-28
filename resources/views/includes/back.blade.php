<div class="col-6 text-left">
@php
  $url = url()->previous();
  if(url()->previous() ==  url()->current() && session()->get("url")) {
    $url = session()->get("url");
  }
@endphp

<button type="button" class="btn btn-danger mt-0" onclick="return window.location.href='{{ $url }}'">Back
</button>
</div>

