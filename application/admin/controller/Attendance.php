<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\ServiceType as UserModel;

class Attendance extends Controller{
    
	public function index($edit,$first_day=null,$last_day=null)
    {
        date_default_timezone_set('PRC');

        $ctrl = new \app\api\controller\Attendance();

      
        if(is_null($first_day))
        {
            $first_day = strtotime(date("Y-m-d")." 00:00:00");	  //当天
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
			$last_day = strtotime(date("Y-m-d")." 23:59:59");	//当天		
            $this->assign("end",date("Y-m-t"));
        }
        else
        {
            $this->assign("end",$last_day);//日期不会错
            $last_day = strtotime($last_day) + 24 * 3600 - 1;//时间戳要改下
        }

		$att = $ctrl->get_all_attendance($first_day,$last_day);
		
		$this->assign("attendance",$att);
        $this->assign("count",count($att));
		$this->assign("edit",$edit);
        return $this->fetch('Attendance/attendance');
    }
}