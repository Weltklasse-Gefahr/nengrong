<?php
namespace Home\Controller;

use Think\Controller;

class ProjectProviderMyInfoController extends Controller {
    
    /**
    **@auth qiujinhan@gmail.com
    **@breif 项目提供方->个人中心->我的资料
    **@date 2015.12.05
    **/
	public function myInformation()
    {
    	//操作类型为1是插入和保存数据
    	$optype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
    	if ( $optype == 1)
    	{
            //12条输入框数据的更新
            $arrUser = array();
            $arrUser['company_contacts'] = $_POST['company_contacts']; //联系人
            $arrUser['company_contacts_phone'] = $_POST['company_contacts_phone']; //联系人手机
            $arrUser['company_contacts_position'] = $_POST['company_contacts_position'];//联系人职务
            $arrUser['company_area'] = $_POST['company_area'];//所在地区
            $arrUser['company_address'] = $_POST['company_address'];//详细地址
            $arrUser['company_capital'] = $_POST['company_capital'];//企业注册资本
            $arrUser['company_name'] = $_POST['company_name'];//企业名称
            $arrUser['company_type'] = $_POST['company_type'];//企业类型
            $arrUser['company_fax'] = $_POST['company_fax'];//公司传真
            $arrUser['company_telephone'] = $_POST['company_telephone'];//其他手机
            $arrUser['company_person'] = $_POST['company_person'];//企业法人
            $arrUser['company_phone'] = $_POST['company_phone'];//座机


            //上传6图片资料上传

            //上传1个doc等文件，调用一个函数，返回一个url

            //拼接插入数据
            $objUser = D("User","Service");
            $strWhere = "id=1";
            $res = $objUser->updateUserInfo($strWhere, $arrUser);
            if ($res == true)
            {
                echo '{"code":"0","msg":"succ"}';
            }
            else
            {
                echo '{"code":"-1","msg":"更新失败"}';
            }

	    }
	    else
	    {
	    	//数据的显示
	    	//从cookie里面读取到用户
	    	$id = $_POST['id'] ? $_POST['id']:$_GET['id'];
	    	$display =$_GET['display'];
	        $objUser = D("User","Service");
            $condition["id"] = $id;
	        $user = $objUser->getUserInfo($condition);
	        if ($display=="json")
	        {
	            echo json_encode($user);
	            exit;
	        }
	    	
	    	$this->assign('user',$user[0]);
	        $this->display("ProjectProvider:myInformation");
	    }
    }


    /**
    **@auth qianqiang
    **@breif 项目提供方->个人中心->安全中心
    **@date 2015.12.05
    **/

    public function securityCenter()
    {
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $email = $_POST['email'];
            $mEmail = $_POST['mEmail'];
            $pwd = $_POST['password'];
            $newPwd = $_POST['newPassword'];
            if ( empty($email) || empty($mEmail) || empty($pwd) || empty($newPwd) ) {
                echo '{"code":"-1","msg":"邮箱或者新旧密码为空！"}';
                exit;
            }
            if (!($mEmail == MD5($email."ENFENF"))) {
                echo '{"code":"-1","msg":"登录信息错误"}';
                exit;
            }

            $user = D('ProjectProviderSafety','Service');
            $objUser = $user->changePasswordService($userName, $pwd, $newPwd);
            if ($_GET['display'] == 'json') {
                dump($objUser);
                echo json_encode($objUser);
                exit;
            }
            $this->display(index);            
        }else{
            $this->display("ProjectProvider:securityCenter");
        }
    }
}