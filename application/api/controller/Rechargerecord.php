<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Rechargerecord as UserModel;

    class Rechargerecord extends Controller{

        public function get_all(){
            return UserModel::all();
        }
        /**
         * 获取某个工号的技师一段时间内的充卡额，默认本月
         * 
         */
        public function get_by_jobnumber($num,$start="",$end=""){
            if($start == "")
                $start=date('Y-m-01', strtotime(date("Y-m-d")));
            if($end == "")
                $end = date('Y-m-d', strtotime("$start +1 month -1 day"));

            if($end < $start){
                $temp = $end;
                $end = $start;
                $start = $temp;
            }

            $record = UserModel::all(['job_number'=>$num]);
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