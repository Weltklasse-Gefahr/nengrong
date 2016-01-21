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
    **@breif 登录
    **@date 
    **/ 
    public function login(){
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $email = $_POST['email'];
            $password = $_POST['password'];
            $keepFlag = $_POST['keepFlag'];
            if (empty($email) || empty($password) ) {
                echo '{"code":"-1","msg":"邮箱或者密码为空！"}';
                exit;
            }

            $user = D('User','Service');
            $users = $user->loginService($email, $password, $keepFlag);
            
            if ($_GET['display'] == 'json') {
                dump($users);
                echo json_encode($users);
                exit;
            }

            if($users["user_type"] == 2){
                echo '{"code":"0","msg":"登录成功！","url":"?c=InnerStaff&a=search"}';
            }else if($users["user_type"] == 3){
                echo '{"code":"0","msg":"登录成功！","url":"?c=ProjectProviderMyPro&a=awaitingAssessment&r=1"}';
            }else if($users["user_type"] == 4){
                echo '{"code":"0","msg":"登录成功！","url":"?c=ProjectInvestorMyPro&a=recommendedProject&r=1"}';
            }
        }else {
            $email = $_COOKIE['email'];
            if(!empty($email)){
                $res = isLogin($email, $_COOKIE['mEmail'], 1);
                if($res == true){
                    echo '{"code":"-1","msg":"不可能出现的错误"}';
                    exit;
                }
                $userObj = D('User','Service');
                $users = $userObj->getUserINfoByEmail($email);
                if($users[0]["user_type"] == 2){
                    echo "<script type='text/javascript'>location.href='?c=InnerStaff&a=search'</script>";
                }else if($users[0]["user_type"] == 3){
                    echo "<script type='text/javascript'>location.href='?c=ProjectProviderMyPro&a=awaitingAssessment&r=1'</script>";
                }else if($users[0]["user_type"] == 4){
                    echo "<script type='text/javascript'>location.href='?c=ProjectInvestorMyPro&a=recommendedProject&r=1'</script>";
                }
            }else{
                $this->display("User:login");
            }
        }
    }

    /**
    **@auth qianqiang
    **@breif 注册
    **@date 
    **/ 
    public function register(){
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
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
                exit;
            }
            echo '{"code":"0","msg":"注册成功！","url":"?c=User&a=loginsus"}';

            $res = $user->sendEmail($email);
            if($res == false){
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"send email error!"}';
                exit;
            }
        }else {
            $this->display("User:register");
        }
    }

    /**
    **@auth qianqiang
    **@breif 用户注销
    **@date 2015.1.9
    **/
    public function logout(){
        isLogin($_COOKIE['email'],$_COOKIE['mEmail']);
        $user = D('User','Service');
        $objUser = $user->logoutService();
        echo '{"code":"0","msg":"注销成功！"}';
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

    /**
    **@auth qianqiang
    **@breif 用户激活
    **@date 2016.1.7
    **/ 
    public function activeUser(){
        $key = $_GET['key'];
        $user = D('User','Service');
        $user->activeService($key);
        // echo '{"code":"0","msg":"用户激活成功"}';
        $this->display("User:login");
    }

    public function test1(){
        // $user = D("User", "Service");
        // $userInfo = $user->getAllCompanyName();
        // dump($userInfo);

        // $investors = '123,456,789,';
        // $newstr = substr($investors,0,strlen($investors)-1); 
        // $investorList = explode(",",$newstr);
        // dump($investorList);exit;

        // $email = "82563912@qq.com";
        // $key = $email.",".md5(addToken($email)).",".time();
        // $encryptKey = encrypt($key, getKey()); 
        // $url = "www.enetf.com/?c=User&a=activeUser&key=".$encryptKey;
        // $name = "能融网用户";
        // $subject = "验证您的电子邮箱地址";
        // $text = "激活邮件内容".$url;
        // $r = think_send_mail($email, $name, $subject, $text, null);
        // dump($r);dump($key);dump($url);
        // exit;

        $area = D("Area", "Service");
        $res = $area->getAreaArrayByHighLevelId("130000");
        header('Content-Type: text/html; charset=utf-8');
        dump($res);
        
        // $email = "qianqiang@qq.com";
        // isDataComplete($email);
        // echo "chenggong!";
        // exit;
        // $User = M("User");
        // $email = $User->where('id=3 and password=123')->getField('email');
        // echo json_encode($email);
        // $list = $User->getField('id,email');
        // dump($list);
        // echo json_encode(sizeof($list));
        // $news = M('news');
        // $new = $news->select(1);
        // echo json_encode($new);
    }

    public function addUser(){
        $user = M('User');
        $data['email'] = 'qianqiang1234567@qq.com';
        $data['password'] = MD5("123456");
        $data['user_type'] = 4;
        $data['status'] = 1;
        $user->add($data);
        echo '{"code":"0","msg":"添加用户"}';
    }

}
