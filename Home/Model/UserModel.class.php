<?php
namespace Home\Model;

use Think\Model;

class UserModel extends Model{

	public function mytest(){
		return M()->query('select * from user');
	}
}
