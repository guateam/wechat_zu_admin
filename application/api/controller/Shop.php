<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Shop as UserModel;
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
    }