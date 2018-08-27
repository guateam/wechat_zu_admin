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
        /**
         * 获取所有服务信息
         * 2018-8-27 创建 袁宜照
         * @param int $id 服务id
         */
        public static function get_all(){
            $service= Service::all();
            return $service;
        }
        /**
         * 修改特定服务的价格
         * 2018-8-27 创建 袁宜照
         * @param int $id       服务id
         * @param int $price    服务价格，单位：分
         * 
         */
        public static function update_price($id,$price){
            $service= Service::get(['ID'=>$id]);
            if($service){
                $service->price = $price;
                $service->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
        /**
         * 修改特定服务的折扣
         * 2018-8-27 创建 袁宜照
         * @param int $id       服务id
         * @param int $ratio    折扣比例，单位：百分比
         * 
         */
        public static function update_discount($id,$ratio){
            $service= Service::get(['ID'=>$id]);
            if($service){
                $service->discount = $ratio;
                $service->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
        /**
         * 修改特定服务的服务时长
         * 2018-8-27 创建 袁宜照
         * @param int $id       服务id
         * @param int $dura     时长，单位：分钟
         * 
         */
        public static function update_duration($id,$dura){
            $service= Service::get(['ID'=>$id]);
            if($service){
                $service->duration = $dura;
                $service->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
        /**
         * 修改特定服务的提成比例
         * 2018-8-27 创建 袁宜照
         * @param int $id       服务id
         * @param int $ratio    提成比例，单位：百分比
         * 
         */
        public static function update_commission($id,$ratio){
            $service= Service::get(['ID'=>$id]);
            if($service){
                $service->commission_ratio = $ratio;
                $service->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
    }