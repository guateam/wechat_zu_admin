<?php
namespace app\admin\controller;
use think\Controller;

class Invitersetting extends Controller{

    public function index(){
        $inviteship = \app\api\model\Inviteship::all();
        $this->assign("inviteship",$inviteship);
        $this->assign("count",count($inviteship));
        return $this->fetch('invitersetting');
    }
}
