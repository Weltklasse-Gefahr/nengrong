<?php
namespace Home\Service;

use Think\Model;

class UserService extends Model{

	/**
    **@auth qianqiang
    **@breif 登录
    **@date 
    **/
	public function loginService($email, $password){
		$user = M("User");
        $users = $user->where("email='%s' and password='%s' and status!=9999", array($email, MD5($password)))->select();

        if (sizeof($users) != 1) {
        	echo '{"code":"-1","msg":"登录信息错误"}';
        	exit;
        }

        setcookie("email", $email, time()+3600);
        setcookie("mEmail", MD5($email."ENFENF"), time()+3600);

        return $users;
	}

	/**
    **@auth qianqiang
    **@breif 注册
    **@date 
    **/
	public function registerService($email, $password){
		$user = M("User");
		$users = $user->where("email='%s' and status!=9999", array($email) )->select();
		if (sizeof($users) == 1) {
			echo '{"code":"-1","msg":"该邮箱已经注册"}';
			exit;
		} 

		$userAdd = M('user');
        $userAdd->email = $email;
        $userAdd->password = md5($password);
        $userAdd->status = 2;
        if(empty($userType) ) {
            $userAdd->user_type = 3;
        }
        else{
            $userAdd->user_type = $userType;
        }
        $user->add();

        $users = $user->where("email='%s' and status!=9999", array($email) )->select();

        if (sizeof($users) != 1) {
        	echo '{"code":"-1","msg":"mysql error!"}';
        	exit;
        }
        return $users;
	}

	/**
    **@auth qianqiang
    **@breif 修改密码
    **@date 
    **/
	public function changePassword($email, $password, $newPwd){
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

		return $objUser;
	}

    /**
    **@auth qianqiang
    **@breif 根据项目编码获取项目信息
    **@date 2015.12.05
    **/
    public function getUserInfo($email){
        //$objUser = M("User");
        //这样写可读性是不是更好
        $objUser = new \Home\Model\UserModel(); 
        $condition["email"] = $email;
        $userInfo = $objUser->where($condition)->select();
        return $userInfo[0];
    }
}
