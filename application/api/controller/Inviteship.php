<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Inviteship as Ship;

    class Inviteship extends Controller{

        public function get_all()
        {
            $data = Ship::all();
            if($data)
			{
                return $data;
            }
        }
		
        public function add($inviter,$newcome,$persentage)
		{
            $ori = Ship::get(['freshman_job_number'=>$newcome]);
            if($ori)
			{
                $ori->delete();
            }
            $data =new Ship(['inviter_job_number'=>$inviter,'freshman_job_number'=>$newcome,'persentage'=>$persentage]);
            $data->save();
            return json(['status'=>1]);
        }
		
		public function update($newcome,$persentage)
		{
            $data = Ship::get(['freshman_job_number'=>$newcome]);
            if($data)
			{
                $data->persentage = $persentage;
                $data->save();
                return json(['status'=>1]);
            }
            return json(['status'=>0]);
        }
    }