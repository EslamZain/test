<?php

namespace App\Http\Controllers;

use App\Sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionsController extends Controller
{
    public function index()
    {
        $sections = Sections::all();
        return view('sections.sections', compact('sections'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request){
        // التأكد من التسجيل مسبقا

        $validated = $request->validate([
        'section_name' => 'required|unique:sections|max:255',
        'description' => 'required',

        ],[

            'section_name.required' => 'يرجى إدخال اسم القسم',
            'section_name.unique' => 'هذا الحقل موجود بالفعل',
            'section_name.max' => 'يجب أن لا يتعدى عدد حروف هذا الحقل 255 حرف',
            'description.required' => 'حقل الوصف مطلوب',
    ]);
        // $input = $request->all();
        // $exist = Sections::where('section_name', '=', $input['section_name'])->exists();
        if ($validated) {
            Sections::create([
            'section_name' => $request->section_name,
             'description' => $request->description,
             'created_by'  => (Auth::user()->name),
        ]);
            session()->flash('Add', 'تم إضافة القسم بنجاح');
            return redirect('/sections');

        }

        // if(!$validated){
        //     session()->flash('error', 'هذا القسم موجود مسبقا');
        //     return redirect('/sections');
        // }
    }

    public function show(Sections $sections)
    {
        //
    }

    public function edit(Sections $sections)
    {

    }

    public function update(Request $request){


        $id = $request->id;
        $valid = $request->validate([
        'section_name' => 'required|max:255|unique:sections,section_name,'.$id,
        'description' => 'required',
        ],[
            'section_name.unique' => 'هذا القسم موجود بالفعل',
            'section_name.required' => 'يرجى إدخال اسم القسم',
            'section_name.max' => 'يجب أن لا يتعدى عدد حروف هذا الحقل 255 حرف',
            'description.required' => 'حقل الوصف مطلوب',
        ]);

        $idSection = Sections::find($id);

        if (!$idSection) {
            return redirect()->back();
        }
        if ($idSection) {
            $idSection->update([
                'section_name' => $request->section_name,
                'description' => $request->description,
            ]);
            session()->flash('Add', 'تم التحديث بنجاح');
            return redirect('/sections');
        }

    }

    public function destroy(Request $request){

             $id = $request->id;

             Sections::find($id)->delete();

            session()->flash('error', 'تم الحذف بنجاح');
            return redirect('/sections');

            }

    }
