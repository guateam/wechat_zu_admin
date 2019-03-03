<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Vipcard as UserModel;
use think\Db;

class Vipcard extends Controller
{
    public function index($edit)
	{
        $vipcard = new \app\api\controller\Vipcard();

		$vipcard_list = $vipcard->getvipcardlist();
		
		$this->assign("vipcardlist",$vipcard_list);		
		$this->assign('count',count($vipcard_list));
		
        return $this->fetch('Vipcard/vipcard');
    }

}