<?php

namespace App\Http\Controllers;

use App\Product;
use App\Sections;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::all();
        $sections = Sections::all();
        return view('products.products', compact('products', 'sections'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|unique:products|max:255',
            'describtion' => 'required',
            'section_id' => 'required',
        ], [

            'product_name.required' => 'يرجى إدخال اسم المنتج',
            'product_name.unique' => 'هذا المنتج موجود بالفعل',
            'section_name.max' => 'يجب أن لا يتعدى عدد حروف هذا الحقل 255 حرف',
            'describtion.required' => 'حقل الوصف مطلوب',
            'section_id.required' => 'حقل الحقل مطلوب',
        ]);
        if ($validated) {
            Product::create([
                'product_name' => $request->product_name,
                'section_id' => $request->section_id,
                'describtion' => $request->describtion,
            ]);
            session()->flash('Add', 'تم إضافة المنتج بنجاح');
            return redirect('/products');

        }

    }

    public function show(Product $product)
    {
        //
    }

    public function edit(Product $product)
    {
        //
    }

    public function update(Request $request)
    {

        $idSection = sections::where('section_name', $request->section_name)->first()->id;
        $idPro = $request->pro_id;
        $valid = $request->validate([
            'product_name' => 'required|max:255|unique:products,product_name,' . $idPro,
            'section_name' => 'required',
            'describtion' => 'required',
        ], [
            'product_name.unique' => 'هذا المنتج موجود بالفعل',
            'product_name.required' => 'يرجى إدخال اسم المنتج',
            'product_name.max' => 'يجب أن لا يتعدى عدد حروف هذا الحقل 255 حرف',
            'describtion.required' => 'حقل الوصف مطلوب',
            'section_name.required' => 'هذا القسم موجود بالفعل',
        ]);

        $idProduct = Product::findOrFail($idPro);

        $idProduct->update([
            'product_name' => $request->product_name,
            'describtion' => $request->describtion,
            'section_id' => $idSection,
        ]);
        session()->flash('Add', 'تم تحديث المنتج بنجاح');
        return redirect('/products');

    }

    public function destroy(Request $request)
    {
        $id = $request->pro_id;
        Product::find($id)->delete();
        session()->flash('error', 'تم حذف المنتج بنجاح');
        return redirect('/products');

    }

}
