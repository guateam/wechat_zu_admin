<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Menu as Menu;
    use think\Db;
    class mMenu extends Controller{

        public static function getmenu(){
            $menu = Db::query("select * from `menu` where `father`=0 order by `ID` asc");
            for($i=0;$i<count($menu);$i++){
                $menu_id = $menu[$i]['ID'];
                $child_menu = Db::query("select * from `menu` where `father`='$menu_id'");
                $menu[$i] = array_merge($menu[$i],['child'=>$child_menu]);
            }
            return $menu;
        }

        public function getchoosenmenu($username){
            $admin_ctrl = new Admin();
            $permission = $admin_ctrl->getpermission($username);
            $menu = self::getmenu();

            if($permission[0] == 0){
                for($i=0;$i<count($menu);$i++){
                    $menu[$i] = array_merge($menu[$i],['permission'=>1]);
                    for($k=0;$k<count($menu[$i]['child']);$k++){
                        $menu[$i]['child'][$k] = array_merge($menu[$i]['child'][$k],['permission'=>1]);
                    }
                }
            }else{
                //初始化permission字段
                for($i=0;$i<count($menu);$i++){
                    $menu[$i] = array_merge($menu[$i],['permission'=>0]);
                    for($k=0;$k<count($menu[$i]['child']);$k++){
                        $menu[$i]['child'][$k] = array_merge($menu[$i]['child'][$k],['permission'=>0]);
                    }
                }
                for($i=0;$i<count($menu);$i++){
                    $menu[$i] = array_merge($menu[$i],['permission'=>0]);
                    for($j=0;$j<count($permission);$j++){
                        if($menu[$i]['ID'] == $permission[$j]){
                            $menu[$i]['permission'] = 1;
        
                            for($k=0;$k<count($menu[$i]['child']);$k++){
                                $menu[$i]['child'][$k] = array_merge($menu[$i]['child'][$k],['permission'=>0]);
                                for($l=0;$l<count($permission);$l++){
                                    if($menu[$i]['child'][$k]['ID'] == $permission[$l]){
                                        $menu[$i]['child'][$k]['permission'] = 1;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $menu;
        }

    }