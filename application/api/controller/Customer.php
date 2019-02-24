<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Customer as UserModel;
    use think\Db;
    class Customer extends Controller{

        public function get_customer($id){
            $data = UserModel::get(['openid'=>$id]);
            if($data){
                return $data;
            }
        }
        public function get_spokename_by_phone($phone){
            $data = Db::query("select spoke_name from customer where phone_number = '$phone'");
            if($data){
                return json(['name'=>$data[0]['spoke_name']]);
            }
            return json(['name'=>'']);
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

        public function get_cash($openid){
            $records = Db::query("select sum(charge)/100 as charge from recharge_record where user_id='$openid' group by user_id");
            $charge =0;
            if(count($records)>0)
                $charge = intval($records[0]['charge']);
            $pay = Db::query("select sum(pay_amount)/100 as pay from consumed_order where user_id='$openid' and (state!=3 and state !=0) and payment_method=3");
            if(count($pay)>0)
                $pay = intval($pay[0]['pay']);
            $money = $charge - $pay;
            return $money;
        }
        
        public function get_cash_by_phone2($phone)
		{
            $tech = Db::query("select * from customer where phone_number = $phone");
            if($tech){
                $money = self::get_cash($tech[0]['openid']);
                return json(['cash'=>$money]);
            }else{
                return json(['cash'=>0]) ;
            }
        }
		
		public function getRoomInfo($roomname)
		{
			$API_URL = "http://103.239.247.197:8899";
			
			$url = $API_URL."/api?url=http://www.dzbsaas.com/footmassage/receptiondesk/listroom.do";			
			$content = file_get_contents($url);	
			$obj = json_decode($content);
			if ($obj->success == false)
			{
				echo $content;//success:false;msg:token is Invalid
				return;
			}

			$ret1 = $obj->data;			
			$RoomRet = json_decode($ret1);
			$Rooms = $RoomRet->Rooms;
			$getRoom = false;

			foreach($Rooms as $eachroom)
			{
				if ($eachroom->name == $roomname)
				{
					if ($eachroom->status == "Occupied")
					{
						if ($eachroom->occupied->Finish->AllFinish == true)
						{
							//准备结帐
							echo json_encode([									
									'success'=>true,
									'status'=>'Finish',
									'checkInId'=>$checkInId = $eachroom->checkin->id,
							]);
							return;
						}
						else
						{
							//还在服务中							
							echo json_encode([
									'success'=>true,
									'status'=>'Busy',
							]);
							return;
						}
					}
					else //空闲或者打扫卫生
					{						
						echo json_encode([
								'success'=>true,
								'status'=>'Idle',
						]);
						return;
					}
				}
			}
		}
		
		public function getPayInfo($checkInId)
		{
			$API_URL = "http://103.239.247.197:8899";
			$mt = time()."000";
			$url2 = $API_URL."/api?url=http://www.dzbsaas.com/footmassage/receptiondesk/getpayinfo.do?checkInId=".$checkInId."&_=".$mt;
			$content2 = file_get_contents($url2);
			echo $content2;//直接返回获取到的json
			return;
		}
		
		public function getPayInfo2($roomname)
		{
			$API_URL = "http://103.239.247.197:8899";
			
			//$roomname = $_GET["roomname"];
			//$roomname = $_POST["roomname"];
			
			$url = $API_URL."/api?url=http://www.dzbsaas.com/footmassage/receptiondesk/listroom.do";			
			$content = file_get_contents($url);	
			$obj = json_decode($content);
			if ($obj->success == false)
			{
				echo $content;//success:false;msg:token is Invalid
				return;
			}

			$ret1 = $obj->data;			
			$RoomRet = json_decode($ret1);
			$Rooms = $RoomRet->Rooms;
			$getRoom = false;

			foreach($Rooms as $eachroom)
			{
				if ($eachroom->status == "Occupied")
				{
					if ($eachroom->occupied->Finish->AllFinish == true)
					{
						if ($eachroom->name == $roomname)
						{
							$getRoom = true;

							$checkInId = $eachroom->checkin->id;

							//$mt = app\api\controller\getMillisecond();
							$mt = time()."000";
							
							$url2 = $API_URL."/api?url=http://www.dzbsaas.com/footmassage/receptiondesk/getpayinfo.do?checkInId=".$checkInId."&_=".$mt;

							$content2 = file_get_contents($url2);

							$obj2 = json_decode($content2);

							//这里要判断一下 token掉线 获取不到数据
							if ($obj2->success == false)
							{
								echo $content2;//success:false;msg:token is Invalid
								return;
							}

							echo $content2;//直接返回获取到的json
							return;
						}
					}
				}
			}

			if ($getRoom == false)
			{
				echo json_encode([
						'success'=>false,
						'info'=>"Found No Room"
				]);
				return;
			}

			echo json_encode([
						'success'=>false,
						'info'=>"No Result"
				]);
			return;

			function getMillisecond()
			{
				list($msec, $sec) = explode(' ', microtime());
				$msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
				return $msectimes = substr($msectime,0,13);
			}
		}
		
		

        public function get_cash_by_phone($phone){
            $user = Db::query("select * from customer where phone_number = '$phone'");
            if($user){
                $money = self::get_cash($user[0]['openid']);
                return $money;
            }else{
                return false;
            }
        }

        public function addinfo($openid,$spoke_name,$phone){
            Db::query("update customer set spoke_name='$spoke_name',phone_number='$phone' where openid='$openid'");
            return json(['status'=>1]);
        }
    }