<?php

namespace App\Http\Controllers;

use App\DataTables\PageSettingDataTable;
use App\Http\Controllers\Controller;
use App\Models\PageSetting;
use Illuminate\Http\Request;
use Yajra\DataTables\Contracts\DataTable;

class PageSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PageSettingDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-page-setting')) {
            return $dataTable->render('page_settings.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (\Auth::user()->can('create-page-setting')) {
            return view('page_settings.create');
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
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

        if (\Auth::user()->can('create-page-setting')) {
            request()->validate([
                'title' => 'required',
            ]);
            $page_setting =  new  PageSetting();
            $page_setting->title = $request->title;
            $page_setting->type = $request->type;
            if ($request->type == 'link') {
                $page_setting->url_type = $request->url_type;
                $page_setting->page_url = filter_var($request->page_url, FILTER_VALIDATE_URL) ? $request->page_url : url($request->page_url);
                $page_setting->friendly_url = filter_var($request->friendly_url, FILTER_VALIDATE_URL) ? $request->friendly_url : url($request->friendly_url);
            } else {
                $page_setting->description = $request->descriptions;
            }
            $page_setting->save();
            return redirect()->route('page-setting.index')->with('success',  __('Page Setting Created successfully'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (\Auth::user()->can('edit-page-setting')) {
            $page_settings = PageSetting::find($id);
            return view('page_settings.edit', compact('page_settings'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-page-setting')) {
            request()->validate([
                'page_title' => 'required',
            ]);
            $page_setting_update = PageSetting::where('id', $id)->first();
            $page_setting_update->title = $request->page_title;
            $page_setting_update->type = $request->type;
            if ($request->type == 'link') {
                $page_setting_update->url_type = $request->url_type;
                $page_setting_update->page_url = filter_var($request->page_url, FILTER_VALIDATE_URL) ? $request->page_url : url($request->page_url);
                $page_setting_update->friendly_url = filter_var($request->friendly_url, FILTER_VALIDATE_URL) ? $request->friendly_url : url($request->friendly_url);
            } else {
                $page_setting_update->description = $request->descriptions;
            }
            $page_setting_update->save();
            return redirect()->route('page-setting.index')->with('success',  __('Page Setting Updated successfully'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-page-setting')) {
            $page_setting_delete = PageSetting::where('id', $id)->first();
            $page_setting_delete->delete();
            return redirect()->back()->with('success', __('Page Setting Deleted Successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
        }
    }
}
