<?php

namespace App\Http\Controllers;

use App\DataTables\BlogDataTable;
use App\DataTables\ZoosDataTable;
use App\Models\Blog;
use App\Models\Zoo;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class ZooController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ZoosDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-blog')) {
            return $dataTable->render('zoos.index');
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
        if (\Auth::user()->can('create-blog')) {
            $categories = BlogCategory::where('status', 1)->pluck('name', 'id');
            // dd($categories);
            return view('zoos.create', compact('categories'));
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
        if (\Auth::user()->can('create-blog')) {
            $this->validate($request, [
                'common_name' => 'required',
                'description' => 'required',
                'url_image' => 'required',
                'category_id' => 'required',
                'habitat' => 'required',
            ]);
            if ($request->hasFile('url_image')) {
                $request->validate([
                    'url_image' => 'required',
                ]);
                $path = $request->file('url_image')->store('zoos');
            }
            $input = $request->all();
            $input['risk'] = ($request->risk == 'on') ? 1 : 0;
            $input['url_image'] = $path;
            $input['created_by'] = \Auth::user()->id;
            $zoos = Zoo::create($input);
            return redirect()->route('zoos.index')->with('success', __('Zoo created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (\Auth::user()->can('edit-blog')) {
            $categories = BlogCategory::where('status', 1)->pluck('name', 'id');
            $blog = Zoo::find($id);
            return view('zoos.edit', compact('blog', 'categories'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Zoo  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-blog')) {
            $this->validate($request, [
                'common_name' => 'required',
                'description' => 'required',
                'category_id' => 'required',
                'habitat' => 'required',
            ]);
            $blog = Blog::find($id);
            $input = $request->all();
            if ($request->hasFile('url_image')) {
                $request->validate([
                    'url_image' => 'required',
                ]);
                $path = $request->file('url_image')->store('zoos');

            }

            $input['risk'] = ($request->risk == 'on') ? 1 : 0;
            $input['url_image'] = $path;
            $input['created_by'] = \Auth::user()->id;
            $zoos = Zoo::create($input);
            return redirect()->route('zoos.index')->with('success', __('Zoo Update successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Zoo  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-blog')) {
            $post = Zoo::find($id);
            $post->delete();
            return redirect()->route('zoos.index')->with('success', __('Posts deleted successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function view_blog($slug , $lang = 'en')
    {
        \App::setLocale($lang);
        $blog =  Blog::where('slug', $slug)->first();
        $all_blogs =  Blog::all();
        return view('blog.view_blog', compact('blog' ,'all_blogs' ,'slug', 'lang'));
    }

    public function see_all_blogs(Request $request , $lang = 'en')
    {
        \App::setLocale($lang);
        if($request->category_id != ''){
            $all_blogs = Blog::where('category_id' , $request->category_id)->paginate(3);
            return response()->json(['all_blogs' => $all_blogs]);
        }
        else{
            $all_blogs = Blog::paginate(3);
        }
        $recent_blogs = Blog::latest()->take(3)->get();
        $last_blog = Blog::latest()->first();
        $categories = BlogCategory::all();
        return view('blog.view_all_blogs' , compact('all_blogs' , 'recent_blogs' , 'last_blog' , 'categories' , 'lang'));
    }
}

