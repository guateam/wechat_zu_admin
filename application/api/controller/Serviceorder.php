<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Serviceorder as UserModel;
    use think\Db;

    class Serviceorder extends Controller{
        /**
         * 获取所有订单-服务信息
         * 2018-8-26    创建   袁宜照
         * 
         */
        public function get_all(){
            $data = UserModel::all();
            
            //读取价格信息
            foreach($data as $order){
                if(is_null($order->price)){
                    //店内服务
                    if($order->service_type==1){
                        $ctrl = new  \app\api\controller\Servicetype();
                        $price = $ctrl->getserviceprice($order->item_id);
                        $price *=$ctrl->getservicediscount($order->item_id)/100.0;
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
        /**
         * 获取特定订单号的订单-服务信息
         * 2018-8-26    创建   袁宜照
         * @param string $order_id 订单号
         */
        public function get_order($order_id)
		{
            $data = UserModel::all(["order_id"=>$order_id]);
            if($data)
			{
                foreach($data as $order)
				{
                    if(is_null($order->price))//付款为空
					{
                        //店内服务
                        if($order->service_type==1)
						{
                            $ctrl = new  \app\api\controller\Servicetype();
                            $price = $ctrl->getserviceprice($order->item_id);
                            $price /= 100;
                            $order->price = $price;  

							
                        }
						else if($order->service_type==3)//酒水饮料
						{
                            $ctrl = new  \app\api\controller\Itemtype();
                            $price = $ctrl->getitemprice($order->item_id);
                            $order->price=$price;
                        }
						else //外卖，这种情况下order->price不应该为null
						{

                        }
                    }

                    $room_id = $order->private_room_number;
							
                    $room = Db::query("select * from private_room where ID =$room_id");
                    if ($room)
                    {
                        $order->private_room_number = $room[0]['name'];
                    }
                    else
                    {
                        $order->private_room_number = '';
                    }
                }
                return $data;
            }
            else return 0;
        }

        public function get_order_by_job_number($job_number)
		{
            $data = UserModel::all(["job_number"=>$job_number]);
            $job = [];
            if($data)
			{
                foreach($data as $dt)
				{
                    array_push($job,[$dt->order_id,$dt->service_type,$dt->item_id]);
                }
                return $job;
            }
            else return null;
        }
    }