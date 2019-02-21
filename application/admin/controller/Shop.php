<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Shop extends Controller{

    public function index($edit){
        $ctrl = new \app\api\controller\Shop();
        $shop = $ctrl->get_all();
        $count = count($shop);
        $this->assign("shop",$shop);
        $this->assign("count",$count);
        $this->assign("edit",$edit);
        return $this->fetch('Shop/shop');
    }
    public function pic($id){
        $ctrl = new \app\api\controller\Shopphoto();
        $photo = $ctrl->get_by_id($id);
        $sv = \app\api\model\Servicetype::all();
        $count = count($photo);
        $this->assign("photo",$photo);
        $this->assign("count",$count);
        $this->assign("service",$sv);
        $this->assign("shop_id",$id);
        return $this->fetch('Shop/shop_picture');
    }
    public function promotion(){
        $ctrl = new \app\api\controller\Promotion();
        $promotion = $ctrl->get_all();
        $this->assign("promotion",$promotion);
        $this->assign("count",count($promotion));
        return $this->fetch('Shop/promotion');
    }
    public function promotion_edit($id){
        $ctrl = new \app\api\controller\Promotion();
        $promotion = $ctrl->get($id);
        if($promotion){
            $promotion = $promotion[0];
        }else{
            $promotion = new \app\api\model\Promotion();
        }
        $this->assign("promotion",$promotion);
        return $this->fetch('Shop/promotion_edit');
    }
    public function promotion_add(){
        return $this->fetch('Shop/promotion_add');
    }

    public function account($edit){
        $account = Db::query("select * from admin");
        for($i=0;$i<count($account);$i++){
            $permissions = $account[$i]['permission'];
            $permissions = explode(',',$permissions);
            if($permissions[0] == 0)
            {
                $account[$i]['permission'] = ['所有'];
                continue;
            }
            $names = [];
            foreach($permissions as $permission){
                $name = Db::query("select name from menu where `ID`='$permission'");
                array_push($names,$name[0]['name']);
            }
            $account[$i]['permission'] = $names;
        }
        $this->assign('account',$account);
        $this->assign('count',count($account));
        $this->assign("edit",$edit);
        return $this->fetch('Shop/account');
    }

    public function editpermission($user){
        $user = urldecode($user);
        $menu_ctrl = new \app\api\controller\Mmenu();
        $menu = $menu_ctrl->getchoosenmenu($user);
         $this->assign('menu',$menu);
         $this->assign('userid',$user);
        return $this->fetch('Shop/editpermission');
    }

    public function newaccount(){
        $menu_ctrl = new \app\api\controller\Mmenu();
        $menu = $menu_ctrl->getmenu();
        $this->assign('menu',$menu);
        return $this->fetch('Shop/newaccount');
    }

    public function editmenu($edit){
        $menu_ctrl = new \app\api\controller\Mmenu();
        $menu = $menu_ctrl->getmenu();
        $this->assign('menu',$menu);
        $this->assign("edit",$edit);
        return $this->fetch('Shop/menu');
    }

    public function editinfo($id){
        $shop = Db::query("select * from shop where `ID` = '$id'");
        $this->assign('shop',$shop[0]);
        return $this->fetch('Shop/editinfo');
    }

    public function payatshop(){
        $technicians = Db::query("select * from technician");
        $service = Db::query("select ID,name,price from service_type");
        $room = Db::query("select ID,name from private_room");
        $spare_tech = [];
        //去除有约的技师
        foreach($technicians as $idx => $tc){
            $job_number = $tc['job_number'];
            $select_time = time();
            
            //在选择的时间以后最近订单被预约并且时间间隔在1个半小时以内,并且订单在预约状态或服务中状态的
            $appoint_after = sql_str("select A.* from service_order A,consumed_order B where  B.order_id = A.order_id and (B.state=1 or B.state=2) and A.appoint_time >= $select_time and A.appoint_time-90*60 <= $select_time and A.job_number='$job_number' order by (A.appoint_time - $select_time) ASC");
            //在选择的时间之前最近订单被预约并且时间间隔在1个半小时以内的,并且订单在预约状态或服务中状态的
            $appoint_before = sql_str("select A.* from service_order A,consumed_order B where  B.order_id = A.order_id and (B.state=1 or B.state=2) and A.appoint_time <= $select_time and A.appoint_time+90*60 >= $select_time and A.job_number='$job_number' order by ($select_time - A.appoint_time ) ASC");

            //是否被预约
            $already_appoint = false;
            //若有预约
            if($appoint_after || $appoint_after){
                //预约情况为true
                $already_appoint = true;
            }

            if(!$already_appoint && $tc['busy'] == 0){
                array_push($spare_tech,$tc);
            }
        }
        $this->assign('technicians',$spare_tech);
        $this->assign('service',$service);
        $this->assign('room',$room);
        return $this->fetch('Shop/payatshop');
    }

    public function boss($begin = '',$end = ''){
        if($begin==''){
            $begin = date("Y-m-01",time());
        }
        $this->assign('begin',$begin);
        $begin.=" 00:00:00";


        $begin = strtotime($begin);
        if($end==''){
            $end = date("Y-m-t",time());
        }
        $this->assign('end',$end);
        $end.=" 23:59:59";
        $end = strtotime($end);

        //时间内的订单
        $consumed_order = Db::query("select A.generated_time as generated_time ,A.payment_method as payment_method, A.order_id as order_id, A.pay_amount as charge,'服务消费' as note, A.source as source,GROUP_CONCAT(C.job_number) as job_number from consumed_order A ,service_order C where (A.state!=0 and A.state!=3) and A.payment_method!=3 and A.generated_time >=$begin and A.generated_time <= $end and C.order_id=A.order_id  GROUP BY A.order_id");
        //时间内的充值记录
        $recharge = Db::query("select record_id as order_id,charge,'会员充值' as note,job_number,generated_time,'小程序支付' as source,'微信支付' as payment_method  from recharge_record where generated_time >=$begin and generated_time <= $end and type=1");
        //时间内的打赏
        $tip = Db::query("select '' as order_id,salary as charge,technician_id as job_number,date as generated_time,'打赏技师' as note,'小程序支付' as source,'微信支付' as payment_method from tip where date >=$begin and date <= $end");
        
        for($i=0;$i<count($consumed_order);$i++){
            $consumed_order[$i]['source'] = ($consumed_order[$i]['source'] == 0?'小程序支付':'到店支付');
            if($consumed_order[$i]['payment_method'] == 1)$consumed_order[$i]['payment_method'] = "微信支付";
            elseif($consumed_order[$i]['payment_method'] == 2)$consumed_order[$i]['payment_method'] = "支付宝支付";
            elseif($consumed_order[$i]['payment_method'] == 3)$consumed_order[$i]['payment_method'] = "会员卡支付";
            elseif($consumed_order[$i]['payment_method'] == 4)$consumed_order[$i]['payment_method'] = "现金支付";
            elseif($consumed_order[$i]['payment_method'] == 5)$consumed_order[$i]['payment_method'] = "银行卡支付";
            elseif($consumed_order[$i]['payment_method'] == 6)$consumed_order[$i]['payment_method'] = "其他方式支付";
            elseif($consumed_order[$i]['payment_method'] == 7)$consumed_order[$i]['payment_method'] = "美团支付";
        }
        
        $result = array_merge($consumed_order,$recharge,$tip);
        for($i=0;$i<count($result);$i++){
            $result[$i]['generated_time'] = date("Y-m-d H:i:s", $result[$i]['generated_time']);
        }
        $this->assign('info',$result);
        $this->assign('count',count($result));
        return $this->fetch('Shop/boss');
    }
}
