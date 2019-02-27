<?php 
namespace app\api\controller;
use think\Controller;
use think\Db;
class Rechargebonus extends Controller
{
    public function delete($id)
	{
        $data = \app\api\model\Rechargebonus::get(["ID"=>$id]);
        $data->delete();
        return json(['status'=>1]);
    }
	
    public function get($id)
	{
        $data = Db::query("select  * from Recharge_bonus where ID='$id'");
        return $data;
    }	
	
    public function get_all()
	{
        $data = Db::query("select  * from Recharge_bonus order by ID");
        return $data;
    }
	
    public function add($recharge,$tech_bonus,$jiedai_bonus,$cashier_bonus)
	{
        $data = new \app\api\model\Rechargebonus();
		
        $data->recharge = $recharge;
        $data->tech_bonus = $tech_bonus;
        $data->jiedai_bonus = $jiedai_bonus;
		$data->cashier_bonus = $cashier_bonus;
        $data->save();
        return json(['status'=>1]);
    }
	
	public function update($id,$recharge,$tech_bonus,$jiedai_bonus,$cashier_bonus)
	{
        $data = \app\api\model\Rechargebonus::get(["ID"=>$id]);
        //将字符串日期转换成时间戳
        
        $data->recharge = $recharge;
        $data->tech_bonus = $tech_bonus;
        $data->jiedai_bonus = $jiedai_bonus;
		$data->cashier_bonus = $cashier_bonus;
		
        $data->save();
        return json(['status'=>1]);
    }
   
}