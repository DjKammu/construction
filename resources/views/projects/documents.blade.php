@extends('layouts.admin-app')

@section('title', 'Search Documents')

@section('content')
      <!-- Start Main View -->
  <div class="card p-2">
    <div class="row">
        <div class="col-md-12">

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left">Search Document</h4>
                    </div>
                    <div class="col-6 text-right">
                      <label>Per Page </label>
                      <select style="height: 26px;" name="per_page"  onchange="selectPerpage(this.value)"> 
                        <option value="">Per Page</option>
                        <option value="25" {{ (request()->per_page == 25) ? 'selected' : ''}}>25</option>
                        <option value="50" {{ (request()->per_page == 50) ? 'selected' : ''}}>50</option>
                        <option value="100" {{ (request()->per_page == 100) ? 'selected' : ''}}> 100</option>
                        <option value="150" {{ (request()->per_page == 150) ? 'selected' : ''}}>150</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-12">
                      <form>
                        <select style="height: 26px;" name="property_type"> 
                        <option value="">Select Property Type</option>
                        @foreach($propertyTypes as $type)
                           <option value="{{ $type->slug }}" {{ (@request()->property_type == $type->slug) ? 'selected' : ''}}> {{ $type->name }}</option>
                        @endforeach
                        </select>
                        <select style="height: 26px;" name="property"> 
                        <option value="">Select Property</option>
                        @foreach($properties as $property)
                           <option value="{{ $property->id }}" {{ (@request()->property == $property->id) ? 'selected' : ''}}> {{ $property->property_name }}</option>
                        @endforeach
                        </select>

                        <select style="height: 26px;" name="tenant"> 
                        <option value="">Select Tenant</option>
                        @foreach($tenants as $tenant)
                           <option value="{{ $tenant->id }}" {{ (@request()->tenant == $tenant->id) ? 'selected' : ''}}> {{ $tenant->name }}</option>
                        @endforeach
                        </select>
                        <select style="height: 26px;" name="document_type"> 
                        <option value="">Select Document Type</option>
                        @foreach($documentTypes as $type)
                           <option value="{{ $type->slug }}" {{ (@request()->document_type == $type->slug) ? 'selected' : ''}}> {{ $type->name }}</option>
                        @endforeach
                        </select>
                        <select style="height: 26px;"  id="year" name="year"> 
                          <option value=""> Select Year</option>

                          @for($i = date('Y'); $i >= date('Y') - 50; $i--)
                            <option value="{{ $i }}" 
                            {{ (@request()->year == $i) ? 'selected' : ''}}
                            >{{ $i }}
                           </option>
                          @endfor

                        </select>
                        <select  style="height: 26px;" id="month" name="month"> 
                          <option value=""> Select Month</option>
                          @for ($i=1; $i<=12; $i++)
                            <option value="{{  $i }}"  {{ (@request()->month == $i ) ? 'selected' : ''}} >{{ date('F', mktime(0, 0, 0, $i, 1)) }}
                           </option>
                          @endfor
                        </select>
                        
                          @php
                          
                          $days = \Carbon\Carbon::now()->daysInMonth;

                          @endphp

                         <select style="height: 26px;" id="date" name="date"> 
                            <option value=""> Select Date</option>

                            @for ($i=1; $i<=$days; $i++)
                              <option value="{{ $i }}" {{ (@request()->date == $i) ? 'selected' : ''}}>{{ sprintf("%02d", $i) }}
                             </option>
                            @endfor

                          </select>

                        <input type="text" name="s" value="{{ @request()->s }}" 
                        id="inputSearch" >
                        <input type="hidden"  name="per_page" value="{{ @request()->per_page }}">
                        <button type="submit" id="search">Search</button>
                        </form>
                    </div>
                </div>
                <!-- Categories Table -->

                  <div class="table-responsive">

                      <table id="subcontractors-table" class="table card-table dataTable no-footer" role="grid" aria-describedby="subcontractors-table_info">
                             <thead class="d-none">
                                <tr role="row">
                                   <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 0px;"></th>
                                </tr>
                             </thead>
                             <tbody class="row">
                               @foreach($documents as $document)
                               @php
                                 $fileInfo = pathinfo($document->file);
                                 $extension = $fileInfo['extension'];
                                
                              if(in_array($extension,['doc','docx','docm','dot',
                              'dotm','dotx'])){
                                  $extension = 'word'; 
                               }
                               else if(in_array($extension,['csv','dbf','dif','xla',
                              'xls','xlsb','xlsm','xlsx','xlt','xltm','xltx'])){
                                  $extension = 'excel'; 
                               }

                               @endphp

                                <tr class="text-center col-lg-2 col-sm-3 odd" style="display: flex; flex-wrap: wrap;" role="row">
                                   <td>
                                       
                                         <div class="card card-table-item" style="width: 100%; height: 100%;">
                                            <div class="card-body pb-0">
                                               <div class="author mt-1">
                                                 <span class="doc_type_m">{{ $document->document->property->property_name 
                                                 }} </span>
                                                 </br>
                                                 <a  href="{{ ($document->file) ? $document->file : route('properties.documents.show', ['id' => request()->property, 'document' => $document->id ]) }}" {{ ($document->file) ? 'target="_blank"' : '' }} >
                                                  <img class="avatar border-gray" src="{{ asset('img/'.$extension.'.png') }}">  
                                                   </a>
                                                  <a href="{{url('/')}}/properties/{{ \Str::slug($document->document->property->id)}}/documents/{{ $document->document->id }}" target="_blank">                     
                                                  <h6 class="title mb-0">{{ @$document->name ?? @$document->document->name }}</h6>
                                                   </a>
                                                   <span class="doc-type"> 
                                                    {{  @$document->document->document_type->name }}</span>
                                                    <span class="doc_type_m">{{ (!$document->file) ? 'Multiple' : '' }} </span>
                                                    
                                               </div>
                                            </div>
                                         </div>
                                     
                                   </td>
                                </tr>
                               
                               @endforeach
                            
                             </tbody>
                          </table>
                    </div>
                    {!! $documents->render() !!}

            </div>
        </div>
    </div>
</div>

@endsection

@section('pagescript')

<script type="text/javascript">
  
   function selectPerpage(perPage){
       var fullUrl = window.location.href;
       let isPerpage = '{{ Request::input("per_page")}}';

       if(!isPerpage){
         window.location.href = fullUrl+(fullUrl.includes('?')?'&':'?')+'per_page='+perPage;
       }
       else if(isPerpage != perPage){
         window.location.href = fullUrl.replace(isPerpage, perPage)
       }
  } 


</script>

<style type="text/css">
  
span.cross{
    position: absolute;
    z-index: 10;
    right: 30px;
    display: none;
}
tr:hover span.cross{
  display: block;
}
button.btn.btn-neutral.bg-transparent.btn-icon{
  background-color: transparent !important;
}
td{
  width: 100%;
}

span.doc-type{
 font-size: 12px;
 padding-top: 8px;
 display: block;
}

span.doc_type_m{
 font-size: 10px;
 padding-top: 3px;
 display: block;
}

</style>
@endsection