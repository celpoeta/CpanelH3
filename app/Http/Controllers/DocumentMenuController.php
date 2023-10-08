<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DocumentMenu;
use Illuminate\Http\Request;
use App\Models\DocumentGenrator;

class DocumentMenuController extends Controller
{
    public function index()
    {
        $doc_menu = DocumentMenu::all();
        return view('document-menu.index', compact('doc_menu'));
    }

    public function create($doc_menu_id)
    {
        $documents = DocumentGenrator::find($doc_menu_id);
        return view('document-menu.create', compact('documents'));
    }

    public function store(Request $request)
    {
        request()->validate([
            'title' => 'required',
        ]);
        $document_id = $request->document_id;
        $doc_menu           = new DocumentMenu();
        $doc_menu->title = $request->title;
        $doc_menu->document_id     = $request->document_id;
        $doc_menu->parent_id     = 0;
        $doc_menu->save();
        return redirect()->route('document.design', $document_id)->with('success', __('Menu created successfully.'));
    }


    public function submenuCreate($id, $doc_menu_id)
    {
        $document_menu  = DocumentMenu::find($id);
        $document = DocumentGenrator::find($doc_menu_id);
        return view('document-menu.submenu-create', compact('document_menu', 'document'));
    }

    public function submenuStore(Request $request)
    {
        request()->validate([
            'title' => 'required',
        ]);
        $document_id = $request->document_id;
        $doc_menu           = new DocumentMenu();
        $doc_menu->title = $request->title;
        $doc_menu->document_id     = $request->document_id;
        $doc_menu->parent_id     = $request->parent_id;
        $doc_menu->save();
        return redirect()->route('document.design', $document_id)->with('success', __('Submenu created successfully.'));
    }

    public function destroy($id)
    {
        $document_menu  = DocumentMenu::find($id);
        if ($document_menu ->parent_id == 0) {
            DocumentMenu::where('parent_id', $id)->delete();
        }
        $document_menu ->delete();
        return redirect()->route('document.index')->with('success', __('Documents deleted successfully.'));
    }

    public function submenuDestroy($id)
    {
        $document_menu  = DocumentMenu::find($id);
        $document_menu ->delete();
        return redirect()->route('document.index')->with('success', __('Documents deleted successfully.'));
    }
}
