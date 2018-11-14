<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\admin as UserModel;

class Index extends Controller{

    public function index($id){
        $menu_ctrl = new \app\api\controller\Mmenu();
        $admin_ctrl = new \app\api\controller\Admin();

        $permission = $admin_ctrl->getpermission($id);
        $menu = $menu_ctrl->getchoosenmenu($id);

        // if($permission[0] == 0){
        //     for($i=0;$i<count($menu);$i++){
        //         $menu[$i] = array_merge($menu[$i],['permission'=>1]);
        //         for($k=0;$k<count($menu[$i]['child']);$k++){
        //             $menu[$i]['child'][$k] = array_merge($menu[$i]['child'][$k],['permission'=>1]);
        //         }
        //     }
        // }else{
        //     for($i=0;$i<count($menu);$i++){
        //         $menu[$i] = array_merge($menu[$i],['permission'=>0]);
        //         for($j=0;$j<count($permission);$j++){
        //             if($menu[$i]['ID'] == $permission[$j]){
        //                 $menu[$i]['permission'] = 1;
    
        //                 for($k=0;$k<count($menu[$i]['child']);$k++){
        //                     $menu[$i]['child'][$k] = array_merge($menu[$i]['child'][$k],['permission'=>0]);
        //                     for($l=0;$l<count($permission);$l++){
        //                         if($menu[$i]['child'][$k]['ID'] == $permission[$l]){
        //                             $menu[$i]['child'][$k]['permission'] = 1;
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }

        $this->assign("menu",$menu);
        return $this->fetch('Index/index');
    }
}