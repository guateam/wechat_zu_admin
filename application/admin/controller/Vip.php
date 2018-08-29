<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Vipinformation as VipModel;

class Vip extends Controller{

    public function index(){
        $vip = VipModel::all();
        $this->assign("vip",$vip);
        $this->assign("count",count($vip));
        return $this->fetch('vip');

    }
}