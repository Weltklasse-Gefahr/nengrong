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
    **@breif 客服->尽职调查入口
    **@date 2015.12.19
    **/
    public function showEvaluation(){
    	$this->display("InnerStaff:jzdc");
    }

    /**
    **@auth qianqiang
    **@breif 客服->保存尽职调查
    **@date 2015.12.19
    **/
    public function saveEvaluation(){

    }

    /**
    **@auth qianqiang
    **@breif 客服->提交尽职调查
    **@date 2015.12.19
    **/
    public function submitEvaluation(){

    }
}