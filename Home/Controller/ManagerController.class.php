<?php
namespace Home\Controller;

use Think\Controller;

class UserController extends Controller
{
	public function index()
    {
        $this->display();
    }

    /**
    **@auth qianqiang
    **@breif 管理员登录
    **@date 2015.12.08
    **/
    public function login(){
    	if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $userName = $_POST['userName'];
            $password = $_POST['password'];
            if (empty($email) || empty($password)) {
                echo '{"code":"-1","msg":"邮箱或者密码或者动态码为空！"}';
                exit;
            }

            $manager = D('Manager','Service');
            $objManager = $manager->loginService($userName, $password);
            
            if ($_GET['display'] == 'json') {
                dump($objManager);
                echo json_encode($objManager);
                exit;
            }
            
            $this->display(index);
        }else {
            $this->display();
        }
    }

    /**
    **@auth qianqiang
    **@breif 修改管理员密码
    **@date 2015.12.09
    **/
	public function changePassword(){
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $userName = $_POST['userName'];
            $mUserName = $_POST['mUserName'];
            $pwd = $_POST['password'];
            $newPwd = $_POST['newPassword'];
            if (empty($pwd) || empty($newPwd)) {
                echo '{"code":"-1","msg":"新旧密码为空！"}';
                exit;
            }
            if (empty($userName) || empty($mUserName)) {
                echo '{"code":"-1","msg":"没有登录！"}';
                exit;
            }
            if (!($mUserName == MD5($userName."ENFENF"))) {
                echo '{"code":"-1","msg":"登录信息错误"}';
                exit;
            }

            $manager = D('Manager','Service');
            $objManager = $manager->changePassword($userName, $pwd, $newPwd);
            if ($_GET['display'] == 'json') {
                dump($objManager);
                echo json_encode($objManager);
                exit;
            }
            $this->display(index);            
        }else{
            $this->display();
        }
    }

    /**
    **@auth qianqiang
    **@breif 重置密码
    **@date 2015.12.12
    **/
    public function resetPassword(){
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $email = $_POST['email'];
            $newPwd = $_POST['newPassword'];
            if ( empty($email) || empty($newPwd) ) {
                echo '{"code":"-1","msg":"邮箱或者新密码为空！"}';
                exit;
            }

            $user = D('User','Service');
            $objUser = $user->resetPasswordService($email, $newPwd);
            if ($_GET['display'] == 'json') {
                dump($objUser);
                echo json_encode($objUser);
                exit;
            }
            $this->display(index);        
        }else{
            $this->display();
        }
    }

    /**
    **@auth qianqiang
    **@breif 删除用户
    **@date 2015.12.12
    **/
    public function deleteUser(){
    	$email = $_POST['email'];
    	if ( empty($email) ) {
    		echo '{"code":"-1","msg":"邮箱为空！"}';
    		exit;
    	}

    	$user = D('User','Service');
    	$user->deleteUserService($email);
    	
    	$this->display(index); 
    }

    /**
    **@auth qianqiang
    **@breif 业务员管理->添加新业务员
    **@date 2015.12.12
    **/
    public function addInnerStaff(){
    	$email = $_POST['email'];
    	$password = $_POST['password'];
    	$userType = $_POST['userType'];
    	if (empty($email) || empty($password)) {
    		echo '{"code":"-1","msg":"邮箱或者密码为空！"}';
    		exit;
    	}

    	$user = D('User','Service');
    	$users = $user->registerService($email, $password, $userType);

    	$display = $_GET['display'];
    	if ($display == 'json') {
    		dump($users);
    		echo json_encode($users);
    		exit;
    	}
    	$this->display(index);
    }

    /**
    **@auth qianqiang
    **@breif 投资方管理->添加项目投资方
    **@date 2015.12.12
    **/
    public function addProjectInvestor(){
    	$email = $_POST['email'];
    	$password = $_POST['password'];
    	$userType = $_POST['userType'];
    	if (empty($email) || empty($password)) {
    		echo '{"code":"-1","msg":"邮箱或者密码为空！"}';
    		exit;
    	}

    	$user = D('User','Service');
    	$users = $user->registerService($email, $password, $userType);

    	$display = $_GET['display'];
    	if ($display == 'json') {
    		dump($users);
    		echo json_encode($users);
    		exit;
    	}
    	$this->display(index);
    }

    /**
    **@auth qianqiang
    **@breif 项目提供方管理->编辑
    **@date 2015.12.12
    **/
    public function changeProjectProviderInfo(){

    }

    /**
    **@auth qianqiang
    **@breif 项目投资方管理->编辑
    **@date 2015.12.12
    **/
    public function changeProjectInvestorInfo(){

    }
    
    /**
    **@auth qianqiang
    **@breif 业务员管理->编辑
    **@date 2015.12.12
    **/
    public function changeInnerStaffInfo(){

    }

    /**
    **@auth qianqiang
    **@breif 展示项目提供方所有用户
    **@date 2015.12.10
    **/
    public function getAllProjectProviderInfo(){
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $user = D('User','Service');
            $users = $user->getAllProjectProviderService();
            $this->assign('listInfo',$users);
            $this->display();
        }else{
            $this->display();
        }
    }

    /**
    **@auth qianqiang
    **@breif 展示项目投资方所有用户
    **@date 2015.12.10
    **/
    public function getAllProjectInvestorInfo(){
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $user = D('User','Service');
            $users = $user->getAllProjectInvestorService();
            $this->assign('listInfo',$users);
            $this->display();
        }else{
            $this->display();
        }
    }

    /**
    **@auth qianqiang
    **@breif 展示客服人员所有用户
    **@date 2015.12.10
    **/
    public function getAllInnerStaffInfo(){
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $user = D('User','Service');
            $users = $user->getAllInnerStaffService();
            $this->assign('listInfo',$users);
            $this->display();
        }else{
            $this->display();
        }
    }
}