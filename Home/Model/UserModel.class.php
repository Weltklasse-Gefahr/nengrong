<?php
namespace Home\Model;

use Think\Model;

class UserModel extends Model{
    /**
    **@auth qianqiang
    **@breif 强哥的测试函数
    **@date 2015.12.05
    **/
	public function mytest(){
		return M()->query('select * from user');
	}
}
