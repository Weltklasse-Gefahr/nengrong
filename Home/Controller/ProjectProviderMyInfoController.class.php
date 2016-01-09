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
        // $email = "qiujinhan@gmail.com";
        $email = $_COOKIE['email'];
        //判断登陆，并且获取用户名的email
        isLogin($_COOKIE['email'],$_COOKIE['mEmail']);
        $display =$_GET['display'];
        //定义6张图片和文件
        $arrPhotosAndFile = array(
            "business_license",         //公司营业执照
            "organization_code",        //组织机构代码证
            "national_tax_certificate", //国税登记证
            "local_tax_certificate",    //地税登记证
            "identity_card_front",      //法人身份证正面
            "identity_card_back",       //法人身份证反面
            "financial_audit",          //财务审计报告doc
        );
        $arrFile = array(
            "financial_audit",          //财务审计报告doc
        );
    	//操作类型为1是插入和保存数据
    	$optype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
    	if ( $optype == 1)
    	{
            //12条输入框数据的更新
            $arrUser = array();
            $arrUser['company_contacts'] = $_POST['company_contacts']; //联系人
            $arrUser['company_contacts_phone'] = $_POST['company_contacts_phone']; //联系人手机
            $arrUser['company_contacts_position'] = $_POST['company_contacts_position'];//联系人职务
            $arrUser['company_address'] = $_POST['company_address'];//详细地址
            $arrUser['company_capital'] = $_POST['company_capital'];//企业注册资本
            $arrUser['company_name'] = $_POST['company_name'];//企业名称
            $arrUser['company_type'] = $_POST['company_type'];//企业类型
            $arrUser['company_fax'] = $_POST['company_fax'];//公司传真
            $arrUser['company_telephone'] = $_POST['company_telephone'];//其他手机
            $arrUser['company_person'] = $_POST['company_person'];//企业法人
            $arrUser['company_phone'] = $_POST['company_phone'];//座机
            $arrUser['company_area'] = $_POST['county'];//省市区

            $objUser = D("Doc","Service");
            $arrRes = $objUser->uploadFileAndPictrue($arrPhotosAndFile, $arrFile); 
            foreach($arrRes as $key=>$val)
            {
                $arrUser[$key] = $val;
            }

            //拼接插入数据
            $objUser = D("User","Service");
            $strWhere = "email='".$email."'";
            if ($display=="json")
            {
                echo json_encode($arrUser);
                exit;
            }
            
            $res = $objUser->updateUserInfo($strWhere, $arrUser);
            if ($res == true)
            {
                echo '{"code":"0","msg":"succ"}';
            }
            else
            {
                echo '{"code":"-1","msg":"更新失败！"}';
            }

	    }
	    else
	    {
	    	//数据的显示
	        $objUser = D("User","Service");
            $condition["email"] = $email;
	        $user = $objUser->getUserInfo($condition);
	        if ($display=="json")
	        {
	            echo json_encode($user[0]);
	            exit;
	        }
            $areaObj = D("Area","Service");
            $arr_company_area = $areaObj->getAreaArrayById($user[0]["company_area"]);
            $user[0]["province"] = $arr_company_area[0];
            $user[0]["city"] = $arr_company_area[1];
            $user[0]["county"] = $arr_company_area[2];
            //处理下文件和图片的信息
            foreach($arrPhotosAndFile as $val)
            {
                
                $condition["id"] = $user[0][$val];
                $objUser = D("Doc","Service");
                $docInfo = $objUser->getDocInfo($condition);
                $user[0][$val] = array();
                $user[0][$val]["id"] = $docInfo[0]["id"];
                $user[0][$val]["token"] = md5(addToken($docInfo[0]["id"]));
                $user[0][$val]["name"] = $docInfo[0]["file_name"];
                $user[0][$val]["url"] = $docInfo[0]["file_rename"];

            }
	    	$this->assign('data',$user[0]);
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
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $email = $_COOKIE['email'];
            $pwd = $_POST['password'];
            $newPwd = $_POST['newPassword'];
            if ( empty($pwd) || empty($newPwd) ) {
                echo '{"code":"-1","msg":"新旧密码不可为空！"}';
                exit;
            }

            $user = D('User','Service');
            $objUser = $user->changePasswordService($email, $pwd, $newPwd);
            if ($_GET['display'] == 'json') {
                dump($objUser);
                exit;
            }
            $user->logoutService();
            echo '{"code":"0","msg":"success"}';
        }else{
            $this->display("ProjectProvider:securityCenter");
        }
    }
}