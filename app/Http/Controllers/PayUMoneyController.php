<?php

namespace App\Http\Controllers;

use App\Facades\Utility;
use App\Facades\UtilityFacades;
use App\Models\Coupon;
use App\Models\Form;
use App\Models\FormValue;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Stripe\Discount;

class PayUMoneyController extends Controller
{
    public function payumoneyfillPaymentPrepare(Request $request)
    {
        // dd($request->all());
        $authuser  = User::find($request->payumoney_created_by);
        $form = Form::find($request->payumoney_form_id);

        $discount_value = null;
        $price  = $request->payumoney_amount;
        $currency = $request->payumoney_currency;
        $symbol = $form->currency_symbol;
        // dd($authuser ,$form ,$discount_value , $price ,  $currency);

        $res_data['form_id'] = $form->id;
        $res_data['email']       = $authuser->email;
        $res_data['total_price'] = $price;
        $key = UtilityFacades::getsettings('payumoney_merchant_key');
        $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
        $salt = UtilityFacades::getsettings('payumoney_salt_key');
        $amount = $price;
        $hashString = $key . '|' . $txnid . '|' . $amount . '|' . $form->title . '|' . $authuser->name . '|' . $authuser->email . '|' . '||||||||||' . $salt;
        $hash = strtolower(hash('sha512', $hashString));

        $payuUrl = 'https://test.payu.in/_payment';

        $paymentData = [
            'key' => $key,
            'txnid' => $txnid,
            'amount' => $res_data['total_price'],
            'productinfo' => $form->title,
            'firstname' => $authuser->name,
            'email' => $authuser->email,
            'phone' => '1234567890',
            'hash' => $hash,
            'surl' => route('payumoneyfillcallback', Crypt::encrypt(['key' => $key, 'productinfo' => $form->name, 'firstname' => $authuser->name,  'phone' => '1234567890', 'email' => $authuser->email, 'amount' => $res_data['total_price'] , 'txnid' => $txnid,  'user_id' => $authuser->id,   'currency' => $currency , 'payment_type'=>'payumoney' , 'status' => 'pending'])),
            'furl' => route('payumoneyfillcallback', Crypt::encrypt(['key' => $key, 'productinfo' => $form->name, 'firstname' => $authuser->name, 'phone' => '1234567890','email' => $authuser->email,  'txnid' => $txnid, 'amount' => $res_data['total_price'], 'user_id' => $authuser->id,  'currency' => $currency, 'payment_type'=>'payumoney' , 'status' => 'failed'])),
        ];
        return view('form.payumoneyRedirect', compact('payuUrl', 'paymentData'));

    }

    public function payumoneyfillPlanGetPayment($data)
    {
        $data = Crypt::decrypt($data);
        $form = Form::find($data['form_id']);
        if ($data['status'] == 'pending') {
            $formvalue = FormValue::where('form_id', $form->id)->latest('id')->first();
            $formvalue->currency_symbol = $form->currency_symbol;
            $formvalue->currency_name = $form->currency_name;
            $formvalue->amount = $form->amount;
            $formvalue->status = 'successfull';
            $formvalue->payment_type = 'payumoney';
        } else {
            $formvalue = FormValue::where('form_id', $form->id)->latest('id')->first();
            $formvalue->currency_symbol = $form->currency_symbol;
            $formvalue->currency_name = $form->currency_name;
            $formvalue->amount = $form->amount;
            $formvalue->status = 'failed';
            $formvalue->payment_type = 'payumoney';
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


    // public function payuFailure(Request $request)
    // {
    //     return redirect()->route('plans.index')->with('success', 'Payment payuFailure');
    // }

    // private function getPayUmoneyPaymentUrl(array $params)
    // {
    //     // Build the payment URL using the PayUmoney API
    //     // You need to construct the URL by appending the required parameters
    //     // Refer to the PayUmoney API documentation for the specific URL format

    //     // Example URL construction
    //     $url = 'https://www.payumoney.com/paybypayumoney/#' . http_build_query($params);

    //     return $url;
    // }
}
