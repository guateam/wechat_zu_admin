<?php
namespace app\admin\controller;
use think\Controller;

class Skilllevel extends Controller
{
    public function index($edit)
	{
        $ctrl_sk = new \app\api\controller\Skill();
        $ctrl_lv = new \app\api\controller\Skilllevel();
        $level = $ctrl_lv->get_all();
        $skill = $ctrl_sk->get_all();
        $count = count($skill);
        $this->assign("skill",$skill);
        $this->assign("level",$level);
        $this->assign("count",$count);
        $this->assign("edit",$edit);
        return $this->fetch('Skilllevel/skilllevel');
    }

    public function level_edit($edit)
	{
        $level = \app\api\model\Skilllevel::all();

        $this->assign('level',$level);
        $this->assign('count',count($level));
        $this->assign("edit",$edit);
        return $this->fetch('Skilllevel/leveledit');
    }
}
