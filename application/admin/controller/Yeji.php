<?php
namespace app\admin\controller;

use think\Controller;

class Yeji extends Controller
{
    public function index($edit,$first_day=null,$last_day=null)
    {
        date_default_timezone_set('PRC');

        $ctrl = new \app\api\controller\Technician();

        //本月第一天
        if(is_null($first_day))
        {
            $first_day = strtotime(date("Y-m-01"));
            $this->assign("begin",date("Y-m-01"));
        }
        else
        {
            $this->assign("begin",$first_day);
            $first_day = strtotime($first_day);
        }
        //本月最后一天

        if(is_null($last_day))
        {
            // $BeginDate = date('Y-m-01', strtotime(date("Y-m-d")));
            // $last_day = strtotime("$BeginDate +1 month") - 1;
            // $EndDate = date('Y-m-d', $last_day);//减1秒            
            // $this->assign("end",$EndDate);
			
			$last_day = strtotime(date("Y-m-t")) + 24 * 3600 - 1;//时间戳要改下
            $this->assign("end",date("Y-m-t"));
        }
        else
        {
            $this->assign("end",$last_day);//日期不会错
            $last_day = strtotime($last_day) + 24 * 3600 - 1;//时间戳要改下
        }

        $yeji = $ctrl->get_all_yeji($first_day,$last_day);
        $this->assign("yeji", $yeji);
        $this->assign("count", count($yeji));
        $this->assign("edit",$edit);
        return $this->fetch('Yeji/Yeji');
    }

    public function detail($job_number,$first_day=null,$last_day=null){
        //本月第一天
        if(is_null($first_day)){
            $first_day = strtotime(date("Y-m-01"));
            $this->assign("begin",date("Y-m-01"));
        }
        else{
            $this->assign("begin",$first_day);
            $first_day = strtotime($first_day);
        }
        //本月最后一天
        if(is_null($last_day)){
            $last_day = strtotime(date("Y-m-t"));
            $this->assign("end",date("Y-m-t"));
        }
        else{
            $this->assign("end",$last_day);
            $last_day = strtotime($last_day);
        }
        $yeji = [];

        $this->assign("count",count($yeji));
        $this->assign("yeji",$yeji);
        return $this->fetch("Yeji/detail");
    }
}
