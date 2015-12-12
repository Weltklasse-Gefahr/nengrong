<?php
namespace Home\Service;

use Think\Model;

class ManagerService extends Model{

	/**
    **@auth qianqiang
    **@breif 管理员登录
    **@date 2015.12.08
    **/
	public function loginService($userName, $password){
		$manager = M("Manager");
        $objManager = $manager->where("user_name='%s' and password='%s' and status!=9999", array($userName, MD5($password)))->select();
        if (sizeof($objManager) != 1) {
        	echo '{"code":"-1","msg":"登录信息错误"}';
        	exit;
        }

        setcookie("userName", $userName, time()+3600);
        setcookie("mUserName", MD5($userName."ENFENF"), time()+3600);

        return $objManager[0];
    }

	/**
    **@auth qianqiang
    **@breif 修改密码
    **@date 2015.12.09
    **/
	public function changePassword($userName, $password, $newPwd){
		$manager = M('Manager');
		$objManager = $manager->where("user_name='%s' and password='%s' and status!=9999", array($userName, MD5($password)))->select();
		if(sizeof($users) == 0){
			echo '{"code":"-1","msg":"原密码错误!"}';
			exit;
		}

		$objManager->user_name = $userName;
		$objManager->password = MD5($newPwd);
		$objManager->save();

		$objManager = $manager->where("user_name='%s' and password='%s' and status!=9999", array($userName, MD5($newPwd)))->select();
		if (sizeof($objManager) != 1) {
			echo '{"code":"-1","msg":"mysql error!"}';
			exit;
		}
		return $objManager[0];
	}
	
}