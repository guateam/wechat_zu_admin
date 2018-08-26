<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Consumedorder as UserModel;

class Consumedorder extends Controller{

    public function index(){
        $ctrl =new \app\api\controller\Consumedorder();
        $appo = new \app\api\controller\Appointment();
        $serveorder = new \app\api\controller\Serviceorder();


        $order = $ctrl->get_all();
        $this->assign("order",$order);
        $this->assign("count",count($order));
        return $this->fetch('consumedorder');
    }
}