<?php 
    namespace app\api\controller;
    use think\Controller;
	use \app\api\model\Attendance as UserModel;
	use think\Db;

	class Attendance extends Controller
	{
		public function get_all_attendance($begin, $end)
		{
			$data = [];
			
			$data = [];
			$technicians = \app\api\model\Technician::all();
			foreach ($technicians as $tech) 
			{
				array_push($data, self::get_attendance($tech->job_number, $begin, $end));
			}
			
			return $data;			
		}
		
		public static function get_attendance($job_number, $begin, $end)
		{
			//考勤类型  (0-签到，1-签退  2--异常）
			$check_in_time = "";
			$tech_att_checkin = Db::query("select * from attendance where job_number = '$job_number' and check_time >= '$begin' and check_time <= '$end' and sign_type = 0" );
			if ($tech_att_checkin)
			{
				$check_in_time = $tech_att_checkin[0]['check_time'];
				$check_in_time = date('Y-m-d H:i:s', $check_in_time);
			}
			
			$check_out_time = "";
			$tech_att_checkout = Db::query("select * from attendance where job_number = '$job_number' and check_time >= '$begin' and check_time <= '$end' and sign_type = 1" );
			if ($tech_att_checkout)
			{
				$check_out_time = $tech_att_checkout[0]['check_time'];				
				$check_out_time = date('Y-m-d H:i:s', $check_out_time);
				
			}
			
			$check_leave_time = "";
			$tech_att_checkleave = Db::query("select * from attendance where job_number = '$job_number' and check_time >= '$begin' and check_time <= '$end' and sign_type = 2" );
			if ($tech_att_checkleave)
			{
				foreach ($tech_att_checkleave as $leave)
				{					
					$leave_time = date('Y-m-d H:i:s', $leave['check_time']);
					$check_leave_time = $check_leave_time.$leave_time."  ,  ";
				}				
			}
			
			return [				
				'job_number' => $job_number,
				'check_in_time' => $check_in_time,
				'check_out_time' => $check_out_time,
				'check_leave_time' => $check_leave_time,	
				'sign_type'=>3,
				'check_time'=>0,
			];
		}
		
	}

	
?>