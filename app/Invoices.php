<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{

    protected $table = 'invoices';
    protected $fillable = ['invoice_number', 'invoice_Date', 'Due_date', 'product',
        'section_id', 'Amount_collection', 'Amount_Commission',
        'Discount', 'Value_VAT', 'Rate_VAT', 'Total', 'Status', 'Value_Status', 'note', 'Payment_Date'];
    protected $hidden = [];

    public function sections()
    {

        return $this->belongsTo('App\Sections', 'section_id', 'id');
    }

}
