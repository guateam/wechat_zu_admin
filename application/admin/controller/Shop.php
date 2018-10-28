<?php
namespace app\admin\controller;
use think\Controller;

class Shop extends Controller{

    public function index(){
        $ctrl = new \app\api\controller\Shop();
        $shop = $ctrl->get_all();
        $count = count($shop);
        $this->assign("shop",$shop);
        $this->assign("count",$count);
        return $this->fetch('Shop/shop');
    }
    public function pic($id){
        $ctrl = new \app\api\controller\Shopphoto();
        $photo = $ctrl->get_by_id($id);
        $sv = \app\api\model\Servicetype::all();
        $count = count($photo);
        $this->assign("photo",$photo);
        $this->assign("count",$count);
        $this->assign("service",$sv);
        $this->assign("shop_id",$id);
        return $this->fetch('Shop/shop_picture');
    }
}
