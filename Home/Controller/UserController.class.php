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

            $res = $user->sendEmail($email, 0);
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

    /**
    **@auth qianqiang
    **@breif 忘记密码
    **@date 2015.1.22
    **/
    public function forgetPassword(){
        if($_GET['r'] == 1){//点击邮件，验证邮件信息和展示设置新密码页
            $key = $_GET['key'];
            $decryptKey = base64_decode(urldecode($key));
            $keyList = explode(",",$decryptKey);
            if(!($keyList[1] == md5(addToken($keyList[0])))){
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"用户信息验证失败，不能重设密码!"}';
                exit;
            }
            $zero1 = strtotime(date("Y-m-d H:i:s",time())); //当前时间
            $zero2 = strtotime(date("Y-m-d H:i:s",$keyList[2])); //注册时间
            $zero0 = ceil(($zero1-$zero2)/3600);
            if($zero0 > 24){ //有效期24小时
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"邮件已超时!"}';
                exit;
            }
            header("Location: ?c=User&a=forgetpwdmodify&key=".$key);
        }elseif($_POST['rtype'] == 1 || $_GET['rtype'] == 1){//输入邮箱-下一步
            $email = $_POST['email'];
            if ( empty($email) ) {
                echo '{"code":"-1","msg":"邮箱或者新密码为空！"}';
                exit;
            }
            $user = D('User','Service');
            $res = $user->sendEmail($email, 1);
            if($res == false){
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"send email error!"}';
                exit;
            }
            echo '{"code":"0","msg":"邮件发送成功！"}';
        }else{
            $this->display("User:forgetpassword");
        }
    }

    /**
    **@auth qianqiang
    **@breif 忘记密码-重置新密码
    **@date 2015.1.22
    **/
    public function forgetpwdmodify(){
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $key = $_GET['key'];
            $password = $_POST['password'];
            $decryptKey = base64_decode(urldecode($key));
            $keyList = explode(",",$decryptKey);
            if(!($keyList[1] == md5(addToken($keyList[0])))){
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"用户信息验证失败，不能重设密码!"}';
                exit;
            }
            $zero1 = strtotime(date("Y-m-d H:i:s",time())); //当前时间
            $zero2 = strtotime(date("Y-m-d H:i:s",$keyList[2])); //注册时间
            $zero0 = ceil(($zero1-$zero2)/3600);
            if($zero0 > 24){ //有效期24小时
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"邮件已超时!"}';
                exit;
            }
            $user = D('User','Service');
            $userInfo = $user->resetPasswordService($keyList[0], $password);
            if(!empty($userInfo)){
                echo '{"code":"0","msg":"重设密码成功"}';
            }else{
                echo '{"code":"-1","msg":"重设密码失败"}';
            }
        }else{
            $this->display("User:forgetpassmodify");
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
        $res = $user->activeService($key);
        if($res === true){
            header("Location: ?c=User&a=login");
            $this->display("User:login");
        }else{
            $data['errmsg'] = $res;
            $this->assign('data', $data);
            $this->display("User:validate_fail");
        }
    }

    // public function test1(){
    //     $com = D('Common', 'Service');
    //     $res = $com->getFileSize(1000230);
    //     echo $res;exit;

    //     // $user = D("User", "Service");
    //     // $userInfo = $user->getAllCompanyName();
    //     // dump($userInfo);

    //     // $investors = '123,456,789,';
    //     // $newstr = substr($investors,0,strlen($investors)-1); 
    //     // $investorList = explode(",",$newstr);
    //     // dump($investorList);exit;

    //     // $email = "82563912@qq.com";
    //     // $key = $email.",".md5(addToken($email)).",".time();
    //     // $encryptKey = encrypt($key, getKey()); 
    //     // $url = "www.enetf.com/?c=User&a=activeUser&key=".$encryptKey;
    //     // $name = "能融网用户";
    //     // $subject = "验证您的电子邮箱地址";
    //     // $text = "激活邮件内容".$url;
    //     // $r = think_send_mail($email, $name, $subject, $text, null);
    //     // dump($r);dump($key);dump($url);
    //     // exit;

    //     // $area = D("Area", "Service");
    //     // $res = $area->getAreaArrayByHighLevelId("130000");
    //     // header('Content-Type: text/html; charset=utf-8');
    //     // dump($res);
        
    //     // $email = "qianqiang@qq.com";
    //     // isDataComplete($email);
    //     // echo "chenggong!";
    //     // exit;
    //     // $User = M("User");
    //     // $email = $User->where('id=3 and password=123')->getField('email');
    //     // echo json_encode($email);
    //     // $list = $User->getField('id,email');
    //     // dump($list);
    //     // echo json_encode(sizeof($list));
    //     // $news = M('news');
    //     // $new = $news->select(1);
    //     // echo json_encode($new);
    // }

    // public function adduser123(){
    //     $userAdd = M('user');
    //     $data['email'] = "lufang@eifesun.com";
    //     $data['password'] = md5("123456");
    //     $data['user_type'] = 3;
    //     $data['status'] = 1;
    //     $data['create_date'] = date("Y-m-d H:i:s",time());
    //     $data['change_date'] = date("Y-m-d H:i:s",time());
    //     $userAdd->add($data);
    // }

}
