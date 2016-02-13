<?php
namespace Home\Service;

use Think\Model;

class UserService extends Model{

	/**
    **@auth qianqiang
    **@breif 登录
    **@date 
    **/
	public function loginService($email, $password, $keepFlag=0){
		$user = M("User");
        $users = $user->where("email='%s' and password='%s' and delete_flag!=9999", array($email, MD5($password)))->select();
        if (sizeof($users) != 1) {
        	echo '{"code":"-1","msg":"登录信息错误"}';
        	exit;
        }
        if($users[0]['status'] == 2){
        	echo '{"code":"-1","msg":"用户未激活,请查收激活邮件"}';
        	exit;
        }
        if($keepFlag == 1){
        	if($users[0]['user_type'] == 2){
	        	setcookie("userType", 2, time()+3600*24*7);
	        	$innerName = "能融网客服";
	        	setcookie("userName", $innerName, time()+3600*24*7);
	        }elseif($users[0]['user_type'] == 3){
	        	setcookie("userType", 3, time()+3600*24*7);
	        	setcookie("userName", $users[0]['company_name'], time()+3600*24*7);
	        }elseif($users[0]['user_type'] == 4){
	        	setcookie("userType", 4, time()+3600*24*7);
	        	setcookie("userName", $users[0]['company_name'], time()+3600*24*7);
	        }
	        setcookie("email", $email, time()+3600*24*7);
	        setcookie("mEmail", MD5(addToken($email)), time()+3600*24*7);
        }else{
	        if($users[0]['user_type'] == 2){
	        	setcookie("userType", 2);
	        	$innerName = "能融网客服";
	        	setcookie("userName", $innerName);
	        }elseif($users[0]['user_type'] == 3){
	        	setcookie("userType", 3);
	        	setcookie("userName", $users[0]['company_name']);
	        }elseif($users[0]['user_type'] == 4){
	        	setcookie("userType", 4);
	        	setcookie("userName", $users[0]['company_name']);
	        }
	        setcookie("email", $email);
	        setcookie("mEmail", MD5(addToken($email)));
        }
        session_start();

        return $users[0];
	}

	/**
    **@auth qianqiang
    **@breif 注册
    **@date 
    **/
	public function registerService($email, $password, $userType){
		$user = M("User");
		$users = $user->where("email='%s' and delete_flag!=9999", array($email) )->select();
		if (sizeof($users) == 1) {
			header('Content-Type: text/html; charset=utf-8');
			echo '{"code":"-1","msg":"该邮箱已经注册"}';
			exit;
		} 

		$userAdd = M('user');
        $data['email'] = $email;
        $data['password'] = md5($password);
        if(empty($userType) ) {//注册时$userType为空
            $data['user_type'] = 3;
            $data['status'] = 2;
        }else{//管理员添加时$userType不为空
            $data['user_type'] = $userType;
            $data['status'] = 1;
        }
        $data['create_date'] = date("Y-m-d H:i:s",time());
        $data['change_date'] = date("Y-m-d H:i:s",time());
        $userAdd->add($data);

        $users = $user->where("email='%s' and delete_flag!=9999", array($email) )->select();

        if (empty($users)) {
        	header('Content-Type: text/html; charset=utf-8');
        	echo '{"code":"-1","msg":"mysql error!"}';
        	exit;
        }        
        return $users[0];
	}

	/**
    **@auth qianqiang
    **@breif 发送邮件,$flag=0激活邮件,$flag=1忘记密码发邮件
    **@date 2015.12.16
    **/
	public function sendEmail($email, $flag){
		if($flag == 0){
			$key = $email.",".md5(addToken($email)).",".time();
	        $encryptKey = base64_encode($key); 
	        $url = "http://www.enetf.com?c=User&a=activeUser&key=".urlencode($encryptKey);
	        $name = "能融网用户";
	        $subject = "验证您的电子邮箱地址";
	        $text = '欢迎使用能融网账号激活功能<br><br>请点击链接激活账号：<br><a target="_blank" href="'.$url.'">'.$url.'</a><br><br>（该链接在24小时内有效）<br>如果上面不是链接形式，请将地址复制到您的浏览器的地址栏再访问';
		}elseif($flag == 1){
			$key = $email.",".md5(addToken($email)).",".time();
	        $encryptKey = base64_encode($key); 
	        $url = "http://www.enetf.com?c=User&a=forgetPassword&key=".urlencode($encryptKey)."&r=1";
	        $name = "能融网用户";
	        $subject = "修改密码邮件";
	        $text = '欢迎使用能融网修改密码功能<br><br>请点击链接修改密码：<br><a target="_blank" href="'.$url.'">'.$url.'</a><br><br>（该链接在24小时内有效）<br>如果上面不是链接形式，请将地址复制到您的浏览器的地址栏再访问';
		}
        $res = think_send_mail($email, $name, $subject, $text, null);
        return $res;
	}

	/**
    **@auth qianqiang
    **@breif 用户激活
    **@date 2015.12.16
    **/
	public function activeService($key){
		$decryptKey = base64_decode(urldecode($key));
		$keyList = explode(",",$decryptKey);
		if(!($keyList[1] == md5(addToken($keyList[0])))){
			return "用户信息验证失败，激活失败!";
		}
		$zero1 = strtotime(date("Y-m-d H:i:s",time())); //当前时间
		$zero2 = strtotime(date("Y-m-d H:i:s",$keyList[2])); //注册时间
		$zero0 = ceil(($zero1-$zero2)/3600);
		if($zero0 > 24){ //有效期24小时
			return "邮件已超时!";
		}

		$user = M('user');
		$userInfo = $user->where("email='".$keyList[0]."' and delete_flag=0")->find();
		if(empty($userInfo)){
			return "用户信息不存在";
		}
		if($userInfo['status']==1){
			return "用户已激活";
		}
		$data['status'] = 1;
		$data['change_date'] = date("Y-m-d H:i:s",time());
		$result = $user->where("email='".$keyList[0]."' and status=2")->save($data);
		if ($result == 0) {
			return "激活失败";
		}
		$this->logoutService();
		return true;
	}

	/**
    **@auth qianqiang
    **@breif 用户注销
    **@date 2015.12.26
    **/
	public function logoutService(){
        setcookie("email", "", time()-3600*24*7);
        setcookie("mEmail", "", time()-3600*24*7);
        setcookie("userType", "", time()-3600*24*7);
        setcookie("userName", "", time()-3600*24*7);
        session_destroy();
    }

	/**
    **@auth qianqiang
    **@breif 修改密码
    **@date 
    **/
	public function changePasswordService($email, $password, $newPwd){
		$user = M('User');
		$objUser = $user->where("email='%s' and password='%s' and delete_flag!=9999", array($email, MD5($password)))->select();
		if(sizeof($objUser) == 0){
			echo '{"code":"-1","msg":"原密码错误!"}';
			exit;
		}

		$data['password'] = MD5($newPwd);
		$data['change_date'] = date("Y-m-d H:i:s",time());
		$result = $user->where("email='".$email."' and delete_flag!=9999")->save($data);

		if ($result != 1) {
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
		$objUser = $user->where("email='%s' and delete_flag!=9999", array($email))->select();
		if(sizeof($objUser) == 0){
			echo '{"code":"-1","msg":"用户不存在!"}';
			exit;
		}

		//$data['email'] = $email;
		$data['password'] = MD5($newPwd);
		$data['change_date'] = date("Y-m-d H:i:s",time());
        $res = $user->where("email='".$email."' and delete_flag!=9999")->save($data);

        if (!$res) {
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

        $objUser = $user->where("id='%d' and password='%s' and delete_flag!=9999", array($id, MD5("123456")))->select();
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
		$users = $user->where("user_type=3 and delete_flag!=9999")->select();
		return $users;
	}

	/**
    **@auth qianqiang
    **@breif 展示项目投资方所有用户
    **@date 2015.12.10
    **/
	public function getAllProjectInvestorService(){
		$user = M('User');
		$users = $user->where("user_type=4 and delete_flag!=9999")->select();
		return $users;
	}

	/**
    **@auth qianqiang
    **@breif 展示业务员所有用户
    **@date 2015.12.10
    **/
	public function getAllInnerStaffService(){
		$user = M('User');
		$users = $user->where("user_type=2 and delete_flag!=9999")->select();
		return $users;
	}

	/**
    **@auth qianqiang
    **@breif 假删除用户，级联删除，项目提供方：提供的项目、推送的项目；项目投资方：推送的项目、投资的项目(需求未要求)
    **@date 2015.12.12
    **/
	public function deleteUserService($id){
		$user = M('User');
		$objUser = $user->where("id='".$id."' and delete_flag!=9999")->find();
		if(empty($objUser)){
			echo '{"code":"-1","msg":"用户不存在!"}';
			exit;
		}

		$data["delete_flag"] = 9999;
        $data['change_date'] = date("Y-m-d H:i:s",time());
        $res = $user->where("id='".$id."'")->save($data);
        if($objUser['user_type'] == 3){
        	$projectObj = D('Project', 'Service');
        	$projectObj->deleteProjectList($objUser['id']);
        }elseif($objUser['user_type'] == 4){
        	$projectObj = D('Project', 'Service');
        	$projectObj->deletePushProject($objUser['id']);
        }

		if (!$res) {
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

        $objUser = $user->where("email='%s' and company_phone='%s' and company_telephone='%s' and delete_flag!=9999", array($email, $phone, $telephone))->select();
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

        $objUser = $user->where("email='%s' and company_name='%s' and delete_flag!=9999", array($email, $companyName))->select();
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

        $objUser = $user->where("email='%s' and code='%s' and name='%s' and delete_flag!=9999", array($email, $code, $name))->select();
		if (empty($objUser)) {
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
		$condition["delete_flag"] = array('neq',9999);
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
		$condition["delete_flag"] = array('neq',9999);
		$userInfo = $this->getUserInfo($condition);
		return $userInfo;
	}

	/**
    **@auth qianqiang
    **@breif 得到所有项目提供方的公司名称
    **@date 2016.1.10
    **/
	public function getAllCompanyName(){
		$userObj = M('User');
		$sql = "select distinct company_name from enf_user where company_name is not null and company_name != '' and user_type=3;";
		$companyName = $userObj->query($sql);
		return $companyName;
	}

	/**
    **@auth qianqiang
    **@breif 获取某一项目的所有项目投资方信息，推送状态push_flag
    **@param $projectCode 项目编码
    **@param $page 第几页，page=-1查询所有的
    **@date 2015.12.30
    **/
	public function getInvestorPush($projectCode, $page){
		$condition['user_type'] = 4;
		$condition["delete_flag"] = array('neq',9999);
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
