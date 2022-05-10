<?php

namespace App\Http\Controllers;

use App\Invoices;
use App\Invoices_attachments;
use App\Invoices_details;
use App\Sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoicesController extends Controller
{

    public function index()
    {
        $allInvoices = Invoices::all();

        return view('invoices.invoices', compact('allInvoices'));
    }

    public function create()
    {

        $sections = Sections::all();
        return view('invoices.add_invoices', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required',
            'invoice_Date' => 'required',
            'Due_date' => 'required',
            'Section' => 'required',
            'Amount_collection' => 'required',
            'Amount_Commission' => 'required',
            'Rate_VAT' => 'required',
            'pic' => 'required|mimes:jpeg,pdf,jpg,png,gif|max:10000',
        ], [

            'invoice_number.required' => 'يرجى إدخال رقم الفاتورة',
            'invoice_Date.required' => 'يرجى إدخال تاريخ الفاتورة',
            'Due_date.required' => 'يرجى إدخال تاريخ الإستحقاق',
            'Section.required' => 'حقل القسم مطلوب',
            'Amount_collection.required' => 'يرجى إدخال مبلغ التحصيل',
            'Amount_Commission.required' => 'يرجى إدخال مبلغ العمولة',
            'Rate_VAT.required' => 'يرجى إدخال نسبة ضريبة القيمة المضافة',
            'pic.required' => 'يرجى ادخال الصورة',

        ]);
        Invoices::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
        ]);

        $invoice_id = Invoices::latest()->first()->id;
        Invoices_details::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        if ($request->hasFile('pic')) {

            $invoice_id = Invoices::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new Invoices_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
        }

        session()->flash('Add', 'تم إضافة الفاتورة بنجاح');
        return redirect('/invoices/create');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show(Invoices $invoices)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoices $invoices)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoices $invoices)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoices $invoices)
    {
        //
    }

    public function getProducts($id)
    {
        $products = DB::table('products')->where('section_id', $id)->pluck('product_name', 'id');
        return json_encode($products);

    }

    public function details($id)
    {
        $invoices = Invoices::where('id', $id)->first();
        $Invoices_details = Invoices_details::where('id_Invoice', $id)->get();
        $Invoices_attachments = Invoices_attachments::where('invoice_id', $id)->get();
        return view('invoices.detalils', compact('invoices', 'Invoices_details', 'Invoices_attachments'));
    }

    public function openFile($invoice_number, $file_name)
    {
        $openFile = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
        return response()->file($openFile);
    }

    public function get_file($invoice_number, $file_name)
    {
        $contents = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
        return response()->download($contents);
    }

    public function delete_file(Request $request)
    {
        $id = $request->id_file;
        Invoices_attachments::findOrFail($id)->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number . '/' . $request->file_name);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }
    public function edit_invoice($id)
    {
        $invoices = Invoices::where('id', $id)->first();
        $sections = Sections::all();
        $product = Invoices::get('product');
        return view('invoices.edit_invoices', compact('invoices', 'sections', 'product'));

    }

    public function invoicesUpdate(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required',
            'invoice_Date' => 'required',
            'Due_date' => 'required',
            'Section' => 'required',
            'Amount_collection' => 'required',
            'Amount_Commission' => 'required',
            'Rate_VAT' => 'required',
        ], [

            'invoice_number.required' => 'يرجى إدخال رقم الفاتورة',
            'invoice_Date.required' => 'يرجى إدخال تاريخ الفاتورة',
            'Due_date.required' => 'يرجى إدخال تاريخ الإستحقاق',
            'Section.required' => 'حقل القسم مطلوب',
            'Amount_collection.required' => 'يرجى إدخال مبلغ التحصيل',
            'Amount_Commission.required' => 'يرجى إدخال مبلغ العمولة',
            'Rate_VAT.required' => 'يرجى إدخال نسبة ضريبة القيمة المضافة',
        ]);

        $invoices = Invoices::findOrFail($request->invoice_id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'note' => $request->note,
        ]);
        session()->flash('Add', 'تم تحديث الفاتورة بنجاح');
        return back();

    }

}
