<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Consumedorder as UserModel;
    class Consumedorder extends Controller{
        /**
         * 获取所有订单
         * 2018-8-26 创建   袁宜照
         * 
         */
        public function get_all(){
            $data = UserModel::all();
            return $data;
        }

        public function pay_count(){
            $data4 = UserModel::all(['state'=>4]);
            $data5 = UserModel::all(['state'=>5]);
            $count = count($data4)+ count($data5);
            return json($count);
        }

        /**
         * 获取特定订单号的订单
         * 2018-8-26 创建   袁宜照
         * @param string $order_id 订单号
         */
        public function get_order($order_id){
            $data = UserModel::get(["order_id"=>$order_id]);
            if($data){
                return $data;
            }
            else return 0;
        }


        public function check_new($count_ori){
            $data4 = UserModel::all(['state'=>4]);
            $data5 = UserModel::all(['state'=>5]);
            $count = count($data4)+ count($data5);
            if($count_ori<$count)
            {
                return $count;
            }
            else 
            {
                return false;
            }
        }
    }