<div class="col-6 text-left">
@php
$isFavourite = @\App\Models\FavouriteUrl::where(function($q){
	            $q->where('user_id', auth()->user()->id); 
	            $q->where('url', request()->getRequestUri()); 
              })->pluck('status')->first();  
$isFavourite = false;                  
@endphp	
<input type="checkbox" id="favourite-url" onchange="makeFavourite(this)" {{ ($isFavourite) ? 'checked' : ''}} >Make this page favourite   <input type="text" style="height: 20px;" placeholder="URL Label" name="label" id="label"> 
</div>

