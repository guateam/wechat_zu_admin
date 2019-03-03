<?php 
namespace app\api\controller;
use think\Controller;
use think\Db;
use \app\api\model\Vipcard as UserModel;


class Vipcard extends Controller
{
	public function getvipcardlist()
	{
        $vipcardlist = Db::query("select * from vipcard");
        
        return $vipcardlist;
    }

}