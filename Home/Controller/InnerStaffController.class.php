<?php
namespace Home\Controller;

use Think\Controller;

class InnerStaffController extends Controller {
    
    /**
    **@auth qianqiang
    **@breif 客服->项目提供方信息（账户向详细信息）
    **@date 2015.12.19
    **/
    public function getProjectProviderInfo(){
    	$projectCode = $_POST["projectCode"];
    	$objProject = D("Project", "Service");
    	$objProjectInfo = $objProject->getProjectInfo($projectCode);
    	$providerId = $objProjectInfo['provider_id'];
    	$userObj = D("User", "Service");
    	$userInfo = $userObj->getUserINfoById($providerId);

    	$areaObj = D("Area", "Service");
    	$areaStr = $areaObj->getAreaById($userInfo['company_area']);

    	$docObj = D("Doc", "Service");
    	$condition['id'] = $userInfo['business_license'];
    	$docInfo = $docObj->getDocInfo($condition);
    	$docData['business_license'] = $docInfo[0]['file_rename'];
    	$condition['id'] = $userInfo['organization_code'];
    	$docInfo = $docObj->getDocInfo($condition);
    	$docData['organization_code'] = $docInfo['file_rename'];
    	$condition['id'] = $userInfo['national_tax_certificate'];
    	$docInfo = $docObj->getDocInfo($condition);
    	$docData['national_tax_certificate'] = $docInfo['file_rename'];
    	$condition['id'] = $userInfo['local_tax_certificate'];
    	$docInfo = $docObj->getDocInfo($condition);
    	$docData['local_tax_certificate'] = $docInfo['file_rename'];
    	$condition['id'] = $userInfo['identity_card_front'];
    	$docInfo = $docObj->getDocInfo($condition);
    	$docData['identity_card_front'] = $docInfo['file_rename'];
    	$condition['id'] = $userInfo['identity_card_back'];
    	$docInfo = $docObj->getDocInfo($condition);
    	$docData['identity_card_back'] = $docInfo['file_rename'];
    	$condition['id'] = $userInfo['financial_audit'];
    	$docInfo = $docObj->getDocInfo($condition);
    	$docData['financial_audit'] = $docInfo['file_rename'];

    	$this->assign('userInfo', $userInfo);
    	$this->assign('areaStr', $areaStr);
    	$this->assign('docData', $docData);
    	$this->display("InnerStaff:providerInfo");
    }

    /**
    **@auth qianqiang
    **@breif 客服->尽职调查
    **@date 2015.12.19
    **/
    public function doEvaluation(){
    	isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
    	
    	$optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
    	if($optype == "save" && $rtype == 1){
    		$projectCode = $_POST['projectCode'];
    		$objProject = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];

    		$proData = array();
    		$proData['project_id'] = $projectId;
            $proData['housetop_owner'] = $_POST['housetopOwner']; 
            $proData['company_type'] = $_POST['companyType'];
            $proData['plan_build_volume'] = $_POST['planBuildVolume'];

            // 项目地点是什么内容？？？
            $proData['project_area'] = $_POST['project_area'];
            $proData['project_address'] = $_POST['project_address'];

            $proData['housetop_type'] = $_POST['housetopType'];
            $proData['housetop_type_other'] = $_POST['housetop_type_other'];
            $proData['synchronize_type'] = $_POST['synchronizeType'];
            $proData['plan_financing'] = $_POST['planFinancing'];
            $proData['financing_type'] = $_POST['financingType'];

            $evaData = array();
            $evaData['project_id'] = $projectId;
            $evaData['IRR'] = $_POST['IRR'];
            $evaData['evaluation_result'] = $_POST['evaluationResult'];
            $evaData['static_payback_time'] = $_POST['staticPaybackTime'];
            $evaData['dynamic_payback_time'] = $_POST['dynamicPaybackTime'];
            $evaData['LCOE'] = $_POST['LCOE'];
            $evaData['npv'] = $_POST['npv'];
            $evaData['power_asset_current_value'] = $_POST['powerAssetCurrentValue'];
            $evaData['evaluation_content'] = $_POST['evaluationContent'];
            $evaData['document_review'] = $_POST['documentReview'];
            $evaData['project_quality_situation'] = $_POST['projectQualitySituation'];
            $evaData['project_invest_situation'] = $_POST['projectInvestSituation'];
            $evaData['project_earnings_situation'] = $_POST['projectEarningsSituation'];

            // $objProject = D("Project", "Service");
            $res = $objProject->saveHousetopProject($proData);
            if($res == true){
            	$objEvaluation = D("Evaluation", "Service");
            	$res = $objEvaluation->saveEvaluationInfo($evaData);
            	if($res == true){
            		echo '{"code":"0","msg":"success"}';
            	}else{
            		echo '{"code":"-1","msg":"更新失败！"}';
            	}
            }else{
            	echo '{"code":"-1","msg":"更新失败！"}';
            }
    	}elseif($optype == "commit" && $rtype == 1){
    		$projectCode = $_POST['projectCode'];
    		$objProject  = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];

    		$proData = array();
            $proData['housetopOwner'] = $_POST['housetopOwner']; 
            $proData['companyType'] = $_POST['companyType'];
            $proData['planBuildVolume'] = $_POST['planBuildVolume'];

            // 项目地点是什么内容？？？
            $proData['project_area'] = $_POST['project_area'];
            $proData['project_address'] = $_POST['project_address'];

            $proData['housetopType'] = $_POST['housetopType'];
            $proData['synchronizeType'] = $_POST['synchronizeType'];
            $proData['planFinancing'] = $_POST['planFinancing'];
            $proData['financingType'] = $_POST['financingType'];

            $evaData = array();
            $evaData['IRR'] = $_POST['IRR'];
            $evaData['evaluationResult'] = $_POST['evaluationResult'];
            $evaData['staticPaybackTime'] = $_POST['staticPaybackTime'];
            $evaData['dynamicPaybackTime'] = $_POST['dynamicPaybackTime'];
            $evaData['LCOE'] = $_POST['LCOE'];
            $evaData['npv'] = $_POST['npv'];
            $evaData['powerAssetCurrentValue'] = $_POST['powerAssetCurrentValue'];
            $evaData['evaluationContent'] = $_POST['evaluationContent'];
            $evaData['documentReview'] = $_POST['documentReview'];
            $evaData['projectQualitySituation'] = $_POST['projectQualitySituation'];
            $evaData['projectInvestSituation'] = $_POST['projectInvestSituation'];
            $evaData['projectEarningsSituation'] = $_POST['projectEarningsSituation'];

			$res = $objProject->submitHousetopProject($proData);
            if($res == true){
            	$objEvaluation = D("Evaluation", "Service");
            	$res = $objEvaluation->submitEvaluationInfo($evaData);
            	if($res == true){
            		echo '{"code":"0","msg":"success"}';
            	}else{
            		echo '{"code":"-1","msg":"更新失败！"}';
            	}
            }else{
            	echo '{"code":"-1","msg":"更新失败！"}';
            }
    	}elseif($rtype != 1){
    		//带修改，每次进来应该把保存的记录读取出来，没有保存记录就读取提交记录

    		$projectCode = $_POST['projectCode'];

    		$objProject  = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];
    		$projectDetail = $objProject->getProjectInEvaluation($projectId, $objProjectInfo['project_type']);
			
			$objEvaluation = D("Evaluation", "service");
			$evaluationInfo = $objEvaluation->getEvaluation($projectId);

            if ($_GET['display'] == 'json') {
                dump($users);
                exit;
            }

    		$this->assign('projectDetail', $projectDetail);
    		$this->assign('evaluationInfo', $evaluationInfo);
    		$this->display("InnerStaff:jzdc");
    	}
    }

    /**
    **@auth qianqiang
    **@breif 客服->项目信息
    **@date 2015.12.26
    **/
    public function projectInfo(){
    	$this->display("InnerStaff:projectInfo");
    }
}