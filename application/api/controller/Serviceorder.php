<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Serviceorder as UserModel;
    class Serviceorder extends Controller{
        public function get_all(){
            $data = UserModel::all();
            return $data;
        }
        public function get_order($order_id){
            $data = UserModel::all(["order_id"=>$order_id]);
            if($data){
                foreach($data as $order){
                    if(is_null($order->price)){
                        //店内服务
                        if($order->service_type==1){
                            $ctrl = new  \app\api\controller\Servicetype();
                            $price = $ctrl->getserviceprice($order->item_id);
                            $order->price=$price;
                        //酒水饮料
                        }else if($order->service_type==2){
                            $ctrl = new  \app\api\controller\Itemtype();
                            $price = $ctrl->getitemprice($order->item_id);
                            $order->price=$price;
                        //外卖，这种情况下order->price不应该为null
                        }else if($order->service_type==3){

                        }
                    }
                }
                return $data;
            }
            else return 0;
        }
    }