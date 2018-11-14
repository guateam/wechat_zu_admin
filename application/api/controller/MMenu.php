<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Menu as Menu;
    use think\Db;
    class Mmenu extends Controller{

        public static function getmenu(){
            $menu = Db::query("select * from `menu` where `father`=0 order by `order` asc");
            for($i=0;$i<count($menu);$i++){
                $menu_id = $menu[$i]['ID'];
                $child_menu = Db::query("select * from `menu` where `father`='$menu_id' order by `order` asc");
                $menu[$i] = array_merge($menu[$i],['child'=>$child_menu]);
            }
            return $menu;
        }
        public function upper($id){
            $id = intval($id);
            $menu_item  = Menu::get(['ID'=>$id]);
            $order = $menu_item->order;
            $upper_item =Menu::get(['order'=>$order-1]);
            if($menu_item->father != $upper_item->father){
                return false;
            }else{
                $ori_order = $menu_item->order;
                $menu_item->order = $upper_item->order;
                $upper_item->order = $ori_order;
                $menu_item->save();
                $upper_item->save();
                return true;
            }
        }
        public function lower($id){
            $id = intval($id);
            $menu_item  = Menu::get(['ID'=>$id]);
            $order = $menu_item->order;
            $lower_item =Menu::get(['order'=>$order+1]);
            if( !$lower_item || $menu_item->father != $lower_item->father){
                return false;
            }else{
                $ori_order = $menu_item->order;
                $menu_item->order = $lower_item->order;
                $lower_item->order = $ori_order;
                $menu_item->save();
                $lower_item->save();
                return true;
            }
        }
        public function getchoosenmenu($username){
            $admin_ctrl = new Admin();
            $permission = $admin_ctrl->getpermission($username);
            $menu = self::getmenu();

            if($permission[0][0] == 0){
                for($i=0;$i<count($menu);$i++){
                    $menu[$i] = array_merge($menu[$i],['permission'=>1]);
                    for($k=0;$k<count($menu[$i]['child']);$k++){
                        $menu[$i]['child'][$k] = array_merge($menu[$i]['child'][$k],['permission'=>1,'edit'=>1]);
                    }
                }
            }else{
                //初始化permission字段
                for($i=0;$i<count($menu);$i++){
                    $menu[$i] = array_merge($menu[$i],['permission'=>0]);
                    for($k=0;$k<count($menu[$i]['child']);$k++){
                        $menu[$i]['child'][$k] = array_merge($menu[$i]['child'][$k],['permission'=>0,'edit'=>0]);

                    }
                }
                for($i=0;$i<count($menu);$i++){
                    $menu[$i] = array_merge($menu[$i],['permission'=>0]);
                    for($j=0;$j<count($permission);$j++){
                        if($menu[$i]['ID'] == $permission[$j][0]){
                            $menu[$i]['permission'] = 1;
        
                            for($k=0;$k<count($menu[$i]['child']);$k++){
                                $menu[$i]['child'][$k] = array_merge($menu[$i]['child'][$k],['permission'=>0]);
                                for($l=0;$l<count($permission);$l++){
                                    if($menu[$i]['child'][$k]['ID'] == $permission[$l][0]){
                                        $menu[$i]['child'][$k]['permission'] = 1;
                                        if(count( $permission[$l]) > 1){
                                            $menu[$i]['child'][$k]['edit'] = $permission[$l][1];
                                        }
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