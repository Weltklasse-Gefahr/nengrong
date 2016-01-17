<?php
namespace Home\Controller;

use Think\Controller;

class ProjectInvestorMyProController extends Controller {
	/**
    **@auth qianqiang
    **@breif 项目投资方->项目管理->推荐项目
    **@date 2016.1.5
    **/
	public function recommendedProject(){
		isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
		$email = $_COOKIE['email'];
		// $email = 'qianqiang@qq.com';
        isDataComplete($email);
        $page = $_GET['page'];
        if(empty($page)) $page=1;
        $pageSize = 6;
		$objProject = D("Project", "Service");
        $listProject = $objProject->getPushProject($email, $page);
        $listTotal = $objProject->getPushProject($email, -1);
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
        $this->display("projectInvestor:recommendedProject");
	}

	/**
    **@auth qianqiang
    **@breif 项目投资方->项目管理->已投资项目
    **@date 2016.1.5
    **/
	public function investmentProject(){
		isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
		$email = $_COOKIE['email'];
        isDataComplete($email);
		//是否需要做？需要建立项目投资表还是加一个字段
		$this->display("projectInvestor:investmentProject");
	}
/**
    **@auth qiujinhan
    **@breif 项目投资方->查看项目信息进度
    **@date 2016.01.03
    **/
    public function projectInfoView()
    {
        //判断登陆，并且获取用户名的email
        isLogin($_COOKIE['email'],$_COOKIE['mEmail']);     

        //接收参数
        $projectCode  = $_POST['no']     ? $_POST['no']:$_GET['no'];
        $mProjectCode = $_POST['token']  ? $_POST['token']:$_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode);
        $optype       = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $rtype        = $_POST['rtype']  ? $_POST['rtype']:$_GET['rtype'];
        //签署意向书的同意按钮，其实是去project和Housetop两个表中更新下status字段就可以了
        $objProject  = D("Project","Service");
        $getJsonFlag = 1;
        //获取项目信息
        $obj   = new ProjectProviderMyProController();
        $data = $obj->projectInfoEdit($projectCode, null, $getJsonFlag);
        //echo $data['project_type'];exit;
        //获取签署意向书信息 
        if($optype == 'agree' &&  $rtype == 1)
        {
            $res = $objProject->updateProjectStatus($data["id"]);
        }

        $projectInfoForIntent = $objProject->getProjectDetail($data["id"], $data['project_type']);
        $status = $projectInfoForIntent["status"];//
        //echo $projectCode;exit;
        //echo $data['project_type'];exit;
        //echo json_encode($projectInfoForIntent);exit;
        //获取强哥的尽职调查信息
        $obj   = new InnerStaffController();
        list($picture,$docListInfo,$projectDetail,$areaArray,$evaluationInfo) = $obj->dueDiligence($projectCode, null, $getJsonFlag);

        //先判断一下当前进度的状态
        //12项目已提交（客服未提交意向书）、13项目已提交（客服已提交意向书）、
        //21签意向合同（客服未提交尽职调查）、22签意向合同（客服已提交尽职调查）
        //"state":"dueDiligence" // projectInfo, intent, dueDiligence
        //substate":"submited" // wait, submited, signed
        if ($status == 22)
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
        }


        //拼接大json
        $bigArr = array();
        $bigArr['step'] = array();
        $bigArr['step']['state'] = $strStatus;
        $bigArr['step']['substate'] = $substate;
        $bigArr['projectInfo'] = $data;//echo json_encode($bigArr);exit;
        $bigArr['intent'] = $projectInfoForIntent['project_intent'];
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
        //echo json_encode($bigArr);exit;
        $this->assign('data', $bigArr);
        if($data['project_type'] == 1){
            if($data['build_state'] == 1){
                $this->display("ProjectProvider:projectInfoView_housetop_nonbuild");
            }elseif($data['build_state'] == 2){
                $this->display("ProjectProvider:projectInfoView_housetop_build");
            }
        }elseif($data['project_type'] == 2 || $objProjectInfo['project_type'] == 3){
            if($data['build_state'] == 1){
                $this->display("ProjectProvider:projectInfoView_ground_nonbuild");
            }elseif($data['build_state'] == 2){
                $this->display("ProjectProvider:projectInfoView_ground_build");
            }
        }

    }

}