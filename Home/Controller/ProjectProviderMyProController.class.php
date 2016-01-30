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
        authentication($_COOKIE['email'], 3);
        $projectInfo["components"] = array("0"=>"");
        $projectInfo["inverters"]  = array("0"=>"");
        $projectInfo["picture_mul"]  = array("0"=>"");
        $this->assign('data',$projectInfo);
    	$this->display("ProjectProvider:projectInfoNew");
    }



    /**
    **@auth qiujinhan@gmail.com
    **@breif 项目提供方->项目信息编辑入口
    **@date 2015.12.05
    **/
	public function projectInfoEdit($projectCode=null, $rtype=null, $getJsonFlag=null, $innerToken=null)
    {
        //echo $getJsonFlag;exit;
        //判断登陆，并且获取用户名的email
       
        isLogin($_COOKIE['email'],$_COOKIE['mEmail']);
        //echo $innerToken.'dd';exit;
        authentication($_COOKIE['email'], 3,$innerToken);

    	//操作类型为1是插入和保存数据
    	$optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        if($rtype == null)
        {
            $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
        }
        //echo $rtype;exit;
        $objDoc  = D("Doc","Service");
        $email = $_COOKIE['email'];
        //屋顶分布式用到的附件
        $arrPhotosAndFileInHousetop = array(
            "picture_full",
            "picture_south", 
            "picture_mul",
            "contract",
            "housetop_property_prove",
            "electricity_pay_list",
            "project_backup",
            "electricity_backup",
            "house_rent_agreement",
            "power_manage_agreement",
            "project_proposal",
            "project_report",
            "housetop_load_prove",
            "completion_report",
            "completion_paper",
            "history_data",
            "electricity_bill",
        );
        //大型地面电站/地面分布式用到的附件
        $arrPhotosAndFileInGround = array(
             "picture_full",
             "picture_field",
             "picture_transformer",
             "picture_mul",
             "contract",
             "project_backup",
             "electricity_backup",
             "ground_rent_agreement",
             "ground_opinion",
             "project_proposal",
             "project_report",
             "environment_assessment",
             "land_certificate",
             "electricity_price_reply",
             "is_old_project",
             "completion_report",
             "completion_paper",
             "history_data",
             "electricity_bill",
        );

        //保存or提交
    	if ( ($optype == "save" || $optype == "submit") && $rtype == 1)
    	{   
            
            //接收前端表单过来的参数，并且处理下
            $arrProInfo = array(); //ENF_Project信息
            $arrInfor = array();     //项目表信息
            //$arrInfor['xxx'] = $_POST['xxxx']; //xxx
            $arrProInfo['project_type'] = $_POST['project_type']; //项目类型，1屋顶分布式，2地面分布式，3大型地面
            $arrProInfo['build_state'] = $_POST['build_state']; //1未建 or 2已建  
            //$arrProInfo['project_code'] = $_POST['project_code']; //项目编码
            //接收参数
            $projectCode  = $_POST['no']     ? $_POST['no']:$_GET['no'];
            $mProjectCode = $_POST['token']  ? $_POST['token']:$_GET['token'];
            //echo $projectCode;
            //echo $mProjectCode;
            //exit;
            if(!empty($projectCode))
            {
                isProjectCodeRight($projectCode, $mProjectCode);
            }
            $arrProInfo['project_code']  =$projectCode;

            //共有的一些参数接收
            $arrInfor['project_area'] = $_POST['county'];//省市区
            
            $arrInfor['project_address'] = $_POST['project_address'];  //详细地址
            $arrInfor['transformer_capacity'] = $_POST['transformer_capacity'];//上级变压器容量
            $arrInfor['voltage_level'] = $_POST['voltage_level'];//并网电压等级
            $arrInfor['plan_financing'] = $_POST['plan_financing'];//拟融资金额


            //上传公用的图片和文件
            $arrPhotosAndFile = array(
                "electricity_backup",         //电网接入备案
                "project_backup",        //项目备案

            );
            $arrFile = array(
                "electricity_backup",         //电网接入备案
                "project_backup",        //项目备案

            );
            $arrRes = $objDoc->uploadFileAndPictrue($arrPhotosAndFile, $arrFile); 
            foreach($arrRes as $key=>$val)
            {
                $arrInfor[$key] = $val;
            }


            //屋顶分布式 接收参数
            if($arrProInfo['project_type'] == 1)
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
                $arrRes = $objDoc->uploadFileAndPictrue($arrPhotosAndFile, $arrFile); 
                foreach($arrRes as $key=>$val)
                {
                    $arrInfor[$key] = $val;
                }
                //接收其他图片，直接调用函数上传n张图片，得到n个id,然后直接拼接picture_mul__hiddenId存数据库
                $res = $objDoc->uploadFileAndPictrueMul();
                $arrInfor['picture_mul'] = implode(',', $res);//echo json_encode($res);exit;//echo jj;
                //echo $arrInfor['picture_mul'];exit;
                //$arrInfor['picture_mul'] = $_POST['picture_mul']; //其他图片
                //获取到当前picture_mul__hiddenId

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
                
                //未建
                if ($arrProInfo['build_state'] == 1)
                {
                    $arrInfor['plan_build_volume'] = $_POST['plan_build_volume']; //拟建设容量
                    $arrInfor['cooperation_type'] = $_POST['cooperation_type']; //与能融网合作方式（可多选）
                    //var_dump($_POST['cooperation_type']);exit;
                    $arrInfor['cooperation_type'] =  implode("&",$arrInfor['cooperation_type']);
                    $arrInfor['financing_type'] = $_POST['financing_type']; //融资方式
                }
                //已建
                if ($arrProInfo['build_state'] == 2)
                {
                    $arrPhotosAndFile = array(
                        "completion_report",//竣工验收报告
                        "completion_paper",//竣工图纸
     
                    );
                    $arrFile = array(
                        "completion_report",//竣工验收报告
                        "completion_paper",//竣工图纸
                    );
                    $arrRes = $objDoc->uploadFileAndPictrue($arrPhotosAndFile, $arrFile); 
                    foreach($arrRes as $key=>$val)
                    {
                        $arrInfor[$key] = $val;
                    }
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
                    $arrInfor['cooperation_type'] =  implode("&",$arrInfor['cooperation_type']);
                    $arrInfor['financing_type'] = $_POST['financing_type']; //融资方式
                    $arrInfor['history_data'] = $_POST['history_data']; //历史发电量数据/辐照数据
                    $arrInfor['electricity_bill'] = $_POST['electricity_bill']; //电费结算票据
                    $arrInfor['electricity_data'] = $_POST['electricity_data']; //历史发电量数据



                }


            }

            //地面分布式 和大型地面  接收参数
            if($arrProInfo['project_type'] == 2 || $arrProInfo['project_type'] == 3)
            {
                //上传图片和文件
                $arrPhotosAndFile = array(
                    "picture_full",         //场地情况全景图
                    "picture_field",//场平照片
                    "picture_transformer",//变电站照片
                    "ground_rent_agreement",    //土地租赁协议
                    "ground_opinion",//土地预审意见
                    "environment_assessment",//环评
                    "project_report",//项目可研报告/项目建议书
 
                );
                $arrFile = array(
                    "ground_rent_agreement",    //土地租赁协议
                    "ground_opinion",//土地预审意见
                    "environment_assessment",//环评
                    "project_report",//项目可研报告/项目建议书
                );
                $arrRes = $objDoc->uploadFileAndPictrue($arrPhotosAndFile, $arrFile); 
                foreach($arrRes as $key=>$val)
                {
                    $arrInfor[$key] = $val;
                }
                //接收其他图片,这个先不做
                //$arrInfor['picture_mul'] = $_POST['picture_mul']; //其他图片
                //接收其他图片，直接调用函数上传n张图片，得到n个id,然后直接拼接picture_mul__hiddenId存数据库
                $res = $objDoc->uploadFileAndPictrueMul();
                $arrInfor['picture_mul'] = implode(',', $res);
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
                
                //未建
                if ($arrProInfo['build_state'] == 1)
                {
                    $arrInfor['plan_build_volume'] = $_POST['plan_build_volume']; //拟建设容量
                    $arrInfor['cooperation_type'] = $_POST['cooperation_type']; //与能融网合作方式（可多选）
                    $arrInfor['cooperation_type'] =  implode("&",$arrInfor['cooperation_type']);
                }
                //已建
                if ($arrProInfo['build_state'] == 2)
                {

                    $arrPhotosAndFile = array(
                        "land_certificate",         //土地证
                        "electricity_price_reply",//物价局电价批复
                        "is_old_project",//是否进入当年省光伏项目目录
                        "completion_report",//竣工验收报告
                        "completion_paper",//竣工图纸
     
                    );
                    $arrFile = array(
                        "land_certificate",    //土地证
                        "electricity_price_reply",//物价局电价批复
                        "is_old_project",//是否进入当年省光伏项目目录
                        "completion_report",//竣工验收报告
                        "completion_paper",//竣工图纸
                    );
                    $arrRes = $objDoc->uploadFileAndPictrue($arrPhotosAndFile, $arrFile); 
                    foreach($arrRes as $key=>$val)
                    {
                        $arrInfor[$key] = $val;
                    }
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
                    $arrInfor['cooperation_type'] =  implode("&",$arrInfor['cooperation_type']);
                    $arrInfor['financing_type'] = $_POST['financing_type']; //融资方式
                    $arrInfor['history_data'] = $_POST['history_data']; //历史发电量数据/辐照数据
                    $arrInfor['electricity_bill'] = $_POST['electricity_bill']; //电费结算票据
                    $arrInfor['electricity_data'] = $_POST['electricity_data']; //历史发电量数据
                }

            }

            //上面的各种代码已经接收好参数和处理参数了

            //先把数据存到Project表中，并且得到id
            $objProject  = D("Project","Service");
            if($optype == "save")
            {
                $arrProInfo["status"] = "11";  //项目未提交状态
                $arrInfor["status"] = "11";  //项目未提交状态
                $oldStatus = "11";        //之前的状态
            }
            if($optype == "submit")
            {
                $arrProInfo["status"] = "12";  //项目提交状态
                $arrInfor["status"] = "12";  //项目未提交状态
                $oldStatus = "11"; //之前的状态
            }

            if (empty($arrProInfo['project_code']))  
            {
                //插入
                $getProjectCode = getProjectCode($_POST['project_type'], $_POST['financing_type'], $_POST['county'] );
                $arrProInfo["project_code"] = $getProjectCode;
                //$arrProInfo["project_code"] = '2323DDDDDDDDDDd'.time();  //之后需要加一下这个生成项目id的功能
                $objUser  = D("User","Service");
                $userInfo = $objUser->getUserINfoByEmail($email);
                $arrProInfo["provider_id"] = $userInfo[0]['id'];//之后需要加一下项目提供方的id
                //echo $email;exit;
                //echo json_encode($userInfo);exit;
                $arrProInfo["highlight_flag"] = 1;
                $arrProInfo["create_time"] = date("Y-m-d H:i:s" ,time());
                $arrProInfo["create_date"] = date("Y-m-d H:i:s" ,time());
                $ret = $objProject->insertProject($arrProInfo);
                if ($ret === false)
                {
                     echo '{"code":"-1","msg":"插入数据库失败9999999999！"}';
                     exit;
                }
                $arrInfor["project_id"] = $ret;
                
                
            }
            else   
            {
                //更新
                $arrProInfo["change_date"] = date("Y-m-d H:i:s" ,time());
                //echo $arrProInfo['project_code'];echo json_encode($arrProInfo);exit;
                $ret = $objProject->saveProject($arrProInfo['project_code'], $arrProInfo);
                if ($ret === false)
                {
                     echo '{"code":"-1","msg":"更新数据库失败！"}';
                     exit;
                }
                $arrInfor["project_id"] = $ret;
            }
            //对于已建项目类型的，需要存好组件和逆变器

            if ($arrProInfo['build_state'] == 2)
            {
                //根据项目id去存一下n个组件
                $ret = $objProject->addComponent($arrInfor["project_id"]);
                if ($ret === false)
                {
                     echo '{"code":"-1","msg":"组件插入数据库失败！"}';
                     exit;
                }
                //根据项目id去存一下n个逆变器
                $ret = $objProject->addInverter($arrInfor["project_id"]);
                if ($ret === false)
                {
                     echo '{"code":"-1","msg":"逆变器插入数据库失败！"}';
                     exit;
                }
            }
            if($arrProInfo['project_type'] == 2 || $arrProInfo['project_type'] == 3)
            {
                $table = 'Ground';
            }
            if($arrProInfo['project_type'] == 1)
            {
                $table = 'Housetop';
            }
            // dump($arrInfor);exit;
            $ret = $objProject->saveHousetopOrGround($arrInfor, $arrInfor["status"], $arrProInfo['project_type'], $oldStatus);
            if ($ret === false)
            {
                 echo '{"code":"-1","msg":"插入数据库失败111！"}';
                 exit;
            }
            echo '{"code":"0","msg":"","id":"'.$arrProInfo["project_code"].'","idm":"'
                 .MD5(addToken($arrProInfo["project_code"])).'"}';

            //保持的处理，数据只保持到xxx_provider

            //提交的处理，这里数据只保持到xxx编码中，正式的
	    }
	    elseif ( $optype == "delete" && $rtype == 1)  //删除
	    {
            //项目删除
            //这里删除以xxx开头的编号数据
            //$projectCode = $_POST['project_code'] ? $_POST['project_code']:$_GET['project_code'];
            //接收参数
            $projectCode  = $_POST['no']     ? $_POST['no']:$_GET['no'];
            $mProjectCode = $_POST['token']  ? $_POST['token']:$_GET['token'];
            isProjectCodeRight($projectCode, $mProjectCode);
            $objProject  = D("Project","Service");
            $res = $objProject->deleteProject($projectCode);//echo $res;
            if ($ret === false)
            {
                 echo '{"code":"-1","msg":"删除项目失败失败！"}';
                 exit;
            }
            echo '{"code":"0","msg":""}';
	    }
        elseif($rtype != 1)  //显示
        {
            //echo jj;exit;
            //这个分支是做数据的显示
            if($projectCode == null)
            {
                //$projectCode = $_POST['project_code'] ? $_POST['project_code']:$_GET['project_code'];
                //接收参数
                $projectCode  = $_POST['no']     ? $_POST['no']:$_GET['no'];
                $mProjectCode = $_POST['token']  ? $_POST['token']:$_GET['token'];
                isProjectCodeRight($projectCode, $mProjectCode);
            }
            $display     =$_GET['display'];
            //根据project_code去查询项目信息
            $objProject  = D("Project","Service");
            $projectInfo = $objProject->getProjectInfo($projectCode);
            //echo $projectCode;exit;
            //echo jj;exit;
            //echo json_encode($projectInfo);exit;
            //在去Housetop，or  Ground 取一下数据
            $projectInfoDetail = $objProject->getProjectDetail($projectInfo['id'], $projectInfo['project_type']);
            //获取组件信息
            $ret = $objProject->getComponent($projectInfo['id']);
            $projectInfo["components"] = $ret;
            //获取逆变器信息
            $ret = $objProject->getInverter($projectInfo['id']);
            $projectInfo["inverters"] = $ret;

            //$arr_project_area = explode("#",$projectInfoDetail["project_area"]);
            $areaObj = D("Area","Service");
            $arr_project_area = $areaObj->getAreaArrayById($projectInfoDetail["project_area"]);
            $projectInfoDetail["cooperation_type"] = explode("&",$projectInfoDetail["cooperation_type"]);
            $projectInfo["province"] = $arr_project_area[0];
            $projectInfo["city"] = $arr_project_area[1];
            $projectInfo["county"] = $arr_project_area[2];
            $objUser = D("Doc","Service");
            $arrPicAndfile = array();
            //处理下文件和图片的信息
            if ($projectInfo['project_type'] == 1)
            {
                 $arrPicAndfile = $arrPhotosAndFileInHousetop;
            }
            else
            {
                $arrPicAndfile = $arrPhotosAndFileInGround;
            }
            //var_dump($arrPicAndfile);exit;
            foreach($projectInfoDetail as $k => $v)
            {
                if($k == 'id') continue;
                $projectInfo[$k] = $v;
            }
            foreach($arrPicAndfile as $val)
            {
                
                if ($val == "picture_mul")
                {
                    $projectInfo["picture_mul"] = array();
                    $arrDocId = explode(',', $projectInfoDetail[$val]); //picture_full
                    foreach ($arrDocId as $vid) {
                        $condition["id"] = $vid;
                        $docInfo = $objUser->getDocInfo($condition);
                        $temp = array();
                        $temp["id"] = $docInfo[0]["id"];
                        $temp["token"] = md5(addToken($docInfo[0]["id"]));
                        $temp["name"] = $docInfo[0]["file_name"];
                        $temp["url"] = $docInfo[0]["file_rename"];
                        $projectInfo["picture_mul"][] = $temp;
                    }
                    $projectInfo["picture_mul"][] = "";
                }
                else
                {
                    $condition["id"] = $projectInfoDetail[$val]; //picture_full
                    $docInfo = $objUser->getDocInfo($condition);
                    $projectInfo[$val] = array();
                    $projectInfo[$val]["id"] = $docInfo[0]["id"];
                    $projectInfo[$val]["token"] = md5(addToken($docInfo[0]["id"]));
                    $projectInfo[$val]["name"] = $docInfo[0]["file_name"];
                    $projectInfo[$val]["url"] = $docInfo[0]["file_rename"];
                }           
            }
            //echo json_encode($projectInfo);exit;
            //给项目进度用,直接截断了,返回json了
            if ($getJsonFlag == 1)
            {
                return $projectInfo;
            }
            if ($display=="json")
            {
                echo json_encode($projectInfo);
                exit;
            }
            //加上项目编码的token
            $projectInfo['project_idm'] = MD5(addToken($projectInfo['project_code']));
            $this->assign('data',$projectInfo);
            $this->display("ProjectProvider:projectInfoNew");
        }
    }


    /**
    **@auth qianqiang
    **@breif 项目提供方->待评估项目
    **@date 2015.12.24
    **/
    public function awaitingAssessment()
    {
        $email = $_COOKIE['email'];
        isLogin($email, $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 3);
        $loginFlag = $_GET['r'];//登录后调用标记
        isDataComplete($email, $loginFlag);
        $optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $filter = $_POST['filter'] ? $_POST['filter']:$_GET['filter'];
        $page = $_GET['page'];
        if(empty($page)) $page=1;
        $pageSize = 6;
        $objProject = D("Project", "Service");
        $listProject = $objProject->getAwaitingAssessment($email, $filter, $page);
        $listTotal = $objProject->getAwaitingAssessment($email, $filter, -1);
        $data = array();
        $data["list"] = $listProject;
        $data["page"] = $page;
        $data["count"] = sizeof($listTotal);
        $data["totalPage"] = ceil($data["count"]/$pageSize+1);
        $data["endPage"] = ceil($data["count"]/$pageSize);
        $data["filter"] = $filter;
        if($_GET['display']=="json"){
            header('Content-Type: text/html; charset=utf-8');
            dump($data);
            exit;
        }
        $this->assign('arrData', $data);
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
        authentication($_COOKIE['email'], 3);
        isDataComplete($email);
        $page = $_GET['page'];
        if(empty($page)) $page=1;
        $pageSize = 6;
        $objProject = D("Project", "Service");
        $listProject = $objProject->getAgreementProject($email, $page);
        $listTotal = $objProject->getAgreementProject($email, -1);
        $data = array();
        $data["list"] = $listProject;
        $data["page"] = $page;
        $data["count"] = sizeof($listTotal);
        $data["totalPage"] = ceil($data["count"]/$pageSize+1);
        $data["endPage"] = ceil($data["count"]/$pageSize);
        if($_GET['display']=="json"){
            header('Content-Type: text/html; charset=utf-8');
            dump($data);
            exit;
        }
        $this->assign('arrData', $data);
        $this->display("ProjectProvider:agreementProject");
    }

    /**
    **@auth qianqiang
    **@breif 项目提供方->已签融资合同项目
    **@date 2015.12.24
    **/
    public function contractProject()
    {
        $email = $_COOKIE['email'];
        isLogin($email, $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 3);
        isDataComplete($email);
        $page = $_GET['page'];
        if(empty($page)) $page=1;
        $pageSize = 6;
        $objProject = D("Project", "Service");
        $listProject = $objProject->getContractProject($email, $page);
        $listTotal = $objProject->getContractProject($email, -1);
        $data = array();
        $data["list"] = $listProject;
        $data["page"] = $page;
        $data["count"] = sizeof($listTotal);
        $data["totalPage"] = ceil($data["count"]/$pageSize+1);
        $data["endPage"] = ceil($data["count"]/$pageSize);
        if($_GET['display']=="json"){
            header('Content-Type: text/html; charset=utf-8');
            dump($data);
            exit;
        }
        $this->assign('arrData', $data);
        $this->display("ProjectProvider:contractProject");
    }

    /**
    **@auth qiujinhan
    **@breif 项目提供方->查看项目信息进度
    **@date 2016.01.03
    **/
    public function projectInfoView()
    {
        //判断登陆，并且获取用户名的email
        isLogin($_COOKIE['email'],$_COOKIE['mEmail']);     
        authentication($_COOKIE['email'], 3);
        //接收参数
        $projectCode  = $_POST['no']     ? $_POST['no']:$_GET['no'];
        $mProjectCode = $_POST['token']  ? $_POST['token']:$_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode);
        $optype       = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $rtype        = $_POST['rtype']  ? $_POST['rtype']:$_GET['rtype'];
        //签署意向书的同意按钮，其实是去project和Housetop两个表中更新下status字段就可以了
        $objProject  = D("Project","Service");
        $objProject->cancelProjectHighlight($projectCode);
        $getJsonFlag = 1;
        //获取项目信息
        //echo XF7407-179K-X16-669723
        //echo $projectCode;exit;
        $rtypeForfun = 9;
        $data = $this->projectInfoEdit($projectCode, $rtypeForfun, $getJsonFlag);
        //echo json_encode($data);exit;
        //获取签署意向书信息 
        if($optype == 'agree' &&  $rtype == 1)
        {
            $res = $objProject->updateProjectStatus($data["id"],'23');
            if($res === false)
            {
                echo '{"code":"-1","msg":"更新失败！"}'; 
            }
            else
            {
                echo '{"code":"0","msg":""}';
            }
            exit;
        }

        $projectInfoForIntent = $objProject->getProjectDetail($data["id"], $data['project_type']);
        $status = $projectInfoForIntent["status"];//
        // echo $status;exit;
        //echo $data['project_type'];exit;
        //echo json_encode($projectInfoForIntent);exit;
        //获取强哥的尽职调查信息
        $obj   = new InnerStaffController();
        $innerToken = "InternalCall";
        list($picture,$docListInfo,$projectDetail,$areaArray,$evaluationInfo) = 
            $obj->dueDiligence($projectCode, null, $getJsonFlag,$innerToken);

        //先判断一下当前进度的状态
        //12项目已提交（客服未提交意向书）、
        //13项目已提交（客服已提交意向书）、
        //21签意向合同（客服未提交尽职调查）、22签意向合同（客服已提交尽职调查）
        //"state":"dueDiligence" // projectInfo, intent, dueDiligence
        

        //后来先尽职调查  然后在意向书替换位置了
        //12项目已提交（已提交）、
        //13已提交意向书（已提交）、
        
        //22已提交尽职调查（未签意向合同）、
        
        //substate":"submited" // wait, submited, signed
        if ($status == 12) //12项目已提交（已提交）
        {
            $strStatus = "dueDiligence";
            $substate  = "wait";
        }
        elseif($status == 22)  //13已提交意向书（已提交）
        {
            $strStatus = "dueDiligence";
            $substate  = "submited";
        }
        elseif($status == 13)  //13已提交意向书（已提交）
        {
            $strStatus = "intent";
            $substate  = "submited";
        }
        else   //21已签意向书
        {
            $strStatus = "intent";
            $substate  = "signed";
        }
        /*if ($status == 22)
        {
            $strStatus = "dueDiligence";
            $substate  = "submited";
        }
        elseif($status == 13)
        {
            $strStatus = "intent";
            $substate  = "submited";
        }
        elseif($status == 21)
        {
            $strStatus = "dueDiligence";
            $substate  = "wait";
        }
        else
        {
            $strStatus = "intent";
            $substate  = "wait";
        }*/



        //拼接大json
        $bigArr = array();
        $bigArr['step'] = array();
        $bigArr['step']['state'] = $strStatus;
        $bigArr['step']['substate'] = $substate;
        $bigArr['projectInfo'] = $data;//echo json_encode($bigArr);exit;
        $bigArr['intent']['yixiangshu'] = $projectInfoForIntent['project_intent'];
        //$picture,$docListInfo,$projectDetail,$areaArray,$evaluationInfo

        $bigArr['dueDiligence'] = array();
        $bigArr['dueDiligence']['picture'] = $picture;
        $bigArr['dueDiligence']['docListInfo'] = $docListInfo;
        $bigArr['dueDiligence']['areaArray'] = $areaArray;
        $bigArr['dueDiligence']['projectDetail'] = $projectDetail;
        $bigArr['dueDiligence']['evaluationInfo'] = $evaluationInfo;
        //echo json_encode($bigArr);exit;


        //判断显示哪个前端页面


        //$bigJson = '{"step":{"state":"dueDiligence","substate":"submited"},"projectInfo":{},"intent":{},"dueDiligence":{}}';
        //$bigJson = json_encode($bigArr);
        // echo json_encode($bigArr);exit;
        $this->assign('data', $bigArr);
        if($data['project_type'] == 1){
            if($data['build_state'] == 1){
                $this->display("ProjectProvider:projectInfoView_housetop_nonbuild");
            }elseif($data['build_state'] == 2){
                $this->display("ProjectProvider:projectInfoView_housetop_build");
            }
        }elseif($data['project_type'] == 2 || $data['project_type'] == 3){
            if($data['build_state'] == 1){
                $this->display("ProjectProvider:projectInfoView_ground_nonbuild");
            }elseif($data['build_state'] == 2){
                $this->display("ProjectProvider:projectInfoView_ground_build");
            }
        }

    }


}