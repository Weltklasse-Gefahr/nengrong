<?php
namespace Home\Controller;

use Think\Controller;

class ProjectInvestorMyProController extends Controller {
	/**
    **@auth qianqiang
    **@breif 项目投资方->项目管理->推荐项目
    **@date 2016.1.5
    **/
	public function showPushProject(){
		isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
		// $email = $_COOKIE['email'];
		$email = 'qianqiang@qq.com';
		$objProject = D("Project", "Service");
        $listProject = $objProject->getPushProject($email);

        if($_GET['display']=="json"){
            header('Content-Type: text/html; charset=utf-8');
            dump($listProject);
            exit;
        }
        $this->assign('listProject', $listProject);
        $this->display();
	}

	/**
    **@auth qianqiang
    **@breif 项目投资方->项目管理->已投资项目
    **@date 2016.1.5
    **/
	public function showInvestProject(){
		isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
		$email = $_COOKIE['email'];
		//是否需要做？需要建立项目投资表还是加一个字段
		$this->display();
	}
}