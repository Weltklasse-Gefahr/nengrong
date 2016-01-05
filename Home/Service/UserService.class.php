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
        setcookie("mEmail", MD5(addToken($email)), time()+3600);

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
        $data['email'] = $email;
        $data['password'] = md5($password);
        if(empty($userType) ) {
            $data['user_type'] = 3;
        }
        else{
            $data['user_type'] = $userType;
        }
        $data['create_date'] = date("Y-m-d H:i:s",time());
        $data['change_date'] = date("Y-m-d H:i:s",time());
        $data['status'] = 2;
        $userAdd->add($data);

        $users = $user->where("email='%s' and status!=9999", array($email) )->select();

        if (sizeof($users) != 1) {
        	echo '{"code":"-1","msg":"mysql error!"}';
        	exit;
        }
        return $users[0];
	}

	/**
    **@auth qianqiang
    **@breif 用户激活
    **@date 2015.12.16
    **/
	public function activeService($email){
		$user = M('user');
		$data['status'] = 1;
		$data['change_date'] = date("Y-m-d H:i:s",time());
		$user->where("email='".$email."' and status=2")->save($data);

		$objUser = $user->where("email='".$email."' and status=1")->select();
		if (sizeof($objUser) != 1) {
			echo '{"code":"-1","msg":"mysql error!"}';
			exit;
		}

		return true;
	}

	/**
    **@auth qianqiang
    **@breif 用户注销
    **@date 2015.12.26
    **/
	public function logoutService(){
        setcookie("email", $email, time()-3600);
        setcookie("mEmail", MD5(addToken($email)), time()-3600);
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

		$data['password'] = MD5($newPwd);
		$data['change_date'] = date("Y-m-d H:i:s",time());
		$user->where("email='".$email."'")->save($data);

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

		//$data['email'] = $email;
		$data['password'] = MD5($newPwd);
		$data['change_date'] = date("Y-m-d H:i:s",time());
        $user->where("email='".$email."'")->save($data);

        $objUser = $user->where("email='%s' and password='%s' and status!=9999", array($email, MD5($newPwd)))->select();
		if (sizeof($objUser) != 1) {
			echo '{"code":"-1","msg":"mysql error!"}';
			exit;
		}

		return $objUser[0];
	}

	/**
    **@auth qianqiang
    **@breif 还原初始密码123456
    **@date 2015.12.14
    **/
	public function setOriginalPasswordService($id){
		$user = M('User');

		//$data['email'] = $email;
		$data['password'] = MD5("123456");
		$data['change_date'] = date("Y-m-d H:i:s",time());
        $user->where("id='".$id."'")->save($data);

        $objUser = $user->where("id='%d' and password='%s' and status!=9999", array($id, MD5("123456")))->select();
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
	public function deleteUserService($id){
		$user = M('User');
		$objUser = $user->where("id='".$id."' and status!=9999")->select();
		if(sizeof($objUser) == 0){
			echo '{"code":"-1","msg":"用户不存在!"}';
			exit;
		}

		$data["status"] = 9999;
        $data['change_date'] = date("Y-m-d H:i:s",time());
        $user->where("id='".$id."'")->save($data);

        $objUser = $user->where("id='".$id."' and status!=9999")->select();
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
	public function changeProjectProviderByManager($id, $email, $phone, $telephone){
		$user = M('User');
		$data['email'] = $email;
		$data['company_phone'] = $phone;
		$data['company_telephone'] = $telephone;
		$data['change_date'] = date("Y-m-d H:i:s",time());
        $user->where("id='".$id."'")->save($data);

        $objUser = $user->where("email='%s' and company_phone='%s' and company_telephone='%s' and status!=9999", array($email, $phone, $telephone))->select();
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
	public function changeProjectInvestorByManager($id, $email, $companyName){
		$user = M('User');
		$data['email'] = $email;
		$data['company_name'] = $companyName;
		$data['change_date'] = date("Y-m-d H:i:s",time());
        $user->where("id='".$id."'")->save($data);

        $objUser = $user->where("email='%s' and company_name='%s' and status!=9999", array($email, $companyName))->select();
		if (sizeof($objUser) != 1) {
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
	public function changeInnerStaffByManager($id, $email, $code, $name){
		$user = M('User');
		$data['email'] = $email;
		$data['code'] = $code;
		$data['name'] = $name;
		$data['change_date'] = date("Y-m-d H:i:s",time());
        $user->where("id='".$id."'")->save($data);

        $objUser = $user->where("email='%s' and code='%s' and name='%s' and status!=9999", array($email, $code, $name))->select();
		if (sizeof($objUser) != 1) {
			echo '{"code":"-1","msg":"mysql error!"}';
			exit;
		}
		return $objUser[0];
	}

	/**
    **@auth qianqiang
    **@breif 根据用户id查询用户信息
    **@date 2015.12.13
    **/
	public function getUserINfoById($id){
		$condition["id"] = $id;
		$condition["status"] = array('neq',9999);
		$userInfo = $this->getUserInfo($condition);
		return $userInfo;
	}

	/**
    **@auth qianqiang
    **@breif 根据用户email查询用户信息
    **@date 2015.12.13
    **/
	public function getUserINfoByEmail($email){
		$condition["email"] = $email;
		$condition["status"] = array('neq',9999);
		$userInfo = $this->getUserInfo($condition);
		return $userInfo;
	}

	/**
    **@auth qianqiang
    **@breif 获取某一项目的所有项目投资方信息，推送状态push_flag
    **@date 2015.12.30
    **/
	public function getInvestorPush($projectCode, $page){
		$condition['user_type'] = 4;
		$condition["status"] = array('neq',9999);
		// $investorList = $this->getUserInfo($condition);
		$objUser = M("User");
		if($page == -1)
			$investorList = $objUser->where($condition)->select();
		else
			$investorList = $objUser->where($condition)->page($page, 6)->select();
		$projectObj = D('Project', 'Service');
		$projectList = $projectObj->getPushProjectByProCode($projectCode);
		$i = 0;
		while($investorList[$i]){
			$j = 0;
			$investorList[$i]['push_flag'] = "未推送";
			while($projectList[$j]){
				if($projectList[$j]['investor_id'] == $investorList[$i]['id']){
					$investorList[$i]['push_flag'] = "已推送";
					break;
				}
				$j += 1;
			}
			$i += 1;
		}
		return $investorList;
	}	

    /**
    **@auth qianqiang
    **@breif 查询用户信息
    **@param condition 数组，查询的条件
    **@return 一个数组
    **@date 2015.12.05
    **/
    public function getUserInfo($condition){
        //$objUser = M("User");
        //这样写可读性是不是更好
        $objUser = new \Home\Model\UserModel(); 
        $userInfo = $objUser->where($condition)->select();
        return $userInfo;
    }

    /**
    **@auth qiujinhan
    **@breif 更新user表数据
    **@param where 字符串格式，更新的条件
    **@param updateData 数组，更新的内容
    **@return 更新成功返回true 更新失败返回false
    **@date 2015.12.05
    **/
    public function updateUserInfo($where, $updateData){
        $objUser = new \Home\Model\UserModel(); 
        $res = $objUser->where($where)->save($updateData);
        return $res;
    }
}
