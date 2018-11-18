<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Rechargerecord as UserModel;
    use think\Db;
    class Rechargerecord extends Controller{

        public function get_all(){
            $all = UserModel::all();
            for($i=0;$i<count($all);$i++){
                $all[$i]->generated_time = date('Y-m-d H:i:s',$all[$i]->generated_time);
            }
            return $all;
        }
        public function get_all_by_user(){
            $records = Db::query("select sum(A.charge)/100 as charge,A.user_id,B.phone_number,B.gender,B.level,B.openid,B.registration_date from recharge_record A,customer B where A.user_id=B.openid
            group by B.openid");
            $cus = new \app\api\controller\Customer();
            for($i=0;$i<count($records);$i++){
                $records[$i]['registration_date'] = date("Y-m-d H:i:s", $records[$i]['registration_date']);
                $records[$i]['charge'] = (int) $records[$i]['charge'];
                $money = $cus->get_cash($records[$i]['openid']);
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