<?php

namespace App\Livewire\Admin\Inventories\Products;

use App\Exports\Products\TemplateExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ImportOfProducts extends Component
{

    public function downloadTemplate()
    {
        return Excel::download(new TemplateExport(), 'Productos_template.xlsx');
    }
    public function render()
    {
        return view('livewire.admin.inventories.products.import-of-products');
    }
}
