<?php

namespace App\Http\Controllers;

use App\DataTables\BlogDataTable;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BlogDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-blog')) {
            return $dataTable->render('blog.index');
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
            return view('blog.create', compact('categories'));
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
            request()->validate([
                'title' => 'required',
                'images' => 'required',
                'description' => 'required',
                'category_id' => 'required',
            ]);
            if ($request->hasFile('images')) {
                $request->validate([
                    'images' => 'required',
                ]);
                $path = $request->file('images')->store('blogs');
            }
            $blog = Blog::create([
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'images' => $path,
                'short_description' => $request->short_description,
                'created_by' => \Auth::user()->id,
            ]);
            return redirect()->route('blogs.index')->with('success', __('Blog created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        if (\Auth::user()->can('edit-blog')) {
            $categories = BlogCategory::where('status', 1)->pluck('name', 'id');
            return view('blog.edit', compact('blog', 'categories'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-blog')) {
            request()->validate([
                'title' => 'required',
                'description' => 'required',
                'category_id' => 'required',
            ]);
            $blog = Blog::find($id);
            if ($request->hasFile('images')) {
                $request->validate([
                    'images' => 'required',
                ]);
                $path = $request->file('images')->store('blogs');
                $blog->images = $path;
            }
            $blog->title = $request->title;
            $blog->description = $request->description;
            $blog->category_id = $request->category_id;
            $blog->short_description = $request->short_description;
            $blog->created_by = \Auth::user()->id;
            $blog->save();
            return redirect()->route('blogs.index')->with('success', __('blogs updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-blog')) {
            $post = Blog::find($id);
            $post->delete();
            return redirect()->route('blogs.index')->with('success', __('Posts deleted successfully.'));
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

