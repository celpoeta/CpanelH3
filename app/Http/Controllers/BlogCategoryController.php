<?php

namespace App\Http\Controllers;

use App\DataTables\BlogCategoryDataTable;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BlogCategoryDataTable $dataTable)
    {
        if (Auth::user()->can('manage-category')) {
            return $dataTable->render('blogcategory.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->can('create-category')) {
            $categories = BlogCategory::all(); // Retrieve all categories
            return view('blogcategory.create', compact('categories'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::user()->can('create-category')) {
            request()->validate([
                'name' => 'required',
                'status' => 'required',
            ]);
            BlogCategory::create([
                'name' => $request->name,
                'status' => $request->status
            ]);
            return redirect()->route('blogcategory.index')->with('success', __('Category created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BlogCategory  $blogCategory
     * @return \Illuminate\Http\Response
     */
    public function show(BlogCategory $blogCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BlogCategory  $blogCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(BlogCategory $blogCategory, $id)
    {
        if (Auth::user()->can('edit-category')) {
            $category = BlogCategory::find($id);
            return view('blogcategory.edit', compact('category'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BlogCategory  $blogCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BlogCategory $blogCategory, $id)
    {
        if (Auth::user()->can('edit-category')) {
            request()->validate([
                'name' => 'required',
                'status' => 'required',
            ]);
            $category = BlogCategory::find($id);
            $category->name = $request->name;
            $category->status = $request->status;
            $category->update();
            return redirect()->route('blogcategory.index')->with('success', __('Category updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BlogCategory  $blogCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(BlogCategory $blogCategory, $id)
    {
        if (Auth::user()->can('delete-category')) {
            $category = BlogCategory::find($id);
            $category->delete();
            return redirect()->route('blogcategory.index')->with('success', __('Category deleted successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function blogcategorystatus(Request $request, $id)
    {
        $category = BlogCategory::find($id);
        $input          = ($request->value == "true") ? 1 : 0;
        if ($category) {
            $category->status = $input;
            $category->save();
        }
        return response()->json(['is_success' => true, 'message' => __('Category status changed successfully.')]);
    }
}
