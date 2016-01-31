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
        authentication($_COOKIE['email'], 4);
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
        authentication($_COOKIE['email'], 4);
		$email = $_COOKIE['email'];
		$user = D('User','Service');
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $companyContacts = $_POST['company_contacts'];
            $companyContactsPhone = $_POST['company_contacts_phone'];
            $companyName = $_POST['company_name'];
            $companyContactsPosition = $_POST['company_contacts_position'];
            $companyPhone = $_POST['company_phone'];
            if(empty($companyContacts) || empty($companyContactsPhone) || empty($companyName)){
                echo '{"code":"-1","msg":"联系人、联系人手机、企业名称为必填项"}';
                exit;
            }
            $condition['email'] = $email;
            $data['company_contacts'] = $companyContacts;
            $data['company_contacts_phone'] = $companyContactsPhone;
            $data['company_name'] = $companyName;
            $data['company_contacts_position'] = $companyContactsPosition;
            $data['company_phone'] = $companyPhone;
            $res = $user->updateUserInfo($condition, $data);
            if($res === false){
                echo '{"code":"-1","msg":"save error"}';
                exit;
            }else{
                setcookie("userName", $companyName, time()+3600*24*7);
                echo '{"code":"0","msg":"修改成功"}';
            }
        }else{
            $userInfo = $user->getUserINfoByEmail($email);
            if ($_GET['display'] == 'json') {
                dump($objUser);
                exit;
            }
            $this->assign("data", $userInfo[0]);
            $this->display("ProjectInvestor:myInformation");
        }
	}
}