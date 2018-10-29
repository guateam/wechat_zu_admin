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
    }