<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Rate as UserModel;
    class Rate extends Controller{
        /**
         * 获取所有评价
         * 2018-8-26    创建   袁宜照
         * 
         */
        public function get_all(){
            $data = UserModel::all();
            return $data;
        }
        /**
         * 获取特定订单号的评价
         * 2018-8-26    创建   袁宜照
         * @param string $order_id 订单号
         */
        public function get_rate($order_id){
            $data = UserModel::all(["order_id"=>$order_id]);
            if($data){
                return $data;
            }
            else return 0;
        }
    }