<?php
namespace Home\Service;

use Think\Model;

class ProjectProviderMyInfoService extends Model{
	/**
    **@auth qianqiang
    **@breif 修改密码
    **@date 2015.12.12
    **/
	public function changePasswordService($email, $password, $newPwd){
		$user = M('User');
		$objUser = $user->where("email='%s' and password='%s' and status!=9999", array($email, MD5($password)))->select();
		if(sizeof($objUser) == 0){
			echo '{"code":"-1","msg":"原密码错误!"}';
			exit;
		}

		$objUser->email = $email;
		$objUser->password = MD5($newPwd);
		$objUser->save();

		$objUser = $user->where("email='%s' and password='%s' and status!=9999", array($email, MD5($newPwd)))->select();
		if (sizeof($objUser) != 1) {
			echo '{"code":"-1","msg":"mysql error!"}';
			exit;
		}

		return $objUser[0];
	}
}