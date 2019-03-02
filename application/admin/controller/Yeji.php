<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

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

    public function detail($job_number,$first_day=null,$last_day=null)
	{
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
	
	public function yejidetail($edit,$job_number=null,$begin=null,$end=null)
	{
		// echo $job_number;
		// echo $begin;
		// echo $end;
		
		//该技师自己的信息
        $tech_self = Db::query("select * from technician where job_number='$job_number'");
        $tech_self = $tech_self[0];
		
		$tech_type = $tech_self['type'];//1为技师，2为接待
        
		if($begin=='')
		{
            $begin = date("Y-m-01",time());
        }
		$this->assign('begin',$begin);
        $begin.=" 00:00:00";
        $begin = strtotime($begin);
		
		
        if($end=='')
		{
            $end = date("Y-m-t",time());
        }
		$this->assign('end',$end);
        $end.=" 23:59:59";
        $end = strtotime($end);
		
		$this->assign('job_number',$job_number);
		
		 //应该从service_order表查询		 
		if ($tech_type == 1)//技师
		{
			$so = Db::query("select * from service_order where job_number ='$job_number' and order_id in (select order_id from consumed_order A where  A.end_time >= $begin and A.end_time <= $end and(A.state=4 or A.state=5))");
		}
		else if ($tech_type == 2)//接待
		{
			$so = Db::query("select * from service_order where jd_number ='$job_number' and order_id in (select order_id from consumed_order A where  A.end_time >= $begin and A.end_time <= $end and(A.state=4 or A.state=5))");
		}
		
        $servicenamelist = [];
		$roomnumberlist = [];
        $endtimelist = [];
		
		foreach($so as $srvod)
		{
			
			$order_id = $srvod['order_id'];
			$co = Db::query("select * from consumed_order A, service_order B where A.order_id = B.order_id and B.job_number = '$job_number' and B.order_id = '$order_id'");
			if ($co)
			{
				$srvod['appoint_time'] = $co[0]['end_time'];//下钟时间				
			}
			
			$endtime = date('Y-m-d H:i:s', $srvod['appoint_time']);
            array_push($endtimelist,$endtime);
			
			
			$item_id = $srvod['item_id'];
			$servicename = Db::query("select * from service_type where ID = '$item_id'");
			if($servicename)
            {
				$eachservicename = $servicename[0]['name'];
                array_push($servicenamelist,$eachservicename);
			}
			
			$room_number = $srvod['private_room_number'];
			$rn = Db::query("select * from private_room where ID = '$room_number'");
			if($rn)
            {
				$roomnumber = $rn[0]['name'];
                array_push($roomnumberlist,$roomnumber);
			}
		}
		
		$this->assign('count',count($so));
        $this->assign('so',$so);
        $this->assign('servicenamelist',$servicenamelist);
		$this->assign('roomnumberlist',$roomnumberlist);
        $this->assign('endtimelist',$endtimelist);
        return $this->fetch('Yeji/yejidetail');
	}
}
