<?php
namespace Home\Controller;

use Think\Controller;

class UserController extends Controller
{
    public function index()
    {
        $this->display();
    }

    //登录
    public function login(){
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $email = $_POST['email'];
            $password = $_POST['password'];
            // $email = "qianqiang@qq.com";
            // $password = "123456";

            if (empty($email) || empty($password) ) {
                echo '{"code":"-1","msg":"邮箱或者密码为空！"}';
                exit;
            }

            $user = D('User','Service');
            $users = $user->loginService($email, $password);
            
            if ($_GET['display'] == 'json') {
                dump($users);
                echo json_encode($users);
                exit;
            }
            
            if($users->user_type == 2){
                $this->display(index);
            }else if($users->user_type == 3){
                $this->display(index);
            }else if($users->user_type == 4){
                $this->display(index);
            }
        }else {
            $this->display("User:login");
        }
    }

    //注册
    public function register(){
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $email = $_POST['email'];
            $password = $_POST['password'];
            $userType = $_POST['userType'];
            // $email = "qianqiang1989@qq.com";
            // $password = "123456";
            // $userType = 3;
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
        }else {
            $this->display("User:register");
        }
    }

    // //修改密码
    // public function changePassword(){
    //     if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
    //         $email = $_POST['email'];
    //         $mEmail = $_POST['mEmail'];
    //         $pwd = $_POST['password'];
    //         $newPwd = $_POST['newPassword'];
    //         if ( empty($email) || empty($mEmail) || empty($pwd) || empty($newPwd) ) {
    //             echo '{"code":"-1","msg":"邮箱或者新旧密码为空！"}';
    //             exit;
    //         }
    //         if (!($mEmail == MD5($email."ENFENF"))) {
    //             echo '{"code":"-1","msg":"登录信息错误"}';
    //             exit;
    //         }

    //         $user = D('User','Service');
    //         $objUser = $user->changePasswordService($email, $pwd, $newPwd);
    //         if ($_GET['display'] == 'json') {
    //             dump($objUser);
    //             echo json_encode($objUser);
    //             exit;
    //         }
    //         $this->display(index);            
    //     }else{
    //         $this->display();
    //     }
    // }

    //忘记密码时重置密码
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

    //用户激活
    public function activeUser(){
        $email = $_POST['email'];
        $mEmail = $_POST['mEmail'];
        if (!($mEmail == MD5($email."ENFENF"))) {
            echo '{"code":"-1","msg":"登录信息错误"}';
            exit;
        }
        
    }

    // //显示用户详细信息
    // public function userInfo(){
    //     $email = $_POST['email'];
    //     $mEmail = $_POST['mEmail'];
    //     if (!($mEmail == MD5($email."ENFENF"))) {
    //         echo '{"code":"-1","msg":"登录信息错误"}';
    //         exit;
    //     }

    // }

    // //修改资料
    // public function changeUserInfo(){
    //     $email = $_POST['email'];
    //     $mEmail = $_POST['mEmail'];
    //     if (!($mEmail == MD5($email."ENFENF"))) {
    //         echo '{"code":"-1","msg":"登录信息错误"}';
    //         exit;
    //     }

    // }


    public function getDynamicCode(){
        code();
    }

    public function test1(){
        $User = M("User");
        $email = $User->where('id=3 and password=123')->getField('email');
        echo json_encode($email);
        $list = $User->getField('id,email');
        dump($list);
        echo json_encode(sizeof($list));
        // $news = M('news');
        // $new = $news->select(1);
        // echo json_encode($new);
        // $test_user = D('User');
        // $test = $test_user->mytest();
        // echo json_encode($test);
    }

    public function addUser(){
        $user = M('User');
        $data['email'] = 'qianqiang@qq.com';
        $data['password'] = MD5("123456");
        $data['user_type'] = 3;
        $user->add($data);
        echo '{"code":"0","msg":"添加用户"}';
    }

}
