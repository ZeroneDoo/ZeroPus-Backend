<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public static function xenditCreateInvoice($req)
    {
        $key = base64_encode(env("XENDIT_API_KEY") ?? "xnd_development_pBDKZYN7fMeitSXBnVkNQ3yx5jZTIMwS3nDuVC7pXC8ODFF4XOVCgK71m9SLBq:");
        $externalId = "ZP-" . date("Ymd") . "-" .time() ."-" . rand(100000, 999999);

        $data = [
            "external_id" => $externalId,
            "description" => "Zeropus ". Credit::find($req->credit_id)->amount ." Credit",
            "invoice_duration" => $req->invoice_duration,
            "amount" => Credit::find($req->credit_id)->price,
            "success_redirect_url" => $req->success_url ?? url("/success/payment")
        ];

        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => "Basic $key"
        ])->post("https://api.xendit.co/v2/invoices", $data);

        return json_decode($response->body());
    }

    public function createInvoice(Request $req)
    {
        try {
            DB::beginTransaction();
            $response = self::xenditCreateInvoice($req);
            
            $data = Payment::create([
                "user_id" => $req->user()->id,
                "credit_id" => $req->credit_id,
                "external_id" => $response->external_id,
                "checkout_link" => $response->invoice_url,
                "description" => $response->description,
                "status" => $response->status,
            ]);

            DB::commit();
    
            return response()->json($data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                "message" => $th->getMessage(),
                "line" => $th->getLine()
            ], 403);

        }
    }
    
    public function callback(Request $req)
    {   
        try {

            DB::beginTransaction();

            // if($req->header("x-callback-token") !== env("XENDIT_WEBHOOK_TOKEN"))
            // {
            //     return response()->json([
            //         "status" => false,
            //         "message" => "webhook token is not recognize"
            //     ]);
            // }
            
            $payment = Payment::with("user","credit")->where("external_id", $req->external_id)->first();

            if(!$payment) {
                return response()->json([
                    "messge" => "payment not found"                    
                ]);
            }

            if($req->status === "EXPIRED")
            {
                $payment->delete();

                DB::commit();

                return response()->json([
                    "status" => true,
                    "message" => "payment has been expired"
                ]);

            } else if($req->status === "PAID") {
                $user = User::find($payment->user_id);
                $credit = Credit::find($payment->credit_id);

                // return response()->json(['credit'=>$user->credit + $credit->amount]);
                
                $user->update([
                    "credit" => $user->credit + $credit->amount
                ]);

                
                $payment->update([
                    "status" => $req->status
                ]);
    
                DB::commit();
    
                return response()->json([
                    "message" => "succeess paid the payment",
                    "data" => $payment
                ]);
            }

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                "message" => $th->getMessage(),
                "line" => $th->getLine()
            ], 403);
        }
    }
}
