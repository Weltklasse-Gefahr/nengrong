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
		isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
		if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $email = $_COOKIE['email'];
            $pwd = $_POST['password'];
            $newPwd = $_POST['newPassword'];
            if ( empty($pwd) || empty($newPwd) ) {
                echo '{"code":"-1","msg":"新旧密码不可为空！"}';
                exit;
            }

            $user = D('User','Service');
            $objUser = $user->changePasswordService($email, $pwd, $newPwd);
            if ($_GET['display'] == 'json') {
                dump($objUser);
                exit;
            }
            $user->logoutService();
            echo '{"code":"0","msg":"success"}';
        }else{
            $this->display("ProjectInvestor:securityCenter");
        }
	}

	/**
    **@auth qianqiang
    **@breif 项目投资方->个人中心->我的资料
    **@date 2015.12.26
    **/
	public function myInformation(){
		isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
		$email = $_COOKIE['email'];
		$user = D('User','Service');
		$userInfo = $user->getUserINfoByEmail($email);
		if ($_GET['display'] == 'json') {
			dump($objUser);
			exit;
		}
		$this->assign("userInfo", $userInfo[0]);
		$this->display("ProjectInvestor:myInformation");
	}
}