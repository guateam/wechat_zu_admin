<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Customer as UserModel;
    use think\Db;
    class Customer extends Controller{

        public function get_customer($id){
            $data = UserModel::get(['openid'=>$id]);
            if($data){
                return $data;
            }
        }

        public function delete_customer($id){
            foreach($id as $i){
                $cus = UserModel::get(["ID"=>$i]);
                if($cus){
                    $cus->delete();
                }
            }
            return ['status'=>1];
        }

        public function get_cash($openid){
            $records = Db::query("select sum(charge)/100 as charge from recharge_record where user_id='$openid' group by user_id");
            $charge =0;
            if(count($records)>0)
                $charge = intval($records[0]['charge']);
            $pay = Db::query("select sum(pay_amount)/100 as pay from consumed_order where user_id='$openid' and (state!=3) and payment_method=3");
            if(count($pay)>0)
                $pay = intval($pay[0]['pay']);
            $money = $charge - $pay;
            return $money;
        }
    }