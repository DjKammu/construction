<div class="col-6 text-left">
@php
$isFavourite = @\App\Models\FavouriteUrl::where(function($q){
	            $q->where('user_id', auth()->user()->id); 
	            $q->where('url', request()->getRequestUri()); 
              })->pluck('status')->first();      
@endphp	
<input type="checkbox" id="favourite-url" onchange="makeFavourite(this)" {{ ($isFavourite) ? 'checked' : ''}} > Make this page favourite 
</div>

