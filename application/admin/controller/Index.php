<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\admin as UserModel;

class Index extends Controller{

    public function index($id){
        $id = urldecode($id);
        $menu_ctrl = new \app\api\controller\Mmenu();
        $admin_ctrl = new \app\api\controller\Admin();

        $permission = $admin_ctrl->getpermission($id);
        $menu = $menu_ctrl->getchoosenmenu($id);


        $this->assign("menu",$menu);
        $this->assign("userid",$id);
        return $this->fetch('Index/index');
    }
}