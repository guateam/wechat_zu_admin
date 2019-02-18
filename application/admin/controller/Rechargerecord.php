<?php
namespace app\admin\controller;
use think\Controller;

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
}
