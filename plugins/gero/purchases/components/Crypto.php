<?php namespace Gero\Purchases\Components;

    use Cms\Classes\ComponentBase;
    use Gero\Site\Models\Rates;
    use Gero\Purchases\Models\Cryptos;
    use Gero\Deposits\Models\Deposits;
    use RainLab\User\Models\User;
    use Auth;
    use Redirect;
    use Input;
    use Flash;
    use AjaxException;
    use Session;
    use CoinpaymentsAPI;

    class Crypto extends ComponentBase{

        public function componentDetails(){
            return [
                'name'          => 'Crypto Controller',
                'description'   => ''
            ];
        }

        public function onRun(){
        }

        public function onBuy(){

            $user = Auth::getUser();

            $rate=Rates::where("currency","USDT.TRC20")->value("buy");

            if((Deposits::where("user_id", $user->id )->where("status", "success")->sum("amount")-Cryptos::where("user_id", $user->id )->whereIn("status", ["success", "creating", "waiting"])->sum("amount")) >= Input::get("give")){
                
                $private_key = '592Ae24e5a3f2d1DDF961d3edE7D49e1B38C11c8e5c7c964CfFEc50Cb1804230';
                $public_key = '098c54b32a9cab123c85d8ec1b246be72bbfdca2e03c062cab5a6132b0875723';

                $fields = [
                    'amount' => (Input::get('give')/$rate),
                    'add_tx_fee' => 0,
                    'currency' => Input::get('currency'),
                    'currency2' => Input::get('currency'),
                    'address' => Input::get('address'),
                    'auto_confirm' => 1,
                    'note' => $user->email." buy ".Input::get("currency")." for ".Input::get("give")." XOF",
                    'ipn_url' => 'https://step-by-step.exchange/ipn/coinpayments'
                ];

                $cps_api = new CoinpaymentsAPI($private_key, $public_key, 'json');
                $trans = $cps_api->CreateWithdrawal($fields);
                $cps_api = null;

                if($trans["error"] == "ok"){

                    $buy = new Cryptos();
                    $buy->user_id = $user->id;
                    $buy->amount = Input::get("give");
                    $buy->ref = $trans["result"]["id"];
                    $buy->address = Input::get("address");
                    $buy->currency = Input::get("currency");
                    $buy->status = "waiting";
                    $buy->save();

                } else{
                    throw new AjaxException($trans["error"]); 
                }


            } else{
                throw new AjaxException("Your balance is insufficient to perform this transaction.");
            }
            
        }

        public function onComplete(){}

        function totalUsdt(){
            return Cryptos::where("status","success")->where("currency","USDT.TRC20")->sum("amount");
        }

        function totalUserUsdt(){
            $user = Auth::getUser();
            return Cryptos::where("status","success")->where("user_id",$user->id)->where("currency","USDT.TRC20")->sum("amount");
        }

        function total(){
            return Cryptos::where("status","success")->sum("amount");
        }

        function totalOne(){
            return Cryptos::where("status","success")->where("user_id",$this->param('id'))->sum("amount");
        }

        function totalUser(){
            $user = Auth::getUser();
            return Cryptos::where("status","success")->where("user_id",$user->id)->sum("amount");
        }

        function list(){
            return Cryptos::latest()->paginate(50);
        }

        function listOne(){
            return Cryptos::where("user_id",$this->param('id'))->latest()->paginate(50);
        }

        function listUser(){
            $user = Auth::getUser();
            return Cryptos::where("user_id",$user->id)->latest()->paginate(50);
        }

        public function balance_coinp(){
        
            $private_key = '592Ae24e5a3f2d1DDF961d3edE7D49e1B38C11c8e5c7c964CfFEc50Cb1804230';
            $public_key = '098c54b32a9cab123c85d8ec1b246be72bbfdca2e03c062cab5a6132b0875723';
    
            $cps_api = new CoinpaymentsAPI($private_key, $public_key, 'json');
            $balances = $cps_api->GetCoinBalances();
            
            
            // Get coinpayment rates
            $rates = $cps_api->GetShortRates();
            $coin_currencies = ['USDT.TRC20'];
            $cps_api = null;

            $fiat_to_btc = $rates['result']['USD']['rate_btc'];
            
            $output = array();
            foreach ($coin_currencies as $currency) {
                $this_currency_rate_btc = $rates['result'][$currency]['rate_btc'];
                $this_currency_price = ($fiat_to_btc / $this_currency_rate_btc);
                $output[$currency] = $this_currency_price;
            }
            
            Session::put('rates', $output);

            return $balances;
        }

    }