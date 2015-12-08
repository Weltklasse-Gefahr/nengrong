<?php
namespace Home\Service;

use Think\Model;

class UserService extends Model{

	//登录
	public function loginService(){
		$user = M("User");
        $users = $user->where("email='%s' and password='%s' and status!=9999", array($email, MD5($password)))->select();

        setcookie("email", $email, time()+3600);
        setcookie("mEmail", MD5($email."ENFENF"), time()+3600);

        return $users;
	}

	//注册
	public function registerService(){
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
        return $users;
	}

	//修改密码
	public function changePassword(){

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
