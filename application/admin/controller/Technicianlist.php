<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Technicianlist extends Controller{

    public function Technicianlist($edit){
        $ctrl =new \app\api\controller\Technician();
        $sk = new \app\api\controller\Skill();
        $so = new \app\api\controller\Serviceorder();
        $rt = new \app\api\controller\Rate();
        $recharge_ctrl = new \app\api\controller\Rechargerecord();
        


        $skill = [];
        //所有技师的订单ID序列
        $order = [];
        $aver_score = [];
        $charge = [];
        $technicians = $ctrl->get_all_technician();
        foreach($technicians as $idx => $tc){
            //技能等级
            $level = $ctrl->get_skill($tc->job_number);
            $technicians[$idx]['level'] = $level;
            $money = $recharge_ctrl->get_by_jobnumber($tc->job_number);
            $money = $money->getdata();
            if($money['status'] ==1)array_push($charge,$money['charge']);
            else{
                array_push($charge,0);
            }
        }

        foreach($technicians as $tc){
            $sk_name = $sk->get_skill_name($tc->job_number);
            if($sk_name){
                array_push($skill,$sk_name);
            }else{
                array_push($skill,"无");
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
            $score_count=0;
            if($od){
                //单个订单
                foreach($od as $orderinfo){
                    $all_rate = $rt->get_service_rate($orderinfo[0],$orderinfo[2]);
                    if($all_rate){
                        foreach($all_rate as $rate){
                            if($rate->bad==1)
                            {
                                $score_count+=1;
                                $total_score+=$rate->score;
                            }
                        }
                    }
                }
            }
            if($score_count==0)$score_count = 1;
            array_push($aver_score,$total_score/$score_count);
        }
        $count = $ctrl->count_all();
        $this->assign("skills",$skill);
        $this->assign("technicians",$technicians);
        $this->assign("count",$count);
        $this->assign("score",$aver_score);
        $this->assign("charge",$charge);
        $this->assign("edit",$edit);
        return $this->fetch('Technicianlist/technicianlist');
    }
    public function salary($first_day = "",$last_day = ""){
        $ctrl =new \app\api\controller\Technician();
        //本月第一天
        if($first_day === "")$first_day = date("Y-m-01");
        //本月最后一天
        if($last_day === "" )$last_day = date("Y-m-d",strtotime(date("Y-m-t"))+24*60*60);
        
        $salarys = $ctrl->get_all_salary($first_day,$last_day);
        $this->assign("salarys",$salarys);
        $this->assign("count",count($salarys));
        $this->assign("begin",$first_day);
        $this->assign("end",$last_day);
        return $this->fetch('Technicianlist/salary');
    }

    public function salary_detail($job_number,$begin,$end){
        return $this->fetch('Technicianlist/salary_detail');
    }
}