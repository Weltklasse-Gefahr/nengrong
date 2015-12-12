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

        return $users[0];
	}

	/**
    **@auth qianqiang
    **@breif 注册
    **@date 
    **/
	public function registerService($email, $password, $userType){
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
        $userAdd->create_date = time();
        $user->add();

        $users = $user->where("email='%s' and status!=9999", array($email) )->select();

        if (sizeof($users) != 1) {
        	echo '{"code":"-1","msg":"mysql error!"}';
        	exit;
        }
        return $users[0];
	}

	/**
    **@auth qianqiang
    **@breif 修改密码
    **@date 
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
		$objUser->change_date = time();
		$objUser->save();

		$objUser = $user->where("email='%s' and password='%s' and status!=9999", array($email, MD5($newPwd)))->select();
		if (sizeof($objUser) != 1) {
			echo '{"code":"-1","msg":"mysql error!"}';
			exit;
		}

		return $objUser[0];
	}

	/**
    **@auth qianqiang
    **@breif 重置密码
    **@date 2015.12.10
    **/
	public function resetPasswordService($email, $newPwd){
		$user = M('User');
		$objUser = $user->where("email='%s' and status!=9999", array($email))->select();
		if(sizeof($objUser) == 0){
			echo '{"code":"-1","msg":"用户不存在!"}';
			exit;
		}

        $user->email = $email;
        $user->password = MD5($newPwd);
        $user->change_date = time();
        $user->save();

        $objUser = $user->where("email='%s' and password='%s' and status!=9999", array($email, MD5($newPwd)))->select();
		if (sizeof($objUser) != 1) {
			echo '{"code":"-1","msg":"mysql error!"}';
			exit;
		}

		return $objUser[0];
	}

	/**
    **@auth qianqiang
    **@breif 展示项目提供方所有用户
    **@date 2015.12.10
    **/
	public function getAllProjectProviderService(){
		$user = M('User');
		$users = $user->where("user_type=3 and status!=9999")->select();
		return $users;
	}

	/**
    **@auth qianqiang
    **@breif 展示项目投资方所有用户
    **@date 2015.12.10
    **/
	public function getAllProjectInvestorService(){
		$user = M('User');
		$users = $user->where("user_type=4 and status!=9999")->select();
		return $users;
	}

	/**
    **@auth qianqiang
    **@breif 展示业务员所有用户
    **@date 2015.12.10
    **/
	public function getAllInnerStaffService(){
		$user = M('User');
		$users = $user->where("user_type=2 and status!=9999")->select();
		return $users;
	}

	/**
    **@auth qianqiang
    **@breif 删除用户
    **@date 2015.12.12
    **/
	public function deleteUserService($email){
		$user = M('User');
		$objUser = $user->where("email='%s' and status!=9999", array($email))->select();
		if(sizeof($objUser) == 0){
			echo '{"code":"-1","msg":"用户不存在!"}';
			exit;
		}

		$user->email = $email;
        $user->status = 9999;
        $user->change_date = time();
        $user->save();

        $objUser = $user->where("email='%s' and status!=9999", array($email))->select();
		if (sizeof($objUser) != 0) {
			echo '{"code":"-1","msg":"mysql error!"}';
			exit;
		}
	}

	/**
    **@auth qianqiang
    **@breif 管理员修改项目提供方信息
    **@date 2015.12.12
    **/
	public function changeProjectProviderByManager($email, $phone){
		$user = M('User');
		$objUser = $user->where("email='%s' and status!=9999", array($email))->select();
		if(sizeof($objUser) == 0){
			echo '{"code":"-1","msg":"用户不存在!"}';
			exit;
		}

		$user->email = $email;
        $user->company_telephone = $phone;
        $user->change_date = time();
        $user->save();

        $objUser = $user->where("email='%s' and company_telephone='%s' and status!=9999", array($email, $phone))->select();
		if (sizeof($objUser) != 1) {
			echo '{"code":"-1","msg":"mysql error!"}';
			exit;
		}
		return $objUser[0];
	}

	/**
    **@auth qianqiang
    **@breif 管理员修改项目投资方信息
    **@date 2015.12.12
    **/
	public function changeProjectInvestorByManager($email, $companyName){
		$user = M('User');
		$objUser = $user->where("email='%s' and status!=9999", array($email))->select();
		if(sizeof($objUser) == 0){
			echo '{"code":"-1","msg":"用户不存在!"}';
			exit;
		}

		$user->email = $email;
        $user->company_name = $companyName;
        $user->change_date = time();
        $user->save();

        $objUser = $user->where("email='%s' and company_name='%s' and status!=9999", array($email, $companyName))->select();
		if (sizeof($objUser) != 0) {
			echo '{"code":"-1","msg":"mysql error!"}';
			exit;
		}
		return $objUser[0];
	}

	/**
    **@auth qianqiang
    **@breif 管理员修改项目业务人员信息
    **@date 2015.12.12
    **/
	public function changeInnerStaffByManager($email, $code, $name){
		$user = M('User');
		$objUser = $user->where("email='%s' and status!=9999", array($email))->select();
		if(sizeof($objUser) == 0){
			echo '{"code":"-1","msg":"用户不存在!"}';
			exit;
		}

		$user->email = $email;
        $user->code = $code;
        $user->name = $name;
        $user->change_date = time();
        $user->save();

        $objUser = $user->where("email='%s' and code='%s' and name='%s' and status!=9999", array($email, $code, $name))->select();
		if (sizeof($objUser) != 0) {
			echo '{"code":"-1","msg":"mysql error!"}';
			exit;
		}
		return $objUser[0];
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
