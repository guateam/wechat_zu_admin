<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Admin as UserModel;
    use think\Db;
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
            $name = urldecode($name);
            $data=UserModel::get(["name"=>$name]);//从数据库调取此用户信息
            if($data){

                if($data->is_admin!=1)return json(['status'=>0]);

                if($data->password == $_POST['password']){
                        return json(['status'=>1]);
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
        
        public function getpermission($name){
            $permission = Db::query("select permission from admin where `name`='$name'");
            $permission = explode(",",$permission[0]['permission']);
            for($i=0;$i<count($permission);$i++){
                $permission[$i] = explode(':',$permission[$i]);
            }
            return $permission;
        }

        public function add($username,$password,$permission){
            //添加父目录项
            foreach($permission as $idx => $it){
                $id = $it[0];
                $fth = Db::query("select father from menu where ID='$id'");
                $fth = $fth[0]['father'];
                if($fth !=0 && !array_search($fth,$permission)){
                    array_push($permission,(string)$fth);
                }
                $permission[$idx] = implode(':',$it);
            }
            $str = implode(',',$permission);
            Db::query("insert into admin (`name`,`password`,`is_admin`,`permission`) values ('$username','$password',1,'$str')");
            return json(['status'=>1]);
        }

        public function delete($id){
            Db::query("delete from admin where `ID`='$id'");
            return json(['status'=>1]);
        }

        public function serve_side($table,$primary_key){
            // DB table to use
            $table = 'datatables_demo';
 
            // Table's primary key
            $primaryKey = 'id';
 
            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            $columns = array(
                array( 'db' => 'first_name', 'dt' => 0 ),
                array( 'db' => 'last_name',  'dt' => 1 ),
                array( 'db' => 'position',   'dt' => 2 ),
                array( 'db' => 'office',     'dt' => 3 ),
                array(
                    'db'        => 'start_date',
                    'dt'        => 4,
                    'formatter' => function( $d, $row ) {
                        return date( 'jS M y', strtotime($d));
                    }
                ),
                array(
                    'db'        => 'salary',
                    'dt'        => 5,
                    'formatter' => function( $d, $row ) {
                        return '$'.number_format($d);
                    }
                )
            );
 
            // SQL server connection information
            $sql_details = array(
                'user' => '',
                'pass' => '',
                'db'   => '',
                'host' => ''
            );
 
 
            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             * If you just want to use the basic configuration for DataTables with PHP
             * server-side, there is no need to edit below this line.
             */
 
            require( 'ssp.class.php' );
 
            echo json_encode(
                SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
            );
        }
    }