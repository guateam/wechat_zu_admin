<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Customer as UserModel;
    class Customer extends Controller{

        public function get_customer($id){
            $data = UserModel::get(['ID'=>$id]);
            if($data){
                return $data;
            }
        }
    }