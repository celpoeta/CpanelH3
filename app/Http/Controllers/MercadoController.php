<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\UtilityFacades;
use Exception;
use Illuminate\Support\Facades\Crypt;
use App\Models\Form;
use App\Models\FormValue;
use Hashids\Hashids;

class MercadoController extends Controller
{
    // mercado form
    public function mercadofillPaymentPrepare(Request $request)
    {
        $mercado_mode = UtilityFacades::keysettings('mercadosetting', $request->mercado_created_by);
        $mercado_access_token = UtilityFacades::keysettings('mercado_access_token', $request->mercado_created_by);
        $form = Form::find($request->mercado_form_id);
        \MercadoPago\SDK::setAccessToken($mercado_access_token);
        try {
            $preference = new \MercadoPago\Preference();
            // Create an item in the preference
            $item              = new \MercadoPago\Item();
            $item->title       = $form->title;
            $item->quantity    = 1;
            $item->unit_price  = $request->mercado_amount;
            $preference->items = array($item);
            $success_url       = route('mercadofillcallback', [Crypt::encrypt(['form_id' => $form->id, 'flag' => 'success' ,'submit_type' => $request->mercado_submit_type])]);
            $failure_url       = route('mercadofillcallback', [Crypt::encrypt(['form_id' => $form->id, 'flag' => 'failure' ,'submit_type' => $request->mercado_submit_type])]);
            $pending_url       = route('mercadofillcallback', [Crypt::encrypt(['form_id' => $form->id, 'flag' => 'pending' ,'submit_type' => $request->mercado_submit_type])]);
            $preference->back_urls = array(
                "success" => $success_url,
                "failure" => $failure_url,
                "pending" => $pending_url,
            );
            $preference->auto_return = "approved";
            $preference->save();
            if ($mercado_mode == 'live') {
                $redirectUrl = $preference->init_point;
                return redirect($redirectUrl);
            } else {
                $redirectUrl = $preference->sandbox_init_point;
                return redirect($redirectUrl);
            }
        } catch (Exception $e) {
            return redirect()->back()->with('failed', __('Something wents wrong.'));
        }
    }

    public function mercadofillPlanGetPayment(Request $request, $data)
    {
        $data = Crypt::decrypt($data);
        $form = Form::find($data['form_id']);
        if ($data['flag'] == 'success') {
            $formvalue = FormValue::where('form_id', $form->id)->latest('id')->first();
            $formvalue->currency_symbol = $form->currency_symbol;
            $formvalue->currency_name = $form->currency_name;
            $formvalue->amount = $form->amount;
            $formvalue->transaction_id = $request->payment_id;
            $formvalue->status = 'successfull';
            $formvalue->payment_type = 'Mercado';
        } else {
            $formvalue = FormValue::where('form_id', $form->id)->latest('id')->first();
            $formvalue->currency_symbol = $form->currency_symbol;
            $formvalue->currency_name = $form->currency_name;
            $formvalue->amount = $form->amount;
            $formvalue->status = 'failed';
            $formvalue->payment_type = 'Mercado';
        }
        $formvalue->save();
        $hashids = new Hashids('', 20);
        $id = $hashids->encodeHex($form->id);
        $success_msg = strip_tags($form->success_msg);
        if ($data['submit_type'] == 'public_fill') {
            return redirect()->route('forms.survey', $id)->with('success', $success_msg);
        } else {
            return redirect()->back()->with('success', $success_msg);
        }
    }
}
