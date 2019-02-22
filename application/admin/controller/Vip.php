<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Vipinformation as VipModel;

class Vip extends Controller
{
    public function index($edit)
	{
        $vip = VipModel::all();
        $this->assign("vip",$vip);
        $this->assign("count",count($vip));
        $this->assign("edit",$edit);
        return $this->fetch('Vip/vip');
    }
}