<?php 
    namespace app\api\controller;
    use think\Controller;
    use \app\api\model\Privateroom as Room;
    class Privateroom extends Controller{
        /**
         * 获取包厢列表
         * 2018-8-24 创建 张煜
         */
        public function getroomlist(){
            $roomlist=Room::all();
            $data=[];
            foreach($roomlist as $value){
                $item=[
                    'ID'=>$value->ID,
                    'name'=>$value->name,
                    'roomid'=>$value->room_id,
                    'capacity'=>$value->capacity,
                    'servicetype'=>$value->service_type,
                    'servicename'=>\app\api\controller\Servicetype::getservicename($value->service_type)
                ];
                array_push($data,$item);
            }
            return $data;
        }
        /**
         * 添加包厢
         * 2018-8-24 创建 张煜
         * @param mixed $name 房间名称
         * @param mixed $roomid 房间号
         * @param int $capacity 房间容量
         * @param mixed $servicetype 房间可提供的服务类型
         */
        public function add($name,$roomid,$capacity,$servicetype){
            if(Room::get(['name'=>$name]) || Room::get(['room_id'=>$roomid])){
                return -1;
            }
            $room=new Room();
            $room->data([
                'name'=>$name,
                'room_id'=>$roomid,
                'capacity'=>$capacity,
                'service_type'=>$servicetype
            ]);
            $room->save();
            return 1;
        }
        /**
         * 清除包厢
         * 2018-8-24 创建 张煜
         * @param int $id 房间内部ID
         */
        public function delete($id){
            $room = Room::get(['ID'=>$id]);
            if($room){
                $room->delete();
                return 1;
            }
            return 0;
        }
        /**
         * 批量清除包厢
         * 2018-8-24 创建 张煜
         * @param array $idlist 房间内部ID数组
         */
        public function deletelist($idlist){
            foreach($idlist as $id){
                $this->delete($id);
            }
            return 1;
        }
        /**
         * 编辑房间信息
         * 2018-8-24 创建 张煜
         * @param int $id 房间内部id
         * @param mixed $name 房间名称
         * @param mixed $roomid 房间号
         * @param int $capacity 房间容量
         * @param mixed $servicetype 房间可提供的服务类型
         */
        public function edit($id,$name,$roomid,$capacity,$servicetype){
            if(Room::get(['name'=>$name])){
                $room1=Room::get(['name'=>$name]);
                if($room1->ID!=$id){
                    return -1;
                }
            }
            if(Room::get(['room_id'=>$roomid])){
                $room1=Room::get(['room_id'=>$roomid]);
                if($room1->ID!=$id){
                    return -1;
                }
            }
            $room = Room::get(['ID'=>$id]);
            if($room){
                $room->data([
                    'name'=>$name,
                    'room_id'=>$roomid,
                    'capacity'=>$capacity,
                    'service_type'=>$servicetype
                ]);
                $room->save();
                return 1;
            }
            return 0;
        }
        /**
         * 获取单个房间信息
         * 2018-8-24 创建 张煜
         * @param int $id 房间内部id
         */
        public function getroom($id){
            $room=Room::get(['ID'=>$id]);
            $data=[];
            if($room){
                $data=[
                    'ID'=>$room->ID,
                    'name'=>$room->name,
                    'roomid'=>$room->room_id,
                    'capacity'=>$room->capacity,
                    'servicetype'=>$room->service_type
                ];
            }
            return $data;
        }
        /**
         * 检查房间名是否正确
         * 2018-8-28 创建 张煜
         * @param mixed $name 房间名
         */
        public function checkname($name){
            $room=Room::get(['name'=>$name]);
            if($room){
                return 0;
            }
            return 1;
        }
        /**
         * 检查房间号是否正确
         * 2018-8-28 创建 张煜
         * @param mixed $id 房间号
         */
        public function checkroomid($id){
            $room=Room::get(['room_id'=>$id]);
            if($room){
                return 0;
            }
            return 1;
        }
        /**
         * 检查房间名是否可以修改
         * 2018-8-28 创建 张煜
         * @param int $id 包厢的内部id
         * @param mixed $name
         */
        public function checknamewithid($id,$name){
            $room=Room::get(['name'=>$name]);
            if($room){
                return $room->ID==$id?1:0;
            }
            return 1;
        }
        /**
         * 检查房间号是否可修改
         * 2018-8-28 创建 张煜
         * @param int $id 包厢内部id
         * @param mixed $roomid 房间号
         */
        public function checkroomidwithid($id,$roomid){
            $room=Room::get(['room_id'=>$roomid]);
            if($room){
                return $room->ID==$id?1:0;
            }
            return 1;
        }
    }