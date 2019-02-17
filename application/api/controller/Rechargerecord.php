<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Rechargerecord as UserModel;
    use think\Db;
    class Rechargerecord extends Controller{

        public function get_all(){
            $all = Db::query("select * from recharge_record");//UserModel::all();
            for($i=0;$i<count($all);$i++){
                $openid = $all[$i]['user_id'];
                $all[$i]['generated_time'] = date('Y-m-d H:i:s',$all[$i]['generated_time']);
                $name = Db::query("select * from customer where openid = '$openid'");
                if($name){
                    $all[$i]['username'] =$name[0]['name'];
                }else{
                    $all[$i]['username'] ="用户不存在";
                }

            }
            return $all;
        }
        public function get_all_by_user(){
            $all_cus = Db::query("select * from customer group by openid");
            $records = [];
            foreach($all_cus as $cus){
                $cus_openid = $cus['openid'];
                $record = Db::query("select sum(A.charge)/100 as charge from recharge_record A,customer B where A.user_id='$cus_openid' and B.openid=A.user_id");
                if($record){
                    if(is_null($record[0]['charge']) )$record[0]['charge'] = 0;
                    $record = array_merge($record[0],['phone_number'=>$cus['phone_number'],'name'=>$cus['name'],'user_id'=>$cus['openid'],'gender'=>$cus['gender'],'level'=>$cus['level'],'registration_date'=>$cus['registration_date']]);
                    array_push($records,$record);
                }
            }
            // $records = Db::query("select sum(A.charge)/100 as charge,A.user_id,B.phone_number,B.gender,B.level,B.openid,B.registration_date from recharge_record A,customer B where A.user_id=B.openid
            // group by B.openid");
            $cus = new \app\api\controller\Customer();
            for($i=0;$i<count($records);$i++){
                if($records[$i]['registration_date'] != "")
                    $records[$i]['registration_date'] = date("Y-m-d H:i:s", $records[$i]['registration_date']);
                else{
                    $records[$i]['registration_date'] = "无";
                }
                $records[$i]['charge'] = (int) $records[$i]['charge'];
                $money = $cus->get_cash($records[$i]['user_id']);
                $records[$i] = array_merge($records[$i],['cash'=>$money]);
            }
            return $records;
        }

        /**
         * 获取某个工号的技师一段时间内的充卡额，默认本月
         * 
         */
        public function get_by_jobnumber($num,$start="",$end=""){
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
        public function get_by_user($num){
            $record = UserModel::all(['user_id'=>$num]);
            if($record){
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