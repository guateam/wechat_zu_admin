<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Consumedorder as UserModel;
    use think\Db;

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
        public function get_all_origin(){
            $data = Db::query("select * from consumed_order");
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
        /**
         * 取消预约
         */
        public function disappointment($order_id){
            $order = UserModel::get(['order_id'=>$order_id]);
            if($order){
                $order->state = 0;
                $order->save();
            }else{
                return json(['state'=>0]);
            }
            return json(['state'=>1]);
        }

        public function change_clock($order_id,$state){
            $order = UserModel::get(['order_id'=>$order_id]);
            
            if($order)
            {
                if($state == 2){
                    //调整为繁忙
                    Db::query("update service_order A,technician B  set B.busy = 1 where A.order_id='$order_id' and B.job_number = A.job_number");
                }else if($state == 4){
                    //调整为空闲
                    Db::query("update service_order A,technician B  set B.busy = 0 where A.order_id='$order_id' and B.job_number = A.job_number");
                }
                $order->state = $state;
                $order->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }

        public function create_order($info,$total_price,$phone,$method,$username,$note){
            $cus = Db::query("select * from customer where phone_number='$phone'");
            //若找不到该账号，则单子内的id自动填为用户名
            $user_id = $username;
            if($cus)
                $user_id=$cus[0]['openid'];

            //若余额支付，判断余额是否足够
            if($method == 3){
                $ctrl =new \app\api\controller\Customer();
                $cash = $ctrl->get_cash_by_phone($phone);
                if($cash === false){
                    return json(['status'=>0,'msg'=>"该手机号未注册会员"]);
                }
                if($cash < $total_price){
                    return json(['status'=>0,'msg'=>"余额不足"]);
                }
            }
            
            $time = time();
            $order_id = strval(date("Ymdhis",$time));
            for($i=0;$i<10;$i++){
                $order_id .= strval(rand(0,9));
            }
            $order = new UserModel(['order_id'=>$order_id,'user_id'=>$user_id,
            'state'=>4,'payment_method'=>$method,'generated_time'=>$time,'appoint_time'=>$time,'contact_phone'=>$phone,
            'pay_amount'=>$total_price*100,'user_num'=>1,'payment_user_id'=>$user_id,'note'=>$note,'source'=>1]);
            $order->save();
            foreach($info as $it){
                $sv_order = new \app\api\model\Serviceorder(['order_id'=>$order_id,'service_type'=>1,
                'item_id'=>$it['service_id'],'job_number'=>$it['job_number'],'price'=>$it['price'],
                'private_room_number'=>$it['room_number'],'clock_type'=>$it['clock'],'appoint_time'=>$time]);
                $sv_order->save();
            }
            return json(['status'=>1,'data'=>$order_id]);
        }

        public function update_order($order_id,$info,$state,$pay_method,$cash,$appoint_time,$note,$source){
            $order = UserModel::get(['order_id'=>$order_id]);
            if(gettype($appoint_time) == "string"){
                $appoint_time = strtotime($appoint_time);
            }
            $service_orders = Db::query("select * from service_order where order_id='$order_id'");
            for($i=0;$i<count($info);$i++){
                $info[$i]['appoint_time']=$service_orders[$i]['appoint_time'];
            }
            Db::query("delete from service_order where order_id='$order_id'");
            if($order){
                $order->state = $state;
                $order->payment_method = $pay_method;
                $order->pay_amount = $cash;
                $order->appoint_time = $appoint_time;
                $order->note = $note;
                $order->source = $source;
                $order->save();
                foreach($info as $it){
                    $service_id = $it['service_id'];
                    $price = Db::query("select price from service_type where ID='$service_id'");
                    if($price){
                        $price = $price[0]['price'];
                    }else{
                        $price="服务不存在";
                    }
                    $sv_order = new \app\api\model\Serviceorder(['order_id'=>$order_id,'service_type'=>1,
                    'item_id'=>$it['service_id'],'job_number'=>$it['job_number'],'price'=>$price,
                    'private_room_number'=>$it['room_number'],'clock_type'=>$it['clock'],'appoint_time'=>$it['appoint_time']]);
                    $sv_order->save();
                }
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
    }