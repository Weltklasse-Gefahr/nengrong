<?php
namespace Home\Service;

use Think\Model;

class AdminService extends Model{

	/**
    **@auth qianqiang
    **@breif 管理员登录
    **@date 2015.12.08
    **/
	public function loginService($userName, $password){
		$manager = M("Admin");
        $objManager = $manager->where("user_name='%s' and password='%s'", array($userName, MD5($password)))->select();
        if (sizeof($objManager) != 1) {
        	echo '{"code":"-1","msg":"登录信息错误"}';
        	exit;
        }

        setcookie("adminName", $userName);
        setcookie("mAdminName", MD5(addToken($userName)));
        session_start();

        return $objManager[0];
    }

    /**
    **@auth qianqiang
    **@breif 管理员注销
    **@date 2015.12.19
    **/
	public function logoutService(){

        setcookie("adminName", $userName, time()-3600);
        setcookie("mAdminName", MD5(addToken($userName)), time()-3600);
        session_destroy();
    }

	/**
    **@auth qianqiang
    **@breif 修改密码
    **@date 2015.12.09
    **/
	public function changePasswordService($userName, $password, $newPwd){
		$manager = M('Admin');
		$objManager = $manager->where("user_name='%s' and password='%s'", array($userName, MD5($password)))->select();
		if(sizeof($objManager) == 0){
			echo '{"code":"-1","msg":"old password error!"}';
			exit;
		}

		$data['password'] = MD5($newPwd);
		$manager->where("user_name='".$userName."'")->save($data);

		$objManager = $manager->where("user_name='%s' and password='%s'", array($userName, MD5($newPwd)))->select();
		if (sizeof($objManager) != 1) {
			echo '{"code":"-1","msg":"mysql error!"}';
			exit;
		}
		return $objManager[0];
	}

    /**
    **@auth qianqiang
    **@breif 真删除用户
    **@date 2015.1.21
    **/
    public function dropUserService($id){
        $user = M('User');
        $objUser = $user->where("id='".$id."' and delete_flag!=9999")->select();
        if(sizeof($objUser) == 0){
            echo '{"code":"-1","msg":"用户不存在!"}';
            exit;
        }

        $res = $user->where("id='".$id."'")->delete();

        if (!$res) {
            echo '{"code":"-1","msg":"mysql error!"}';
            exit;
        }
    }
	
}