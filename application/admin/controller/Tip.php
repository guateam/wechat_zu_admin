<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\Technician as UserModel;

class Tip extends Controller{
    public function index($edit){
        $tip=new \app\api\controller\Tip();
        $data=$tip->gettiplist();
        $this->assign('count',count($data));
        $this->assign('list',$data);
        return $this->fetch('Tip/tip');
    }
}