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

        //订单数量
        public function pay_count(){
            $data = UserModel::all();
            $count = count($data);
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
            $data = UserModel::all();
            $count = count($data);
            if($count_ori<$count)
            {
                return $count;
            }
            else 
            {
                return false;
            }
        }

        public function pay_order($order_id,$open_id){
            $paying_order = UserModel::get(['order_id'=>$order_id]);
            if($paying_order){
                //检查余额是否足够支付
                $cus = new \app\api\controller\Customer();
                $cash = $cus->get_cash($open_id);
                if($cash < $paying_order->pay_amount/100){
                    return json(['status'=>-1]);
                }

                //如果足够支付，设置订单状态为已支付
                $paying_order->state = 4;
                $paying_order->save();
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }

        }

        public function get_payamount($order_id){
            $paying_order = UserModel::get(['order_id'=>$order_id]);
            if($paying_order){
                return json(['status'=>1,'payamount'=>$paying_order->pay_amount/100]);
            }else{
                return json(['status'=>0]);
            }
        }
    }