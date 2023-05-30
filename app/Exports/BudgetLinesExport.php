<?php

namespace App\Exports;

use App\Models\BudgetLine;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BudgetLinesExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public $_data = [];

    public function __construct ($data){
         $this->_data =  $data;
    }

    
    public function view(): View
    {
    	// dd($this->_data);
    	return view('projects.budget.excel', $this->_data);
    }
}
