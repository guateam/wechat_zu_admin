<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Vipinformation as VipModel;

class Editvip extends Controller{
public function index($vip){
        $info = VipModel::get(["level"=>$vip]);
        $this->assign("vip",$info);
        return $this->fetch("editvip");
    }
}