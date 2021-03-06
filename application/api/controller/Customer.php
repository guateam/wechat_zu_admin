<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Customer as UserModel;
    use think\Db;
    class Customer extends Controller
	{

		public static $API_URL = "http://103.239.247.197:8899/api?url=";
		
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

		public function get_cash($openid)//获取客户余额 先不改成 chongka_record
		{
            $records = Db::query("select sum(charge)/100 as charge from recharge_record where user_id='$openid' group by user_id");
            $charge =0;
			if(count($records)>0)
			{
				$charge = intval($records[0]['charge']);//取整
			}
            //$pay = Db::query("select sum(pay_amount)/100 as pay from consumed_order where user_id='$openid' and (state!=3 and state !=0) and payment_method=3");
			$pay = Db::query("select sum(pay_amount)/100 as pay from consumed_order where user_id='$openid' and (state!=3 and state !=0) and payment_method=11");
			if(count($pay)>0)
			{
				$pay = intval($pay[0]['pay']);//取整
			}
            $money = $charge - $pay;//充卡的金额 - 消费的金额
            return $money;
        }
        
        public function get_cash_by_phone2($phone)
		{
            $tech = Db::query("select * from customer where phone_number = $phone");
            if($tech)
			{
                $money = self::get_cash($tech[0]['openid']);
                return json(['cash'=>$money]);
            }
			else
			{
                return json(['cash'=>0]) ;
            }
		}
		
		public function get_cash_by_openid($openid)
		{
			$cus = new \app\api\controller\Customer();			
			$money = $cus->get_cash($openid);     
			
			return json(['success'=>true,'msg'=>$money]);
        }
		
		public function get_cash_by_card($cardNo)
		{
			
			//先同步，更新数据库，然后显示给客户
			$index = 0;
			$mt = time()."000";			
			$ori_url = "http://www.dzbsaas.com/footmassage/member/getlist.do?JWTTOKEN=b1f30285-1247-41f1-98f2-1cc725006191&sEcho=3&iColumns=16&sColumns=id%2Cname%2Cgender%2Cmobile%2Cphone%2CbirthS%2CcardNo%2Cbalance%2Cpoint%2CmemberRank.name%2CloginDate%2CcreateDate%2CcardType%2CisEnabled%2CisLost%2CchainStoreUse&iDisplayStart=".$index."&iDisplayLength=1000&mDataProp_0=id&sSearch_0=&bRegex_0=false&bSearchable_0=false&bSortable_0=false&mDataProp_1=name&sSearch_1=&bRegex_1=false&bSearchable_1=true&bSortable_1=false&mDataProp_2=gender&sSearch_2=&bRegex_2=false&bSearchable_2=false&bSortable_2=false&mDataProp_3=mobile&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=false&mDataProp_4=phone&sSearch_4=&bRegex_4=false&bSearchable_4=false&bSortable_4=false&mDataProp_5=birthS&sSearch_5=&bRegex_5=false&bSearchable_5=false&bSortable_5=false&mDataProp_6=cardNo&sSearch_6=&bRegex_6=false&bSearchable_6=true&bSortable_6=true&mDataProp_7=balance&sSearch_7=&bRegex_7=false&bSearchable_7=false&bSortable_7=true&mDataProp_8=point&sSearch_8=&bRegex_8=false&bSearchable_8=false&bSortable_8=true&mDataProp_9=memberRank.name&sSearch_9=&bRegex_9=false&bSearchable_9=false&bSortable_9=false&mDataProp_10=loginDate&sSearch_10=&bRegex_10=false&bSearchable_10=false&bSortable_10=true&mDataProp_11=createDate&sSearch_11=&bRegex_11=false&bSearchable_11=false&bSortable_11=true&mDataProp_12=cardType&sSearch_12=&bRegex_12=false&bSearchable_12=false&bSortable_12=false&mDataProp_13=isEnabled&sSearch_13=&bRegex_13=false&bSearchable_13=false&bSortable_13=false&mDataProp_14=isLost&sSearch_14=&bRegex_14=false&bSearchable_14=false&bSortable_14=false&mDataProp_15=chainStoreUse&sSearch_15=&bRegex_15=false&bSearchable_15=false&bSortable_15=false&sSearch=&bRegex=false&iSortCol_0=11&sSortDir_0=desc&iSortingCols=1&_=".$mt;

			$url = Customer::$API_URL.urlencode($ori_url);
			
			$content = file_get_contents($url);	
			$obj = json_decode($content);
			if ($obj->success == false)
			{
				echo $content;//success:false;msg:token is Invalid
				return;
			}
			$ret1 = $obj->data;	
			//var_dump($ret1);
			
			$CardRet = json_decode($ret1);
			$Cards = $CardRet->data;

			$cardbalance = 0;

			try
			{
				foreach($Cards as $eachcard)
				{   
					$name = $eachcard->name;
					
					if ($name == "所有合计")
					{
						//skip
					}
					else 
					{
						$thisCardNo = $eachcard->cardNo;	
						
						if ($thisCardNo == $cardNo)
						{						
							$name = $eachcard->name;	
							$gender = $eachcard->gender;	

							$gender = 0;
							if ($gender == "male")
							{
								$gender = 1;
							}
							else if ($gender == "female")
							{
								$gender = 2;
							}
							$mobile = $eachcard->mobile;
							$phone = $eachcard->phone;
							$birth = $eachcard->birthS;
							$cardNo = $eachcard->cardNo;
							$balance = $eachcard->balance;
							$point = $eachcard->point;
							if(property_exists($eachcard,'modifyDate'))
							{
								$modifyDate = $eachcard->modifyDate;
							}
							else
							{
								$modifyDate = "";
							}
							$createDate = $eachcard->createDate;
							$cardType = $eachcard->cardType;
							$isEnabled = $eachcard->isEnabled;
							$isLost = $eachcard->isLost;
							$chainStoreUse = $eachcard->chainStoreUse;

							$mycard = Db::query("select * from vipcard where cardNo = '$cardNo'");
							if ($mycard)
							{                       
								Db::query("update vipcard set name = '$name', gender = '$gender', mobile = '$mobile', phone = '$phone', birth = '$birth', cardNo = '$cardNo', balance = '$balance', point = '$point', point = '$point', modifyDate = '$modifyDate', createDate = '$createDate', cardType = '$cardType', isEnabled = '$isEnabled', isLost = '$isLost', chainStoreUse = '$chainStoreUse' where cardNo = '$cardNo'");//存在就更新                        					
							} 
							else
							{
								Db::query("insert into vipcard (`name`,`gender`,`mobile`,`phone`,`birth`,`cardNo`,`balance`,`point`,`modifyDate`,`createDate`,`cardType`,`isEnabled`,`isLost`,`chainStoreUse`) values ('$name','$gender','$mobile','$phone','$birth','$cardNo','$balance','$point','$modifyDate','$createDate','$cardType','$isEnabled','$isLost','$chainStoreUse')");//不存在就插入
							}
						}
					}							
				}			
			}
			catch(Exception $e)
			{
				echo json_encode([
						'success'=>false,
						'msg'=>'sync fail'
				]);
				return;
			}
			
			$cardbalance = $balance;
			return json(['success'=>true,'msg'=>$cardbalance]) ;
        }
		
		public function priceSync()
		{
			$mt = time()."000";			
			$ori_url = "http://www.dzbsaas.com/footmassage/servitem/getlist.do?sEcho=1&iColumns=13&sColumns=id%2Cname%2Cunit%2Cprice%2CunitRate%2CmaxRebate%2CservIsTimer%2CservLineUp%2CisMarketable%2Cmemo%2Corder%2CproductCategory.name%2Coption&iDisplayStart=0&iDisplayLength=100&mDataProp_0=id&sSearch_0=&bRegex_0=false&bSearchable_0=false&mDataProp_1=name&sSearch_1=&bRegex_1=false&bSearchable_1=false&mDataProp_2=unit&sSearch_2=&bRegex_2=false&bSearchable_2=false&mDataProp_3=price&sSearch_3=&bRegex_3=false&bSearchable_3=false&mDataProp_4=unitRate&sSearch_4=&bRegex_4=false&bSearchable_4=false&mDataProp_5=maxRebate&sSearch_5=&bRegex_5=false&bSearchable_5=false&mDataProp_6=servIsTimer&sSearch_6=&bRegex_6=false&bSearchable_6=false&mDataProp_7=servLineUp&sSearch_7=&bRegex_7=false&bSearchable_7=false&mDataProp_8=isMarketable&sSearch_8=&bRegex_8=false&bSearchable_8=false&mDataProp_9=memo&sSearch_9=&bRegex_9=false&bSearchable_9=false&mDataProp_10=order&sSearch_10=&bRegex_10=false&bSearchable_10=false&mDataProp_11=productCategory.name&sSearch_11=&bRegex_11=false&bSearchable_11=true&mDataProp_12=&sSearch_12=&bRegex_12=false&bSearchable_12=false&sSearch=&bRegex=false&_=".$mt;

			$url = Customer::$API_URL.urlencode($ori_url);
			
			$content = file_get_contents($url);	
			$obj = json_decode($content);
			if ($obj->success == false)
			{
				echo $content;//success:false;msg:token is Invalid
				return;
			}
			$ret1 = $obj->data;	
			//var_dump($ret1);
			
			$ServRet = json_decode($ret1);
			$Services = $ServRet->data;
			$num = 0;

			try
			{
				foreach($Services as $eachservice)
				{
					$servicename = $eachservice->name;
					$serviceprice = ($eachservice->price) * 100;

					$myservice = Db::query("select * from service_type where name = '$servicename'");
					if ($myservice)
					{
						Db::query("update service_type set price = '$serviceprice' where name = '$servicename'");
						$num = $num + 1;						
					}           	
				}
				echo json_encode([
								'success'=>true,
								'msg'=>'',
								'num'=>$num,
				]);
				return;
			}
			catch(Exception $e)
			{
				echo json_encode([
						'success'=>false,
						'msg'=>'sync fail'
				]);
				return;
			}
			return;
		}
		
		public function getRoomInfo($roomname)
		{
			$url = Customer::$API_URL."http://www.dzbsaas.com/footmassage/receptiondesk/listroom.do";			
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
			$mt = time()."000";
			$url2 = Customer::$API_URL."http://www.dzbsaas.com/footmassage/receptiondesk/getpayinfo.do?checkInId=".$checkInId."&_=".$mt;
			$content2 = file_get_contents($url2);
			echo $content2;//直接返回获取到的json
			return;
		}
		
		public function getPayInfo2($roomname)
		{
			//$roomname = $_GET["roomname"];
			//$roomname = $_POST["roomname"];
			
			$url = Customer::$API_URL."http://www.dzbsaas.com/footmassage/receptiondesk/listroom.do";			
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
							
							$url2 = Customer::$API_URL."/api?url=http://www.dzbsaas.com/footmassage/receptiondesk/getpayinfo.do?checkInId=".$checkInId."&_=".$mt;

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