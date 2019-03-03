<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\ServiceType as UserModel;

class Attendance extends Controller{
    // public function index()
	// {
        // $att = \app\api\model\Attendance::all();		
        // $this->assign("attendance",$att);
        // $this->assign("count",count($att));
        // return $this->fetch('Attendance/attendance');
    // }
	
	public function index($edit,$first_day=null,$last_day=null)
    {
        date_default_timezone_set('PRC');

        $ctrl = new \app\api\controller\Attendance();

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
			$last_day = strtotime(date("Y-m-t")) + 24 * 3600 - 1;//时间戳要改下
            $this->assign("end",date("Y-m-t"));
        }
        else
        {
            $this->assign("end",$last_day);//日期不会错
            $last_day = strtotime($last_day) + 24 * 3600 - 1;//时间戳要改下
        }

        //$att = $ctrl->get_all_attendance($first_day,$last_day);

		/*
        $checkin = $ctrl->get_all_checkin($first_day,$last_day);
        $checkout = $ctrl->get_all_checkout($first_day,$last_day);
        $checkleave = $ctrl->get_all_checkleave($first_day,$last_day);
        
		$att = \app\api\model\Attendance::all();	
      
        $this->assign("attendance",$att);
		
		$this->assign("checkin",$checkin);
		$this->assign("checkout",$checkout);
		$this->assign("checkleave",$checkleave);
		*/
		
		$att = $ctrl->get_all_attendance($first_day,$last_day);
		
		$this->assign("attendance",$att);
        $this->assign("count",count($att));
		$this->assign("edit",$edit);
        return $this->fetch('Attendance/attendance');
    }
}