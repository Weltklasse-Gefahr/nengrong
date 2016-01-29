<?php
namespace Home\Controller;

use Think\Controller;

class InnerStaffController extends Controller {
 

    /**
    **@auth qiujinhan
    **@breif 在客服中导出一个项目的信息
    **@date 2016.1.17
    **@参数 ?c=InnerStaff&a=export&no=sss&token=xxxx
    **/
    public function export()
    {
        //echo jj;exit;
        /*
        isLogin($_COOKIE['email'],$_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2);
        $projectCode  = $_POST['no']     ? $_POST['no']:$_GET['no'];
        $mProjectCode = $_POST['token']  ? $_POST['token']:$_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode); 
        $getJsonFlag = 1;
        //获取项目投资方信息
        $data = $this->getProjectProviderInfo($projectCode, null, $getJsonFlag);
        */
        import("Org.Util.PHPExcel");
        $obpe = new \PHPExcel();
        $obpe_pro = $obpe->getProperties();
        $obpe_pro->setCreator('midoks')//设置创建者
             ->setLastModifiedBy('2013/2/16 15:00')//设置时间
             ->setTitle('data')//设置标题
             ->setSubject('beizhu')//设置备注
             ->setDescription('miaoshu')//设置描述
             ->setKeywords('keyword')//设置关键字 | 标记
             ->setCategory('catagory');//设置类别
            //设置当前sheet索引,用于后续的内容操作
            //一般用在对个Sheet的时候才需要显示调用
            //缺省情况下,PHPExcel会自动创建第一个SHEET被设置SheetIndex=0
            //设置SHEET
            $obpe->setactivesheetindex(0);
            //写入多行数据
            //模拟数据
            $mulit_arr = array(
                array('标题1', '标题2', '标题3'),
                array('a', 'b', 'c'),
                array('d', 'e', 'f')
            );
                       
            //创建一个新的工作空间(sheet)
            $obpe->createSheet();
            $obpe->setactivesheetindex(1);
            //写入多行数据
            foreach($mulit_arr as $k=>$v){
                $k = $k+1;
                /* @func 设置列 */
                $obpe->getactivesheet()->setcellvalue('A'.$k, $v[0]);
                $obpe->getactivesheet()->setcellvalue('B'.$k, $v[1]);
                $obpe->getactivesheet()->setcellvalue('C'.$k, $v[2]);
            }
            import("Org.Util.PHPExcel.IOFactory");
            $objWriter = \PHPExcel_IOFactory::CreateWriter($obpe,"Excel2007");
            //$objWriter->save('qiujinhan.xls');exit;
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
            header('Content-Type:application/force-download');
            header('Content-Type:application/vnd.ms-execl');
            header('Content-Type:application/octet-stream');
            header('Content-Type:application/download');
            header("Content-Disposition:attachment;filename='qiujinhan.xls'");
            header('Content-Transfer-Encoding:binary');
            $objWriter->save('php://output');

    }

    /**
    **@auth qianqiang
    **@breif 客服->项目提供方信息（账户向详细信息）
    **@date 2015.12.19
    **/
    public function getProjectProviderInfo(){
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2);
        $projectCode = $_POST['no'] ? $_POST['no']:$_GET['no'];
        $mProjectCode = $_POST['token'] ? $_POST['token']:$_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode);
        $objProject = D("Project", "Service");
        $objProjectInfo = $objProject->getProjectInfo($projectCode);
        $providerId = $objProjectInfo['provider_id'];
        $userObj = D("User", "Service");
        $userInfo = $userObj->getUserINfoById($providerId);

        $areaObj = D("Area", "Service");
        $areaStr = $areaObj->getAreaById($userInfo[0]['company_area']);
        $docObj = D("Doc", "Service");
        $condition['id'] = $userInfo[0]['business_license'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['business_license']['id'] = $docInfo[0]['id'];
        $docData['business_license']['file_name'] = $docInfo[0]['file_name'];
        $docData['business_license']['file_rename'] = $docInfo[0]['file_rename'];
        $condition['id'] = $userInfo[0]['organization_code'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['organization_code']['id'] = $docInfo[0]['id'];
        $docData['organization_code']['file_name'] = $docInfo[0]['file_name'];
        $docData['organization_code']['file_rename'] = $docInfo[0]['file_rename'];
        $condition['id'] = $userInfo[0]['national_tax_certificate'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['national_tax_certificate']['id'] = $docInfo[0]['id'];
        $docData['national_tax_certificate']['file_name'] = $docInfo[0]['file_name'];
        $docData['national_tax_certificate']['file_rename'] = $docInfo[0]['file_rename'];
        $condition['id'] = $userInfo[0]['local_tax_certificate'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['local_tax_certificate']['id'] = $docInfo[0]['id'];
        $docData['local_tax_certificate']['file_name'] = $docInfo[0]['file_name'];
        $docData['local_tax_certificate']['file_rename'] = $docInfo[0]['file_rename'];
        $condition['id'] = $userInfo[0]['identity_card_front'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['identity_card_front']['id'] = $docInfo[0]['id'];
        $docData['identity_card_front']['file_name'] = $docInfo[0]['file_name'];
        $docData['identity_card_front']['file_rename'] = $docInfo[0]['file_rename'];
        $condition['id'] = $userInfo[0]['identity_card_back'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['identity_card_back']['id'] = $docInfo[0]['id'];
        $docData['identity_card_back']['file_name'] = $docInfo[0]['file_name'];
        $docData['identity_card_back']['file_rename'] = $docInfo[0]['file_rename'];
        $condition['id'] = $userInfo[0]['financial_audit'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['financial_audit']['id'] = $docInfo[0]['id'];
        $docData['financial_audit']['file_name'] = $docInfo[0]['file_name'];
        $docData['financial_audit']['file_rename'] = $docInfo[0]['file_rename'];
        $docData['financial_audit']['token'] = md5(addToken($docInfo[0]["id"]));

        if ($_GET['display'] == 'json') {
            header('Content-Type: text/html; charset=utf-8');
            dump($userInfo);
            dump($areaStr);
            dump($docData);
            exit;
        }
        $this->assign('userInfo', $userInfo[0]);
        $this->assign('areaStr', $areaStr);
        $this->assign('docData', $docData);
        $this->display("InnerStaff:providerInfo");
    }

    /**
    **@auth qianqiang
    **@breif 客服->尽职调查
    **@date 2015.12.19
    **/
    public function dueDiligence($projectCode=null, $rtype=null, $getJsonFlag=null, $innerToken=null){
    	isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2, $innerToken);
    	$optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        if( $rtype == null)
        {
            $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
        }
        if($optype == "upload" && $rtype == 1){
            $docFile = array(
                "attachment",
                );
            $doc = D("Doc", "Service");
            $docInfo = $doc->uploadFileAndPictrue($docFile, $docFile);
            if(!empty($docInfo)){
                echo '{"code":"0","msg":"success","id":"'.$docInfo['attachment'].'"}';
            }else{
                echo '{"code":"-1","msg":"上传失败！"}';
            }
        }elseif($optype == "save" && $rtype == 1){
            if($projectCode == null){
                $projectCode = $_POST['no'] ? $_POST['no']:$_GET['no'];
                $mProjectCode = $_POST['token'] ? $_POST['token']:$_GET['token'];
                isProjectCodeRight($projectCode, $mProjectCode);
            }

    		$objProject = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];

    		$proData = array();
            if($objProjectInfo['project_type'] == 1){
                $proData['project_id'] = $projectId;
                $proData['housetop_owner'] = $_POST['housetop_owner']; 
                $proData['company_type'] = $_POST['company_type'];
                $proData['plan_build_volume'] = $_POST['plan_build_volume'];
                $proData['project_area'] = $_POST['county'];
                $proData['project_address'] = $_POST['project_address'];
                $proData['housetop_type'] = $_POST['housetop_type'];
                $proData['housetop_type_other'] = $_POST['housetop_type_other'];
                $proData['synchronize_type'] = $_POST['synchronize_type'];
                $proData['plan_financing'] = $_POST['plan_financing'];
                $proData['financing_type'] = $_POST['financing_type'];
            }elseif($objProjectInfo['project_type'] == 2 || $objProjectInfo['project_type'] == 3){
                $proData['project_id'] = $projectId;
                $proData['plan_build_volume'] = $_POST['plan_build_volume'];
                $proData['project_area'] = $_POST['county'];
                $proData['project_address'] = $_POST['project_address'];
                $proData['project_name'] = $_POST['project_name'];
                $proData['project_finish_date'] = $_POST['project_finish_date'];
                $proData['project_electricity_price'] = $_POST['project_electricity_price'];
                $proData['project_investment'] = $_POST['project_investment'];
                $proData['ground_condition'] = $_POST['ground_condition'];
                $proData['ground_property'] = $_POST['ground_property'];
                $proData['ground_property_other'] = $_POST['ground_property_other'];
                $proData['ground_area'] = $_POST['ground_area'];
                $proData['plan_financing'] = $_POST['plan_financing'];
                $proData['financing_type'] = $_POST['financing_type'];
            }
    		
            $evaData = array();
            $evaData['project_id'] = $projectId;
            $evaData['IRR'] = $_POST['IRR'];
            $evaData['evaluation_result'] = $_POST['evaluation_result'];
            $evaData['static_payback_time'] = $_POST['static_payback_time'];
            $evaData['dynamic_payback_time'] = $_POST['dynamic_payback_time'];
            $evaData['LCOE'] = $_POST['LCOE'];
            $evaData['npv'] = $_POST['npv'];
            $evaData['power_asset_current_value'] = $_POST['power_asset_current_value'];
            $evaData['evaluation_content'] = $_POST['evaluation_content'];
            $evaData['document_review'] = $_POST['document_review'];
            $evaData['project_quality_situation'] = $_POST['project_quality_situation'];
            $evaData['project_invest_situation'] = $_POST['project_invest_situation'];
            $evaData['project_earnings_situation'] = $_POST['project_earnings_situation'];
            $evaData['doc_mul'] = implode(",", $_POST['doc_mul']);

            $res = $objProject->saveHousetopOrGround($proData, 51, $objProjectInfo['project_type']);
            if($res == true){
            	$objEvaluation = D("Evaluation", "Service");
            	$res = $objEvaluation->saveEvaluationInfo($evaData);
            	if($res == true){
            		echo '{"code":"0","msg":"success"}';
            	}else{
            		echo '{"code":"-1","msg":"Evaluation更新失败！"}';
            	}
            }else{
            	echo '{"code":"-1","msg":"Housetop更新失败！"}';
            }
    	}elseif($optype == "submit" && $rtype == 1){
            if($projectCode == null){
                $projectCode = $_POST['no'] ? $_POST['no']:$_GET['no'];
                $mProjectCode = $_POST['token'] ? $_POST['token']:$_GET['token'];
                isProjectCodeRight($projectCode, $mProjectCode);
            }

    		$objProject  = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];

    		$proData = array();
            if($objProjectInfo['project_type'] == 1){
                $proData['project_id'] = $projectId;
                $proData['housetop_owner'] = $_POST['housetop_owner']; 
                $proData['company_type'] = $_POST['company_type'];
                $proData['plan_build_volume'] = $_POST['plan_build_volume'];
                $proData['project_area'] = $_POST['county'];
                $proData['project_address'] = $_POST['project_address'];
                $proData['housetop_type'] = $_POST['housetop_type'];
                $proData['housetop_type_other'] = $_POST['housetop_type_other'];
                $proData['synchronize_type'] = $_POST['synchronize_type'];
                $proData['plan_financing'] = $_POST['plan_financing'];
                $proData['financing_type'] = $_POST['financing_type'];
            }elseif($objProjectInfo['project_type'] == 2 || $objProjectInfo['project_type'] == 3){
                $proData['project_id'] = $projectId;
                $proData['plan_build_volume'] = $_POST['plan_build_volume'];
                $proData['project_area'] = $_POST['county'];
                $proData['project_address'] = $_POST['project_address'];
                $proData['project_name'] = $_POST['project_name'];
                $proData['project_finish_date'] = $_POST['project_finish_date'];
                $proData['project_electricity_price'] = $_POST['project_electricity_price'];
                $proData['project_investment'] = $_POST['project_investment'];
                $proData['ground_condition'] = $_POST['ground_condition'];
                $proData['ground_property'] = $_POST['ground_property'];
                $proData['ground_property_other'] = $_POST['ground_property_other'];
                $proData['ground_area'] = $_POST['ground_area'];
                $proData['plan_financing'] = $_POST['plan_financing'];
                $proData['financing_type'] = $_POST['financing_type'];
            }

            $evaData = array();
            $evaData['project_id'] = $projectId;
            $evaData['IRR'] = $_POST['IRR'];
            $evaData['evaluation_result'] = $_POST['evaluation_result'];
            $evaData['static_payback_time'] = $_POST['static_payback_time'];
            $evaData['dynamic_payback_time'] = $_POST['dynamic_payback_time'];
            $evaData['LCOE'] = $_POST['LCOE'];
            $evaData['npv'] = $_POST['npv'];
            $evaData['power_asset_current_value'] = $_POST['power_asset_current_value'];
            $evaData['evaluation_content'] = $_POST['evaluation_content'];
            $evaData['document_review'] = $_POST['document_review'];
            $evaData['project_quality_situation'] = $_POST['project_quality_situation'];
            $evaData['project_invest_situation'] = $_POST['project_invest_situation'];
            $evaData['project_earnings_situation'] = $_POST['project_earnings_situation'];
            $evaData['doc_mul'] = implode(",", $_POST['doc_mul']);

            $projectInfo = $objProject->where("id=".$proData['project_id']." and delete_flag!=9999")->find();
            if($projectInfo['status'] == 21){
                $sta = 23;
            }else{
                $sta = 22;
            }
			$res = $objProject->submitHousetopOrGround($proData, $sta, $objProjectInfo['project_type']);
            if($res == true){
            	$objEvaluation = D("Evaluation", "Service");
            	$res = $objEvaluation->submitEvaluationInfo($evaData);
            	if($res == true){
            		echo '{"code":"0","msg":"success"}';
            	}else{
            		echo '{"code":"-1","msg":"Evaluation更新失败！"}';
            	}
            }else{
            	echo '{"code":"-1","msg":"Housetop更新失败！"}';
            }
    	}elseif($rtype != 1){
            if($projectCode == null){
                $projectCode = $_POST['no'] ? $_POST['no']:$_GET['no'];
                $mProjectCode = $_POST['token'] ? $_POST['token']:$_GET['token'];
                isProjectCodeRight($projectCode, $mProjectCode);
            }

    		$objProject  = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];
    		$projectDetail = $objProject->getProjectInEvaluation($projectId, $objProjectInfo['project_type']);
            $projectDetail['state_type'] = $objProject->getTypeAndStateStr($objProjectInfo['project_type'], $objProjectInfo['build_state']);
            
            $area = D("Area", "Service");
            $areaArray = $area->getAreaArrayById($projectDetail['project_area']);
			
			$objEvaluation = D("Evaluation", "Service");
			$evaluationInfo = $objEvaluation->getEvaluation($projectId);

            $objDoc = D("Doc", "Service");
            $condition['id'] = $projectDetail['picture_full'];
            $picture = $objDoc->getDocInfo($condition);
            $docList = explode(',', $evaluationInfo['doc_mul']);
            $docListInfo = $objDoc->getAllDocInfo($docList);

            if ($_GET['display'] == 'json') {
                header('Content-Type: text/html; charset=utf-8');
                dump($picture);
                dump($docListInfo);
                dump($projectDetail);
                dump($areaArray);
                dump($evaluationInfo);
                exit;
            }
            //给项目进度用,直接截断了,返回json了
            if ($getJsonFlag == 1)
            {
              return array($picture[0]['file_rename'],$docListInfo,$projectDetail,$areaArray,$evaluationInfo);
            }

            $this->assign('picture', $picture[0]['file_rename']);
            $this->assign('docListInfo', $docListInfo);
    		$this->assign('projectDetail', $projectDetail);
            $this->assign('areaArray', $areaArray);
    		$this->assign('evaluationInfo', $evaluationInfo);

            if($objProjectInfo['project_type'] == 1){
                if($objProjectInfo['build_state'] == 1){
                    $this->display("InnerStaff:dueDiligence_housetop_nonbuild");
                }elseif($objProjectInfo['build_state'] == 2){
                    $this->display("InnerStaff:dueDiligence_housetop_build");
                }
            }elseif($objProjectInfo['project_type'] == 2 || $objProjectInfo['project_type'] == 3){
                if($objProjectInfo['build_state'] == 1){
                    $this->display("InnerStaff:dueDiligence_ground_nonbuild");
                }elseif($objProjectInfo['build_state'] == 2){
                    $this->display("InnerStaff:dueDiligence_ground_build");
                }
            }
    	}
    }

    /**
    **@auth qianqiang
    **@breif 客服->项目信息
    **@date 2015.12.26
    **/
    public function projectInfo(){
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2);
        $projectCode  = $_POST['no']     ? $_POST['no']:$_GET['no'];
        $mProjectCode = $_POST['token']  ? $_POST['token']:$_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode);
        $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
        $objProject  = D("Project","Service");
        $projectInfo = $objProject->getProjectInfo($projectCode);
        if($rtype == 1){
            $proData['comment'] = $_POST['comment'];
            // $proData['comment'] = "sldfjiofnosdkfj是的发生的";
            $res = $objProject->saveProjectDetail($projectCode, $projectInfo['project_type'], $proData);
            if($res > 0){
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"0","msg":"保存成功"}';
            }else{
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"保存失败"}';
            }
        }else{
            $common = D("Common","Service");
            //获取项目信息
            $getJsonFlag = 1;
            $obj   = new ProjectProviderMyProController();
            $innerToken = "InternalCall";
            $data = $obj->projectInfoEdit($projectCode, null, $getJsonFlag, $innerToken);
            $data['typeStr'] = $objProject->getTypeAndStateStr($data['project_type'], $data['build_state']);
            $areaObj = D('Area', 'Service');
            $areaInfo = $data['county']?$data['county']:$data['city'];
            $areaInfo = $areaInfo?$areaInfo:$data['province'];
            $data['areaStr'] = $areaObj->getAreaById($areaInfo);
            $data['companyType'] = $common->getProjectCompanyType($data['company_type']);
            $data['housetopType'] = $common->getHousetopType($data['housetop_type']);
            $data['synchronizeType'] = $common->getSynchronizeType($data['synchronize_type']);
            $data['financingType'] = $common->getFinancingType($data['financing_type']);
            $data['electricityClearType'] = $common->getElectricityClearType($data['electricity_clear_type']);
            $data['groundProperty'] = $common->getGroundProperty($data['ground_property']);
            $data['groundCondition'] = $common->getGroundProperty($data['ground_condition']);
            $data['measurePoint'] = $common->getMeasurePoint($data['measure_point']);
            $data['projectHolderType'] = $common->getProjectHolderType($data['project_holder_type']);
            $data['groundProjectType'] = $common->getGroundProjectType($data['ground_project_type']);
            $data['housetopDirection'] = $common->getHousetopDirection($data['housetop_direction']);
            $dataBig  = array('projectInfo' => $data);
            $this->assign('data',$dataBig);
            if ($_GET['display'] == 'json') {
                header('Content-Type: text/html; charset=utf-8');
                dump($dataBig);
                exit;
            }
            if($data['project_type'] == 1){
                if($data['build_state'] == 1){
                    $this->display("InnerStaff:projectInfo_housetop_nonbuild");
                }elseif($data['build_state'] == 2){
                    $this->display("InnerStaff:projectInfo_housetop_build");
                }
            }elseif($data['project_type'] == 2 || $data['project_type'] == 3){
                if($data['build_state'] == 1){
                    $this->display("InnerStaff:projectInfo_ground_nonbuild");
                }elseif($data['build_state'] == 2){
                    $this->display("InnerStaff:projectInfo_ground_build");
                }
            }
        }
        // $projectCode = $_POST['project_code'] ? $_POST['project_code']:$_GET['project_code'];
        /*$projectCode = 'qwertyuio';
        $objProject = D("Project", "Service");
        $projectInfo = $objProject->getProjectInfo($projectCode);
        if($rtype == 1){
            $proData['comment'] = $_POST['comment'];
            // $proData['comment'] = "sldfjiofnosdkfj是的发生的";
            $res = $objProject->saveProjectDetail($projectCode, $projectInfo['project_type'], $proData);
            if($res > 0){
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"0","msg":"保存成功"}';
            }else{
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"保存失败"}';
            }
        }else{
            $projectDetail = $objProject->getProjectDetail($projectInfo['id'], $projectInfo['project_type']);
            if ($_GET['display'] == 'json') {
                header('Content-Type: text/html; charset=utf-8');
                dump($projectDetail);
                exit;
            }
            $this->assign("projectDetail", $projectDetail);
            if($projectInfo['project_type'] == 1){
                if($projectInfo['build_state'] == 1){
                    $this->display("InnerStaff:housetop_nonbuild");
                }elseif($projectInfo['build_state'] == 2){
                    $this->display("InnerStaff:housetop_build");
                }
            }elseif($projectInfo['project_type'] == 2 || $projectInfo['project_type'] == 3){
                if($projectInfo['build_state'] == 1){
                    $this->display("InnerStaff:ground_nonbuild");
                }elseif($projectInfo['build_state'] == 2){
                    $this->display("InnerStaff:ground_build");
                }
            }else{
                // 应该是异常界面
                $this->display("User:login");
            }
        }*/
    }

    /**
    **@auth qianqiang
    **@breif 客服->意向书
    **@date 2015.12.28
    **/
    public function intent(){
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2);
        $projectCode = $_POST['no'] ? $_POST['no']:$_GET['no'];
        $mProjectCode = $_POST['token'] ? $_POST['token']:$_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode);
        $optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
        if($optype == "save" && $rtype == 1){
            $intentText = $_POST["yixiangshu"];
            if($intentText == "" || $intentText == null){
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"意向书不能为空"}';
            }
            $project = D("Project", "Service");
            //echo $projectCode;echo jj;exit;
            $result = $project->saveIntent($projectCode, $intentText);
            if($result === true)
                echo '{"code":"0","msg":"save success"}';
            else
                echo '{"code":"-1","msg":"save error"}';
        }elseif($optype == "submit" && $rtype == 1){
            $intentText = $_POST["yixiangshu"];
            if($intentText == "" || $intentText == null){
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"意向书不能为空"}';
            }
            $project = D("Project", "Service");
            $result = $project->submitIntent($projectCode, $intentText);
            if($result === true)
                echo '{"code":"0","msg":"save success"}';
            else
                echo '{"code":"-1","msg":"save error"}';
        }elseif($rtype != 1){
            $project = D("Project", "Service");
            $projectInfo = $project->getIntent($projectCode);
            //dump($projectInfo);exit;
            if($_GET['display']=="json"){
                header('Content-Type: text/html; charset=utf-8');
                dump($projectInfo);
                exit;
            }
            $this->assign("projectInfo", $projectInfo);
            $this->display("InnerStaff:intent");
        }
    }

    /**
    **@auth qianqiang
    **@breif 客服->推送项目
    **@date 2015.12.30
    **/
    public function pushProject(){
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2);
        $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
        // $projectCode = $_POST['project_code'] ? $_POST['project_code']:$_GET['project_code'];
        $projectCode = $_POST['no'] ? $_POST['no']:$_GET['no'];
        $mProjectCode = $_POST['token'] ? $_POST['token']:$_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode);
        $investors = $_POST['investors'];
        $investorStr = substr($investors, 0, strlen($investors)-1);
        $investorList = explode(",",$investorStr);
        if($rtype == 1){
            $projectObj = D('Project', 'Service');
            $result = $projectObj->pushProject($projectCode, $investorList);
            if($result === true)
                echo '{"code":"0","msg":"push project success"}';
            else
                echo '{"code":"-1","msg":"push project error"}';
        }else{
            $page = $_GET['page'];
            if(empty($page)) $page=1;
            $pageSize = 6;
            $investor = D('User', 'Service');
            $investorList = $investor->getInvestorPush($projectCode, $page);
            $investorTotal = $investor->getInvestorPush($projectCode, -1);
            $data = array();
            $data["list"] = $investorList;
            $data["page"] = $page;
            $data["count"] = sizeof($investorTotal);
            $data["totalPage"] = ceil($data["count"]/$pageSize+1);
            $data["endPage"] = ceil($data["count"]/$pageSize);
            if($_GET['display']=="json"){
                header('Content-Type: text/html; charset=utf-8');
                dump($data);
                exit;
            }
            $this->assign("arrData", $data);
            $this->display("InnerStaff:pushProject");
        }
    }

    /**
    **@auth qianqiang
    **@breif 客服->综合查询
    **@date 2016.1.8
    **/
    public function search(){
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2);
        $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
        $optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $projectObj = D("Project", "Service");
        $userObj = D("User", "Service");
        // if($rtype == 1 && $optype == 'delete'){
        //     $projectCode = $_GET['no'];
        //     $mProjectCode = $_GET['token'];
        //     isProjectCodeRight($projectCode, $mProjectCode);
        //     $condition['status'] = $_POST['status'];
        //     $condition['project_code'] = $projectCode;
        //     $proInfo = $projectObj->where($condition)->find();
        //     $res = $projectObj->deleteProjectService($proInfo['id']);
        //     if($res){
        //         header('Content-Type: text/html; charset=utf-8');
        //         echo '{"code":"0","msg":"删除成功！"}';
        //     }
        // }else
        if($rtype == 1 && $optype == 'change'){
            $projectCode = $_GET['no'];
            $mProjectCode = $_GET['token'];
            // isProjectCodeRight($projectCode, $mProjectCode);
            $oldStatus = $_GET['oldStatus'];
            $status = $_GET['status'];
            if($status == 11){//未提交
                $newStatus = 11;
            }elseif($status == 12){//已提交
                $newStatus = 12;
            }elseif($status == 14){//已尽职调查
                $newStatus = 22;
            }elseif($status == 13){//已签意向书
                $newStatus = 23;
            }elseif($status == 15){//已签融资合同
                $newStatus = 31;
            }
            // $projectCode = 'testintent';
            // $oldStatus = 13;
            // $newStatus = 12;
            $res = $projectObj->changeProjectStatus($projectCode, $oldStatus, $newStatus);
            if($res === true){
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"0","msg":"修改成功！"}';
            }else{
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"'.$res.'"}';
            }
        }else{
            $companyName = $_GET['company_name'];
            $companyType = $_GET['project_type'];
            $situation = $_GET['province'];
            $startDate = $_GET['startDate'];
            $endDate = $_GET['endDate'];
            $status = $_GET['status'];
            $cooperationType = $_GET['cooperation_type'];
            // $companyName = "哈哈哈公司";
            // $companyType = "地面分布式-未建";
            // $situation = '110000';
            // $startDate = '2016-01-01' ;
            // $endDate = '2016-01-11' ;
            // $status = "已签意向书";
            // $cooperationType = "EPC";
            // header('Content-Type: text/html; charset=utf-8');
            // dump($companyName);dump($companyType);
            //     exit;
            $page = $_GET['page'];
            if(empty($page)) $page=1;
            $pageSize = 6;
            // if($rtype == 1){
                $projectList = $projectObj->searchService($companyName, $companyType, $situation, $startDate, $endDate, $status, $cooperationType, $page);
                $projectTotal = $projectObj->searchService($companyName, $companyType, $situation, $startDate, $endDate, $status, $cooperationType, -1);
            // }else{
            //     $projectList = $projectObj->searchService(null, null, null, null, null, null, null, $page);
            //     $projectTotal = $projectObj->searchService(null, null, null, null, null, null, null, -1);
            // }
            $companyNameList = $userObj->getAllCompanyName();
            $data = array();
            $data["list"] = $projectList;
            $data["campanyName"] = $companyNameList;
            $data["page"] = $page;
            $data["count"] = sizeof($projectTotal);
            $data["totalPage"] = ceil($data["count"]/$pageSize+1);
            $data["endPage"] = ceil($data["count"]/$pageSize);
            $data["searchInfo"]["companyName"] = $companyName;
            $data["searchInfo"]["companyType"] = $companyType;
            $data["searchInfo"]["situation"] = $situation;
            $data["searchInfo"]["startDate"] = $startDate;
            $data["searchInfo"]["endDate"] = $endDate;
            $data["searchInfo"]["status"] = $status;
            $data["searchInfo"]["cooperationType"] = $cooperationType;
            if($_GET['display']=="json"){
                header('Content-Type: text/html; charset=utf-8');
                dump($data);
                exit;
            }
            $this->assign("arrData", $data);
            $this->display("InnerStaff:search");
        }
    }

    /**
    **@auth qianqiang
    **@breif 客服->删除项目
    **@date 2016.1.20
    **/
    public function delete(){
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2);
        $projectCode = $_GET['no'];
        $mProjectCode = $_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode);
        $projectObj = D("Project", "Service");
        $condition['project_code'] = $projectCode;
        $condition["delete_flag"] = array('neq',9999);
        $proInfo = $projectObj->where($condition)->find();
        $res = $projectObj->deleteProjectService($proInfo['id']);
        if($res){
            header('Content-Type: text/html; charset=utf-8');
            echo '{"code":"0","msg":"删除成功！"}';
        }else{
            header('Content-Type: text/html; charset=utf-8');
            echo '{"code":"0","msg":"删除失败！"}';
        }
    }
}