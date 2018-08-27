<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Servicetype as Service;
    class Servicetype extends Controller{
        /**
         * 获取服务总数
         * 2018-8-27    创建   赖品钊
         * 
         */
        public function count_all(){
            $data = UserModel::all();
            return count($data);
        }
        /**
         * 获取服务种类
         * 2018-8-24 创建 张煜
         * @param int $id 服务所属id
         */
        public static function getservicename($id){
            $service= Service::get(['ID'=>$id]);
            if($service){
                return $service->name;
            }
            return "未知";
        }
        /**
         * 获取服务列表
         * 2018-8-24 创建 张煜
         */
        public static function getservicelist(){
            $list = Service::all();
            $data=[];
            foreach($list as $value){
                $item=[
                    'id'=>$value->ID,
                    'name'=>$value->name
                ];
                array_push($data,$item);
            }
            return $data;
        }
        /**
         * 获取服务基础价格
         * 2018-8-26 创建 袁宜照
         * @param int $id 服务id
         */
        public static function getserviceprice($id){
            $service= Service::get(['ID'=>$id]);
            if($service){
                return $service->price;
            }
            return "未知";
        }
    }