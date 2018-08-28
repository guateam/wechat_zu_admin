<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Technicianlist extends Controller{

    public function Technicianlist(){
        $ctrl =new \app\api\controller\Technician();
        $sk = new \app\api\controller\Skill();
        $so = new \app\api\controller\Serviceorder();
        $rt = new \app\api\controller\Rate();

        $skill = [];
        //所有技师的订单ID序列
        $order = [];
        $aver_score = [];

        $technicians = $ctrl->get_all_technician();
        foreach($technicians as $tc){
            $sk_name = $sk->get_skill_name($tc->job_number);
            if($sk_name){
                array_push($skill,$sk_name);
            }
            $od = $so->get_order_by_job_number($tc->job_number);
            if($od){
                array_push($order,$od);
            }else{
                array_push($order,null);
            }
        }
        //单个技师的订单序列
        foreach($order as $od){
            $total_score = 0;
            $score_count=1;
            if($od){
                $score_count = count($od);
                //单个订单
                foreach($od as $orderinfo){
                    $all_rate = $rt->get_service_rate($orderinfo[0],$orderinfo[2]);
                    if($all_rate){
                        foreach($all_rate as $rate){
                        $total_score+=$rate->score;
                        }
                    }
                }
            }
            array_push($aver_score,$total_score/$score_count);
        }
        $count = $ctrl->count_all();
        $this->assign("skills",$skill);
        $this->assign("technicians",$technicians);
        $this->assign("count",$count);
        $this->assign("score",$aver_score);
        return $this->fetch();
    }
}