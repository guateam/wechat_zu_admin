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

        public function create_order($info,$total_price,$phone,$cardNo,$method,$username,$jiedai,$note,$should_price,$openid)
		{
			if ($openid != "")
			{
				$user_id = $openid;   
			}
			else
			{
				if ($phone != '')//输入了手机号码
				{
					$cus = Db::query("select * from customer where phone_number='$phone'");
					if($cus)
					{
						$user_id = $cus[0]['openid'];
					}
				}
				else//没有输入手机号码
				{  
					//若找不到该账号，则单子内的id自动填为用户名
					 $user_id = $username;   
					                       
				}
			}	

            //若余额支付，判断余额是否足够
            if($method == 3 || $method == 9)
			{
                //$ctrl = new \app\api\controller\Customer();
                //$cash = $ctrl->get_cash_by_card($cardNo);

                $vc = Db::query("select * from vipcard where cardNo='$cardNo'");
                $cash = 0;
                if ($vc)
                {
                    $cash = $vc[0]['balance'];
                }
                
                if($cash == 0)
				{
                    return json(['status'=>0,'msg'=>"卡号出错，请重试"]);//创建订单失败
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
            
            Db::startTrans();
			try
			{
                $item_id = "";
                foreach($info as $it)
                {
					$servicename = $it['service_name'];//先根据服务名称来查询 item_id
                    $item = Db::query("select * from service_type where name='$servicename'");//从 service_type表根据 servicename 查询 item_id
					
					if ($item)//存在
					{
						$item_id = $item[0]['ID'];
					}
                    else  //有 service_id
                    {
						if ($it['service_id'] !='')//有 service_id
						{
							$item_id = $it['service_id'];
						}
						else
						{
							$item_id = 0;//临时把商品的 $item_id = 0
						}
                    }

                    $job_number = $it['job_number'];
                    
					$yongjin = 0;
                    $ticheng = 0;                    
					$jd_ticheng = 0;

					if ($item_id > 0)//商品先不录入
					{	
						$service_type = Db::query("select * from service_type where ID='$item_id' ");
						$technician = Db::query("select * from technician where job_number='$job_number'");

						if ($service_type)
						{
							$yongjin = $service_type[0]['invite_income'];
						}
						
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
						
						$room_id = $it['room_number'];//网页传过来的就是 roomid

						$sv_order = new \app\api\model\Serviceorder(['order_id'=>$order_id,'service_type'=>1,
						'item_id'=>$item_id,'job_number'=>$it['job_number'],'price'=>$it['price']*100,
						'private_room_number'=>$room_id,'clock_type'=>$it['clock'],'appoint_time'=>$time,
						'ticheng'=>$ticheng,'yongjin'=>$yongjin,'jd_number'=>$jiedai,'jd_ticheng'=>$jd_ticheng]);

						$sv_order->save();
					}
                }
                
                //-------------------------------------------
                $order = new \app\api\model\Consumedorder(['order_id'=>$order_id,'user_id'=>$user_id,
                'state'=>4,'payment_method'=>$method,'generated_time'=>$time,'appoint_time'=>$time,'contact_phone'=>$phone,
                'pay_amount'=>$total_price*100,'user_num'=>1,'payment_user_id'=>$user_id,'note'=>$note,'source'=>1,'end_time'=>$time,'shouldpay_amount'=>$should_price*100,'cardNo'=>$cardNo]);
                
                $order->save();
                //-------------------------------------------

                Db::commit(); 
                return json(['status'=>1,'data'=>$order_id]);           
            }
			catch (Exception $e) 
			{
				//$db->rollback();
				Db::rollback();//回滚
				return json(['status'=>0,'data'=>'出现异常，请重试']);
			}
			
            return json(['status'=>0]);

        }

        public function update_order($order_id,$info,$state,$pay_method,$cash,$appoint_time,$note,$source,$jiedai,$cardNo)
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
            
			Db::startTrans();
			try
			{
				for($i=0;$i<count($service_orders);$i++)
				{
					// $order_id = $service_orders[$i]['order_id'];
					// $bak_order_id = $order_id."b";
					// Db::query("insert service_order_bak set order_id='$bak_order_id' where order_id='$order_id'");//不要删除记录，改成更新记录，把原记录换个order_id保存起来
					
					$so = $service_orders[$i];
					
					$order_id = $so['order_id'];
					$service_type = $so['service_type'];
					$item_id = $so['item_id'];
					$job_number = $so['job_number'];
					$private_room_number = $so['private_room_number'];
					$price = $so['price'];
					$clock_type = $so['clock_type'];
					$show = $so['show'];
					$appoint_time = $so['appoint_time'];
					$ticheng = $so['ticheng'];
					$yongjin = $so['yongjin'];
					$jd_number = $so['jd_number'];
					$jd_ticheng = $so['jd_ticheng'];
					
					//把数据插入另一张表
					Db::query("insert into service_order_bak (`order_id`,`service_type`,`item_id`,`job_number`,`private_room_number`,`price`,`clock_type`,`show`,`appoint_time`,`ticheng`,`yongjin`,`jd_number`,`jd_ticheng`) values ('$order_id','$service_type','$item_id','$job_number','$private_room_number','$price','$clock_type','$show','$appoint_time','$ticheng','$yongjin','$jd_number','$jd_ticheng')");				
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
							//$price = $eachservice[0]['price'];//这是活动价
							$price = $eachservice[0]['market_price'];//这里改成门市价 算业绩需要
						}
						else
						{
							$price="服务不存在";
						}

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
						
						//$jiedai = 0;//这里有问题，回头再改，修改订单，技师工号都会变成 0
						
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
				}
				
				Db::commit(); 
                return json(['status'=>1]);
            }
			catch (Exception $e) 
			{
				//$db->rollback();
				Db::rollback();//回滚！
				return json(['status'=>0,'msg'=>'出现异常，请重试']);
			}
			
            return json(['status'=>0]);
        }
    }