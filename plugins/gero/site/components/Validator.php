<?php namespace Gero\Site\Components;

    use Cms\Classes\ComponentBase;
    use Gero\Site\Models\Validation;
    use RainLab\User\Models\User;
    use Auth;
    use Redirect;
    use Input;
    use Flash;
    use AjaxException;

    class Validator extends ComponentBase{

        public function componentDetails(){
            return [
                'name'          => 'Validation Controller',
                'description'   => ''
            ];
        }

        public function onRun(){
        }

        function status(){
            $user = Auth::getUser();
            return Validation::where("user_id",$user->id)->exists();
        }

        function onValidate(){
        
            $user = Auth::getUser();
            
            if(Validation::where("user_id",$user->id)->exists()) throw new AjaxException('You already have a validation request pending. Please wait for it to be processed.');
            else{

                $vali = new Validation();
                $vali->user_id = $user->id;
                $vali->idcard = Input::file('idcard');
                $vali->selfie = Input::file('selfie');
                $vali->save();

                return true;
            }
        }

        function onReject(){
            $vali = Validation::where("user_id", Input::get("id"));
            $vali->delete();

            $user = User::find(Input::get("id"));
            $user->validate = 0;
            $user->save();

            Flash::success("The account was rejected.");
            return Redirect::refresh();
        }

        function onValid(){
            $user = User::find(Input::get("id"));
            $user->validate = 1;
            $user->save();

            Flash::success("The account is validated.");
            return Redirect::refresh();
        }

        function pending(){
           return Validation::whereHas('user', function($q){ $q->where('validate', '=', 2);})->latest()->paginate(20);
        }

        function list(){
            if($this->param('status') != ""){
				if($this->param('status') == "validated") $status = 1; else $status = 2;
			} else $status = 2;

            if($this->param('order') != ""){
				if($this->param('order') == "desc") $order = "desc"; else $order = "asc";
			} else $order = "asc";

            return Validation::whereHas('user', function($q) use ($status) {$q->where('validate', '=', $status);})->orderBy('created_at', $order)->paginate(20);
        }

        function one(){
            return Validation::where("user_id", $this->param('id'))->first();
        }

        function all(){
           return Validation::latest()->paginate(20);
        }
    
        function onFilter(){
            if(Input::get('status') != "")  $status = Input::get("status"); else $status = 1;
            if(Input::get('order') != "") $order = Input::get("order"); else $order = "desc";

            $this->listes = Validation::whereHas('user', function($q) use ($status) {$q->where('validate', $status);})->orderBy('created_at', $order)->paginate(20);
        }

        public $listes;

    }