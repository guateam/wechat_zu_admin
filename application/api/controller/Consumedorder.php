<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Consumedorder as UserModel;
    class Consumedorder extends Controller{
        public function get_all(){
            $data = UserModel::all();
            return $data;
        }
        public function get_order($order_id){
            $data = UserModel::get(["order_id"=>$order_id]);
            if($data){
                return $data;
            }
            else return 0;
        }
    }