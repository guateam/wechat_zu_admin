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
        public function get_spokename_by_phone($phone){
            $data = Db::query("select spoke_name from customer where phone_number = '$phone'");
            if($data){
                return json(['name'=>$data[0]['spoke_name']]);
            }
            return json(['name'=>'']);
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
            $pay = Db::query("select sum(pay_amount)/100 as pay from consumed_order where user_id='$openid' and (state!=3 and state !=0) and payment_method=3");
            if(count($pay)>0)
                $pay = intval($pay[0]['pay']);
            $money = $charge - $pay;
            return $money;
        }
        
        public function get_cash_by_phone2($phone){
            $tech = Db::query("select * from customer where phone_number = $phone");
            if($tech){
                $money = self::get_cash($tech[0]['openid']);
                return json(['cash'=>$money]);
            }else{
                return json(['cash'=>0]) ;
            }
        }

        public function get_cash_by_phone($phone){
            $user = Db::query("select * from customer where phone_number = '$phone'");
            if($user){
                $money = self::get_cash($user[0]['openid']);
                return $money;
            }else{
                return false;
            }
        }

        public function addinfo($openid,$spoke_name,$phone){
            Db::query("update customer set spoke_name='$spoke_name',phone_number='$phone' where openid='$openid'");
            return json(['status'=>1]);
        }
    }