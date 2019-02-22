<?php
namespace app\admin\controller;
use think\Controller;

class Invitersetting extends Controller
{
    public function index($edit)
	{
        $inviteship = \app\api\model\Inviteship::all();
        $this->assign("inviteship",$inviteship);
        $this->assign("count",count($inviteship));
        $this->assign("edit",$edit);
        return $this->fetch('Invitersetting/invitersetting');
    }
}
