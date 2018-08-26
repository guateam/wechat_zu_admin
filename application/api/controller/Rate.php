<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Rate as UserModel;
    class Rate extends Controller{
        public function get_all(){
            $data = UserModel::all();
            return $data;
        }
        public function get_rate($order_id){
            $data = UserModel::all(["order_id"=>$order_id]);
            if($data){
                return $data;
            }
            else return 0;
        }
    }