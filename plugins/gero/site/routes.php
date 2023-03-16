<?php
    use Gero\Deposits\Models\Deposits;
    use Gero\Purchases\Models\Cryptos;
    use Gero\Site\Models\Logs;
    use RainLab\User\Models\User;


    Route::match(['get'], '/generate_token_paydunya', function () {
            
        $fields = array(
            'invoice' => array(
                'total_amount'  => Session::get("amount"),
                'description'  => 'Deposit from Step by step -'.Session::get("ref")
            ),
            'store' => array(
                'name' => 'Step by step'
            ),
            'actions' => array(
                "cancel_url" => "https://step-by-step.exchange/deposits",
                "return_url" => "https://step-by-step.exchange/deposits"
            )
        );
                    
        $headers = array(
            'Accept'                => 'application/x-www-form-urlencoded',
            'PAYDUNYA-PRIVATE-KEY'  => 'live_private_CVi1CCefEFhTAFX2qd5FIC31ubz',
            'PAYDUNYA-MASTER-KEY'   => 'uMZ5IFIR-j46J-wz35-JiQ1-GIvs0BPVSDVk',
            'PAYDUNYA-TOKEN'        => 'hMQ4mhI9oLoPyBM0uygj'
        );
        
        $request = Requests::post("https://app.paydunya.com/api/v1/checkout-invoice/create",
                                    $headers, json_encode($fields),
                                    array('timeout' => 30)
                                );
        $var = json_decode($request->body);
          
        if($var->response_code == "00"){

            $depo = Deposits::where("ref", Session::get("ref"))->first();
            $depo->ref = $var->token;
            $depo->status="waiting";
            $depo->save();
                
            Session::put("ref", $var->token);
                    
            return json_encode(array("success"=>$request->success, "token"=>$var->token));
            
        }
         
        else return json_encode(array("success"=>"error"));

    })->middleware('web');

    Route::match(['post'], '/ipn/paydunya', function () {
            
        $log = date("Y-m-d h:i:sa")." : [PayDunya] [IPN] [ENTER] ". "<br>";
        
        if(!Request::all()){
            $log .= "<br>". date("Y-m-d h:i:sa")." No Data ". "<br>";
            
            $reg = new Logs();
            $reg->msg = $log;
            $reg->save();

           return Redirect::to("/deposits");
        }
        else{
            
            $res = json_decode(json_encode(Request::all()));
            
            $log .= "<br>". date("Y-m-d h:i:sa")." ".json_encode($res)."<br>";
            
            $pay = Deposits::where("ref", $res->data->invoice->token)->first();
            
            if( $pay->status == "waiting"){
                $log .= "<br>". date("Y-m-d h:i:sa")." [DEPOSIT] [SQL] ".$res->data->invoice->token." Response : ".json_encode($pay->id). " old is : ".json_encode($pay->status). "------ new is : ".strtoupper($res->data->status). "<br>";
                
                if($res->data->status == "completed") $status = "success"; 
                else if(in_array($res->data->status, ["failed", "cancelled"])) $status = "failed";
                else $status = "waiting"; 

                $stater = Deposits::find($pay->id);
                $stater->status = $status;
                $stater->save();

                $user = User::where("id",$pay->user_id)->first();

                $template = "step_deposit_".$status;
                    
                $vars = ['name' => $user->name." ".$user->surname, 
                    'email' => $user->email,
                    'via' => "Paydunya",
                    'amount' => $pay->amount,
                    'status' => $status
                ];
        
                Mail::send($template, $vars, function($message) use ($vars) {
                    $message->to($vars["email"]);
                    $message->subject("[".strtoupper($vars['status'])."] Deposit of ".$vars['amount']." XOF via ".$vars['via']);
                });

            } else  $log .= "<br>". date("Y-m-d h:i:sa")." Already treated.";

            $reg = new Logs();
            $reg->msg = $log;
            $reg->save();

            return Redirect::to("/deposits");

        }

    });

    Route::match(['post'], '/ipn/fedapay', function () {
            
        $log = date("Y-m-d h:i:sa")." : [Fedapay] [IPN] [ENTER] ". "<br>";
        
        if(!Request::all()){
            $log .= "<br>". date("Y-m-d h:i:sa")." No Data ". "<br>";
            
            $reg = new Logs();
            $reg->msg = $log;
            $reg->save();

            http_response_code(400);
        }
        else{
            
            $res = json_decode(json_encode(Request::all()));
            
            $log .= "<br>". date("Y-m-d h:i:sa")." ".json_encode($res)."<br>";

            $pay = Deposits::where("real", $res->entity->description)->first();
            //$pay = Deposits::where("ref", $res->entity->id)->first();
            //if($pay==null) $pay = Deposits::where("ref", $res->entity->description)->first();
            
            if($pay != null){
                $log .= "<br>". date("Y-m-d h:i:sa")." [DEPOSIT] [SQL] ".$res->entity->id." Response : ".json_encode($pay->id). " old is : ".json_encode($pay->status). "------ new is : ".strtoupper($res->entity->status). "<br>";
                    
                if(in_array($pay->status, ["waiting","creating"])){
                    if($res->entity->status == "approved") $status = "success"; 
                    else if(in_array($res->entity->status, ["declined", "canceled"])) $status = "failed";
                    else $status = "waiting"; 

                    $stater = Deposits::find($pay->id);
                    $stater->status = $status;
                    $stater->ref = $res->entity->id;
                    $stater->save();

                    $user = User::where("id",$pay->user_id)->first();

                    if($status != "waiting"){
        
                        $template = "step_deposit_".$status;
                            
                        $vars = ['name' => $user->name." ".$user->surname,
                            'email' => $user->email,
                            'via' => "Fedapay",
                            'amount' => $pay->amount,
                            'status' => $status
                        ];
                
                        Mail::send($template, $vars, function($message) use ($vars) {
                            $message->to($vars["email"]);
                            $message->subject("[".strtoupper($vars['status'])."] Deposit of ".$vars['amount']." XOF via ".$vars['via']);
                        });
                    }
                } else  $log .= "<br>". date("Y-m-d h:i:sa")." Already treated.";
            } else   $log .= "<br>". date("Y-m-d h:i:sa")." Not found.";

            $reg = new Logs();
            $reg->msg = $log;
            $reg->save();

            http_response_code(200);

        }

    });

    Route::match(['get','post'], '/ipn/kkiapay', function () { 
            
        $log = date("Y-m-d h:i:sa")." : [KkiaPay] [IPN] [ENTER] ". "<br>";
        
        if(!Request::all()){
            $log .= "<br>". date("Y-m-d h:i:sa")." No Data ". "<br>";
            
            $reg = new Logs();
            $reg->msg = $log;
            $reg->save();

            http_response_code(400);
        }
        else{
            
            $res = json_decode(json_encode(Request::all()));
            
            $log .= "<br>". date("Y-m-d h:i:sa")." ".json_encode($res)."<br>";
            
            if($res->event == "transaction.ping"){
                $reg = new Logs();
                $reg->msg = $log;
                $reg->save();
            } else {
                $pay = Deposits::where("real", $res->stateData)->first();

                if ( $pay != null){
                    if( $pay->status != "success"){
                        $log .= "<br>". date("Y-m-d h:i:sa")." [DEPOSIT] [SQL] ".$res->stateData." Response : ".json_encode($pay->id). " old is : ".json_encode($pay->status). "------ new is : ".strtoupper($res->event). "<br>"; 
                        
                        if($res->isPaymentSucces == true) $status = "success";
                        else $status = "waiting"; 

                        $stater = Deposits::where("id",$pay->id)->first();
                        $stater->status = $status;
                        $stater->save();

                        $user = User::where("id",$pay->user_id)->first();

                        $template = "step_deposit_".$status;
                            
                        $vars = ['name' => $user->name." ".$user->surname,
                            'email' => $user->email,
                            'via' => "KKiaPay",
                            'amount' => $pay->amount,
                            'status' => $status
                        ];
                
                        Mail::send($template, $vars, function($message) use ($vars) {
                            $message->to($vars["email"]);
                            $message->subject("[".strtoupper($vars['status'])."] Deposit of ".$vars['amount']." XOF via ".$vars['via']);
                        });

                    } else  $log .= "<br>". date("Y-m-d h:i:sa")." Already treated.";

                    $reg = new Logs();
                    $reg->msg = $log;
                    $reg->save();

                } else {
                    $log .= "<br>". date("Y-m-d h:i:sa")." Transaction not found ". "<br>";

                    $reg = new Logs();
                    $reg->msg = $log;
                    $reg->save();
                }
            }
            http_response_code(200);

        }

    });

    Route::match(['get','post'], '/ipn/coinpayments', function () {
            
        $log = date("Y-m-d h:i:sa")." : [Coinpayments] [IPN] [ENTER] ". "<br>";
        
        if(!Request::all()){
            $log .= "<br>". date("Y-m-d h:i:sa")." No Data ". "<br>";
            
            $reg = new Logs();
            $reg->msg = $log;
            $reg->save();
            
            return Redirect::to("/buy");
        }
        else{
            
            $res = json_decode(json_encode(Request::all()));
            
            $log .= "<br>". date("Y-m-d h:i:sa")." ".json_encode($res)."<br>";
            
            $pay = Cryptos::where("ref", $res->id)->first();
            
            if( $pay->status == "waiting"){
                $log .= "<br>". date("Y-m-d h:i:sa")." [DEPOSIT] [SQL] ".$res->id." Response : ".json_encode($pay->id). " old is : ".json_encode($pay->status). "------ new is : ".strtoupper($res->status_text). "<br>";
                
                if((int)$res->status >= 100 || (int)$res->status == 2) $status = "success"; 
                else if($res->status < 0) $status = "failed";
                else $status = "waiting"; 

                $stater = Cryptos::find($pay->id);
                $stater->status = $status;
                //$stater->hash = $res->send_tx;
                $stater->save();

                $user = User::where("id",$pay->user_id)->first();

                $template = "step_crypto_".$status;
                    
                $vars = ['name' => $user->name." ".$user->surname,
                    'email' => $user->email,
                    'currency' => $pay->currency,
                    'amount' => $pay->amount, 
                    'status' => $status
                ];
        
                Mail::send($template, $vars, function($message) use ($vars) {
                    $message->to($vars["email"]);
                    $message->subject("[".strtoupper($vars['status'])."] Purchase of ".$vars['currency']." for ".$vars['amount']."XOF");
                });

            } else  $log .= "<br>". date("Y-m-d h:i:sa")." Already treated.";

            $reg = new Logs();
            $reg->msg = $log;
            $reg->save();

            return Redirect::to("/buy");

        }

    });

    Route::match(['get'], '/return/kkiapay', function () {
            
        if(Request::input("transaction_id")){
            Session::forget('amount');
            Session::forget('user');
            Session::forget('ref');
            Session::forget('via');

            $log = new Logs();
            $log->msg = "[KKiapay] [CALLBACK] [ENTER] : TRX ID ".json_encode(Request::input("transaction_id"));
            $log->save();

            $fields = array(
                'transactionId' => Request::input("transaction_id")
            );
            
            $headers = array(
                'Content-Type'      => 'application/json',
                'x-api-key'         => 'abc5d6b0492111ebac8c011d955383e3'
            );
        
            $request = Requests::post("https://api.kkiapay.me/api/v1/transactions/status",
                                    $headers, json_encode($fields),
                                    array('timeout' => 30)
                                );
        
            $var = json_decode($request->body);
            
            $rlw = new Logs();
            $rlw->msg = "[KKiapay] [CALLBACK] [RESPONSE] ".json_encode($var);
            $rlw->save();

            if($var != ""){

                
                $rlw = new Logs();
                $rlw->msg = "[KKiapay] enter ";
                $rlw->save();

                $pay = Deposits::where("real", $var->state)->first();

                
                $rlw = new Logs();
                $rlw->msg = "[KKiapay] pay ".json_encode($pay);
                $rlw->save();

                $user = User::where("id",$pay->user_id)->first();

                
                
                $rlw = new Logs();
                $rlw->msg = "[KKiapay] user ".json_encode($user);
                $rlw->save();

                
                if(($pay->status != "success") && ($var->status == "SUCCESS")){

                    $rlw = new Logs();
                    $rlw->msg = "[KKiapay] success ";
                    $rlw->save();
                        
                    $rlw = new Logs();
                    $rlw->msg = "KKiapay] [CALLBACK] Success";
                    $rlw->save();

                    $stater = Deposits::where("id",$pay->id)->first();
                    $stater->ref = Request::input("transaction_id");
                    $stater->status = "success";
                    $stater->save();
                    
                    $vars = ['name' => $user->name." ".$user->surname,
                        'email' => $user->email,
                        'via' => "KKiaPay",
                        'amount' => $pay->amount,
                        'status' => "success"
                    ];
            
                    Mail::send("step_deposit_success", $vars, function($message) use ($vars) {
                        $message->to($vars["email"]);
                        $message->subject("Your deposit of ".$vars['amount']." XOF via ".$vars['via']." completed and ".$vars['status']);
                    });

                    Flash::success("Deposit successfully completed.");
                    return Redirect::to('/deposits');
                    
                }else {
                    
                    $rlw = new Logs();
                $rlw->msg = "[KKiapay] soucis ";
                $rlw->save();

                    $stater = Deposits::where("id",$pay->id)->first();
                    $stater->status = "waiting";
                    $stater->save();
            
                    Flash::success("Your deposit is pending.");
                    return Redirect::to('/deposits');

                }

            } else {
            
                $rlw = new Logs();
                $rlw->msg = "[KKiapay] [CALLBACK] [FETCH] Failed from kkia";
                $rlw->save();
            
                Flash::success("Your deposit is pending.");
                return Redirect::to('/deposits');
            }
        } else{
            return Redirect::to('/error');
        }
    });