<?php

namespace App\Http\Controllers;

use App\PaymentInstrument;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentInstrumentController extends Controller
{
    public function __construct(Request $request){
        $this->perpage = 10;
        $this->page = (null !== $request->input("page"))? (int)$request->input("page"):1;
        $this->spage = ($this->page > 1) ? ($this->page * $this->perpage) - $this->perpage : 0;
    }
    public function paymentInstruments(Request $request){
        $paymentInstruments = PaymentInstrument::limit($this->perpage)->offset($this->spage)->orderBy('id', 'desc')->get();
        $total_paymentInstrument = PaymentInstrument::all()->count();
        $total_page = ceil($total_paymentInstrument / $this->perpage);
        $out = [
            "message" => "Success",
            "data"    => [
              "total_page"=>$total_page,
              "total_data"=>$total_paymentInstrument,
              "current_page" => $this->page,
              "records"=>$paymentInstruments
            ],
            "code"    => 200
        ];
        return response()->json($out, $out['code']);
    }

    public function paymentInstrumentShow(Request $request, $id){
        $total_paymentInstrument = PaymentInstrument::where("id",$id)->count();
        if($total_paymentInstrument !==0){
          $paymentInstrument = PaymentInstrument::where("id",$id)->get();
          $out = [
              "message" => "Success",
              "data"    => [
                "record"=> $paymentInstrument
              ],
              "code"    => 200
          ];
        }else{
          $out = [
              "message" => "Failed to Show Payment Instrument",
              "code"   => 500,
          ];
        }
        return response()->json($out, $out['code']);
    }

    public function paymentInstrumentShowByGameCodeAndPaymentInstrument(Request $request, $gamecode, $payment_instrument){
        if($request->input("is_used")=="yes"){
          $paymentInstruments = PaymentInstrument::where("is_used","yes")->where("gamecode",$gamecode)->where("payment_instrument",$payment_instrument)->limit($this->perpage)->offset($this->spage)->orderBy('id', 'desc')->get();
          $total_paymentInstrument = PaymentInstrument::where("is_used","yes")->where("gamecode",$gamecode)->where("payment_instrument",$payment_instrument)->count();
        }else if($request->input("is_used")=="no"){
          $paymentInstruments = PaymentInstrument::where("is_used","no")->where("gamecode",$gamecode)->where("payment_instrument",$payment_instrument)->limit($this->perpage)->offset($this->spage)->orderBy('id', 'desc')->get();
          $total_paymentInstrument = PaymentInstrument::where("is_used","no")->where("gamecode",$gamecode)->where("payment_instrument",$payment_instrument)->count();
        }else{
          $paymentInstruments = PaymentInstrument::where("gamecode",$gamecode)->where("payment_instrument",$payment_instrument)->limit($this->perpage)->offset($this->spage)->orderBy('id', 'desc')->get();
          $total_paymentInstrument = PaymentInstrument::where("gamecode",$gamecode)->where("payment_instrument",$payment_instrument)->count();
        }

        $total_page = ceil($total_paymentInstrument / $this->perpage);
        $out = [
            "message" => "Success",
            "data"    => [
              "total_page"=>$total_page,
              "total_data"=>$total_paymentInstrument,
              "current_page" => $this->page,
              "records"=>$paymentInstruments
            ],
            "code"    => 200
        ];
        return response()->json($out, $out['code']);
    }

    public function paymentInstrumentUse(Request $request, $payment_instrument, $amount, $gamecode){
      $validate=\Validator::make([
        "payment_instrument"=>$payment_instrument,
        "gamecode"=>$gamecode,
        "amount"=>$amount
      ],
      array(
        'payment_instrument' => 'required',
        'gamecode' => 'required|min:6',
        'amount' => 'required'
      ));

      if ($validate->fails()) {
          $out = [
              "message" => $validate->messages(),
              "code"    => 500
          ];
          return response()->json($out, $out['code']);
      }

      $description = $request->input("description") !== null || $request->input("description") !== "" ? $request->input("description") : "";

      for($i=1; $i <= $amount; $i++){
        $pi=PaymentInstrument::where("is_used","no")->where("gamecode",$gamecode)->where("payment_instrument",$payment_instrument)->limit(1)->orderBy("id","DESC")->first();
        $paymentInstrument = PaymentInstrument::find($pi->id);
        $paymentInstrument->description = $description;
        $paymentInstrument->is_used = "yes";
        $paymentInstrument->save();
      }
      $out = [
          "message" => "Success",
          "code"    => 200,
      ];
      return response()->json($out, $out['code']);
    }

    public function paymentInstrumentAdd(Request $request, $payment_instrument, $amount, $gamecode){
      $validate=\Validator::make([
        "payment_instrument"=>$payment_instrument,
        "gamecode"=>$gamecode,
        "amount"=>$amount
      ],
      array(
        'payment_instrument' => 'required',
        'gamecode' => 'required|min:6',
        'amount' => 'required'
      ));

      if ($validate->fails()) {
          $out = [
              "message" => $validate->messages(),
              "code"    => 500
          ];
          return response()->json($out, $out['code']);
      }

      $description = $request->input("description") !== null || $request->input("description") !== "" ? $request->input("description") : "";

      for($i=1; $i <= $amount; $i++){
        $data = [
            "payment_instrument" => $payment_instrument,
            "gamecode" => $gamecode,
            "description" => $description,
            "is_used" => "no"
        ];
        PaymentInstrument::create($data);
      }
      $out = [
          "message" => "Success",
          "code"    => 200,
      ];
      return response()->json($out, $out['code']);
    }

    public function paymentInstrumentCreate(Request $request){
        $validate=\Validator::make($request->all(),
        array(
          'payment_instrument' => 'required',
          'gamecode' => 'required|min:6',
          'description' => 'min:10',
          'is_used' => 'required|in:yes,no'
        ));

        if ($validate->fails()) {
            $out = [
                "message" => $validate->messages(),
                "code"    => 500
            ];
            return response()->json($out, $out['code']);
        }

        $payment_instrument = $request->input("payment_instrument");
        $gamecode= $request->input("gamecode");
        $description = $request->input("description");
        $is_used = $request->input("is_used");

        $data = [
            "payment_instrument" => $payment_instrument,
            "gamecode" => $gamecode,
            "description" => $description,
            "is_used" => $is_used
        ];

        if (PaymentInstrument::create($data)) {
            $out = [
                "message" => "Success",
                "code"    => 200,
            ];
        } else {
            $out = [
                "message" => "Failed to Create Payment Instrument",
                "code"   => 500,
            ];
        }

        return response()->json($out, $out['code']);
    }

    public function paymentInstrumentUpdate(Request $request, $id){
        $total_paymentInstrument = PaymentInstrument::where("id",$id)->count();
        if($total_paymentInstrument !==0){
          $validate=\Validator::make($request->all(),
          array(
            'payment_instrument' => 'required',
            'gamecode' => 'required|min:6',
            'description' => 'min:10',
            'is_used' => 'required|in:yes,no'
          ));

          if ($validate->fails()) {
              $out = [
                  "message" => $validate->messages(),
                  "code"    => 500
              ];
              return response()->json($out, $out['code']);
          }

          $payment_instrument = $request->input("payment_instrument");
          $gamecode= $request->input("gamecode");
          $description = $request->input("description");
          $is_used = $request->input("is_used");

          $paymentInstrument = PaymentInstrument::find($id);
          $paymentInstrument->payment_instrument = $payment_instrument;
          $paymentInstrument->gamecode = $gamecode;
          $paymentInstrument->description = $description;
          $paymentInstrument->is_used = $is_used;
          $paymentInstrument->save();

          $out = [
              "message" => "Success",
              "code"    => 200,
          ];
        }else{
          $out = [
              "message" => "Failed to Update Payment Instrument",
              "code"   => 500,
          ];
        }
        return response()->json($out, $out['code']);
    }

    public function paymentInstrumentDelete(Request $request, $id){
        $total_paymentInstrument = PaymentInstrument::where("id",$id)->count();
        if($total_paymentInstrument !==0){
          $paymentInstrument = PaymentInstrument::find($id);
          $paymentInstrument->delete();
          $out = [
              "message" => "Success",
              "code"    => 200,
          ];
        }else{
          $out = [
              "message" => "Failed to Delete Payment Instrument",
              "code"   => 500,
          ];
        }
        return response()->json($out, $out['code']);
    }
}
