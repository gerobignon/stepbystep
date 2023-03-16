<?php namespace Gero\Deposits\Components;

    use Cms\Classes\ComponentBase;
    use Gero\Deposits\Models\Deposits;
    use RainLab\User\Models\User;
    use Gero\Site\Models\Logs;
    use Auth;
    use Redirect;
    use Input;
    use Flash;
    use Mail;
    use AjaxException;
    use Session;
    use Requests;
    use Carbon\Carbon;

    class Deposit extends ComponentBase{

        public function componentDetails(){
            return [
                'name'          => 'Deposit Controller',
                'description'   => ''
            ];
        }

        public function onRun(){
        }

        public function onPay(){

            $user = Auth::getUser();

            if(Input::get("via") == "payplus"){
                $fields = array(
                    "commande"      =>  array (
                        "invoice"       =>  array (
                            "items"         =>  array (
                                "name"          =>  "Deposit on Step by step",
                                "quantity"      =>  1,
                                "unit_price"    =>  Input::get("amount"),
                                "total_price"   =>  Input::get("amount")
                            ),
                            "total_amount"      =>  Input::get("amount"),
                            "devise"            =>  "xof",
                            "description"       =>  "Deposit on Step by step",
                            "customer"          =>  $user->phone,
                            "otp"               =>  ""
                        ),
                        "store"         => array (
                            "name"              =>  "Step by step Exchange",
                            "website_url"       =>  "https://step-by-step.exchange"
                        ),
                        "actions"       =>  array (
                            "cancel_url"        =>  "https://step-by-step.exchange/pay_with_payplus",
                            "return_url"        =>  "https://step-by-step.exchange/pay_with_payplus",
                            "callback_url"      =>  "https://step-by-step.exchange/pay_with_payplus"
                        )
                    )
                );
                
                $headers = array(
                    'Apikey'	    => 'YTYY741H8D8STXWUX',
                    'Authorization'	=> 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZF9hcHAiOiI4NzIiLCJpZF9hYm9ubmUiOjYzOTUsImRhdGVjcmVhdGlvbl9hcHAiOiIyMDIyLTA1LTI4IDEwOjQ1OjAxIn0.0KjJd2uUtbM5pM-PObEUS98U86av362UU3URVw3Xt2M',
                    'Content-Type'	=> 'application/json',
                    'Accept'		=> 'application/json'
                );
            
                if($request = Requests::post("https://app.payplus.africa/pay/v01/redirect/checkout-invoice/create", $headers, json_encode($fields), array('timeout' => 30) )){
                
                    $res = json_decode($request->body);
                    
                    $reg = new Logs();
                    $reg->msg = json_encode($res);
                    $reg->save();

                } else {
                    
                    $reg = new Logs();
                    $reg->msg = json_encode($request);
                    $reg->save();
                    
                }
                
            } else{

                $ref = sha1($user->id.uniqid().uniqid(time()).time());

                $dep = new Deposits();
                $dep->user_id = $user->id;
                $dep->amount = Input::get("amount");
                $dep->ref = $ref;
                $dep->real = $ref;
                $dep->status = "creating";
                $dep->via = Input::get("via"); 
                $dep->save();

                $reg = new Logs();
                $reg->msg = "New ".Input::get("via")." deposit ".$dep->id." for user : ".$user->id." (".$user->email."). Amount : ".Input::get("amount")." - Ref : ".$ref;
                $reg->save();

                Session::put("amount", Input::get("amount"));
                Session::put("user", $user->id);
                Session::put("ref", $ref);
                Session::put("via", Input::get("via"));
            }
            
        }

        public function onComplete(){
            
            if(Session::get("via")=="fedapay"){
                if(Input::get("status") == "approved") $status = "success"; 
                else if(in_array(Input::get("status"), ["declined", "canceled"])) $status = "failed";
                else $status = "waiting"; 
            }
            elseif(Session::get("via")=="paydunya"){
                if(Input::get("status") == "completed") $status = "success"; 
                else if(in_array(Input::get("status"), ["failed", "cancelled"])) $status = "failed";
                else $status = "waiting"; 
            }
            elseif(Session::get("via")=="cinetpay"){
                if(Input::get("status") == "ACCEPTED") $status = "success"; 
                else if(Input::get("status")== "REFUSED") $status = "failed";
                else $status = "waiting"; 
            }
            
            $depo = Deposits::where("ref",Session::get("ref"))->first();
            if($depo==null) $depo = Deposits::where("ref", Input::get('reference'))->first();

            $depo->status = $status;
            $depo->ref = Input::get('reference');
            $depo->save();
            
            Session::forget('amount');
            Session::forget('user');
            Session::forget('ref');
            Session::forget('via');

            if(Session::get("via")=="cinetpay" && $status != "waiting"){

                $user = User::where("id",$depo->user_id)->first();

                $template = "step_deposit_".$status;
                    
                $vars = ['name' => $user->name." ".$user->surname,
                    'email' => $user->email,
                    'via' => "CinetPay",
                    'amount' => $depo->amount,
                    'status' => $status
                ];
        
                Mail::send($template, $vars, function($message) use ($vars) {
                    $message->to($vars["email"]);
                    $message->subject("Your deposit of ".$vars['amount']."via ".$vars['via']." XOF completed and ".$vars['status']);
                });
            } 

            Flash::success("Transaction is processing...");

            return Redirect::to("/deposits");
        
        }

        public function onCancel(){
            
            Session::forget('amount'); 
            Session::forget('user');
            Session::forget('ref');
            Session::forget('via');

            return Redirect::to("/deposits");
        
        }

        public function onChangeManual(){
            
            
            $depo = Deposits::find(Input::get('id'));

            $depo->status = "success";
            $depo->save();

            $user = User::find($depo->user_id);
                
            $vars = ['name' => $user->name." ".$user->surname,
                'email' => $user->email,
                'via' => $depo->via,
                'amount' => $depo->amount,
                'status' => "success"
            ];
    
            Mail::send("step_deposit_success", $vars, function($message) use ($vars) {
                $message->to($vars["email"]);
                $message->subject("Your deposit of ".$vars['amount']."via ".$vars['via']." XOF completed and ".$vars['status']);
            });

            Flash::success("Transaction marked as success.");
            return Redirect::refresh();


        }

        function totalFedapay(){
            return Deposits::where("via","fedapay")->where("status","success")->sum("amount");
        }

        function totalUserFedapay(){
            $user = Auth::getUser();
            return Deposits::where("user_id",$user->id)->where("status","success")->where("via","fedapay")->sum("amount");
        }

        function totalPaydunya(){
            return Deposits::where("via","paydunya")->where("status","success")->sum("amount");
        }

        function totalUserPaydunya(){
            $user = Auth::getUser();
            return Deposits::where("user_id",$user->id)->where("status","success")->where("via","paydunya")->sum("amount");
        }

        function totalCinetpay(){
            return Deposits::where("via","cinetpay")->where("status","success")->sum("amount");
        }

        function totalUserCinetpay(){
            $user = Auth::getUser();
            return Deposits::where("user_id",$user->id)->where("status","success")->where("via","cinetpay")->sum("amount");
        }

        function totalKkiapay(){
            return Deposits::where("via","kkiapay")->where("status","success")->sum("amount");
        }

        function totalUserKkiapay(){
            $user = Auth::getUser();
            return Deposits::where("user_id",$user->id)->where("status","success")->where("via","kkiapay")->sum("amount");
        }

        function totalPayplus(){
            return Deposits::where("via","payplus")->where("status","success")->sum("amount");
        }

        function totalUserPayplus(){
            $user = Auth::getUser();
            return Deposits::where("user_id",$user->id)->where("status","success")->where("via","payplus")->sum("amount");
        }

        function total(){
            return Deposits::where("status","success")->sum("amount");
        }

        function totalOne(){
            return Deposits::where("user_id",$this->param('id'))->where("status","success")->sum("amount");
        }

        function totalUser(){
            $user = Auth::getUser();
            return Deposits::where("user_id",$user->id)->where("status","success")->sum("amount");
        }

        function list(){
            $depo = Deposits::latest()->paginate(50);

            foreach($depo as $item){
				if(($item->status == "creating") && ($item->created_at < Carbon::now()->subdays(2)) || ($item->status == "waiting") && ($item->created_at < Carbon::now()->subdays(7))){
                    $maj = Deposits::find($item->id);
                    $maj->status = "failed";
                    $maj->save();
                }
				
			}

            return $depo;
        }

        function listOne(){
            $depo = Deposits::where("user_id",$this->param('id'))->latest()->get();

            foreach($depo as $item){
				if(($item->status == "creating") && ($item->created_at < Carbon::now()->subdays(2)) || ($item->status == "waiting") && ($item->created_at < Carbon::now()->subdays(7))){
                    $maj = Deposits::find($item->id);
                    $maj->status = "failed";
                    $maj->save();
                }
			}

            return $depo;
        }

        function listUser(){
            $user = Auth::getUser();
            $depo = Deposits::where("user_id",$user->id)->latest()->paginate(50);

            foreach($depo as $item){
				if(($item->status == "creating") && ($item->created_at < Carbon::now()->subdays(2)) || ($item->status == "waiting") && ($item->created_at < Carbon::now()->subdays(7))){
                    $maj = Deposits::find($item->id);
                    $maj->status = "failed";
                    $maj->save();
                }
			}

            return $depo;
        }

    }