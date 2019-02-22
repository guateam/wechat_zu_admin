<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Itemlist extends Controller
{
    public function index($edit)
	{
        $tea_ctrl = new \app\api\controller\Itemtype();
        $tea_list = $tea_ctrl->get_tea();
        $this->assign("edit",$edit);
        $this->assign("count",count($tea_list));
        $this->assign("tealist",$tea_list);
        return $this->fetch('Itemlist/itemlist');
    }

    public function add()
	{
        return $this->fetch('Itemlist/additem');
    }

    public function edit($id)
	{
        $tea = Db::query("select * from item_type where `ID` = '$id'");
        if(count($tea) == 1)
            $this->assign('tea',$tea[0]);
        else 
            $this->assign('tea',[]);
        return $this->fetch('Itemlist/edititem');
    }
}