<?php
namespace app\admin\controller;
use think\Controller;

class Servicelist extends Controller{

    public function index($edit){
        $ctrl =new \app\api\controller\Servicetype();
        $shop =\app\api\model\Shop::get(['ID'=>1]);
        $servicelist = $ctrl->get_all();
        $count = $ctrl->count_all();
        $this->assign('shop',$shop);
        $this->assign("servicelist",$servicelist);
        $this->assign("count",$count);
        $this->assign("edit",$edit);
        return $this->fetch('Servicelist/service');
    }

    public function detail($id){
        $sv = \app\api\model\Servicetype::get(['ID'=>$id]);
        $this->assign("service_id",$id);
        $this->assign("service",$sv);
        $this->assign("procedure",$sv->procedure);
        $this->assign("attention",$sv->attention);
        $this->assign("benefit",$sv->benefit);
        $this->assign("img",$sv->image);
        return $this->fetch('Servicelist/service_detail');
    }
}
