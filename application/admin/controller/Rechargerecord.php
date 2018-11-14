<?php
namespace app\admin\controller;
use think\Controller;

class Rechargerecord extends Controller{

    public function index($edit){
        $ctrl = new \app\api\controller\Rechargerecord();
        $record =$ctrl->get_all();
        $this->assign("record",$record);
        $this->assign("count",count($record));
        return $this->fetch('Rechargerecord/recharge');
    }
}
