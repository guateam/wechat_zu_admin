<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Shop extends Controller
{
    public function index($edit)
	{
        $ctrl = new \app\api\controller\Shop();
        $shop = $ctrl->get_all();
        $count = count($shop);
        $this->assign("shop",$shop);
        $this->assign("count",$count);
        $this->assign("edit",$edit);
        return $this->fetch('Shop/shop');
    }
	
    public function pic($id)
	{
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
	
	//-----------------------------------------------------
    public function promotion()
	{
        $ctrl = new \app\api\controller\Promotion();
        $promotion = $ctrl->get_all();
        $this->assign("promotion",$promotion);
        $this->assign("count",count($promotion));
        return $this->fetch('Shop/promotion');
    }
	
    public function promotion_edit($id)
	{
        $ctrl = new \app\api\controller\Promotion();
        $promotion = $ctrl->get($id);
        if($promotion)
		{
            $promotion = $promotion[0];
        }
		else
		{
            $promotion = new \app\api\model\Promotion();
        }
        $this->assign("promotion",$promotion);
        return $this->fetch('Shop/promotion_edit');
    }
	
    public function promotion_add()
	{
        return $this->fetch('Shop/promotion_add');
    }
	//-----------------------------------------------------
	
	//-----------------------------------------------------
    public function rechargebonus()
	{
        $ctrl = new \app\api\controller\Rechargebonus();
        $rechargebonus = $ctrl->get_all();
        $this->assign("rechargebonus",$rechargebonus);
        $this->assign("count",count($rechargebonus));
        return $this->fetch('Shop/rechargebonus');
    }
	
    public function rechargebonus_edit($id)
	{
        $ctrl = new \app\api\controller\Rechargebonus();
        $rechargebonus = $ctrl->get($id);
        if($rechargebonus)
		{
            $rechargebonus = $rechargebonus[0];
        }
		else
		{
            $rechargebonus = new \app\api\model\Rechargebonus();
        }
        $this->assign("rechargebonus",$rechargebonus);
        return $this->fetch('Shop/rechargebonus_edit');
    }
	
    public function rechargebonus_add()
	{
        return $this->fetch('Shop/rechargebonus_add');
    }
	//-----------------------------------------------------

    public function account($edit)
	{
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
            foreach($permissions as $permission)
			{
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

    public function editpermission($user)
	{
        $user = urldecode($user);
        $menu_ctrl = new \app\api\controller\Mmenu();
        $menu = $menu_ctrl->getchoosenmenu($user);
         $this->assign('menu',$menu);
         $this->assign('userid',$user);
        return $this->fetch('Shop/editpermission');
    }

    public function newaccount()
	{
        $menu_ctrl = new \app\api\controller\Mmenu();
        $menu = $menu_ctrl->getmenu();
        $this->assign('menu',$menu);
        return $this->fetch('Shop/newaccount');
    }

    public function editmenu($edit)
	{
        $menu_ctrl = new \app\api\controller\Mmenu();
        $menu = $menu_ctrl->getmenu();
        $this->assign('menu',$menu);
        $this->assign("edit",$edit);
        return $this->fetch('Shop/menu');
    }

    public function editinfo($id)
	{
        $shop = Db::query("select * from shop where `ID` = '$id'");
        $this->assign('shop',$shop[0]);
        return $this->fetch('Shop/editinfo');
    }

    public function payatshop()
	{
        $technicians = Db::query("select * from technician where type !=2");
        //$service = Db::query("select ID,name,market_price from service_type");//门市价
		$service = Db::query("select ID,name,price from service_type");//活动价
        $room = Db::query("select ID,name from private_room");
		
		$jiedai = Db::query("select * from technician where type =2");

        $this->assign('technicians',$technicians);
		$this->assign('jiedai',$jiedai);
        $this->assign('service',$service);
        $this->assign('room',$room);
        return $this->fetch('Shop/payatshop');
    }

    public function boss($begin = '',$end = '')
	{
        if($begin=='')
		{
            $begin = date("Y-m-01",time());
        }
        $this->assign('begin',$begin);
        $begin.=" 00:00:00";
        $begin = strtotime($begin);		
		
        if($end=='')
		{
            $end = date("Y-m-t",time());
        }
        $this->assign('end',$end);
        $end.=" 23:59:59";
        $end = strtotime($end);

        //时间段内的订单  payment_method =3 会员卡支付计算尽量  payment_method =9 赠送卡支付也显示出来
        $consumed_order = Db::query("select A.generated_time as generated_time ,A.payment_method as payment_method, A.order_id as order_id, A.pay_amount as charge,A.shouldpay_amount as should_charge, note, A.source as source,GROUP_CONCAT(C.job_number) as job_number from consumed_order A ,service_order C where (A.state!=0 and A.state!=3) and A.generated_time >=$begin and A.generated_time <= $end and (A.payment_method != 10)  and C.order_id=A.order_id  GROUP BY A.order_id");//时间段内，非会员支付，状态非未支付非取消，把工号合并起来
		
        //时间段内的充值记录
        $recharge = Db::query("select record_id as order_id,charge,charge as should_charge,'' as note,job_number,generated_time,'会员充值' as source,payment_method  from recharge_record where generated_time >=$begin and generated_time <= $end and type=1");
		
		for($i=0;$i<count($recharge);$i++)
		{
            if($recharge[$i]['payment_method'] == 1)
			{
                $recharge[$i]['payment_method'] = '微信支付';
            }
            elseif($recharge[$i]['payment_method'] == 2)$recharge[$i]['payment_method'] = "支付宝支付";
            elseif($recharge[$i]['payment_method'] == 3)$recharge[$i]['payment_method'] = "会员卡支付";
            elseif($recharge[$i]['payment_method'] == 4)$recharge[$i]['payment_method'] = "现金支付";
            elseif($recharge[$i]['payment_method'] == 5)$recharge[$i]['payment_method'] = "银行卡支付";
            elseif($recharge[$i]['payment_method'] == 6)$recharge[$i]['payment_method'] = "其他方式支付";
            elseif($recharge[$i]['payment_method'] == 7)$recharge[$i]['payment_method'] = "美团支付";
			elseif($recharge[$i]['payment_method'] == 8)$recharge[$i]['payment_method'] = "微信扫码支付";
			elseif($recharge[$i]['payment_method'] == 9)$recharge[$i]['payment_method'] = "赠送卡支付";
			elseif($recharge[$i]['payment_method'] == 10)$recharge[$i]['payment_method'] = "优惠券支付";
        }
		
        //时间段内的打赏
        $tip = Db::query("select '' as order_id,salary as charge,salary as should_charge,technician_id as job_number,date as generated_time,'打赏技师' as note,'网络打赏' as source,'微信支付' as payment_method from tip where date >=$begin and date <= $end");
        
        for($i=0;$i<count($consumed_order);$i++)
		{
            if($consumed_order[$i]['payment_method'] == 1)
			{
                $consumed_order[$i]['payment_method'] = '微信支付';
            }
            elseif($consumed_order[$i]['payment_method'] == 2)$consumed_order[$i]['payment_method'] = "支付宝支付";
            elseif($consumed_order[$i]['payment_method'] == 3)$consumed_order[$i]['payment_method'] = "会员卡支付";
            elseif($consumed_order[$i]['payment_method'] == 4)$consumed_order[$i]['payment_method'] = "现金支付";
            elseif($consumed_order[$i]['payment_method'] == 5)$consumed_order[$i]['payment_method'] = "银行卡支付";
            elseif($consumed_order[$i]['payment_method'] == 6)$consumed_order[$i]['payment_method'] = "其他方式支付";
            elseif($consumed_order[$i]['payment_method'] == 7)$consumed_order[$i]['payment_method'] = "美团支付";
			elseif($consumed_order[$i]['payment_method'] == 8)$consumed_order[$i]['payment_method'] = "微信扫码支付";
			elseif($consumed_order[$i]['payment_method'] == 9)$consumed_order[$i]['payment_method'] = "赠送卡支付";
			elseif($consumed_order[$i]['payment_method'] == 10)$consumed_order[$i]['payment_method'] = "优惠券支付";

            $consumed_order[$i]['source'] = ($consumed_order[$i]['source'] == 0?'网络预约':'到店消费');
        }
        
        $result = array_merge($consumed_order,$recharge,$tip);//三组数据整合在一起
        for($i=0;$i<count($result);$i++)
		{
            $result[$i]['generated_time'] = date("Y-m-d H:i:s", $result[$i]['generated_time']);//时间戳转换
        }
        $this->assign('info',$result);
        $this->assign('count',count($result));
        return $this->fetch('Shop/boss');
    }
}
