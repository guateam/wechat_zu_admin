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
	
	public function cancelvipcard()
	{
		//输入openid（扫码），自动刷出手机号，或者输入手机号 ，点击绑定按钮
		//绑定后，输入卡号，自动刷出卡内余额
        return $this->fetch('Vipcard/cancelvipcard');
	}

}