<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Skilllevel as UserModel;
    class Skilllevel extends Controller{

        public function get_all(){
            $all = UserModel::all();
            return $all;
        }
    }