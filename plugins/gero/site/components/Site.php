<?php namespace Gero\Site\Components;

    use Cms\Classes\ComponentBase;
    use Gero\Site\Models\Wallets;
    use Gero\Site\Models\Rates;
    use Gero\Site\Models\Options;
    use Gero\Site\Models\Logs;
    use Gero\Deposits\Models\Deposits;
    use Gero\Purchases\Models\Cryptos;
    use RainLab\User\Models\User;
    use Auth;
    use Redirect;
    use Input;
    use Flash;
    use AjaxException;
    use Carbon\Carbon;

    class Site extends ComponentBase{

        public function componentDetails(){
            return [
                'name'          => 'Site Controller',
                'description'   => ''
            ];
        }

        public function onRun(){
            
        }

        public function state(){
            return Options::find(1);
        }

        public function onAddWallet(){

            $user = Auth::getUser();

            $wall = new Wallets();
            $wall->user_id = $user->id;
            $wall->name = Input::get("name");
            $wall->currency = Input::get("currency");
            $wall->wallet = Input::get("wallet");
            $wall->save();

        }

        public function onChangeRate(){

            $rate = Rates::where("currency", Input::get('currency'))->first();
            $rate->buy = Input::get("rates");
            $rate->save();

        }

        public function onChangeHomeText(){

            $var = Options::find(1);
            $var->text = Input::get("hometext");
            $var->save();

        }

        public function rates(){
            return Rates::get();
        }

        public function wallet(){

            $user = Auth::getUser();
            return Wallets::where("user_id",$user->id)->get();
        }

        public function userslist(){
            $list = User::latest()->paginate(50);

            foreach($list as $item){
				$item->wallet = Deposits::where("user_id",$item->id)->where("status","success")->sum("amount") - Cryptos::where("status","success")->where("user_id",$item->id)->sum("amount");
			}

			return $list;

			
        }

        public function countUsers7days(){
            return User::where('created_at','>=',Carbon::now()->subdays(7))->count("id");
        }

        public function countUsers(){
            return User::count("id");
        }

        public function countUsersActive(){
            return User::where("is_activated",1)->count("id");
        }

        public function countUsersValid(){
            return User::where("validate",1)->count("id");
        }

        public function userOne(){
            return User::find($this->param('id'));
        }

        public function onDisable(){
            $user = User::find($this->param('id'));
            $user->is_activated = 0;
            $user->save();

            Flash::success("User successfully disabled.");
            return Redirect::refresh();
        }

        public function onActivate(){
            $user = User::find($this->param('id'));
            $user->is_activated = 1;
            $user->save();

            Flash::success("User successfully activated.");
            return Redirect::refresh();
        }

        public function userlast(){
            $user = Auth::getUser();
            $repdep = Deposits::where("user_id", $user->id )->latest()->take(10)->get()->toArray();
            $repcryp = Cryptos::where("user_id", $user->id )->latest()->take(10)->get()->toArray();
            
  			$array = array();
			return array_merge($array, $repdep, $repcryp);
        }

        public function userWallet(){
            $user = Auth::getUser();
            $dep = Deposits::where("user_id", $user->id )->where("status", "success")->sum("amount");
            $cryp = Cryptos::where("user_id", $user->id )->whereIn("status", ["success", "creating", "waiting"])->sum("amount");
            
			return $dep - $cryp;
        }

        public function onLogs(){
            $reg = new Logs();
            $reg->msg = Input::get("log");
            $reg->save();
        }

        public function onFedapayDisable(){$act = Options::find(1);    $act->fedapay = 0;  $act->save(); return Redirect::refresh();}
        public function onFedapayActivate(){$act = Options::find(1);    $act->fedapay = 1;  $act->save(); return Redirect::refresh();}

        public function onPaydunyaDisable(){$act = Options::find(1);    $act->paydunya = 0;  $act->save(); return Redirect::refresh();}
        public function onPaydunyaActivate(){$act = Options::find(1);    $act->paydunya = 1;  $act->save(); return Redirect::refresh();}
        
        public function onCinetpayDisable(){$act = Options::find(1);    $act->cinetpay = 0;  $act->save(); return Redirect::refresh();}
        public function onCinetpayActivate(){$act = Options::find(1);    $act->cinetpay = 1;  $act->save(); return Redirect::refresh();}
        
        public function onPayplusDisable(){$act = Options::find(1);    $act->payplus = 0;  $act->save(); return Redirect::refresh();}
        public function onPayplusActivate(){$act = Options::find(1);    $act->payplus = 1;  $act->save(); return Redirect::refresh();}
        
        public function onKkiapayDisable(){$act = Options::find(1);    $act->kkiapay = 0;  $act->save(); return Redirect::refresh();}
        public function onKkiapayActivate(){$act = Options::find(1);    $act->kkiapay = 1;  $act->save(); return Redirect::refresh();}
        
        public function onUsdtDisable(){$act = Options::find(1);    $act->usdt = 0;  $act->save(); return Redirect::refresh();}
        public function onUsdtActivate(){$act = Options::find(1);    $act->usdt = 1;  $act->save(); return Redirect::refresh();}
        

    }