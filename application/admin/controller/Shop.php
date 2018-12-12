<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Shop extends Controller{

    public function index($edit){
        $ctrl = new \app\api\controller\Shop();
        $shop = $ctrl->get_all();
        $count = count($shop);
        $this->assign("shop",$shop);
        $this->assign("count",$count);
        $this->assign("edit",$edit);
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
    public function promotion(){
        $ctrl = new \app\api\controller\Promotion();
        $promotion = $ctrl->get_all();
        $this->assign("promotion",$promotion);
        $this->assign("count",count($promotion));
        return $this->fetch('Shop/promotion');
    }
    public function promotion_edit($id){
        $ctrl = new \app\api\controller\Promotion();
        $promotion = $ctrl->get($id);
        if($promotion){
            $promotion = $promotion[0];
        }else{
            $promotion = new \app\api\model\Promotion();
        }
        $this->assign("promotion",$promotion);
        return $this->fetch('Shop/promotion_edit');
    }
    public function promotion_add(){
        return $this->fetch('Shop/promotion_add');
    }

    public function account($edit){
        $account = Db::query("select * from admin");
        for($i=0;$i<count($account);$i++){
            $permissions = $account[$i]['permission'];
            $permissions = explode(',',$permissions);
            if($permissions[0] == 0)
            {
                $account[$i]['permission'] = ['所有'];
                continue;
            }
            $names = [];
            foreach($permissions as $permission){
                $name = Db::query("select name from menu where `ID`='$permission'");
                array_push($names,$name[0]['name']);
            }
            $account[$i]['permission'] = $names;
        }
        $this->assign('account',$account);
        $this->assign('count',count($account));
        $this->assign("edit",$edit);
        return $this->fetch('Shop/account');
    }

    public function editpermission($user){
        $user = urldecode($user);
        $menu_ctrl = new \app\api\controller\Mmenu();
        $menu = $menu_ctrl->getchoosenmenu($user);
         $this->assign('menu',$menu);
         $this->assign('userid',$user);
        return $this->fetch('Shop/editpermission');
    }

    public function newaccount(){
        $menu_ctrl = new \app\api\controller\Mmenu();
        $menu = $menu_ctrl->getmenu();
        $this->assign('menu',$menu);
        return $this->fetch('Shop/newaccount');
    }

    public function editmenu($edit){
        $menu_ctrl = new \app\api\controller\Mmenu();
        $menu = $menu_ctrl->getmenu();
        $this->assign('menu',$menu);
        $this->assign("edit",$edit);
        return $this->fetch('Shop/menu');
    }

    public function editinfo($id){
        $shop = Db::query("select * from shop where `ID` = '$id'");
        $this->assign('shop',$shop[0]);
        return $this->fetch('Shop/editinfo');
    }
}
