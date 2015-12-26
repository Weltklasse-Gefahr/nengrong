<?php
namespace Home\Controller;

use Think\Controller;

class ProjectInvestorMyInfoController extends Controller {
	/**
    **@auth qianqiang
    **@breif 项目投资方->个人中心->安全中心
    **@date 2015.12.26
    **/
	public function securityCenter(){
		$this->display();
	}

	/**
    **@auth qianqiang
    **@breif 项目投资方->个人中心->我的资料
    **@date 2015.12.26
    **/
	public function myInformation(){
		$this->display();
	}
}