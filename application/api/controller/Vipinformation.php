<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Vipinformation as UserModel;
    class Vipinformation extends Controller{

        /**
         * 
         * @param int $lv 用于在更新数据时，判断查找到的重复项是不是该项本身
         */
        public function check_name($lv=-1,$name){
            $lv = (int)$lv;
            $repeat = UserModel::get(['name'=>$name]);
            if($repeat){
                if($repeat->level != $lv)
                    return false;
            }
            return true;
        }
        public function delete($lv){
            if(count($lv) == 0)return json(['status'=>0]);

            foreach($lv as $elv){
                $level = UserModel::get(['level'=>$elv]);
                if($level){
                    $level->delete();
                }else{
                    return json(['status'=>0]);
                }
            }
            $level = UserModel::all();
            $lv = 1;
            foreach($level as $each){
                $each->level = $lv;
                $each->save();
                $lv++;
            }
            return json(['status'=>1]);
        }
        public function update_level($lv,$name,$charge,$discount,$dura){
            $level = UserModel::get(['level'=>$lv]);
            if($level){
                $level->name = $name;
                $level->necessary_charge_to_level_up = $charge*100;
                $level->discount_ratio = $discount;
                $level->extra_duration = $dura;
                $level->save();
                return json(["status"=>1]);
            }
            return json(["status"=>0]);
        }

        public function add_level($name,$charge,$discount,$dura){
            $level = UserModel::all();
            $highest_lv = 0;
            foreach($level as $lv){
                if($lv->level > $highest_lv)$highest_lv = $lv->level;
            }
            $highest_lv++;
            $new_lv = new UserModel(['level'=>$highest_lv,"name"=>$name,"necessary_charge_to_level_up"=>$charge,
                                        'discount_ratio'=>$discount,"extra_duration"=>$dura]);
            $new_lv->save();
            return ['status'=>1];
        }
    }