<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Vipcard as UserModel;
use think\Db;

class Vipcard extends Controller
{
    public function index($edit)
	{
        $vipcard = \app\api\controller\Vipcard::getvipcardlist();
        
        //$data = $Vipcard->gettiplist();
        
        //$Vipcard = UserModel::all();
        
		$vipcard_list = $vipcard->getvipcardlist();
		
		$this->assign("vipcard_list",$vipcard_list);		
		$this->assign('count',count($vipcardlist));
		
		
        //$this->assign('list',$data);
		
        //return $this->fetch('Vipcard/vipcard');
		return;
    }

    public function vipcard($edit)
    {
        $vipcard = new \app\api\controller\Vipcard();

		$vipcard_list = $vipcard->getvipcardlist();
		
		$this->assign("vipcardlist",$vipcard_list);		
		$this->assign('count',count($vipcard_list));
		
        return $this->fetch('Vipcard/vipcard');
    }


}