<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Notice as UserModel;
    class Notice extends Controller{
        /**
         * 获取所有公告
         * 2018-8-25    创建   袁宜照
         * 
         */
        public function get_all_notice(){
            $data = UserModel::all();
            for($i=0;$i<count($data);$i++){
                $data[$i]->date = date("Y-m-d H:i:s",$data[$i]->date);
            }
            return $data;
        }
        /**
         * 添加公告
         * 2018-8-25    创建    袁宜照
         * @param   string    $title  公告标题
         * @param   string    $text   公告正文 
         */
        public function add_notice($title,$text){
            $data = new UserModel(['title'=>$title,'text'=>$text,'date'=>time()]);
            $data->save();
            return json(['status'=>1]);
        }
        /**
         * 获取公告数量
         * 2018-8-25    创建   袁宜照
         * 
         */
        public function count_all(){
            return count(UserModel::all());
        }
        /**
         * 删除指定id的公告
         * 2018-8-25    创建   袁宜照
         * @param array|string $ids 需要删除的公告ID列表
         */
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