<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Notice as UserModel;
    class Notice extends Controller{
        public function get_all_notice(){
            $data = UserModel::all();
            return $data;
        }

        public function add_notice($title,$text){
            $data = new UserModel(['title'=>$title,'text'=>$text]);
            $data->save();
            return json(['status'=>1]);
        }
        public function count_all(){
            return count(UserModel::all());
        }
        public function delete_notice($ids){
            foreach($ids as $id){
                $data = UserModel::get(['ID'=>$id]);
                if($data){
                    $data->delete();
                }else{
                    return json(['status'=>0]);
                }
            }
            return json(['status'=>1]);
        }
    }