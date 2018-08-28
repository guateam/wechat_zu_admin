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

        public function delete_customer($id){
            foreach($id as $i){
                $cus = UserModel::get(["ID"=>$i]);
                if($cus){
                    $cus->delete();
                }
            }
            return ['status'=>1];
        }
    }