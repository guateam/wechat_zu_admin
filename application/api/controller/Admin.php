<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Admin as UserModel;
    class Admin extends Controller{
        /**
         * 检查cookie是否有效，返回用户id
         */
        public function checkuser($cookie){
            $user=\app\api\model\Admin::get(['Cookie'=>$cookie]);
            if($user){
                return $user->ID;
            }
        }
        /**
         * 内部方法，生成一个不重复的cookie
        */
        private function getcookie(){
            $cookie = $this->makekeys();
            if(!isset($_COOKIE['login']))setcookie("login",$cookie);
            else{
                while($cookie ==$_COOKIE['login'] ){
                    $cookie = $this->makekeys();
                }
            }
            //if (UserModel::get(["Cookie" => $cookie])) {
            //    $cookie = $this->getcookie();
            //}
            return $cookie;
        }
        /**
         * 内部方法，生成随机的一串代码
         */
        private function makekeys($length = 8){

            // 密码字符集，可任意添加你需要的字符
            $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
                'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's',
                't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
                'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
                'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

            // 在 $chars 中随机取 $length 个数组元素键名

            $keys = '';
            for ($i = 0; $i < $length; $i++) {
                // 将 $length 个数组元素连接成字符串
                $keys .= $chars[mt_rand(0, 61)];
            }
            return $keys;
        }

        /**
         * 登录方法
         */
        public function login(){
            $name = $_POST['username'];
            $data=UserModel::get(["name"=>$name]);//从数据库调取此用户信息
            if($data){
                if($data->password == $_POST['password']){
                    if($data->is_admin==1)
                        return json(['status'=>1]);
                    else return json(['status'=>0]);
                }else return json(['status'=>-1]);
            }else return json(['status'=>-2]);
        }

        /**
         * 验证该用户名是否重复
         */
        public function checkrepeatname($new_name){
            $list = UserModel::all();
            foreach($list as $each){
                if($each->Username == $new_name)return false;
            }
            return true;
        }
        /**
         * 验证该手机号是否重复
         */
        public function checkrepeatphone($new_phone){
            $list = UserModel::all();
            foreach($list as $each){
                if($each->PhoneNumber == $new_phone)return false;
            }
            return true;
        }
        
        /**
         * 获取姓名
         */
        public function getname($userid){
            $user=UserModel::get(['ID'=>$userid]);
            if($user){
                return $user->name;
            }
            return "未知";
        }
        
    }