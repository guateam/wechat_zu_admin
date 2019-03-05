<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Rechargerecord as UserModel;
    use think\Db;
    class Rechargerecord extends Controller{

        public function get_all()
		{
            $all = Db::query("select * from recharge_record order by generated_time");//UserModel::all();
            for($i=0;$i<count($all);$i++)
			{
                $openid = $all[$i]['user_id'];
                $all[$i]['generated_time'] = date('Y-m-d H:i:s',$all[$i]['generated_time']);

                if ($openid != '')
                {
                    $name = Db::query("select * from customer where openid = '$openid'");
                    if($name)
                    {
                        $all[$i]['username'] =$name[0]['name'];
                    }
                    else
                    {
                        $all[$i]['username'] ="用户名未知";
                    }
                }
                else
                {
                    $all[$i]['username'] =$all[$i]['user_name'];
                }

            }
            return $all;
        }
		
		public function rechargeMoney($cardNo,$phone,$username,$cash,$paymethod,$tech)
		{
			/*
			//根据手机号码，查询 customer表，查到客户的openid
			 $cus = Db::query("select openid from customer where phone_number = $phone");
			 $openid = "";
			 if ($cus)
			 {
				 $openid = $cus[0]['openid'];
			 }
			 */
            
            $openid = '';//到店充卡的时候 微信账号和会员卡不关联 

			$card = Db::query("select * from vipcard where cardNo = '$cardNo'"); 
			if (!$card)
			{
				return json(['status'=>0,'msg'=>'对不起，该卡号不存在']);
			}
			 
			//根据promotion表 计算充多少送多少
			$promotion = Db::query("select * from promotion order by need desc");
			$cash = $cash * 100;

            $back = 0;
            for ($i=0;$i<count($promotion);$i++)
            {
                if ($cash >= $promotion[$i]['need'])
                {
                    $back =  $promotion[$i]['back'];
                    break;
                }                
            }
			
			//-------------------------------------------------
			$dict=['1','2','3','4','5','6','7','8','9','0'];
			$rnd = "";
			$rnd.=date("YmdHis");//年月日时分秒
			
			for($i=0;$i<2;$i++)
			{
				$rnd.=$dict[rand(0,count($dict)-1)];//6位随机整数
			}
			for($i=0;$i<4;$i++)
			{
				$rnd.=$dict[rand(0,count($dict)-1)];
			}
			//-------------------------------------------------
            //插入数据库
			$time = time();
			Db::query("insert into recharge_record (`record_id`,`user_id`,`user_name`,`phone_number`,`cardNo`,`charge`,`payment_method`,`job_number`,`generated_time`,`note`,`type`) 
					values ('$rnd','$openid','$username','$phone','$cardNo','$cash','$paymethod','$tech','$time','充值','1')");

            if ($back > 0)
            {
                $rnd_b = $rnd.'b';
                Db::query("insert into recharge_record (`record_id`,`user_id`,`user_name`,`phone_number`,`cardNo`,`charge`,`payment_method`,`job_number`,`generated_time`,`note`,`type`) 
					values ('$rnd_b','$openid','$username','$phone','$cardNo','$back','$paymethod','$tech','$time','充值送钱','2')");
            }
			
			//vipcard表不要update，永远不要，vipcard表是同步过来的

            return json(['status'=>1,'msg'=>$rnd]);
		}
		
        public function get_all_by_user()
		{
            $all_cus = Db::query("select * from customer group by openid");
            $records = [];
            foreach($all_cus as $cus)
			{
                //没有openid的用户属于异常数据，不进行处理
                $cus_openid = $cus['openid'];
                if($cus_openid == "")continue;

                $record = Db::query("select sum(A.charge)/100 as charge from recharge_record A,customer B where A.user_id='$cus_openid' and B.openid=A.user_id");
                if($record){
                    if(is_null($record[0]['charge']) )$record[0]['charge'] = 0;
                    $record = array_merge($record[0],['spoke_name'=>$cus['spoke_name'],'phone_number'=>$cus['phone_number'],'name'=>$cus['name'],'user_id'=>$cus['openid'],'gender'=>$cus['gender'],'level'=>$cus['level'],'registration_date'=>$cus['registration_date']]);
                    array_push($records,$record);
                }
            }
            // $records = Db::query("select sum(A.charge)/100 as charge,A.user_id,B.phone_number,B.gender,B.level,B.openid,B.registration_date from recharge_record A,customer B where A.user_id=B.openid
            // group by B.openid");
            $cus = new \app\api\controller\Customer();
            for($i=0;$i<count($records);$i++)
			{
                if($records[$i]['registration_date'] != "")
                    $records[$i]['registration_date'] = date("Y-m-d H:i:s", $records[$i]['registration_date']);
                else{
                    $records[$i]['registration_date'] = "无";
                }
                $records[$i]['charge'] = intval($records[$i]['charge']);
                $money = $cus->get_cash($records[$i]['user_id']);
                $records[$i] = array_merge($records[$i],['cash'=>$money]);
            }
            return $records;
        }

        /**
         * 获取某个工号的技师一段时间内的充卡额，默认本月
         * 
         */
        public function get_by_jobnumber($num,$start="",$end="")
		{
            if($start == "")
                $start=strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
            if($end == "")
                $end = strtotime("$start +1 month -1 day");

            if($end < $start){
                $temp = $end;
                $end = $start;
                $start = $temp;
            }
            $record = Db::query("select sum(A.charge) as charge from recharge_record A where `job_number`='$num'");
            if($record){
                $money = $record[0]['charge']/100;
                return json(['status'=>1,'charge'=>$money]);
            }
            return json(['status'=>0]);
        }
		
        /**
         * 获取某个客户的充卡数额
         * 
         */
        public function get_by_user($num)
		{
            $record = UserModel::all(['user_id'=>$num]);
            if($record)
			{
                $money = 0;
                foreach($record as $rcd){
                    $time = $rcd->generated_time;
                    $time = substr($time,0,10);
                    if($time>=$start && $time<=$end)
                        $money+=$rcd->charge;
                }
                $money/=100;
                return json(['status'=>1,'charge'=>$money]);
            }
            return json(['status'=>0]);
        }
        
    }