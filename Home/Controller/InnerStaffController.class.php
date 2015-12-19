<?php
namespace Home\Controller;

use Think\Controller;

class InnerStaffController extends Controller {
    
    /**
    **@auth qianqiang
    **@breif 客服->项目提供方信息（账户向详细信息）入口
    **@date 2015.12.19
    **/
    public function getProjectProviderInfo(){
    	$this->display("InnerStaff:providerInfo");
    }

    /**
    **@auth qianqiang
    **@breif 客服->尽职调查
    **@date 2015.12.19
    **/
    public function doEvaluation(){
    	isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
    	$email = $_COOKIE['email'];
    	$optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
    	if($optype == "save" && $rtype == 1){

    	}elseif($optype == "commit" && $rtype == 1){

    	}elseif($rtype != 1){
    		
    		$this->display("InnerStaff:jzdc");
    	}
    }


}