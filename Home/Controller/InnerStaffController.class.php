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
    	$docData['organization_code'] = $docInfo[0]['file_rename'];
    	$condition['id'] = $userInfo['national_tax_certificate'];
    	$docInfo = $docObj->getDocInfo($condition);
    	$docData['national_tax_certificate'] = $docInfo[0]['file_rename'];
    	$condition['id'] = $userInfo['local_tax_certificate'];
    	$docInfo = $docObj->getDocInfo($condition);
    	$docData['local_tax_certificate'] = $docInfo[0]['file_rename'];
    	$condition['id'] = $userInfo['identity_card_front'];
    	$docInfo = $docObj->getDocInfo($condition);
    	$docData['identity_card_front'] = $docInfo[0]['file_rename'];
    	$condition['id'] = $userInfo['identity_card_back'];
    	$docInfo = $docObj->getDocInfo($condition);
    	$docData['identity_card_back'] = $docInfo[0]['file_rename'];
    	$condition['id'] = $userInfo['financial_audit'];
    	$docInfo = $docObj->getDocInfo($condition);
    	$docData['financial_audit'] = $docInfo[0]['file_rename'];

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
    public function dueDiligence(){
    	isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
    	
    	$optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
        if($optype == "upload" && $rtype == 1){
            //上传附件，返回附件id
        }elseif($optype == "save" && $rtype == 1){
    		$projectCode = $_POST['project_code'];
    		$objProject = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];

    		$proData = array();
    		$proData['project_id'] = $projectId;
            $proData['housetop_owner'] = $_POST['housetop_owner']; 
            $proData['company_type'] = $_POST['company_type'];
            $proData['plan_build_volume'] = $_POST['plan_build_volume'];

            $proData['project_area'] = $_POST['project_area'];
            $proData['project_address'] = $_POST['project_address'];

            $proData['housetop_type'] = $_POST['housetop_type'];
            $proData['housetop_type_other'] = $_POST['housetop_type_other'];
            $proData['synchronize_type'] = $_POST['synchronize_type'];
            $proData['plan_financing'] = $_POST['plan_financing'];
            $proData['financing_type'] = $_POST['financing_type'];

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
            $evaData['doc_mul'] = $_POST['doc_mul'];

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
    		$projectCode = $_POST['project_code'];
    		$objProject  = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];

    		$proData = array();
            $proData['project_id'] = $projectId;
            $proData['housetop_owner'] = $_POST['housetop_owner']; 
            $proData['company_type'] = $_POST['company_type'];
            $proData['plan_build_volume'] = $_POST['plan_build_volume'];

            $proData['project_area'] = $_POST['project_area'];
            $proData['project_address'] = $_POST['project_address'];

            $proData['housetop_type'] = $_POST['housetop_type'];
            $proData['housetop_type_other'] = $_POST['housetop_type_other'];
            $proData['synchronize_type'] = $_POST['synchronize_type'];
            $proData['plan_financing'] = $_POST['plan_financing'];
            $proData['financing_type'] = $_POST['financing_type'];

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
            $evaData['doc_mul'] = $_POST['doc_mul'];

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
    		$projectCode = $_POST['project_code'];

    		$objProject  = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];
    		$projectDetail = $objProject->getProjectInEvaluation($projectId, $objProjectInfo['project_type']);
			
			$objEvaluation = D("Evaluation", "Service");
			$evaluationInfo = $objEvaluation->getEvaluation($projectId);

            if ($_GET['display'] == 'json') {
                dump($users);
                exit;
            }

    		$this->assign('projectDetail', $projectDetail);
    		$this->assign('evaluationInfo', $evaluationInfo);
    		$this->display("InnerStaff:dueDiligence");
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