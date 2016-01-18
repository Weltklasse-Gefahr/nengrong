<?php
namespace Home\Controller;

use Think\Controller;

class AdminController extends Controller
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
            if (empty($userName) || empty($password)) {
                echo '{"code":"-1","msg":"邮箱或者密码为空！"}';
                exit;
            }

            $manager = D('Admin','Service');
            $objManager = $manager->loginService($userName, $password);

            if ($_GET['display'] == 'json') {
                dump($objManager);
                //echo json_encode($users);
                exit;
            }
            
            echo '{"code":"0","msg":"登录成功！"}';
        }else {
            $this->display("Admin:admin_login");
        }
    }

    /**
    **@auth qianqiang
    **@breif 管理员注销
    **@date 2015.12.19
    **/
    public function logout(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);
        $manager = D('Admin','Service');
        $objManager = $manager->logoutService();
        $this->display("Admin:admin_login");
    }

    /**
    **@auth qianqiang
    **@breif 修改管理员密码
    **@date 2015.12.09
    **/
	public function changePassword(){
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);

            $userName = $_COOKIE['userName'];
            $pwd = $_POST['password'];
            $newPwd = $_POST['newPwd'];
            $confirmNewPwd = $_POST['confirmNewPwd'];
            // $userName = "admin1";
            // $pwd = "admin1";
            // $newPwd = "admin1";
            // $confirmNewPwd = "admin1";
            if (empty($pwd) || empty($newPwd) || empty($confirmNewPwd)) {
                echo '{"code":"-1","msg":"新旧密码为空"}';
                exit;
            }
            if($newPwd != $confirmNewPwd){
                echo '{"code":"-1","msg":"twice new password different"}';
                exit;
            }

            $manager = D('Admin','Service');
            $objManager = $manager->changePasswordService($userName, $pwd, $newPwd);
            if ($_GET['display'] == 'json') {
                dump($objManager);
                exit;
            }
            echo '{"code":"0","msg":"change password success"}';        
        }else{
            $this->display("Admin:admin_change_pwd");
        }
    }

    /**
    **@auth qianqiang
    **@breif 重置密码为原始密码
    **@date 2015.12.12
    **/
    public function resetPassword(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);

        $id = $_POST['id'];
            // $id = 7;
        if ( empty($id) ) {
            echo '{"code":"-1","msg":"id为空！"}';
            exit;
        }

        $user = D('User','Service');
        $objUser = $user->setOriginalPasswordService($id);
        if ($_GET['display'] == 'json') {
            dump($objUser);
            echo json_encode($objUser);
            exit;
        }
        echo '{"code":"0","msg":"修改成功"}';
    }

    /**
    **@auth qianqiang
    **@breif 删除用户
    **@date 2015.12.12
    **/
    public function deleteUser(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);

    	$id = $_POST['id'];
    	if ( empty($id) ) {
    		echo '{"code":"-1","msg":"邮箱为空！"}';
    		exit;
    	}

    	$user = D('User','Service');
    	$user->deleteUserService($id);
    	
    	echo '{"code":"0","msg":"delete success"}';
    }

    /**
    **@auth qianqiang
    **@breif 业务员管理->添加新业务员
    **@date 2015.12.12
    **/
    public function addInnerStaff(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);

    	$email = $_POST['email'];
        $code = $_POST['code'];
        $name = $_POST['name'];
        // $email = "123abc";
        // $code = "123";
        // $name = "123";
    	$password = "123456";
    	$userType = 2;
    	if (empty($email)) {
    		echo '{"code":"-1","msg":"邮箱为空！"}';
    		exit;
    	}

    	$user = D('User','Service');
    	$users = $user->registerService($email, $password, $userType);
        $objUser = $user->changeInnerStaffByManager($users['id'], $email, $code, $name);

    	$display = $_GET['display'];
    	if ($display == 'json') {
    		dump($objUser);
    		exit;
    	}
    	echo '{"code":"0","msg":"add user success"}';
    }

    /**
    **@auth qianqiang
    **@breif 投资方管理->添加项目投资方
    **@date 2015.12.12
    **/
    public function addProjectInvestor(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);

    	$email = $_POST['email'];
        $companyName = $_POST['companyName'];
    	$password = "123456";
    	$userType = 4;
    	if (empty($email)) {
    		echo '{"code":"-1","msg":"邮箱为空！"}';
    		exit;
    	}

    	$user = D('User','Service');
    	$users = $user->registerService($email, $password, $userType);
        $objUser = $user->changeProjectInvestorByManager($users['id'], $email, $companyName);

    	$display = $_GET['display'];
    	if ($display == 'json') {
    		dump($objUser);
    		exit;
    	}
    	echo '{"code":"0","msg":"add user success"}';
    }

    /**
    **@auth qianqiang
    **@breif 项目提供方管理->编辑
    **@date 2015.12.12
    **/
    public function changeProjectProviderInfo(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);
        $id = $_POST['id'];
    	$email = $_POST['email'];
        $phone = $_POST['phone'];
    	$telephone = $_POST['telephone'];
        if (empty($email) || empty($phone) || empty($telephone)) {
        	echo '{"code":"-1","msg":"邮箱或者电话为空！"}';
            exit;
        }

        $user = D('User','Service');
    	$users = $user->changeProjectProviderByManager($id, $email, $phone, $telephone);

    	$display = $_GET['display'];
    	if ($display == 'json') {
    		dump($users);
    		exit;
    	}
    	echo '{"code":"0","msg":"修改成功！"}';
    }

    /**
    **@auth qianqiang
    **@breif 项目投资方管理->编辑
    **@date 2015.12.12
    **/
    public function changeProjectInvestorInfo(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);
        $id = $_POST['id'];
    	$email = $_POST['email'];
    	$companyName = $_POST['companyName'];
        if (empty($email) || empty($companyName)) {
        	echo '{"code":"-1","msg":"邮箱或者公司名称为空！"}';
        }

		$user = D('User','Service');
    	$users = $user->changeProjectInvestorByManager($id, $email, $companyName);

    	$display = $_GET['display'];
    	if ($display == 'json') {
    		dump($users);
    		echo json_encode($users);
    		exit;
    	}
    	echo '{"code":"0","msg":"修改成功！"}';
    }

    /**
    **@auth qianqiang
    **@breif 业务员管理->编辑
    **@date 2015.12.12
    **/
    public function changeInnerStaffInfo(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);
        $id = $_POST['id'];
    	$email = $_POST['email'];
    	$code = $_POST['code'];
    	$name = $_POST['name'];
        // $id = "6";
        // $email = "qianqiang@qq.com";
        // $code = "qwe";
        // $name = "qwe";
    	if (empty($email) || empty($code) || empty($name)) {
    		echo '{"code":"-1","msg":"邮箱、员工编号、名称不能为空！"}';
    	}

    	$user = D('User','Service');
    	$users = $user->changeInnerStaffByManager($id, $email, $code, $name);

    	$display = $_GET['display'];
    	if ($display == 'json') {
    		dump($users);
    		echo json_encode($users);
    		exit;
    	}
    	echo '{"code":"0","msg":"修改成功！"}';
    }

    /**
    **@auth qianqiang
    **@breif 展示项目提供方所有用户
    **@date 2015.12.10
    **/
    public function getAllProjectProviderInfo(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);

        $user = D('User','Service');
        $users = $user->getAllProjectProviderService();
        $this->assign('listInfo',$users);

        $display = $_GET['display'];
        if ($display == 'json') {
            dump($users);
            exit;
        }    
        $this->display("admin:admin_provider");
    }

    /**
    **@auth qianqiang
    **@breif 展示项目投资方所有用户
    **@date 2015.12.10
    **/
    public function getAllProjectInvestorInfo(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);

        $user = D('User','Service');
        $users = $user->getAllProjectInvestorService();
        $this->assign('listInfo',$users);

        $display = $_GET['display'];
        if ($display == 'json') {
            dump($users);
            exit;
        }    
        $this->display("Admin:admin_investors");
    }

    /**
    **@auth qianqiang
    **@breif 展示客服人员所有用户
    **@date 2015.12.10
    **/
    public function getAllInnerStaffInfo(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);

        $user = D('User','Service');
        $users = $user->getAllInnerStaffService();
        $this->assign('listInfo',$users);

        $display = $_GET['display'];
        if ($display == 'json') {
            dump($users);
            exit;
        }
            
        $this->display("Admin:admin_inner_staff");
    }

    /**
    **@auth qianqiang
    **@breif 展示要编辑的用户信息
    **@date 2015.12.13
    **/
    public function getEditUserInfo(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);
        $id = $_GET["id"];
        // $id = 2;
        $user = D('User','Service');
        $users = $user->getUserINfoById($id);
        $this->assign('userInfo',$users);

        $display = $_GET['display'];
        if ($display == 'json') {
            dump($users);
            exit;
        }
        
        if(intval($users[0]["user_type"]) == 2){
            $this->display("Admin:admin_inner_staff_edit");
        }else if(intval($users[0]["user_type"]) == 3){
            $this->display("Admin:admin_provider_edit");
        }else if(intval($users[0]["user_type"]) == 4){
            $this->display("Admin:admin_investors_edit");
        }else{
            echo '{"code":"-1","msg":"user type not exist"}';
        }
    }

    /**
    **@auth qianqiang
    **@breif 展示所有项目信息
    **@date 2016.1.15
    **/
    public function getAllProjectInfo(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);

        $project = D('Project','Service');
        $projects = $project->getAllProject();
        $projectList = $project->formatProject($projects);
        $this->assign('listInfo',$projectList);

        $display = $_GET['display'];
        if ($display == 'json') {
            header('Content-Type: text/html; charset=utf-8');
            dump($projectList);
            exit;
        }    
        $this->display("Admin:admin_project");
    }

    /**
    **@auth qianqiang
    **@breif 真删除项目
    **@date 2016.1.15
    **/
    public function deleteProject(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);

        $id = $_POST['id'];
        if ( empty($id) ) {
            echo '{"code":"-1","msg":"项目id为空！"}';
            exit;
        }

        $project = D('Project','Service');
        $project->dropProjectService($id);
        
        echo '{"code":"0","msg":"delete success"}';
    }

    /**
    **@auth qianqiang
    **@breif 恢复项目
    **@date 2016.1.15
    **/
    public function recoveryProject(){
        isAdminLogin($_COOKIE['userName'],$_COOKIE['mUserName']);

        $id = $_POST['id'];
        if ( empty($id) ) {
            echo '{"code":"-1","msg":"项目id为空！"}';
            exit;
        }

        $project = D('Project','Service');
        $project->recoveryProjectService($id);
        
        echo '{"code":"0","msg":"recovery project success"}';
    }


    public function addAdmin(){
        $manager = M('Admin');
        $data['user_name'] = 'admin';
        $data['password'] = MD5("admin");
        $manager->add($data);
        echo '{"code":"0","msg":"添加管理员"}';
    }

}