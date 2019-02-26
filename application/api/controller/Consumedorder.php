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

        public function ErrorLogTest()
        {
            try
            {
                throw new ExceptionNew("sunyue");
            }
            catch(Exception $e)
            {
               $info = $e->getMessage();
               $time = time();
               Db::query("insert into errorlog (`operation`,`loginfo`,`time`) values ('test','$info','$time')");
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

        public function change_clock($order_id,$state,$roomid,$jdid)
		{
            $order = UserModel::get(['order_id'=>$order_id]);
            
            if($order)
            {
                if($state == 2)
				{
					Db::query("update service_order A,technician B  set B.busy = 1 where A.order_id='$order_id' and B.job_number = A.job_number");//调整为繁忙

					$jd = Db::query("select B.commission2 from service_order A, service_type B where A.order_id = $order_id and A.item_id = B.id");
					if($jd)
					{
						$jd_ticheng = $jd[0]['commission2'];
					}
					
					Db::query("update service_order set private_room_number = $roomid, jd_number = $jdid, jd_ticheng = $jd_ticheng where order_id='$order_id'");//填入房间id和接待工号 接待提成
                }
				else if($state == 4)
				{
					$t = time();
					Db::query("update consumed_order set end_time=$t where order_id='$order_id'");
					
                    //调整为空闲
                    Db::query("update service_order A,technician B  set B.busy = 0 where A.order_id='$order_id' and B.job_number = A.job_number");
                }
                $order->state = $state;//修改订单状态
                $order->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }

        public function create_order($info,$total_price,$phone,$method,$username,$jiedai,$note)
		{
            if ($phone != '')
            {
                $cus = Db::query("select * from customer where phone_number='$phone'");
                if($cus)
                {
                    $user_id=$cus[0]['openid'];
                }
                else
                {
                    //若找不到该账号，则单子内的id自动填为用户名
                    $user_id = $username;   
                }
            }
            else//没有输入手机号码
            {  
                //若找不到该账号，则单子内的id自动填为用户名
                $user_id = $username;          
            }

            //若余额支付，判断余额是否足够
            if($method == 3)
			{
                $ctrl = new \app\api\controller\Customer();
                $cash = $ctrl->get_cash_by_phone($phone);
                if($cash === false)
				{
                    return json(['status'=>0,'msg'=>"该手机号未注册会员"]);//创建订单失败
                }
                if($cash < $total_price)
				{
                    return json(['status'=>0,'msg'=>"余额不足"]);//创建订单失败
                }
            }
            
            $time = time();
            $order_id = strval(date("Ymdhis",$time));
            for($i=0;$i<10;$i++)
			{
                $order_id .= strval(rand(0,9));//订单号
            }
			
            $order = new \app\api\model\Consumedorder(['order_id'=>$order_id,'user_id'=>$user_id,
            'state'=>4,'payment_method'=>$method,'generated_time'=>$time,'appoint_time'=>$time,'contact_phone'=>$phone,
            'pay_amount'=>$total_price*100,'user_num'=>1,'payment_user_id'=>$user_id,'note'=>$note,'source'=>1,'end_time'=>$time]);
			
            $order->save();
            
			foreach($info as $it)
			{
                if ($it['service_id'] =='')
                {
                    $servicename = $it['service_name'];
                    $item = Db::query("select * from service_type where name='$servicename'");

                    if($item)
                    {
                        $item_id = $item[0]['ID'];
                    }
                }
                else 
                {
                    $item_id = $it['service_id'];
                }

                $job_number = $it['job_number'];
				
                $ticheng = 0;
                $yongjin = 0;

                $service_type = Db::query("select * from service_type where ID='$item_id' ");
                $technician = Db::query("select * from technician where job_number='$job_number'");

                $yongjin = $service_type[0]['invite_income'];
               
                 
				if($it['clock'] == 1)//排钟
				{
					$ticheng = $service_type[0]['pai_commission'];                    
				}
				else if($it['clock'] == 2)//点钟
				{
					$ticheng = $service_type[0]['commission'];
				}
				
				if($it['clock'] == 1)//排钟
				{
					$jd_ticheng = $service_type[0]['pai_commission2'];              
				}
				else if($it['clock'] == 2)//点钟
				{
					$jd_ticheng = $service_type[0]['commission2'];
				}
				
				$room_number = $it['room_number'];
				$room = Db::query("select * from private_room where name='$room_number' ");
				if($room)
				{
					$room_id = $room[0]['ID'];
				}				

                $sv_order = new \app\api\model\Serviceorder(['order_id'=>$order_id,'service_type'=>1,
                'item_id'=>$item_id,'job_number'=>$it['job_number'],'price'=>$it['price']*100,
                'private_room_number'=>$room_id,'clock_type'=>$it['clock'],'appoint_time'=>$time,
                'ticheng'=>$ticheng,'yongjin'=>$yongjin,'jd_number'=>$jiedai,'jd_ticheng'=>$jd_ticheng]);

                $sv_order->save();
            }
            return json(['status'=>1,'data'=>$order_id]);
        }

        public function update_order($order_id,$info,$state,$pay_method,$cash,$appoint_time,$note,$source,$jiedai)
		{
            $order = UserModel::get(['order_id'=>$order_id]);
            if(gettype($appoint_time) == "string")
			{
                $appoint_time = strtotime($appoint_time);
            }
            $service_orders = Db::query("select * from service_order where order_id='$order_id'");
            for($i=0;$i<count($info);$i++)
			{
                $info[$i]['appoint_time']=$service_orders[$i]['appoint_time'];
            }
            Db::query("delete from service_order where order_id='$order_id'");//把service_order表中与本订单相关内容先删除
            //为什么需要先删除，service_order订单号相同的很多，技师也可以改，服务项目也可以改，找不到相应的去更新
            if($order)
			{
                $order->state = $state;
                $order->payment_method = $pay_method;
                $order->pay_amount = $cash;
                $order->appoint_time = $appoint_time;
                $order->note = $note;
                $order->source = $source;
                $order->save();
                foreach($info as $it)
				{
                    $service_id = $it['service_id'];
                    $eachservice = Db::query("select * from service_type where ID='$service_id'");//service_type表根据service_id查询到价格
                    if($eachservice)
					{
                        //$price = $eachservice[0]['price'];//这里改成市场价
                        $price = $eachservice[0]['market_price'];//这里改成市场价
                    }
					else
					{
                        $price="服务不存在";
                    }

                    //包厢号不对，提成和佣金没有了

                    //提成从service表里取
                    if ($it['clock'] == 2)//点钟
                    {
                        $ticheng = $eachservice[0]['commission'];
						$jd_ticheng = $eachservice[0]['commission2'];
                    }
                    else if ($it['clock'] == 1)//排钟
                    {
                        $ticheng = $eachservice[0]['pai_commission'];
						$jd_ticheng = $eachservice[0]['pai_commission2'];   
                    }					
					

                    //佣金从service表里取
                    $yongjin = $eachservice[0]['invite_income'];
                    
					$jiedai = 0;//这里有问题，回头再改，修改订单，技师工号都会变成 0
					
                    $sv_order = new \app\api\model\Serviceorder(['order_id'=>$order_id,'service_type'=>1,
                    'item_id'=>$it['service_id'],'job_number'=>$it['job_number'],'price'=>$price,
                    'private_room_number'=>$it['room_id'],'clock_type'=>$it['clock'],'appoint_time'=>$it['appoint_time'], 
                    'ticheng'=>$ticheng,
                    'yongjin'=>$yongjin,
					'jd_number'=>$jiedai,
					'jd_ticheng'=>$jd_ticheng
                    ]);
                    
					$sv_order->save();
                }
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
    }