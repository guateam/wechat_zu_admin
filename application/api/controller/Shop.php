<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Shop as UserModel;
    use think\Db;
    class Shop extends Controller{

        public function get_all(){
            $all = UserModel::all();
            return $all;
        }
        public function change_state(){
            $shop = UserModel::get(['ID'=>1]);
            if($shop->appoint_allow == 1){
                $shop->appoint_allow = 0;
            }else{
                $shop->appoint_allow = 1;
            }
            $shop->save();
        }
        public function change_modify($id){
            $shop = UserModel::get(['ID'=>$id]);
            if($shop->ip_modify == 1){
                $shop->ip_modify = 0;
            }else{
                $shop->ip_modify = 1;
            }
            $shop->save();
        }
        public function change_ip($ip){
            $ip = $ip['geoplugin_request'];
            $shop = UserModel::get(['ID'=>1]);
            if($shop && $shop['ip_modify'] == 0){
                $shop->ip_address = $ip;
                $shop->save();
            }
        }
        public function update($shopname,$address,$open,$close,$phone,$ip,$id,$recharge_income,$recharge_income_2){
            $shop = UserModel::get(['ID'=>$id]);
            if($shop){
                $shop->name = $shopname;
                $shop->position = $address;
                $shop->open_time = $open;
                $shop->close_time = $close;
                $shop->phone = $phone;
                $shop->ip_address = $ip;
                $shop->recharge_income = $recharge_income;
                $shop->recharge_income_2 = $recharge_income_2;
                $shop->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
        public function update_name($id,$value){
            $shop = UserModel::get(['ID'=>$id]);
            if($shop){
                $shop->name = $value;
                $shop->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }

        public function update_position($id,$value){
            $shop = UserModel::get(['ID'=>$id]);
            if($shop){
                $shop->position = $value;
                $shop->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }


        public function update_opentime($id,$value){
            $shop = UserModel::get(['ID'=>$id]);
            if($shop){
                $shop->open_time = $value;
                $shop->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
        public function update_closetime($id,$value){
            $shop = UserModel::get(['ID'=>$id]);
            if($shop){
                $shop->close_time = $value;
                $shop->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
        public function update_phone($id,$value){
            $shop = UserModel::get(['ID'=>$id]);
            if($shop){
                $shop->phone = $value;
                $shop->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }

        public function update_ip($id,$value){
            $shop = UserModel::get(['ID'=>$id]);
            if($shop){
                $shop->ip_address = $value;
                $shop->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }

        public function changepassword($id,$val){
           $result =  Db::query("update admin set `password`='$val' where `ID`='$id'");
           return json(['status'=>1]);
        }

        public function changeper($id,$val){
            //添加父目录项
            foreach($val as $idx => $it){
                $menu_id = $it[0];
                $fth = Db::query("select father from menu where ID='$menu_id'");
                $fth = $fth[0]['father'];
                if($fth !=0 && !array_search($fth,$val)){
                    array_push($val,(string)$fth);
                }
                $val[$idx] = implode(':',$it);
            }
            $str = implode(',',$val);
            $result = Db::query("update admin set `permission`='$str' where `name`='$id' ");
            return json(['status'=>1]);
        }
    }