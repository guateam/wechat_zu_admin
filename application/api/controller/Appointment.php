<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Appointment as UserModel;
    class Appointment extends Controller{
        /**
         * 获取所有预约信息
         * 2018-8-26 创建   袁宜照
         * 
         */
        public function get_all(){
            $data = UserModel::all();
            return $data;
        }
        /**
         * 获取指定订单的预约信息
         * 2018-8-26 创建   袁宜照
         * @param string $order_id 订单号
         */
        public function get_appointment($order_id){
            $data = UserModel::get(["order_id"=>$order_id]);
            if($data){
                return $data;
            }
            else return 0;
        }

    }