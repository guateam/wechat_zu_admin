<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Notice as UserModel;

class Historynotice extends Controller
{
    public function index($edit)
	{
        $ctrl =new \app\api\controller\Notice();
        $notice = $ctrl->get_all_notice();
        $count = $ctrl->count_all();
        $this->assign("notice",$notice);
        $this->assign("count",$count);
        $this->assign("edit",$edit);
        return $this->fetch("Historynotice/lishigonggao");
    }

    public function techindex($edit)
	{
        $ctrl =new \app\api\controller\Notice();
        $notice = $ctrl->get_technotice();
        $count = count($notice);
        $this->assign("notice",$notice);
        $this->assign("count",$count);
        $this->assign("edit",$edit);
        return $this->fetch("Historynotice/history_tech_notice");
    }

    public function edittech($id)
	{
        $ctrl = new \app\api\controller\Notice();
        $notice = $ctrl->get_technotice_by_id($id);
        if($notice)
		{
            $this->assign("notice",$notice);
            return $this->fetch("Historynotice/edit_tech_notice");
        }
    }
}