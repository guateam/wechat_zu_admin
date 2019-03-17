<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Rechargerecord extends Controller
{

    public function index($edit,$username = "")
	{
        $ctrl = new \app\api\controller\Rechargerecord();
        $record =$ctrl->get_all();
        $this->assign("username",$username);
        $this->assign("record",$record);
        $this->assign("count",count($record));
        $this->assign("edit",$edit);
        return $this->fetch('Rechargerecord/recharge');
    }

    public function bill($edit,$openid = "")//客户账单
	{
        $recharge_record = Db::query("select A.record_id as ID,A.user_id as openid,A.charge/100 as money,A.job_number as job_number,A.payment_method as payment_method,A.generated_time as generated_time,A.note as note,B.`name` as username from recharge_record A,customer B where A.user_id='$openid' and B.openid = A.user_id");
        $pay_record = Db::query("select A.order_id as ID,C.openid as openid,C.`name` as `username`,A.pay_amount/100 as money,'' as job_number,A.payment_method as payment_method,A.generated_time as generated_time,A.note as note from consumed_order A,customer C where A.user_id='$openid' and C.openid = A.user_id order by A.generated_time desc");
        $tip = Db::query("select A.ID as ID, A.user_id as openid,A.salary/100 as money,'微信' as payment_method, '打赏技师' as note,technician_id as job_number,date as generated_time from tip A where user_id = '$openid'");

        for($i=0;$i<count($pay_record);$i++)
		{
            $order_id = $pay_record[$i]['ID'];
            $job_number = Db::query("select GROUP_CONCAT(job_number) as job_number from service_order where order_id = '$order_id'");
            $pay_record[$i]['job_number'] = $job_number[0]['job_number'];
            $pay_record[$i]['note'] .= ' 消费';
            $pay_record[$i]['generated_time'] = date("Y-m-d H:i:s",$pay_record[$i]['generated_time']);
            $pay_record[$i]['money'] = intval($pay_record[$i]['money']);
        }
        for($i=0;$i<count($recharge_record);$i++)
		{
            if( $recharge_record[$i]['note'] == ""){
                $recharge_record[$i]['note'] = "充值";
            }
            $recharge_record[$i]['generated_time'] = date("Y-m-d H:i:s",$recharge_record[$i]['generated_time']);
            $recharge_record[$i]['money'] = intval($recharge_record[$i]['money']);
        }
        for($i=0;$i<count($tip);$i++)
		{
            $openid = $tip[$i]['openid'];
            $name = Db::query("select name from customer where openid='$openid'");
            if($name)
            {
                $tip[$i]['username'] = $name[0]['name'];
            }
            else
            {
                $tip[$i]['username'] = '顾客不存在';
            }
            $tip[$i]['generated_time'] = date("Y-m-d H:i:s",$tip[$i]['generated_time']);
            $tip[$i]['payment_method'] = 1;
			$tip[$i]['money'] = intval($tip[$i]['money']);
        }
        $total_record = array_merge($recharge_record,$pay_record,$tip);

        $this->assign('record',$total_record);
        $this->assign('count',count($total_record));
        $this->assign('edit',$edit);
        return $this->fetch('Rechargerecord/bill');
    }
	
	public function shoprecharge()
	{
		$ctrl = new \app\api\controller\Technician();
        $technicians = $ctrl->get_all_technician();
        $count = $ctrl->count_all();
        $this->assign("technicians",$technicians);
		
        return $this->fetch('Rechargerecord/shoprecharge');
	}
}
