<?php
namespace Home\Controller;

use Think\Controller;

class ProjectProviderMyProController extends Controller {
    
    /**
    **@auth qiujinhan@gmail.com
    **@breif 项目提供方->项目信息未填写入口
    **@date 2015.12.05
    **/
    public function projectInfoNew()
    {
        //判断登陆，并且获取用户名的email
        isLogin($_COOKIE['email'],$_COOKIE['mEmail']);
    	$this->display("ProjectProvider:projectInfoNew");
    }



    /**
    **@auth qiujinhan@gmail.com
    **@breif 项目提供方->项目信息编辑入口
    **@date 2015.12.05
    **/
	public function projectInfoEdit()
    {
        //判断登陆，并且获取用户名的email
        isLogin($_COOKIE['email'],$_COOKIE['mEmail']);

    	//操作类型为1是插入和保存数据
    	$optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
        //保存or提交
    	if ( ($optype == "save" || $optype == "commit") && $rtype == 1)
    	{
            
            //接收前端表单过来的参数，并且处理下
            $arrProInfo = array(); //ENF_Project信息
            $arrInfo = array();     //项目表信息
            //$arrInfor['xxx'] = $_POST['xxxx']; //xxx
            $arrInfo['project_type'] = $_POST['project_type']; //项目类型，1屋顶分布式，2地面分布式，3大型地面
            $arrInfo['build_state'] = $_POST['build_state']; //1未建 or 2已建

            $objUser = D("Doc","Service");
            //屋顶分布式a
            if($arrInfo['project_type'] == 1)
            {
                //上传图片和文件
                $arrPhotosAndFile = array(
                    "picture_full",         //屋顶全景图
                    "picture_south",        //屋顶正南向照片
                    "housetop_property_prove",         //屋顶产权证明
                    "electricity_pay_list",           //电费单
                    "house_rent_agreement",    //屋顶租赁协议
                    "power_manage_agreement", //合同能源管理协议
                    "project_proposal",//项目可研报告/项目建议书
                    "housetop_load_prove",//屋顶载荷证明
                );
                $arrFile = array(
                    "housetop_property_prove",         //屋顶产权证明
                    "electricity_pay_list",           //电费单
                    "house_rent_agreement",    //屋顶租赁协议
                    "power_manage_agreement", //合同能源管理协议
                    "project_proposal",//项目可研报告/项目建议书
                    "housetop_load_prove",//屋顶载荷证明
                );
                $arrRes = $objUser->uploadFileAndPictrue($arrPhotosAndFile, $arrFile); 
                foreach($arrRes as $key=>$val)
                {
                    $arrProInfo[$key] = $val;
                }
                //接收其他图片
                //$arrInfor['picture_mul'] = $_POST['picture_mul']; //其他图片

                $arrInfor['housetop_owner'] = $_POST['housetop_owner']; //屋顶业主名称
                $arrInfor['company_type'] = $_POST['company_type']; //企业类型
                $arrInfor['company_capital'] = $_POST['company_capital']; //注册资本金
                $arrInfor['electricity_total'] = $_POST['electricity_total']; //年用电量
                $arrInfor['electricity_pay'] = $_POST['electricity_pay']; //电费
                $arrInfor['housetop_type'] = $_POST['housetop_type']; //屋顶类型
                $arrInfor['housetop_type_other'] = $_POST['housetop_type_other']; //屋顶类型其他可填写
                $arrInfor['housetop_area'] = $_POST['housetop_area']; //屋顶面积
                $arrInfor['housetop_age'] = $_POST['housetop_age']; //屋顶使用寿命
                $arrInfor['housetop_direction'] = $_POST['housetop_direction']; //屋顶朝向
                $arrInfor['housetop_direction_other'] = $_POST['housetop_direction_other']; //屋顶朝向其他可填写
                $arrInfor['synchronize_type'] = $_POST['synchronize_type']; //并网方式
                
                
                
                $arrInfor['rent_time'] = $_POST['rent_time']; //租赁年限
                $arrInfor['rent_pay'] = $_POST['rent_pay']; //租赁租金
                $arrInfor['electricity_clear_type'] = $_POST['electricity_clear_type']; //电价结算方式
                $arrInfor['electricity_clear'] = $_POST['electricity_clear']; //结算电价
                $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                
                //未建
                if ($arrInfo['build_state'] == 1)
                {
                    $arrInfor['plan_build_volume'] = $_POST['plan_build_volume']; //拟建设容量
                    $arrInfor['cooperation_type'] = $_POST['cooperation_type']; //与能融网合作方式（可多选）
                    $arrInfor['financing_type'] = $_POST['financing_type']; //融资方式
                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                }
                //已建
                if ($arrInfo['build_state'] == 2)
                {
                    $arrInfor['housetop_waterproof_time'] = $_POST['housetop_waterproof_time']; //屋顶防水周期
                    $arrInfor['housetop_load'] = $_POST['housetop_load']; //屋顶活载荷
                    $arrInfor['has_shelter'] = $_POST['has_shelter']; //附近有无遮挡
                    $arrInfor['has_pollution'] = $_POST['has_pollution']; //有无污染源
                    $arrInfor['electricity_distance'] = $_POST['electricity_distance']; //电网接入点距离
                    $arrInfor['plan_build_volume'] = $_POST['plan_build_volume']; //拟建设容量
                    $arrInfor['company_invest'] = $_POST['company_invest']; //单位投资
                    $arrInfor['company_EPC'] = $_POST['company_EPC']; //EPC厂家
                    $arrInfor['capacity_level'] = $_POST['capacity_level']; //资质等级
                    $arrInfor['synchronize_date'] = $_POST['synchronize_date']; //并网时间
                    $arrInfor['cooperation_type'] = $_POST['cooperation_type']; //与能融网合作方式（可多选）
                    $arrInfor['financing_type'] = $_POST['financing_type']; //融资方式

                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx

                }


            }






            //地面分布式
            if($arrInfo['project_type'] == 2)
            {
                //上传图片和文件
                $arrPhotosAndFile = array(
                    "picture_full",         //场地情况全景图
                    "picture_field",//场平照片
                    "picture_transformer",//变电站照片
 
                );
                $arrFile = array(
                    "xxx",         //xxxx

                $arrRes = $objUser->uploadFileAndPictrue($arrPhotosAndFile, $arrFile); 
                foreach($arrRes as $key=>$val)
                {
                    $arrProInfo[$key] = $val;
                }
                //接收其他图片,这个先不做
                //$arrInfor['picture_mul'] = $_POST['picture_mul']; //其他图片
                //公用的
                $arrInfor['ground_property'] = $_POST['ground_property']; //土地性质
                $arrInfor['ground_property_other'] = $_POST['ground_property_other']; //土地性质其他可填入
                $arrInfor['ground_area'] = $_POST['ground_area']; //租赁土地面积
                $arrInfor['rent_time'] = $_POST['rent_time']; //租赁年限
                $arrInfor['rent_pay'] = $_POST['rent_pay']; //租赁租金
                $arrInfor['ground_condition'] = $_POST['ground_condition']; //土地平整情况
                $arrInfor['electricity_distance'] = $_POST['electricity_distance']; //电网接入点距离
                $arrInfor['measure_point'] = $_POST['measure_point']; //计量点
                $arrInfor['project_holder_type'] = $_POST['project_holder_type']; //项目支架类型
                $arrInfor['ground_project_type'] = $_POST['ground_project_type']; //项目类型
                $arrInfor['financing_type'] = $_POST['financing_type']; //融资方式
                $arrInfor['xxx'] = $_POST['xxxx']; //electricity_distance
                $arrInfor['xxx'] = $_POST['xxxx']; //electricity_distance
                $arrInfor['xxx'] = $_POST['xxxx']; //electricity_distance
                $arrInfor['xxx'] = $_POST['xxxx']; //electricity_distance
                $arrInfor['xxx'] = $_POST['xxxx']; //electricity_distance
                $arrInfor['xxx'] = $_POST['xxxx']; //electricity_distance
                $arrInfor['xxx'] = $_POST['xxxx']; //electricity_distance

                
                //未建
                if ($arrInfo['build_state'] == 1)
                {
                    $arrInfor['plan_build_volume'] = $_POST['plan_build_volume']; //拟建设容量
                    $arrInfor['cooperation_type'] = $_POST['cooperation_type']; //与能融网合作方式（可多选）
                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                }
                //已建
                if ($arrInfo['build_state'] == 2)
                {

                    $arrInfor['control_room_area'] = $_POST['control_room_area']; //中控室建筑面积
                    $arrInfor['sell_sum'] = $_POST['sell_sum']; //出让金额
                    $arrInfor['has_shelter'] = $_POST['has_shelter']; //附近有无遮挡
                    $arrInfor['has_pollute'] = $_POST['has_pollute']; //有无污染源
                    $arrInfor['plan_build_volume'] = $_POST['plan_build_volume']; //拟建设容量
                    $arrInfor['company_invest'] = $_POST['company_invest']; //单位投资
                    $arrInfor['company_EPC'] = $_POST['company_EPC']; //EPC厂家
                    $arrInfor['capacity_level'] = $_POST['capacity_level']; //资质等级
                    $arrInfor['synchronize_date'] = $_POST['synchronize_date']; //并网时间
                    $arrInfor['cooperation_type'] = $_POST['cooperation_type']; //与能融网合作方式（可多选）
                    $arrInfor['financing_type'] = $_POST['financing_type']; //融资方式
                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                }

            }




            //大型地面
            if($arrInfo['project_type'] == 3)
            {
                //上传图片和文件
                $arrPhotosAndFile = array(
                    "xxx",         //xxx
 
                );
                $arrFile = array(
                    "xxx",         //xxxx

                $arrRes = $objUser->uploadFileAndPictrue($arrPhotosAndFile, $arrFile); 
                foreach($arrRes as $key=>$val)
                {
                    $arrProInfo[$key] = $val;
                }
                //接收其他图片
                //$arrInfor['picture_mul'] = $_POST['picture_mul']; //其他图片
                //公用的
                $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                
                //未建
                if ($arrInfo['build_state'] == 1)
                {
                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                }
                //已建
                if ($arrInfo['build_state'] == 2)
                {

                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                    $arrInfor['xxx'] = $_POST['xxxx']; //xxx
                }
            }





            //上传图片是调用一个函数，返回一个url
            //上传doc等文件，调用一个函数，返回一个url
            //拼接url插入数据


            //保持的处理，数据只保持到xxx_provider

            //提交的处理，这里数据只保持到xxx编码中，正式的
	    }
	    elseif ( $optype == "delete" && $rtype == 1)
	    {
            //项目删除
            //这里删除以xxx开头的编号数据
	    }
        elseif($rtype != 1)
        {
            //这个分支是做数据的显示
            $projectCode = $_POST['project_code'] ? $_POST['project_code']:$_GET['project_code'];
            $display     =$_GET['display'];
            //根据project_code去查询项目信息
            $objProject  = D("Project","Service");
            $projectInfo = $objProject->getProjectInfo($projectCode);
            if ($display=="json")
            {
                echo json_encode($projectInfo);
                exit;
            }
            $this->assign('project',$projectInfo);
            $this->display("ProjectProvider:projectInfoNew");
        }
    }


    /**
    **@auth qiujinhan@gmail.com
    **@breif 项目提供方->待评估项目入口
    **@date 2015.12.13
    **/
    public function awaitingAssessment()
    {
        $email = $_COOKIE['email'];
        isLogin($email, $_COOKIE['mEmail']);
        isDataComplete($email);

        $objProject = D("Project", "Service");
        $listProject = $objProject->getAwaitingAssessment($email);
        $this->assign('listProject', $listProject);
        $this->display("ProjectProvider:awaitingAssessment");
    }

    /**
    **@auth qianqiang
    **@breif 项目提供方->已签意向书项目
    **@date 2015.12.24
    **/
    public function agreementProject()
    {
        $email = $_COOKIE['email'];
        isLogin($email, $_COOKIE['mEmail']);
        isDataComplete($email);

        $objProject = D("Project", "Service");
        $listProject = $objProject->getAggrementProject($email);
        $this->assign('listProject', $listProject);
        $this->display("ProjectProvider:aggrementProject");
    }

    /**
    **@auth qianqiang
    **@breif 项目提供方->已签融资合同项目
    **@date 2015.12.24
    **/
    public function xxxxx()
    {
        $email = $_COOKIE['email'];
        isLogin($email, $_COOKIE['mEmail']);
        isDataComplete($email);
        
        $objProject = D("Project", "Service");
        $listProject = $objProject->getxxxxx($email);
        $this->assign('listProject', $listProject);
        $this->display("ProjectProvider:aggrementProject");
    }

}