<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Rechargerecord extends Controller{

    public function index($edit,$username = ""){
        $ctrl = new \app\api\controller\Rechargerecord();
        $record =$ctrl->get_all();
        $this->assign("username",$username);
        $this->assign("record",$record);
        $this->assign("count",count($record));
        $this->assign("edit",$edit);
        return $this->fetch('Rechargerecord/recharge');
    }

    public function bill($edit,$openid = ""){
        $recharge_record = Db::query("select A.record_id as ID,A.user_id as openid,A.charge/100 as money,A.job_number as job_number,A.payment_method as payment_method,A.generated_time as generated_time,A.note as note,B.`name` as username from recharge_record A,customer B where A.user_id='$openid' and B.openid = A.user_id");
        $pay_record = Db::query("select A.order_id as ID,C.openid as openid,C.`name` as `username`,A.pay_amount/100 as money,'' as job_number,A.payment_method as payment_method,A.generated_time as generated_time,A.note as note from consumed_order A,customer C where A.user_id='$openid' and C.openid = A.user_id order by A.generated_time desc");
        
        for($i=0;$i<count($pay_record);$i++){
            $pay_record[$i]['note'] .= ' 消费';
            $pay_record[$i]['generated_time'] = date("Y-m-d H:i:s",$pay_record[$i]['generated_time']);
            $pay_record[$i]['money'] = intval($pay_record[$i]['money']);
        }
        for($i=0;$i<count($recharge_record);$i++){
            if( $recharge_record[$i]['note'] == ""){
                $recharge_record[$i]['note'] = "充值";
            }
            $recharge_record[$i]['generated_time'] = date("Y-m-d H:i:s",$recharge_record[$i]['generated_time']);
            $recharge_record[$i]['money'] = intval($recharge_record[$i]['money']);
        }
        $total_record = array_merge($recharge_record,$pay_record);

        $this->assign('record',$total_record);
        $this->assign('count',count($total_record));
        $this->assign('edit',$edit);
        return $this->fetch('Rechargerecord/bill');
    }
}
