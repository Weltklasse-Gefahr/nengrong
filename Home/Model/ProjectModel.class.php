<?php
namespace Home\Model;

use Think\Model;

class ProjectModel extends Model{


    /**
    **@auth qiujinhan@gmail.com
    **@breif 自己简单的封装了下project的select语句(没有什么鸟用，thinkPHP基本都已经封装好了)
    **@date 2015.12.05
    **/
	public function mySelect($whereInfo){
		return M()->query('select * from project '. $whereInfo);
	}
}
