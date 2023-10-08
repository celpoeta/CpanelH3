<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\SmsTemplateDataTable;
use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;

class SmsTemplateController extends Controller
{
    public function index(SmsTemplateDataTable $dataTable)
    {
        return $dataTable->render('sms_template.index');
    }

    public function edit($id)
    {
        $sms_template = SmsTemplate::find($id);
        return view('sms_template.edit', compact('sms_template'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'event' => 'required',
            'template' => 'required',
        ]);
        $input = $request->all();
        $sms_template = SmsTemplate::find($id);
        $sms_template->update($input);
        return redirect()->route('sms-template.index')->with('success', __('Sms template updated successfully.'));
    }
}

