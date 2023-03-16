<?php namespace Gero\Site\Components;

    use Cms\Classes\ComponentBase;
    use Gero\Site\Models\Options;
    use Gero\Deposits\Models\Deposits;
    use Auth;
    use Flash;
    use Redirect;
    use Session;
    use Carbon\Carbon;

    class Loader extends ComponentBase{

        public function componentDetails(){
            return [
                'name'          => 'Loader Controller',
                'description'   => ''
            ];
        }

        public function onRun(){
            $notLogged = array("auth-login", "auth-register", "auth-reset", "home", "error", "maintenance");
            $valid = array("dashboard", "deposits", "buys");
            $admin = array("admin-validation", "admin-deposits", "admin-buys", "admin-users", "admin-options");
            $pay = array("pay-fedapay", "pay-cinetpay", "pay-paydunya", "pay-payplus", "pay-kkiapay");
            
            if(Auth::check()){
                $user = Auth::getUser(); 

                if(in_array($this->page->id, $notLogged)) //déjà connecté
                    return Redirect::to('/dashboard');
                else if($user->is_activated == 0 && ($this->page->id != "auth-activation")){ // si non activé
                    Flash::error("You must validate your email address before you can access this page.");
                    return Redirect::to('/activation');
                } else if(in_array($this->page->id, $admin) && (!isset($user->groups[0]) || (isset($user->groups[0]) && $user->groups[0]->code != 'admin'))){ // si non admin
                    return Redirect::to('/dashboard');
                } else if(($user->validate == 1) && ($this->page->id == "auth-validation")){ // si validé
                    Flash::warning("You no longer have access to this page. Your account is already validated.");
                    return Redirect::to('/dashboard');
                } else if(in_array($this->page->id, $valid) && $user->validate != 1){ // si non validé
                    Flash::error("You must complete the KYC check before you can access this page.");
                    return Redirect::to('/validation');
                }

                if(in_array($this->page->id, $pay)){
                    if(Session::get('ref')){
                        if((Deposits::where("real",Session::get('ref'))->exists()) && (Deposits::where("real",Session::get('ref'))->value("created_at") < Carbon::now()->subMinutes(2))){
                            Flash::error("This transaction has expired. Please try again.");
                            return Redirect::to('/deposits');
                        }
                    } else{
                        Flash::error("You cannot access this page by yourself");
                        return Redirect::to('/deposits');
                    }
                }

                // Processors state
                $processor = array(
                    "pay-fedapay" => Options::where("id",1)->value("fedapay"),
                    "pay-paydunya" => Options::where("id",1)->value("paydunya"),
                    "pay-cinetpay" => Options::where("id",1)->value("cinetpay"),
                    "pay-payplus" => Options::where("id",1)->value("payplus"),
                    "pay-kkiapay" => Options::where("id",1)->value("kkiapay"),
                    "buy" => Options::where("id",1)->value("usdt")
                );

                if((!isset($user->groups[0]) || (isset($user->groups[0]) && $user->groups[0]->code != 'admin')) && (in_array($this->page->id, $pay) && $processor[$this->page->id] == 0)){
                    Flash::error("Transaction with this processor is temporarily disabled. Agrega :".$processor[$this->page->id]."------- Group: ".$user->groups[0]." Pay : ".$pay);
                    //return Redirect::back();
                    return Redirect::to('/dashboard');
            
                    // Session::forget('amount');
                    // Session::forget('user');
                    // Session::forget('ref');
                    // Session::forget('via');
                    
                }
            
            } else {

                if(!in_array($this->page->id, $notLogged)){
                    //Flash::warning("You must be logged in to access this page.");
                    return Redirect::to('/login');
                }

            }
        }

    }