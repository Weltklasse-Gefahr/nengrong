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
            //数据的插入和保存更新
            //接收前端表单过来的参数
            //上传图片是调用一个函数，返回一个url
            //上传doc等文件，调用一个函数，返回一个url
            //拼接url插入数据

	    }
	    else
	    {
	    	//数据的显示
	    	//从cookie里面读取到用户
	    	$email = $_POST['email'] ? $_POST['email']:$_GET['email'];
	    	$display =$_GET['display'];
	        $objUser = D("User","Service");
	        $user = $objUser->getUserInfo($email);
	        if ($display=="json")
	        {
	            echo json_encode($user);
	            exit;
	        }
	    	
	    	$this->assign('user',$user);
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
        $this->display();
    }
}