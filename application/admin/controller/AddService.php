<?php
namespace app\admin\controller;
use think\Controller;
use \app\api\model\ServiceType as UserModel;


        $ctrl = new \app\api\controller\Servicetype();
        return $this->fetch('addservice');
    }
}