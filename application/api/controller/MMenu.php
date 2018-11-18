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
            //目前操作的目录顺序和父目录
            $order = $menu_item->order;
            $father = $menu_item->father;
            //相同父目录的目录项
            $same_father_menu = Db::query("select * from menu where `father`='$father' order by `order` asc");

            foreach($same_father_menu as $idx => $fm){
                //若要操作的目录位于同目录项的第一个，则不能上移
                if($fm['order'] == $order && $idx == 0){
                    return false;
                }else{
                    //若找到自己，则和上一个交换位置
                    if($fm['order'] == $order){
                        //上一个位置和ID
                        $last_order = $same_father_menu[$idx-1]['order'];
                        $last_id = $same_father_menu[$idx-1]['ID'];
                        Db::query("update menu set `order`='$last_order' where `ID`='$id' ");
                        Db::query("update menu set `order`='$order' where `ID`='$last_id' ");
                        return true;
                    }
                    
                }

            }

            // if($menu_item->father != $upper_item->father){
            //     return false;
            // }else{
            //     $ori_order = $menu_item->order;
            //     $menu_item->order = $upper_item->order;
            //     $upper_item->order = $ori_order;
            //     $menu_item->save();
            //     $upper_item->save();
            //     return true;
            // }
        }
        public function lower($id){
            $id = intval($id);
            $menu_item  = Menu::get(['ID'=>$id]);
            $order = $menu_item->order;
            //目前操作的目录顺序和父目录
            $order = $menu_item->order;
            $father = $menu_item->father;
            //相同父目录的目录项
            $same_father_menu = Db::query("select * from menu where `father`='$father' order by `order` asc");

            foreach($same_father_menu as $idx => $fm){
                //若要操作的目录位于同目录项的最后一个，则不能下移
                if($fm['order'] == $order && $idx == count($same_father_menu)-1){
                    return false;
                }else{
                    //若找到自己，则和上一个交换位置
                    if($fm['order'] == $order){
                        //上一个位置和ID
                        $last_order = $same_father_menu[$idx+1]['order'];
                        $last_id = $same_father_menu[$idx+1]['ID'];
                        Db::query("update menu set `order`='$last_order' where `ID`='$id' ");
                        Db::query("update menu set `order`='$order' where `ID`='$last_id' ");
                        return true;
                    }
                    
                }

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