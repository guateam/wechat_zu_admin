<?php
namespace app\api\controller;

use think\Controller;
use think\Db;
use \app\api\model\Technician as UserModel;

class Technician extends Controller
{
    /**
     * 获取技师列表
     * 2018-8-24    创建   袁宜照
     *
     */
    public function get_all_technician()
    {
        $data = UserModel::all();
        return $data;
    }
    /**
     * 获取技师总数
     * 2018-8-24    创建   袁宜照
     *
     */
    public function count_all()
    {
        $data = UserModel::all();
        return count($data);
    }
    /**
     * 获取指定工号的技师信息
     * 2018-8-24    创建   袁宜照
     * @param string $job_number 工号
     *
     */
    public function get_technician($job_number)
    {
        $data = UserModel::get(["job_number" => $job_number]);
        if ($data) {
            return $data;
        } else {
            return json(["status" => 0]);
        }
    }
    /**
     * 删除指定工号的技师
     * 2018-8-24    创建   袁宜照
     * @param array|string $job_number 工号列表
     *
     */
    public function delete_technician($job_number = [])
    {
        $single_number;
        if (count($job_number) == 0) {
            return json(["status" => 1]);
        }

        for ($i = 0; $i < count($job_number); $i++) {
            $single_number = $job_number[$i];

            $skill = \app\api\model\Skill::all(["job_number" => $single_number]);
            foreach ($skill as $sk) {
                $sk->delete();
            }

            $data = UserModel::get(["job_number" => $single_number]);
            if ($data) {
                $data->delete();
            } else {
                return json(["status" => 0]);
            }
        }
        return json(["status" => 1]);
    }
    /**
     * 删除所有技师
     * 2018-8-24    创建   袁宜照
     * 未完成
     */
    public function delete_all()
    {

    }
    /**
     * 检测技师录入是否有重复信息
     * 2018-8-24    创建   袁宜照
     * @param string $name              录入的姓名
     * @param string $mobile            录入的手机号
     * @param string $job_number        录入的工号
     * @param string $idcard            录入的身份证号
     * @param string $ori_job_number    修改前的工号(添加新技师时不提供该参数)
     *
     *
     */
    public function check_repeat($name = "", $mobile = "", $job_number = "", $idcard = "", $ori_job_number = "")
    {

        if ($name != "") {
            $repeat = UserModel::get(['name' => $name]);
            if ($repeat) {
                if ($repeat->job_number != $ori_job_number) {
                    return -2;
                }
            }
        }
        if ($mobile != "") {
            $repeat = UserModel::get(['phone_number' => $mobile]);
            if ($repeat) {
                if ($repeat->job_number != $ori_job_number) {
                    return 0;
                }
            }
        }
        if ($job_number != "") {
            $repeat = UserModel::get(['job_number' => $job_number]);
            if ($repeat) {
                if ($repeat->job_number != $ori_job_number) {
                    return -1;
                }
            }
        }
        if ($idcard != "") {
            $repeat = UserModel::get(['id_number' => $idcard]);
            if ($repeat) {
                if ($repeat->job_number != $ori_job_number) {
                    return -3;
                }
            }
        }

        return 1;
    }

    public function check_name($name, $ori_number = "")
    {
        $repeat = UserModel::get(['name' => $name]);
        if ($repeat) {
            if ($repeat->job_number != $ori_number) {
                return false;
            }

        }
        return true;
    }
    public function check_phone_number($num, $ori_number = "")
    {
        $repeat = UserModel::get(['phone_number' => $num]);
        if ($repeat) {
            if ($repeat->job_number != $ori_number) {
                return false;
            }

        }
        return true;
    }
    public function check_job_number($num, $ori_number = "")
    {
        $repeat = UserModel::get(['job_number' => $num]);
        if ($repeat) {
            if ($repeat->job_number != $ori_number) {
                return false;
            }

        }
        return true;
    }
    public function check_idnumber($num, $ori_number = "")
    {
        $repeat = UserModel::get(['id_number' => $num]);
        if ($repeat) {
            if ($repeat->job_number != $ori_number) {
                return false;
            }

        }
        return true;
    }
    /**
     * 添加技师
     * 2018-8-24    创建   袁宜照
     * @param string $name       技师姓名
     * @param string $gender     技师性别
     * @param string $mobile     技师手机号
     * @param string $job_number 技师工号
     * @param string $skill      技师的技能
     *
     */
    public function add_technician($name, $idcard, $birthday, $gender, $mobile, $job_number, $skill = '', $describe = '', $level, $invite, $type)
    {
        if ($skill == '' && $type == 1) {
            return json(['status' => -4]);
        }

        $is_repeat = self::check_repeat($name, $mobile, $job_number, $idcard, "");
        if ($is_repeat != 1) {
            return json(["status" => $is_repeat]);
        }

        $data = new UserModel(['name' => $name, 'gender' => $gender, 'phone_number' => $mobile,
            'job_number' => $job_number, 'birthday' => $birthday, "id_number" => $idcard,
            'entry_date' => time(), 'description' => $describe, 'in_job' => 1, 'type' => $type]);
        $data->save();
        if ($skill != '') {
            for ($i = 0; $i < count($skill); $i++) {
                $skill_info = new \app\api\model\Skill(['job_number' => $job_number, 'level' => $level, 'service_id' => $skill[$i], 'extra_income' => 0]);
                $skill_info->save();
            }
        }
        if ($invite != '') {
            self::set_inviter($job_number, $invite);
        }
        return json(['status' => 1]);
    }
    /**
     * 修改技师信息
     * 2018-8-24    创建    袁宜照
     * 2018-8-25    修改    袁宜照
     * @param string $ori_job_number    原始工号
     * @param string $name              修改后的姓名
     * @param string $gender            修改后的性别
     * @param string $mobile            修改后的手机号
     * @param string $job_number        修改后的工号
     * @param string $skill             修改后的技能
     *
     */
    public function update_technician($ori_job_number, $name, $idcard, $birthday, $gender,
        $mobile, $job_number, $skill = '', $describe = '', $level = '', $invite = '', $type = 1,$nickname = '') {

        $is_repeat = self::check_repeat($name, $mobile, $job_number, $idcard, $ori_job_number);
        if ($is_repeat != 1) {
            return json(["status" => $is_repeat]);
        }

        $data = UserModel::get(["job_number" => $ori_job_number]);

        $data->type = $type;
        $data->name = $name;
        $data->gender = $gender;
        $data->phone_number = $mobile;
        $data->job_number = $job_number;
        $data->id_number = $idcard;
        $data->birthday = $birthday;
        $data->description = $describe;
        $data->nickname = $nickname;
        
        $skill_info = \app\api\model\Skill::all(['job_number' => $ori_job_number]);
        foreach ($skill_info as $it) {
            $it->delete();
        }

        if ($type == 1) {
            foreach ($skill as $sk) {
                $new_skill = new \app\api\model\Skill(['job_number' => $job_number, 'level' => $level, 'service_id' => $sk, 'extra_income' => 0]);
                $new_skill->save();
            }
            $data->level = $level;
        } else {
            $data->level = "";
        }

        $data->save();
        self::set_inviter($job_number, $invite);
        return json(["status" => 1]);
    }
    /**
     * 指定技师的邀请人
     * 2018-8-25    创建    袁宜照
     * @param string $job_number            技师工号
     * @param string $inviter_job_number    邀请人工号
     *
     */
    public function set_inviter($job_number, $inviter_job_number)
    {
        $data = UserModel::get(["job_number" => $job_number]);
        if ($data) {
            $data->inviter = $inviter_job_number;
            $old_data = \app\api\model\Inviteship::get(['freshman_job_number' => $job_number]);
            if (!$old_data) {
                $new_data = new \app\api\model\Inviteship();
                $new_data->data([
                    'inviter_job_number' => $inviter_job_number,
                    'freshman_job_number' => $job_number,
                ]);
                $new_data->save();
            } else {
                $old_data->inviter_job_number = $inviter_job_number;
                $old_data->save();
            }

            $data->save();
            return json(["status" => 1]);
        }
        // $ctrl = new \app\api\controller\Inviteship();
        // $result = $ctrl->add($inviter_job_number,$job_number,0);
        // $result = $result->getdata();
        // if($result['status']!=1)
        // {
        //     return json(['status'=>0]);
        // }
        return json(["status" => 0]);
    }
    /**
     * 改变技师在职状态
     * 2018-8-25    创建    袁宜照
     * @param string $job_number            技师工号
     *
     */
    public function change_in_job($job_number)
    {
        $data = UserModel::get(["job_number" => $job_number]);
        if ($data) {
            $data->in_job = abs($data->in_job - 1);
            $data->save();
            return json(["status" => 1]);
        }
        return json(['status' => 0]);
    }
    public static function get_lost($job_number, $begin, $end)
    {
        //该技师在时间内自己做的所有完成的（4，5）订单
        $so = Db::query("select A.* from service_order A,consumed_order B where A.job_number='$job_number' and B.order_id = A.order_id and B.end_time >=$begin and B.end_time <= $end and (B.state=4 or B.state=5)");
        //邀请该技师的人
        $self_p = \app\api\model\Inviteship::get(['freshman_job_number' => $job_number]);
        //付给邀请自己的人的钱
        $lost = 0;
        foreach ($so as $svod) {
            $lost += $svod['yongjin'];
        }
        return $lost;
    }

    public static  function getMonthNum( $date1, $date2, $tags='-' )
    {
        $date1 = explode($tags,$date1);
        $date2 = explode($tags,$date2);
        return abs($date1[0] - $date2[0]) * 12 - $date2[1] + abs($date1[1]);
    }

    /**
     * 获取技师的业绩
     */
    public static function get_yeji($job_number, $begin, $end)
    {
        //该技师自己的信息
        $tech_self = Db::query("select * from technician where job_number='$job_number'");
        $tech_self = $tech_self[0];
		
		$tech_type = $tech_self['type'];//1为技师，2为接待
        
        //该技师邀请的人
        $invited = \app\api\model\Inviteship::all(['inviter_job_number' => $job_number]);
        //邀请该技师的人
        $self_p = \app\api\model\Inviteship::get(['freshman_job_number' => $job_number]);
        //点钟提成
        $dian_price = 0;
        //排钟提成
        $pai_price = 0;
        //点钟数量
        $dian = 0;
        //排钟数量
        $pai = 0;
		
		$name = $tech_self['name'];
		
        //来自邀请的人的收入
        $yongjin = 0;
        //邀请自己的人将得到的钱，不从自己这里出，由店内承担支出
        $lost = 0;
        //业绩
        $yeji = 0;
		
		
		 //应该从service_order表查询		 
		if ($tech_type == 1)//技师
		{
			$so = Db::query("select * from service_order where job_number ='$job_number' and order_id in (select order_id from consumed_order A where  A.end_time >= $begin and A.end_time <= $end and(A.state=4 or A.state=5))");
			
			if ($so) 
			{
				for($i=0;$i<count($so);$i++)
				{
					$yeji += $so[$i]['price'];//技师的业绩 服务费用
				}
				
				foreach ($so as $svod) 
				{
					if($svod['clock_type']==1)//排钟
					{
						$pai++;
						$pai_price += $svod['ticheng'];
					}
					else if($svod['clock_type']==2)//点钟
					{
						$dian++;
						$dian_price += $svod['ticheng'];
					}
				}
			}
		}
		else if ($tech_type == 2)//接待
		{
			$so = Db::query("select * from service_order where jd_number ='$job_number' and order_id in (select order_id from consumed_order A where  A.end_time >= $begin and A.end_time <= $end and(A.state=4 or A.state=5))");
			
			if ($so) 
			{
				for($i=0;$i<count($so);$i++)
				{
					$yeji += $so[$i]['jd_ticheng'];//技师的业绩 服务费用（服务提成+充卡提成）
				}
				
				foreach ($so as $svod) 
				{
					if($svod['clock_type']==1)//排钟
					{
						$pai++;
						$pai_price += $svod['jd_ticheng'];
					}
					else if($svod['clock_type']==2)//点钟
					{
						$dian++;
						$dian_price += $svod['jd_ticheng'];
					}
				}
			}
		}
		
        //-----------充卡提成重新计算-----------------------------------
        //?有个问题，充卡提成是按照金额台阶计算的，而 技师上钟，接待上钟的提成和佣金，是不含台阶的
        //台阶的实效是一个月
        //所以时间段超过一个月后，就应该拆分月来计算 充卡提成

        //$monthCount = ($end - $begin) / (3600*24*30);//时间段内的月份数量

        //date('Y-m-d',$end);
        $monthCount = Technician::getMonthNum( date('Y-m-d',$end), date('Y-m-d',$begin));

		$recharge = 0;
		$recharge_ticheng = 0;//充卡提成
		$rcg = Db::query("select sum(charge) from recharge_record where job_number='$job_number' and generated_time >= $begin and generated_time <= $end");		
		if ($rcg)//这里查到的是 某个技师 在时间段内的充卡金额
		{
            if ($rcg[0]['sum(charge)'])
            {
                $recharge = $rcg[0]['sum(charge)'];
                //$tech_type  技师 接待 收银 technician 表type=1 技师 =2 接待 =3 收银
                $bonus = Db::query("select * from recharge_bonus order by recharge desc");
                if ($bonus)
                {
                    for($i =0 ; $i < count($bonus); $i++)
                    {
                        if ($tech_type == 1)//0
                        {
                            if($recharge >= ( $bonus[$i]['tech_bonus'] * 100 * $monthCount) ) 
                            {
                                $recharge_ticheng = $bonus[$i]['tech_bonus'] * 100 * $monthCount;
                                break;
                            }
                        }
                        else if ($tech_type == 2)
                        {
                            if($recharge >= ( $bonus[$i]['jiedai_bonus'] * 100 * $monthCount) ) 
                            {
                                $recharge_ticheng = $bonus[$i]['jiedai_bonus'] * 100 * $monthCount;
                                break;
                            }
                        }
                        else if ($tech_type == 3)
                        {
                            if($recharge >= ( $bonus[$i]['cashier_bonus'] * 100 * $monthCount) ) 
                            {
                                $recharge_ticheng = $bonus[$i]['cashier_bonus'] * 100 * $monthCount;
                                break;
                            }
                        }
                    }
                }
            }
            
            /*
			for($i=0;$i<count($rcg);$i++)
			{
				$recharge += $rcg[0]['charge'];
				$recharge_ticheng += $rcg[0]['ticheng'];
			}
            */
		}
        //-------------------------------------------------------------
		
		$yeji = $yeji + $recharge_ticheng;       
		
        if ($invited) //佣金
		{
            foreach ($invited as $inv) 
			{
                $data = self::get_lost($inv->freshman_job_number, $begin, $end);
                $yongjin += $data;
            }
        }
		
        return [
            'name' => $name,
			'job_number' => $job_number,
            'status' => 1,
            'pai' => $pai,
            'dian' => $dian,
            'pai_earn' => $pai_price/100,
            'dian_earn'=> $dian_price/100,
			'recharge'=> $recharge/100,
			'recharge_ticheng'=> $recharge_ticheng/100,
            'come_from_other' => $yongjin/100,
            'yeji' =>$yeji/100,
            'lost' => 0,
            'final_salary' => ($pai_price + $dian_price + $yongjin + $recharge_ticheng)/100,
        ];
    }

    public function get_inviter($job_number)
    {
        $inviter = Db::query("select inviter_job_number from inviteship where freshman_job_number='" . $job_number . "'");
        if ($inviter) {
            return $inviter[0]['inviter_job_number'];
        } else {
            return "";
        }

    }

    public function get_skill($job_number)
    {
        $skill = Db::query("select B.name from skill A, skill_level B where A.job_number='$job_number' and A.level > 0 and B.ID=A.level limit 1");
        if ($skill) {
            return $skill[0]['name'];
        } else {
            return "";
        }

    }

    public function change_busy($job_number)
	{
        $user = UserModel::get(['job_number'=>$job_number]);
        if($user->busy == 1)
        {
            $user->busy = 0;
        }
        else{
            $user->busy = 1;
        }
        $user->save();
    }
	
	public function check_busy($job_number)
	{
        $user = UserModel::get(['job_number'=>$job_number]);        
		
		if($user->busy == 1)
        {
            return json(['status'=>1]);
        }
        else
		{
            return json(['status'=>0]);
        }
    }

    /**
     * 获取所有技师的业绩
     */
    public function get_all_yeji($begin, $end)
    {
        $data = [];
        $technicians = \app\api\model\Technician::all();
        foreach ($technicians as $tech) 
		{
            array_push($data, self::get_yeji($tech->job_number, $begin, $end));
        }
        return $data;
    }
    public function upload()
    {
        $dir = $_SERVER['DOCUMENT_ROOT'] . "/photo/";
        $save_dir = "/photo/";
        $job_number = $_POST['job_number'];
        $allowedExts = array("gif", "jpeg", "jpg", "png", "PNG");
        $temp = explode(".", $_FILES["file"]["name"]);
        $extension = end($temp); // 获取文件后缀名

        $dict = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u',
            'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
        $rnd_str = "";
        for ($i = 0; $i < 7; $i++) {
            $idx = rand(0, count($dict) - 1);
            $rnd_str .= $dict[$idx];
        }

        if ((
            ($_FILES["file"]["type"] == "image/jpeg")
            || ($_FILES["file"]["type"] == "image/jpg")
            || ($_FILES["file"]["type"] == "image/x-png")
            || ($_FILES["file"]["type"] == "image/png"))
        ) {
            if ($_FILES["file"]["error"] > 0) {
                return json_encode(["state" => $_FILES["file"]["error"]]);
            } else {
                // 判断当期目录下的 upload 目录是否存在该文件
                // 如果没有 upload 目录，你需要创建它，upload 目录权限为 777
                $tm = date("ymdhis", time());
                $sv = $save_dir . $rnd_str . $tm . $_FILES["file"]["name"];
                $tm = $dir . $rnd_str . $tm . $_FILES["file"]["name"];
                $tc = UserModel::get(["job_number" => $job_number]);
                // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
                if ($tc) {
                    if ($tc->photo) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . $tc->photo);
                    }
                    move_uploaded_file($_FILES["file"]["tmp_name"], $tm);
                    //上传头像不需要视为技师的上传项目
                    // $tf = new \app\api\model\Technicianphoto();
                    // $tf->job_number = $job_number;
                    // $tf->img = $sv;
                    // $tf->save();
                    $tc->photo = $sv;
                    $tc->save();
                    return json_encode(["state" => 1, 'url' => $sv]);
                } else {
                    return json_encode(["state" => 0]);
                }
            }

        } else {
            return json_encode(["state" => "格式错误:" . $_FILES["file"]["type"]]);
        }
    }
	
    public function get_all_salary($begin, $end)
    {
        //如果是字符串类型的日期，转换成时间戳
        $type = gettype($begin);
        if ($type !== "integer") 
		{
            $begin = strtotime($begin);
        }
        
		$type = gettype($end);
        if ($type !== "integer") 
		{
            $end = strtotime($end);
        }
        //获取所有技师
        $technicians = Db::query("select * from technician");

        $salarys = [];
        foreach ($technicians as $tech) 
		{
            $salary = [];
            //获取上钟提成，点钟数量以及推荐人提成
            $yeji = self::get_yeji($tech['job_number'], $begin, $end);
            $job_number = $tech['job_number'];

			/*
            //要查的店铺id，用以获取该店的充值提成比例
            $shopid = 1;
            //获取两种充值提成标准
            $recharge_ticheng = Db::query("select recharge_income,recharge_income_2 from shop where ID = '$shopid'");
            $recharge_ticheng_2 = 0;
            if ($recharge_ticheng) {
                $recharge_ticheng_2 = (float) $recharge_ticheng[0]['recharge_income_2'];
                $recharge_ticheng = (float) $recharge_ticheng[0]['recharge_income'];
            } else {
                $recharge_ticheng = 0;
            }
            //获取充值额,从数据库查出来的单位是分，转换成元
            $recharge= Db::query("select sum(charge)/100 as salary,ticheng from recharge_record where generated_time >= $begin and generated_time <= $end and job_number = '$job_number'");
            $recharge_income = 0;
            if ($recharge) {
                $recharge_income = $recharge[0]['ticheng'];
                $recharge = (int) $recharge[0]['salary'];
                if (is_null($recharge)) {
                    $recharge = 0;
                }
            } else {
                $recharge = 0;
            }
			*/

            //获取打赏金额
            $dashang = Db::query("select sum(salary)/100 as salary from tip where technician_id = '$job_number' and date >=$begin and date <= $end");
            if($dashang)
			{
                $dashang = (int)$dashang[0]['salary'];
                if(is_null($dashang))
				{
                    $dashang = 0;
                }
            }
			else
			{
                $dashang = 0;
            }
        
            $salary = array_merge($salary, ['name' => $tech['name'], 'job_number' => $tech['job_number'], 
            'entry_date' => date("Y-m-d", $tech['entry_date']),'pai_income'=>$yeji['pai_earn'], 'dian_income' => $yeji['dian_earn'], 
            'dian' => $yeji['dian'],'pai'=>$yeji['pai'], 'dian_bonus' => 0, 'invite_income' => $yeji['come_from_other'], 
            'recharge_amount'=>$recharge,'tip_income'=>$dashang,'recharge_income' => (int)($recharge_income)]);
            array_push($salarys, $salary);
        }
		
        //根据点钟数量计算排行并赋予点钟奖励
        //前三名的点钟数量
        $first_three = [0, 0, 0];
        //前三名的工号
        $job_numbers = ["", "", ""];
        for ($i = 0; $i < count($salarys); $i++) 
		{
            $j = 0;
            for (; $j < count($first_three); $j++) 
			{
                if ($salarys[$i]['dian'] >= $first_three[$j] && $salarys[$i]['dian'] != 0) 
				{
                    for ($k = count($first_three) - 1; $k >= $j + 1; $k--) 
					{
                        $first_three[$k] = $first_three[$k - 1];
                        $job_numbers[$k] = $job_numbers[$k - 1];
                    }
                    $first_three[$j] = $salarys[$i]['dian'];
                    $job_numbers[$j] = $salarys[$i]['job_number'];
                    break;
                }
            }
        }
		
        //计算总工资
        for ($i = 0; $i < count($salarys); $i++) 
		{
            //$salarys[$i] = array_merge($salarys[$i], ['total_salary' => $salarys[$i]['pai_income'] + $salarys[$i]['dian_income'] + $salarys[$i]['dian_bonus']+ $salarys[$i]['recharge_income'] + $salarys[$i]['invite_income'] + $salarys[$i]['tip_income']]);
			$salarys[$i] = array_merge($salarys[$i], ['total_salary' => $salarys[$i]['pai_income'] + $salarys[$i]['dian_income'] + $salarys[$i]['recharge_income'] + $salarys[$i]['invite_income'] + $salarys[$i]['tip_income']]);
        }
        return $salarys;
    }
}
