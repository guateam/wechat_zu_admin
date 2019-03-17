<?php 
namespace app\api\controller;
use think\Controller;
use think\Db;
use \app\api\model\Vipcard as UserModel;

class Vipcard extends Controller
{
	public static $API_URL = "http://103.239.247.197:8899/api?url=";
	
	public function getvipcardlist()
	{
        $vipcardlist = Db::query("select * from vipcard");
        
        return $vipcardlist;
    }
	
	public function get_phone_by_card($cardNo)
	{
		$phone = "";

		$phoneList = Db::query("select * from vipcard where cardNo = '$cardNo'");
		if ($phoneList)
		{
			$phone = $phoneList[0]['phone'];
		}
		
		return json_encode([
				'success'=>true,
				'msg'=>$phone,
			]);
	}
	
	public function get_phone_by_openid($openid)
	{
		$phone = "";

		$phoneList = Db::query("select * from customer where openid = '$openid'");
		if ($phoneList)
		{
			$phone = $phoneList[0]['phone_number'];
			
			if ($phone != "")
			{
				return json_encode([
					'success'=>true,
					'msg'=>$phone,
				]);
			}
			else
			{
				return json_encode([
					'success'=>false,
					'msg'=>"failure",
				]);
			}
		}
		else
		{
			return json_encode([
				'success'=>false,
				'msg'=>"failure",
			]);
		}	
		
	}
	
	public function bindwechat($openid,$phone)
	{
		if ($phone != "")
		{
			 Db::query("update customer set phone_number = '$phone' where openid = '$openid'");
			 
			 return json_encode([
				'success'=>true,
			]);
		}
		else
		{
			return json_encode([
				'success'=>false,
			]);
		}	
		
	}
	
	public function cancelcard($openid,$cardNo,$phone,$cash)//销卡转微信 会员卡注销，暂时不用这个功能 先不改成 chongka_record
	{
		//分两笔，一笔是 卡号对应的 加一条负数的记录
		//第二笔是 openid对应的，加一条充值记录 recharge_record
		
		//-------------------------------------------------
		$dict=['1','2','3','4','5','6','7','8','9','0'];
		$rnd = "";
		$rnd.=date("YmdHis");//年月日时分秒
		
		for($i=0;$i<2;$i++)
		{
			$rnd.=$dict[rand(0,count($dict)-1)];//6位随机整数
		}
		for($i=0;$i<4;$i++)
		{
			$rnd.=$dict[rand(0,count($dict)-1)];
		}
		//-------------------------------------------------
		
		//-------------------------------------------------
		$dict=['1','2','3','4','5','6','7','8','9','0'];
		$rnd2 = "";
		$rnd2.=date("YmdHis");//年月日时分秒
		
		for($i=0;$i<2;$i++)
		{
			$rnd2.=$dict[rand(0,count($dict)-1)];//6位随机整数
		}
		for($i=0;$i<4;$i++)
		{
			$rnd2.=$dict[rand(0,count($dict)-1)];
		}
		//-------------------------------------------------
		
		$cash = $cash * 100;
		$time = time();	
		$neg_cash = -1 * $cash;
		Db::startTrans();
		try
		{
			Db::query("insert into recharge_record (`record_id`,`phone_number`,`cardNo`,`charge`,`payment_method`,`generated_time`,`note`,`type`) 
					values ('$rnd','$phone','$cardNo','$neg_cash','3','$time','销卡','1')");
					
			Db::query("insert into recharge_record (`record_id`,`user_id`,`phone_number`,`charge`,`payment_method`,`generated_time`,`note`,`type`) 
					values ('$rnd2','$openid','$phone','$cash','3','$time','销卡转钱','1')");
					
			Db::commit(); 
            return json(['status'=>1]);
		 }
		catch (Exception $e) 
		{
			//$db->rollback();
			Db::rollback();//回滚
			return json(['status'=>0,'data'=>'出现异常，请重试']);
		}
	}
	
	public function vipSync()
	{
        $index = 0;

		$mt = time()."000";			
		$ori_url = "http://www.dzbsaas.com/footmassage/member/getlist.do?JWTTOKEN=b1f30285-1247-41f1-98f2-1cc725006191&sEcho=3&iColumns=16&sColumns=id%2Cname%2Cgender%2Cmobile%2Cphone%2CbirthS%2CcardNo%2Cbalance%2Cpoint%2CmemberRank.name%2CloginDate%2CcreateDate%2CcardType%2CisEnabled%2CisLost%2CchainStoreUse&iDisplayStart=".$index."&iDisplayLength=1000&mDataProp_0=id&sSearch_0=&bRegex_0=false&bSearchable_0=false&bSortable_0=false&mDataProp_1=name&sSearch_1=&bRegex_1=false&bSearchable_1=true&bSortable_1=false&mDataProp_2=gender&sSearch_2=&bRegex_2=false&bSearchable_2=false&bSortable_2=false&mDataProp_3=mobile&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=false&mDataProp_4=phone&sSearch_4=&bRegex_4=false&bSearchable_4=false&bSortable_4=false&mDataProp_5=birthS&sSearch_5=&bRegex_5=false&bSearchable_5=false&bSortable_5=false&mDataProp_6=cardNo&sSearch_6=&bRegex_6=false&bSearchable_6=true&bSortable_6=true&mDataProp_7=balance&sSearch_7=&bRegex_7=false&bSearchable_7=false&bSortable_7=true&mDataProp_8=point&sSearch_8=&bRegex_8=false&bSearchable_8=false&bSortable_8=true&mDataProp_9=memberRank.name&sSearch_9=&bRegex_9=false&bSearchable_9=false&bSortable_9=false&mDataProp_10=loginDate&sSearch_10=&bRegex_10=false&bSearchable_10=false&bSortable_10=true&mDataProp_11=createDate&sSearch_11=&bRegex_11=false&bSearchable_11=false&bSortable_11=true&mDataProp_12=cardType&sSearch_12=&bRegex_12=false&bSearchable_12=false&bSortable_12=false&mDataProp_13=isEnabled&sSearch_13=&bRegex_13=false&bSearchable_13=false&bSortable_13=false&mDataProp_14=isLost&sSearch_14=&bRegex_14=false&bSearchable_14=false&bSortable_14=false&mDataProp_15=chainStoreUse&sSearch_15=&bRegex_15=false&bSearchable_15=false&bSortable_15=false&sSearch=&bRegex=false&iSortCol_0=11&sSortDir_0=desc&iSortingCols=1&_=".$mt;

		$url = Vipcard::$API_URL.urlencode($ori_url);
		
		$content = file_get_contents($url);	
		$obj = json_decode($content);
		if ($obj->success == false)
		{
			echo $content;//success:false;msg:token is Invalid
			return;
		}
		$ret1 = $obj->data;	
		//var_dump($ret1);
		
		$CardRet = json_decode($ret1);
		$Cards = $CardRet->data;

		try
		{
			foreach($Cards as $eachcard)
			{   
                $name = $eachcard->name;
                
                if ($name == "所有合计")
                {
                    //skip
                }
                else
                {
                    $cardNo = $eachcard->cardNo;	
                    
                    $name = $eachcard->name;	
                    $gender = $eachcard->gender;	

                    $gender = 0;
                    if ($gender == "male")
                    {
                        $gender = 1;
                    }
                    else if ($gender == "female")
                    {
                        $gender = 2;
                    }
                    $mobile = $eachcard->mobile;
                    $phone = $eachcard->phone;
                    $birth = $eachcard->birthS;
                    $cardNo = $eachcard->cardNo;
                    $balance = $eachcard->balance;
					$point = $eachcard->point;
					
					if(property_exists($eachcard,'modifyDate'))
					{
						$modifyDate = $eachcard->modifyDate;
					}
					else
					{
						$modifyDate = "";
					}

                    $createDate = $eachcard->createDate;
                    $cardType = $eachcard->cardType;
                    $isEnabled = $eachcard->isEnabled;
                    $isLost = $eachcard->isLost;
                    $chainStoreUse = $eachcard->chainStoreUse;

                    $mycard = Db::query("select * from vipcard where cardNo = '$cardNo'");
                    if ($mycard)
                    {                       
                        Db::query("update vipcard set name = '$name', gender = '$gender', mobile = '$mobile', phone = '$phone', birth = '$birth', cardNo = '$cardNo', balance = '$balance', point = '$point', point = '$point', modifyDate = '$modifyDate', createDate = '$createDate', cardType = '$cardType', isEnabled = '$isEnabled', isLost = '$isLost', chainStoreUse = '$chainStoreUse' where cardNo = '$cardNo'");//存在就更新                        					
                    } 
                    else
                    {
                        Db::query("insert into vipcard (`name`,`gender`,`mobile`,`phone`,`birth`,`cardNo`,`balance`,`point`,`modifyDate`,`createDate`,`cardType`,`isEnabled`,`isLost`,`chainStoreUse`) values ('$name','$gender','$mobile','$phone','$birth','$cardNo','$balance','$point','$modifyDate','$createDate','$cardType','$isEnabled','$isLost','$chainStoreUse')");//不存在就插入
                    }
                }

                      	
			}
			echo json_encode([
							'success'=>true,
							'msg'=>'',
			]);
			return;
		}
		catch(Exception $e)
		{
			echo json_encode([
					'success'=>false,
					'msg'=>'sync fail'
			]);
			return;
		}
		return;
	}

}