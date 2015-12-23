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
    	
    	$optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
    	if($optype == "save" && $rtype == 1){
    		$projectCode = $_POST['projectCode'];
    		$objProject  = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];

    		$proData = array();
    		$proData['projectId'] = $projectId;
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
            $evaData['projectId'] = $projectId;
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

            //提交后应该把保存的记录复制到提交的记录中，并将保存的记录删除
            //如果没有保存记录，就直接存储

            if ($res == true)
            {
                echo '{"code":"0","msg":"succ"}';
            }
            else
            {
                echo '{"code":"-1","msg":"更新失败！"}';
            }
    	}elseif($rtype != 1){
    		//带修改，每次进来应该把保存的记录读取出来，没有保存记录就读取提交记录

    		$projectCode = $_POST['projectCode'];

    		$objProject  = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];
    		$projectDetail = $objProject->getProjectDetail($projectId, $objProjectInfo['project_type']);
			
			$objEvaluation = D("Evaluation", "service");
			$evaluationInfo = $objEvaluation->getEvaluationInfo($projectId);

            if ($_GET['display'] == 'json') {
                dump($users);
                exit;
            }

    		$this->assign('projectDetail', $projectDetail);
    		$this->assign('evaluationInfo', $evaluationInfo);
    		$this->display("InnerStaff:jzdc");
    	}
    }


}