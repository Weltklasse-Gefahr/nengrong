<?php
namespace Home\Service;

use Think\Model;

class ProjectService extends Model{

    /**
    **@auth qiujinhan@gmail.com
    **@breif 根据项目编码获取项目信息
    **@date 2015.12.05
    **/	
	public function getProjectInfo($projectCode){
		$objProject = D("Project");
        $condition["project_code"] = $projectCode;
        $projectInfo = $objProject->where($condition)->select();
        return $projectInfo[0];
	}

    /**
    **@auth qianqiang
    **@breif 根据项目ID和项目类型获取项目详细信息
    **@date 2015.12.19
    **/ 
    public function getProjectDetail($projectId, $projectType){
        if($projectType == 1){
            $housetop = M("Housetop");
            $housetopInfo = $housetop->where("project_id='%s' and delete_flag!=9999", $projectId)->select();
            return $housetopInfo[0];
        }elseif($projectType == 2 || $projectType == 3){
            $ground = M("Ground");
            $groundInfo = $ground->where("project_id='%s' and delete_flag!=9999", $projectId)->select();
            return $groundInfo[0];
        }
    }

    /**
    **@auth qianqiang
    **@breif 根据项目ID,项目类型和项目状态获取项目详细信息
    **@date 2015.12.29
    **/ 
    public function getProjectDetails($projectId, $status, $projectType){
        if($projectType == 1){
            $housetop = M("Housetop");
            $condition['project_id'] = $projectId;
            $condition['status'] = array("in","$status");
            //echo json_encode($condition['status']);exit;
            $condition["delete_flag"] = array('neq',9999);
            $housetopInfo = $housetop->where($condition)->select();
            return $housetopInfo[0];
        }elseif($projectType == 2 || $projectType == 3){
            $ground = M("Ground");
            $condition['project_id'] = $projectId;
            $condition['status'] = array("in","$status");
            $condition["delete_flag"] = array('neq',9999);
            $groundInfo = $ground->where($condition)->select();
            return $groundInfo[0];
        }
    }

    /**
    **@auth qianqiang
    **@breif 查看尽职调查中的项目信息，如果有保存的返回保存的数据，没有保存的返回正常数据
    **@date 2015.12.25
    **/ 
    public function getProjectInEvaluation($projectId, $projectType){
        if($projectType == 1){
            $housetop = M("Housetop");
            $condition['project_id'] = $projectId;
            $condition['status'] = 51;
            $condition["delete_flag"] = array('neq',9999);
            $housetopInfo = $housetop->where($condition)->select();
            if(sizeof($housetopInfo) > 0){
                $condition['status'] = array('in','12,13,21,22,23');
                $housetopInfo[0]['picture_full'] = $housetop->where($condition)->getField('picture_full');
                return $housetopInfo[0];
            }
            else{
                $condition['status'] = array('in','12,13,21,22,23');
                $housetopInfo = $housetop->where($condition)->select();
                return $housetopInfo[0];
            }
        }elseif($projectType == 2 || $projectType == 3){
            $ground = M("Ground");
            $condition['project_id'] = $projectId;
            $condition['status'] = 51;
            $condition["delete_flag"] = array('neq',9999);
            $groupInfo = $ground->where($condition)->select();
            if(sizeof($groupInfo) > 0){
                $condition['status'] = array('in','12,13,21,22,23');
                $groupInfo[0]['picture_full'] = $ground->where($condition)->getField('picture_full');
                return $groupInfo[0];
            }else{
                $condition['status'] = array('in','12,13,21,22,23');
                $groupInfo = $ground->where($condition)->select();
                return $groupInfo[0];
            }
        }
    }

    /**
    **@auth qianqiang
    **@breif 查看意向书，如果有保存的返回保存的数据
    **@return 返回项目详细信息
    **@date 2015.12.29
    **/ 
    public function getIntent($projectCode){
        $projectInfo = $this->getProjectInfo($projectCode);
        $condition['project_id'] = $projectInfo['id'];
        $condition['status'] = 61;
        $condition["delete_flag"] = array('neq',9999);
        if($projectInfo['project_type'] == 1){
            $housetop = M('Housetop');
            $resInfo = $housetop->where($condition)->find();
            if(!empty($resInfo)){
                $condition['status'] = array('neq',61);
                $resInfo['display_flag'] = $housetop->where($condition)->getField('status');
            }
        }elseif($projectInfo['project_type'] == 2 || $projectInfo['project_type'] == 3){
            $ground = M('Ground');
            $resInfo = $ground->where($condition)->find();
            if(!empty($resInfo)){
                $condition['status'] = array('neq',61);
                $resInfo['display_flag'] = $ground->where($condition)->getField('status');
            }
        }
        if(empty($resInfo)){
            $resInfo = $this->getProjectDetail($projectInfo['id'], $projectInfo['project_type']);
            $resInfo['display_flag'] = $resInfo['status'];
            return $resInfo;
        }else{
            return $resInfo;
        }
    }

    /**
    **@auth qianqiang
    **@breif 查询待评估项目
    **@date 2015.12.26
    **/
    public function getAwaitingAssessment($email, $filter, $page){
        if(!empty($email)){
            $user = D('User');
            //echo $email;
            $userInfo = $user->where("email='".$email."' and delete_flag!=9999")->find();
            //echo json_encode($userInfo);exit;
            $condition['provider_id'] = $userInfo['id'];
        }
        if($filter == "committed"){
            $condition['status'] = array('in','12,13,22');
        }elseif($filter == "uncommitted"){
            $condition['status'] = 11;
        }else{
            $condition['status'] = array('in','11,12,13,22');
        }
        $condition["delete_flag"] = array('neq',9999);
        $projectInfo = $this->getProjectsInfo($condition, $page, 6);
        //echo json_encode($projectInfo);exit;
        $projectList = $this->formatProject($projectInfo);
        return $projectList; 
    }

    /**
    **@auth qiujinhan
    **@breif 删除旧的组件信息，然后重新插入
    **@param project_id 项目id
    **@date 2015.12.24
    **/ 
    public function addComponent($project_id){
        //删除旧的组件信息
        $obj = M("Component");
        $condition['project_id'] = $project_id;
        $obj->where($condition)->delete();

        //重新插入新的组件信息
        $arrHiddenId = $_POST['component_company_hiddenId'];
        $index = 0;
        $ArrCompany = $_POST['component_company'];
        $ArrType    = $_POST['component_type'];
        $ArrCount   = $_POST['component_count'];
        //var_dump($arrHiddenId);var_dump($ArrType);
        foreach($ArrCompany as $value)
        {
            $data['project_id'] = $project_id;
            $data['component_company'] = $ArrCompany[$index];
            $data['component_type'] = $ArrType[$index];
            $data['component_count'] = $ArrCount[$index];
            $result = $obj->add($data);
        }
        return $result;
    }  
    /**
    **@auth qiujinhan
    **@breif 删除项目
    **@param projectCode 项目id
    **@date 2015.12.24
    **/ 
    public function deleteProject($projectCode){
        //删除旧的组件信息
        $obj = M("Project");
        $condition['project_code'] = $projectCode;
        $res = $obj->where($condition)->select();
        
        return $this->deleteProjectService($res[0]['id']);
    }

    /**
    **@auth qiujinhan
    **@breif 获取组件信息
    **@param project_id 项目id
    **@date 2015.12.24
    **/ 
    public function getComponent($project_id){
        //删除旧的组件信息
        $obj = M("Component");
        $condition['project_id'] = $project_id;
        $condition["delete_flag"] = array('neq',9999);
        $ret = $obj->where($condition)->select();
        return $ret;
    }  
    /**
    **@auth qiujinhan
    **@breif 获取逆变器信息
    **@param project_id 项目id
    **@date 2015.12.24
    **/ 
    public function getInverter($project_id){
        //删除旧的组件信息
        $obj = M("Inverter");
        $condition['project_id'] = $project_id;
        $condition["delete_flag"] = array('neq',9999);
        $ret = $obj->where($condition)->select();
        return $ret;
    }  


    /**
    **@auth qiujinhan
    **@breif 删除旧的逆变器信息，然后重新插入
    **@param project_id 项目id
    **@date 2015.12.24
    **/ 
    public function addInverter($project_id){
        //删除旧的组件信息
        $obj = M("Inverter");
        $condition['project_id'] = $project_id;
        $obj->where($condition)->delete();

        //重新插入新的组件信息
        $arrHiddenId = $_POST['inverter_company_hiddenId'];
        $index = 0;
        $ArrCompany = $_POST['inverter_company'];
        $ArrType    = $_POST['inverter_type'];
        $ArrCount   = $_POST['inverter_count'];
        foreach($ArrCompany as $value)
        {
            $data['project_id'] = $project_id;
            $data['inverter_company'] = $ArrCompany[$index];
            $data['inverter_type'] = $ArrType[$index];
            $data['inverter_count'] = $ArrCount[$index];
            $result = $obj->add($data);
        }
        return $result;
    }   

    /**
    **@auth qianqiang
    **@breif 查询已签意向书项目
    **@date 2015.12.24
    **/ 
    public function getAgreementProject($email, $page){
        if(!empty($email)){
            $user = D('User');
            $userInfo = $user->where("email='".$email."' and delete_flag!=9999")->find();
            $condition['provider_id'] = $userInfo['id'];
        }
        $condition['status'] = array('in','21,23');
        $condition["delete_flag"] = array('neq',9999);
        $projectInfo = $this->getProjectsInfo($condition, $page, 6);
        $projectList = $this->formatProject($projectInfo);
        return $projectList; 
    }

    /**
    **@auth qianqiang
    **@breif 查询已签融资合同项目
    **@date 2015.12.24
    **/ 
    public function getContractProject($email, $page){
        if(!empty($email)){
            $user = D('User');
            $userInfo = $user->where("email='".$email."' and delete_flag!=9999")->find();
            $condition['provider_id'] = $userInfo['id'];
        }
        $condition['status'] = array('in','31');
        $condition["delete_flag"] = array('neq',9999);
        $projectInfo = $this->getProjectsInfo($condition, $page, 6);
        $projectList = $this->formatProject($projectInfo);
        return $projectList; 
    }

    /**
    **@auth qianqiang
    **@breif 查询推荐项目
    **@date 2016.1.5
    **/ 
    public function getPushProject($email, $page){
        if(!empty($email)){
            $user = D('User');
            $userInfo = $user->where("email='".$email."' and delete_flag!=9999")->find();
            $condition['investor_id'] = $userInfo['id'];
        }else{
            return null;
        }
        $condition["delete_flag"] = array('neq',9999);
        $pushPro = M('Pushproject');
        if($page == -1){
            $pushProInfo = $pushPro->where($condition)->order('highlight_flag desc, push_time desc')->select();
        }else{
            $pushProInfo = $pushPro->where($condition)->order('highlight_flag desc, push_time desc')->page($page, 6)->select();
        }
        $projectInfo = $this->getProTypeListFromPushPro($pushProInfo);
        $projectList = $this->formatProject($projectInfo);
        return $projectList; 
    }

    /**
    **@auth qianqiang
    **@breif 获取推荐项目的项目类型，存入推荐项目列表中
    **@date 2016.1.5
    **/ 
    public function getProTypeListFromPushPro($pushProInfo){
        if(empty($pushProInfo)) return null;
        $projectObj = M('Project');
        $i = 0;
        while($pushProInfo[$i]){
            $condition['project_code'] = $pushProInfo[$i]['project_code'];
            $condition["delete_flag"] = array('neq',9999);
            $projectInfo = $projectObj->where($condition)->find();
            $pushProInfo[$i]['project_type'] = $projectInfo['project_type'];
            $pushProInfo[$i]['status'] = $projectInfo['status'];
            $i += 1;
        }
        return $pushProInfo;
    }

    /**
    **@auth qianqiang
    **@breif 将项目列表中的信息规范化显示,添加加密后的项目编码、中文状态、中文项目类型
    **@date 2015.12.30
    **/ 
    public function formatProject($projectList){
        if(empty($projectList)) return $projectList;
        $i = 0;
        while($projectList[$i]){
            $projectList[$i]['m_project_code'] = md5(addToken($projectList[$i]['project_code']));
            
            if($projectList[$i]['delete_flag'] == 9999){
                $projectList[$i]['statusStr'] = "已删除";
            }elseif($projectList[$i]['status'] == 11){
                $projectList[$i]['statusStr'] = "未提交";
            }elseif($projectList[$i]['status'] == 12 || $projectList[$i]['status'] == 13){
                $projectList[$i]['statusStr'] = "已提交";
            }elseif($projectList[$i]['status'] == 21 || $projectList[$i]['status'] == 23){
                $projectList[$i]['statusStr'] = "已签意向合同";
            }elseif($projectList[$i]['status'] == 52 || $projectList[$i]['status'] == 22){
                $projectList[$i]['statusStr'] = "已尽职调查";
            }elseif($projectList[$i]['status'] == 31){
                $projectList[$i]['statusStr'] = "已签融资合同";
            }elseif($projectList[$i]['status'] == 41){
                $projectList[$i]['statusStr'] = "已推送";
            }else{
                $projectList[$i]['statusStr'] = "其他";
            }

            if($projectList[$i]['project_type'] == 1){
                $projectList[$i]['type'] = "屋顶分布式";
                $proObj = M('Housetop');
            }elseif($projectList[$i]['project_type'] == 2){
                $projectList[$i]['type'] = "地面分布式";
                $proObj = M('Ground');
            }elseif($projectList[$i]['project_type'] == 3){
                $projectList[$i]['type'] = "大型地面";
                $proObj = M('Ground');
            }
            if($projectList[$i]['build_state'] == 1){
                $projectList[$i]['type'] = "未建-".$projectList[$i]['type'];
            }elseif($projectList[$i]['build_state'] == 2){
                $projectList[$i]['type'] = "已建-".$projectList[$i]['type'];
            }

            if(!empty($projectList[$i]['create_date'])){
                $projectList[$i]['create_date'] = date('Y-m-d', strtotime($projectList[$i]['create_date']));
            }
            if(!empty($projectList[$i]['push_time'])){
                $projectList[$i]['push_time'] = date('Y-m-d', strtotime($projectList[$i]['push_time']));
            }

            $condition['project_id'] = $projectList[$i]['id'];
            $condition['status'] = $projectList[$i]['status'];
            $condition['delete_flag'] = array('neq',9999);
            $proDetails = $proObj->where($condition)->find();
            $areaObj = D('Area', 'Service');
            $areaStr = $areaObj->getAreaById($proDetails['project_area']);
            $projectList[$i]['area'] = $areaStr.$proDetails['project_address'];
            $i += 1;
        }        
        return $projectList; 
    }

    /**
    **@auth qianqiang
    **@breif 保存意向书     先获取要存储的数据，然后将意向书存入要插入的数据中，保存
    **@return 成功返回true，失败返回false
    **@date 2015.12.29
    **/ 
    public function saveIntent($projectCode, $intentText){
        $projectInfo = $this->getProjectInfo($projectCode);
        $flag = $this->isIntentProject($projectInfo['id'], $projectInfo['project_type']);
        if($flag === false){
            echo '{"code":"-1","msg":"status error, cannot save intent"}';
            exit;
        }
        if($this->hasSaveHousetopOrGround($projectInfo['id'], 61, $projectInfo['project_type'])){
            $projectDetails = $this->getProjectDetails($projectInfo['id'], 61, $projectInfo['project_type']);//61意向书保存状态（项目已提交）
        }else{
            $projectDetails = $this->getProjectDetails($projectInfo['id'], 12, $projectInfo['project_type']);//12项目已提交（客服未提交意向书）
            $projectDetails['id'] = null;
        }
        $projectDetails['project_intent'] = $intentText;
        $result = $this->saveHousetopOrGround($projectDetails, 61, $projectInfo['project_type']);
        return $result;
    }

    /**
    **@auth qianqiang
    **@breif 提交意向书：更新housetop/ground、project表，如果有保存的意向书则删除
    **@return 成功返回true，失败返回false
    **@date 2015.12.29
    **/ 
    public function submitIntent($projectCode, $intentText){
        $projectInfo = $this->getProjectInfo($projectCode);
        if($projectInfo['status'] != 22){
            echo '{"code":"-1","msg":"请先提交尽职调查，再提交意向书"}';
            exit;
        }
        $flag = $this->isIntentProject($projectInfo['id'], $projectInfo['project_type']);
        if($flag === false){
            echo '{"code":"-1","msg":"status error, cannot submit intent"}';
            exit;
        }
        $projectDetails = $this->getProjectDetails($projectInfo['id'], "12,22", $projectInfo['project_type']);//12项目已提交（客服未提交意向书）
        $projectDetails['project_intent'] = $intentText;
        $projectDetails['status'] = 13;
        $projectDetails['change_date'] = date("Y-m-d H:i:s",time());
        if($projectInfo['project_type'] == 1){
            $housetop = M('Housetop');
            $housetopResult = $housetop->where("project_id='".$projectDetails['project_id']."' and (status=12 or status=22) and delete_flag!=9999")->save($projectDetails);
            if($housetopResult == 0) return false;
            if($this->hasSaveHousetopOrGround($projectInfo['id'], 61, $projectInfo['project_type'])){
                $condition['project_id'] = $projectDetails['project_id'];
                $condition['status'] = 61;
                $housetop->where($condition)->delete();
            }
        }elseif($projectInfo['project_type'] == 2 || $projectInfo['project_type'] == 3){
            $ground = M('Ground');
            $groundResult = $ground->where("project_id='".$projectDetails['project_id']."' and (status=12 or status=22) and delete_flag!=9999")->save($projectDetails);
            if($groundResult == 0) return false;
            if($this->hasSaveHousetopOrGround($projectInfo['id'], 61, $projectInfo['project_type'])){
                $condition['project_id'] = $projectDetails['project_id'];
                $condition['status'] = 61;
                $ground->where($condition)->delete();
            }
        }
        $project = M("Project");
        $data['status'] = 13;
        $data['highlight_flag'] = 1;
        $data['change_date'] = date("Y-m-d H:i:s",time());
        $projectResult = $project->where("id='".$projectInfo['id']."' and (status=12 or status=22) and delete_flag!=9999")->save($data);
        if($projectResult == 0) return false;
        return true;
    }

    /**
    **@auth qianqiang
    **@breif 尽职调查保存时，保存项目信息
    **@return 保存成功返回true，失败返回false
    **@date 2015.12.23
    **/ 
    public function saveHousetopProject($proData){
        $housetop = M("Housetop");
        if($this->hasSaveHousetopProject($proData['project_id'])){
            $proData['change_date'] = date("Y-m-d H:i:s",time());
            $result = $housetop->where("project_id='".$proData['project_id']."' and status=51")->save($proData);
        }else{
            $proData['status'] = 51;
            $proData['create_date'] = date("Y-m-d H:i:s",time());
            $proData['change_date'] = date("Y-m-d H:i:s",time());
            $result = $housetop->add($proData);
        }
        if($result == false)
            return false;
        else
            return true;
    }

    /**
    **@auth qianqiang  
    **@breif 保存house或者ground（保存状态，包括保存尽职调查，保存意向书），如果已经存在就更新，不存在就插入
    **@param $proData 保存的数据
    **@param $status 保存的项目状态，如：进行尽职调查保存，需要传参数为51
    **@param $projectType 项目类型
    **@return 保存成功返回true，失败返回false
    **@date 2015.12.29
    **/ 
    public function saveHousetopOrGround($proData, $status, $projectType, $oldStatus=null){
        $result = false;
        if($projectType == 1){
            $housetop = M("Housetop");
            if($this->hasSaveHousetopOrGround($proData['project_id'], $status, $projectType, $oldStatus)){
                $condition['project_id'] = $proData['project_id'];
                $condition['status'] = $status;
                //避免项目提供方点击提交的时候，重复插入问题，比较奇怪的代码
                if($oldStatus == "11")
                {
                     $condition["status"] = $oldStatus;
                }
                $proData['change_date'] = date("Y-m-d H:i:s",time());
                $result = $housetop->where($condition)->save($proData);
            }else{
                $proData['status'] = $status;
                $proData['create_date'] = date("Y-m-d H:i:s",time());
                $proData['change_date'] = date("Y-m-d H:i:s",time());
                $result = $housetop->add($proData);
            }
        }elseif($projectType == 2 || $projectType == 3){
            $ground = M("Ground");
            if($this->hasSaveHousetopOrGround($proData['project_id'], $status, $projectType, $oldStatus)){
                $condition['project_id'] = $proData['project_id'];
                $condition['status'] = $status;
                //避免项目提供方点击提交的时候，重复插入问题，比较奇怪的代码
                if($oldStatus == "11")
                {
                     $condition["status"] = $oldStatus;
                }
                $proData['change_date'] = date("Y-m-d H:i:s",time());
                $result = $ground->where($condition)->save($proData);
            }else{
                $proData['status'] = $status;
                $proData['create_date'] = date("Y-m-d H:i:s",time());
                $proData['change_date'] = date("Y-m-d H:i:s",time());
                $result = $ground->add($proData);
            }
        }
        if($result == false)
            return false;
        else
            return true;
    }

    /**
    **@auth qiujinhan
    **@breif 判断house表和ground表是否存在保存状态，如保存意向书、保存尽职调查
    **@param $projectId 项目id
    **@param $status 要查询的项目状态
    **@param $projectType 项目类型
    **@return 存在返回true，不存在返回false
    **@date 2015.12.29
    **/ 
    public function hasSaveHousetopOrGround($projectId, $status, $projectType, $oldStatus = null){
        $condition["project_id"] = $projectId;
        $condition["status"] = $status;
        //避免项目提供方点击提交的时候，重复插入问题，比较奇怪的代码
        if($oldStatus == "11")
        {
             $condition["status"] = $oldStatus;
        }
        $condition["delete_flag"] = array('neq',9999);
        if($projectType == 1){
            $housetop = M("Housetop");
            $proInfo = $housetop->where($condition)->select();
        }elseif($projectType == 2 || $projectType == 3){
            $ground = M("Ground");
            $proInfo = $ground->where($condition)->select();
        }
        if(empty($proInfo))
            return false;
        else
            return true;
    }


    /**
    **@auth qianqiang
    **@breif 提交尽职调查：更新housetop、project表，如果有保存的尽职调查则删除
    **@return 提交成功返回true，失败返回false
    **@date 2015.12.23
    **/ 
    public function submitHousetopProject($proData){
        //更新项目资料housetop表project表
        //如果有save数据，进行删除
        $housetop = M("Housetop");
        $proData['status'] = 22;
        $proData['change_date'] = date("Y-m-d H:i:s",time());
        $housetopResult = $housetop->where("project_id='".$proData['project_id']."' and (status=21 or status=22)")->save($proData);
        // echo "@@:".$housetopResult;exit;
        if($housetopResult > 0){
            $project = M("Project");
            $data['status'] = 22;
            $data['change_date'] = date("Y-m-d H:i:s",time());
            $projectResult = $project->where("id='".$proData['project_id']."' and (status=21 or status=22)")->save($data);
            if($this->hasSaveHousetopProject($proData['project_id'])){
                $condition['project_id'] = $proData['project_id'];
                $condition['status'] = 51;
                $housetop->where($condition)->delete();
            }
            return true;
        }else{
            return false;
        }
    }

    /**
    **@auth qianqiang  
    **@breif 提交house或者ground，更新项目资料ground表project表，如果有save数据，进行删除
    **@param $proData 提交的数据
    **@param $status 提交的项目状态(要改成什么状态)，如：进行尽职调查保存，需要传参数为51
    **@param $projectType 项目类型
    **@return 提交成功返回true，失败返回false
    **@date 2015.12.29
    **/ 
    public function submitHousetopOrGround($proData, $status, $projectType){
        if($projectType == 1){
            $housetop = M("Housetop");
            $proData['status'] = $status;
            $proData['change_date'] = date("Y-m-d H:i:s",time());
            $result = $housetop->where("project_id='".$proData['project_id']."' and status!=51 and status!=61 and delete_flag!=9999")->save($proData);
            if($this->hasSaveHousetopOrGround($proData['project_id'], 51, $projectType)){
                $condition['project_id'] = $proData['project_id'];
                $condition['status'] = 51;//echo jj;exit;
                $housetop->where($condition)->delete();
            }
        }elseif($projectType == 2 || $projectType == 3){
            $ground = M("Ground");
            $proData['status'] = $status;
            $proData['change_date'] = date("Y-m-d H:i:s",time());
            $result = $ground->where("project_id='".$proData['project_id']."' and status!=51 and status!=61 and delete_flag!=9999")->save($proData);
            if($this->hasSaveHousetopOrGround($proData['project_id'], 51, $projectType)){
                $condition['project_id'] = $proData['project_id'];
                $condition['status'] = 51;
                $ground->where($condition)->delete();
            }
        }//echo qq;exit;
        if($result > 0){
            $project = M("Project");
            $data['status'] = $status;
            // if($status == 22){
                $data['highlight_flag'] = 1;
            // }
            $data['change_date'] = date("Y-m-d H:i:s",time());
            $projectResult = $project->where("id='".$proData['project_id']."' and status!=51 and status!=61 and delete_flag!=9999")->save($data);
            return true;
        }else{
            return false;
        }
    }

    /**
    **@auth qianqiang
    **@breif 获取某项目的推送记录
    **@date 2015.12.30
    **/ 
    public function getPushProjectByProCode($projectCode){
        $pushProject = D("Pushproject");
        $condition["project_code"] = $projectCode;
        $condition["status"] = 41;
        $condition["delete_flag"] = array('neq',9999);
        $proInfo = $pushProject->where($condition)->select();
        return $proInfo;
    }

    /**
    **@auth qianqiang
    **@breif 推送项目
    **@date 2015.12.30
    **/ 
    public function pushProject($projectCode, $investorList){
        // if($this->isPushProject($projectCode) == false){
        //     echo '{"code":"-1","msg":"该项目不能进行推送操作"}';
        //     exit;
        // }
        $pushProject = D("Pushproject");
        $data = array();
        $data['project_code'] = $projectCode;
        $data['highlight_flag'] = 1;
        $data['status'] = 41;
        $data['push_time'] = date("Y-m-d H:i:s",time());
        $i = 0;
        while($investorList[$i]){
            $data['investor_id'] = $investorList[$i]['id'];
            $res = $pushProject->add($data);
            if($res === false) return false;
            $i += 1;
        }
        return true;
    }

    /**
    **@auth qianqiang
    **@breif 判断项目是否可以进行推送,签署意向书后可进行推送操作
    **@return 可以推送返回true，不可以推送返回false
    **@date 2015.12.30
    **/ 
    public function isPushProject($projectCode){
        $projectObj = M("Project");
        $condition["project_code"] = $projectCode;
        $condition["status"] = array('in','21,23');
        $condition["delete_flag"] = 0;
        $res = $projectObj->where($condition)->select();
        if(empty($res)) 
            return false;
        else
            return true;
    }

    /**
    **@auth qianqiang
    **@breif 判断是否有保存的屋顶项目
    **@return 存在返回true，不存在返回false
    **@date 2015.12.23
    **/ 
    public function hasSaveHousetopProject($projectId){
        $objProject = D("Housetop");
        $condition["project_id"] = $projectId;
        $condition["status"] = 51;
        $condition["delete_flag"] = array('neq',9999);
        $proInfo = $objProject->where($condition)->select();
        if(sizeof($proInfo) == 1)
            return true;
        else
            return false;
    }

    /**
    **@auth qianqiang
    **@breif 判断是否可以进行意向书操作（保存和提交）
    **@return 可允许操作返回true，不允许返回false
    **@date 2015.12.29
    **/ 
    public function isIntentProject($projectId, $projectType){
        //echo $projectId;echo $projectType;exit;
        $condition['project_id'] = $projectId;
        $condition["delete_flag"] = array('neq',9999);
        if($projectType == 1){
            $housetop = M('Housetop');
            $res = $housetop->where($condition)->where('status=61 or status=12 or status=22')->find();
        }elseif($projectType == 2 || $projectType == 3){
            $ground = M('Ground');
            $res = $ground->where($condition)->where('status=61 or status=12 or status=22')->find();
        }
        if(empty($res))
            return false;
        else
            return true;
    }

    /**
    **@auth qianqiang
    **@breif 查询project表信息，并按照是否高亮显示排序
    **@param condition 数组，查询的条件
    **@return 一个数组
    **@date 2015.12.24
    **/
    public function getProjectsInfo($condition, $page=-1, $pageSize=6){
        $objProject = new \Home\Model\ProjectModel(); 
        if($page == -1){
            $projectInfo = $objProject->where($condition)->order('highlight_flag desc, create_date desc')->select();
        }else{
            $projectInfo = $objProject->where($condition)->page($page, $pageSize)->order('highlight_flag desc, create_date desc')->select();
        }
        return $projectInfo;
    }

    /**
    **@auth qiujinhan
    **@breif 添加一个新的项目，插入一条新的数据到project表中
    **@return 保存成功返回project id，失败返回false
    **@date 2015.12.23
    **/ 
    public function insertProject($proData){
        $project = M("Project");
        $project->add($proData);
        $projectInfo = $project->where($proData)->where("status!=51")->select();
        return !empty($projectInfo) ? $projectInfo[0]["id"]:false;
    }

    /**
    **@auth qiujinhan
    **@breif 更新project表信息
    **@return 保存成功返回project id，失败返回false
    **@date 2015.12.23
    **/ 
    public function saveProject($project_code, $proData){
        $project = M("Project");
        $ret = $project->where("project_code = '".$project_code."'")->save($proData);
        $condition["project_code"] = $project_code;
        $condition["delete_flag"] = array('neq',9999);
        $projectInfo = $project->where($condition)->where("status!=51")->select();
        return !empty($projectInfo) ? $projectInfo[0]["id"]:false;
    }

    /**
    **@auth qianqiang
    **@breif 更新housetop/ground表信息
    **@return 保存成功返回影响数据数，失败返回false
    **@date 2016.1.13
    **/ 
    public function saveProjectDetail($projectCode, $projectType, $proData){
        $project = M("Project");
        $condition["project_code"] = $projectCode;
        $condition["delete_flag"] = array('neq',9999);
        $projectInfo = $project->where($condition)->where("status!=51 and status!=61 and delete_flag!=9999")->find();
        if($projectType == 1){
            $housetopObj = M("Housetop");
            $ret = $housetopObj->where("project_id = '".$projectInfo['id']."'")->save($proData);
        }elseif($projectType == 2 || $projectType == 3){
            $groundObj = M("Ground");
            $ret = $groundObj->where("project_id = '".$projectInfo['id']."'")->save($proData);
        }        
        return $ret;
    }

    /**
    **@auth qianqiang
    **@breif 项目状态和建设情况拼接
    **@date 2016.1.9
    **/
    public function getTypeAndStateStr($projectType, $buildState){
        if($projectType == 1) $type = "屋顶分布式";
        elseif($projectType == 2) $type = "地面分布式";
        elseif($projectType == 3) $type = "大型地面";
        if($buildState == 1) $state = "未建";
        elseif($buildState == 2) $state = "已建";
        return $state."-".$type;
    }

    /**
    **@auth qianqiang
    **@breif 综合查询
    **@param companyName 公司名称
    **@param companyType 项目类型（类型及建设状态）
    **@param situation 位置
    **@param startDate 起始时间
    **@param endDate 终止时间
    **@param status 状态
    **@param cooperationType 合作方式
    **@param page 第几页
    **@date 2016.1.8
    **/
    public function searchService($companyName, $companyType, $situation, $startDate, $endDate, $status, $cooperationType, $page){
        $housetopSql = "";
        $groundSql = "";
        if($companyType == null || $companyType == 'all'){
            $housetopSql = "select p.id,p.project_code,p.project_type,p.build_state,p.provider_id,p.highlight_flag,p.create_date,h.id as h_id,h.project_id,h.project_area,h.project_address,h.status,u.id as u_id,u.email,u.user_type,u.company_name from enf_project p join enf_housetop h on p.id=h.project_id join enf_user u on p.provider_id=u.id where h.delete_flag!=9999 and h.status!=51 and h.status!=61 and h.status!=11";
            $groundSql = "select p.id,p.project_code,p.project_type,p.build_state,p.provider_id,p.highlight_flag,p.create_date,g.id as g_id,g.project_id,g.project_area,g.project_address,g.status,u.id as u_id,u.email,u.user_type,u.company_name from enf_project p join enf_ground g on p.id=g.project_id join enf_user u on p.provider_id=u.id where g.delete_flag!=9999 and g.status!=51 and g.status!=61 and g.status!=11";
        }else{
            if($companyType == "1"){//屋顶分布式－未建
                $housetopSql = "select p.id,p.project_code,p.project_type,p.build_state,p.provider_id,p.highlight_flag,p.create_date,h.id as h_id,h.project_id,h.project_area,h.project_address,h.status,u.id as u_id,u.email,u.user_type,u.company_name from enf_project p join enf_housetop h on p.id=h.project_id join enf_user u on p.provider_id=u.id where h.delete_flag!=9999 and h.status!=51 and h.status!=61 and h.status!=11 and p.project_type=1";
                $housetopSql = $housetopSql." and p.build_state=1";
            }elseif($companyType == "2"){//地面分布式－未建
                $groundSql = "select p.id,p.project_code,p.project_type,p.build_state,p.provider_id,p.highlight_flag,p.create_date,g.id as g_id,g.project_id,g.project_area,g.project_address,g.status,u.id as u_id,u.email,u.user_type,u.company_name from enf_project p join enf_ground g on p.id=g.project_id join enf_user u on p.provider_id=u.id where g.delete_flag!=9999 and g.status!=51 and g.status!=61 and g.status!=11 and p.project_type=2";
                $groundSql = $groundSql." and p.build_state=1";
            }elseif($companyType == "3"){//大型地面－未建
                $groundSql = "select p.id,p.project_code,p.project_type,p.build_state,p.provider_id,p.highlight_flag,p.create_date,g.id as g_id,g.project_id,g.project_area,g.project_address,g.status,u.id as u_id,u.email,u.user_type,u.company_name from enf_project p join enf_ground g on p.id=g.project_id join enf_user u on p.provider_id=u.id where g.delete_flag!=9999 and g.status!=51 and g.status!=61 and g.status!=11 and p.project_type=3";
                $groundSql = $groundSql." and p.build_state=1";
            }elseif($companyType == "4"){//屋顶分布式－已建
                $housetopSql = "select p.id,p.project_code,p.project_type,p.build_state,p.provider_id,p.highlight_flag,p.create_date,h.id as h_id,h.project_id,h.project_area,h.project_address,h.status,u.id as u_id,u.email,u.user_type,u.company_name from enf_project p join enf_housetop h on p.id=h.project_id join enf_user u on p.provider_id=u.id where h.delete_flag!=9999 and h.status!=51 and h.status!=61 and h.status!=11 and p.project_type=1";
                $housetopSql = $housetopSql." and p.build_state=2";
            }elseif($companyType == "5"){//地面分布式－已建
                $groundSql = "select p.id,p.project_code,p.project_type,p.build_state,p.provider_id,p.highlight_flag,p.create_date,g.id as g_id,g.project_id,g.project_area,g.project_address,g.status,u.id as u_id,u.email,u.user_type,u.company_name from enf_project p join enf_ground g on p.id=g.project_id join enf_user u on p.provider_id=u.id where g.delete_flag!=9999 and g.status!=51 and g.status!=61 and g.status!=11 and p.project_type=2";
                $groundSql = $groundSql." and p.build_state=2";
            }elseif($companyType == "6"){//大型地面－已建
                $groundSql = "select p.id,p.project_code,p.project_type,p.build_state,p.provider_id,p.highlight_flag,p.create_date,g.id as g_id,g.project_id,g.project_area,g.project_address,g.status,u.id as u_id,u.email,u.user_type,u.company_name from enf_project p join enf_ground g on p.id=g.project_id join enf_user u on p.provider_id=u.id where g.delete_flag!=9999 and g.status!=51 and g.status!=61 and g.status!=11 and p.project_type=3";
                $groundSql = $groundSql." and p.build_state=2";
            }else{
                echo '{"code":"-1","msg":"project type error!"}';
                exit;
            }
        }
        // dump($companyType);exit;
        if(!($companyName == null || $companyName == 'all')){
            if($housetopSql != ""){
                $housetopSql = $housetopSql." and u.company_name='".$companyName."'";
            }
            if($groundSql != ""){
                $groundSql = $groundSql." and u.company_name='".$companyName."'";
            }
        }
        if(!($situation == null || $situation == 'all')){
            $areaObj = D('Area', 'Service');
            $areaList = $areaObj->getAreaArrayByHighLevelId($situation);
            $areaStr = "";
            $i = 0;
            while($areaList[$i]){
                $areaStr = $areaStr."'".$areaList[$i]."',";
                $i += 1;
            }
            $areaStr = substr($areaStr, 0, strlen($areaStr)-1);
            if($housetopSql != ""){
                $housetopSql = $housetopSql." and h.project_area in (".$areaStr.")";
            }
            if($groundSql != ""){
                $groundSql = $groundSql." and g.project_area in (".$areaStr.")";
            }
        }
        if(!($status == null || $status == 'all')){
            if($housetopSql != ""){
                if($status == "11"){
                    $housetopSql = $housetopSql." and p.status=11";
                }elseif($status == "12"){
                    $housetopSql = $housetopSql." and (p.status>=12 and p.status<=13)";
                }elseif($status == "20"){
                    $housetopSql = $housetopSql." and (p.status>=21 and p.status<=22)";
                }elseif($status == "50"){
                    $housetopSql = $housetopSql." and p.status=22";
                }elseif($status == "30"){
                    $housetopSql = $housetopSql." and p.status=31";
                }
            }
            if($groundSql != ""){
                if($status == "11"){
                    $groundSql = $groundSql." and p.status=11";
                }elseif($status == "12"){
                    $groundSql = $groundSql." and (p.status>=12 and p.status<=13)";
                }elseif($status == "20"){
                    $groundSql = $groundSql." and (p.status>=21 and p.status<=22)";
                }elseif($status == "50"){
                    $groundSql = $groundSql." and p.status=22";
                }elseif($status == "30"){
                    $groundSql = $groundSql." and p.status=31";
                }
            }
        }
        if(!($cooperationType == null || $cooperationType == 'all')){
            if($housetopSql != ""){
                $housetopSql = $housetopSql." and h.cooperation_type like '%".$cooperationType."%'";
            }
            if($groundSql != ""){
                $groundSql = $groundSql." and g.cooperation_type like '%".$cooperationType."%'";
            }
        }
        if($startDate != null && $endDate != null){
            if($housetopSql != ""){
                $housetopSql = $housetopSql." and h.create_date>=date('".$startDate."') and h.create_date<=date('".$endDate."')";
            }
            if($groundSql != ""){
                $groundSql = $groundSql." and g.create_date>=date('".$startDate."') and g.create_date<=date('".$endDate."')";
            }
        }
        if($page != -1){
            $pageSize = 6;
            $start = ($page-1)*$pageSize;
            if($housetopSql != ""){
                $housetopSql = $housetopSql." order by highlight_flag desc, create_date desc"." limit ".$start.",".$pageSize;
            }
            if($groundSql != ""){
                $groundSql = $groundSql." order by highlight_flag desc, create_date desc"." limit ".$start.",".$pageSize;
            }
        }
        /*
        header('Content-Type: text/html; charset=utf-8');
        dump($housetopSql);
        dump($groundSql);
        exit;
        */

        $Dao = M();
        if($housetopSql != ""){
            $housetopList = $Dao->query($housetopSql);
        }
        if($groundSql != ""){
            $groundList = $Dao->query($groundSql);
        }
        if(!empty($housetopList) && empty($groundList)){
            $projectList = $housetopList;
        }elseif(empty($housetopList) && !empty($groundList)){
            $projectList = $groundList;
        }elseif(!empty($housetopList) && !empty($groundList)){
            $projectList = array_merge($housetopList, $groundList);
        }
        $resList = $this->formatProject($projectList);
        return $resList;
    }

    /**
    **@auth qianqiang
    **@breif 修改项目状态，如果存在保存的状态删掉，如果有尽职调查删掉
    **@param projectCode 项目编码
    **@param oldStatus 需要修改项目的当前状态
    **@param newStatus 需要改后的项目状态
    **@return 成功返回true，失败返回失败信息
    **@date 2016.1.14
    **/
    public function changeProjectStatus($projectCode, $oldStatus, $newStatus){
        $data['status'] = $newStatus;
        $data['highlight_flag'] = 1;
        $projectObj = M('Project');
        $condition['project_code'] = $projectCode;
        $condition['status'] = $oldStatus;
        $condition["delete_flag"] = array('neq',9999);
        $projectInfo = $projectObj->where($condition)->find();
        $res = $projectObj->where($condition)->save($data);
        if($res > 0){
            if($projectInfo['project_type'] == 1){
                $housetopObj = M('Housetop');
                $hCondition['project_id'] = $projectInfo['id'];
                $hCondition['status'] = $oldStatus;
                $res2 = $housetopObj->where($hCondition)->save($data);
                if(!$res2){
                    return "housetop status change error";
                }
                //housetop中如果存在状态不是修改后状态的数据，删除掉
                $tcondition['project_id'] = $projectInfo['id'];
                $tcondition['status'] = array('neq', $newStatus);
                $tdata['delete_flag'] = 9999;
                $housetopObj->where($tcondition)->save($tdata);
            }elseif($projectInfo['project_type'] == 2 || $projectInfo['project_type'] == 3){
                $groundObj = M('Ground');
                $gCondition['project_id'] = $projectInfo['id'];
                $gCondition['status'] = $oldStatus;
                $res2 = $groundObj->where($gCondition)->save($data);
                if(!$res2){
                    return "ground status change error";
                }
                //ground中如果存在状态不是修改后状态的数据，删除掉
                $tcondition['project_id'] = $projectInfo['id'];
                $tcondition['status'] = array('neq', $newStatus);
                $tdata['delete_flag'] = 9999;
                $groundObj->where($tcondition)->save($tdata);
            }
        }else{
            return "project status change error";
        }
        //project中如果存在状态不是修改后状态的数据，删除掉
        $tcondition['project_code'] = $projectCode;
        $tcondition['status'] = array('neq', $newStatus);
        $tdata['delete_flag'] = 9999;
        $projectObj->where($tcondition)->save($tdata);
        //如果新状态为11或12的，并且存在尽职调查，删除尽职调查
        $evaluationObj = M('Evaluation');
        if($newStatus == 11 || $newStatus == 12){
            $econdition['project_id'] = $projectInfo['id'];
            $econdition['delete_flag'] = 0;
            $evaluationInfo = $evaluationObj->where($econdition)->select();
            if(!empty($evaluationInfo)){
                $edata['delete_flag'] = 9999;
                $res = $evaluationObj->where("project_id='".$projectInfo['id']."'")->save($edata);
                if(!$res){
                    echo '{"code":"-1","msg":"delete evaluation error"}';
                    exit;
                }
            }
        }
        return true;
    }

    /**
    **@auth qianqiang
    **@breif 获取所有项目,包括已删除的
    **@date 2016.1.15
    **/
    public function getAllProject(){
        $project = M('Project');
        $projects = $project->select();
        return $projects;
    }


    /**
    **@auth qiujinhan
    **@breif 修改项目状态
    **@date 2016.1.15
    **/
    public function updateProjectStatus($id, $status){
        $project = M('Project');
        $objProject = $project->where("id='".$id."' and delete_flag!=9999")->find();
        if(empty($objProject)){
            echo '{"code":"-1","msg":"项目不存在!"}';
            exit;
        }

        $data["status"] = $status;
        $data["highlight_flag"] = 1;
        $data['change_date'] = date("Y-m-d H:i:s",time());
        $res = $project->where("id='".$id."'")->save($data);
        if(!$res){
            echo '{"code":"-1","msg":"mysql error!"}';
            exit;
        }
        if($objProject['project_type'] == 1){
            $housetopObj = M('Housetop');
            $res = $housetopObj->where("project_id='".$id."'")->save($data);
        }elseif($objProject['project_type'] == 2 || $objProject['project_type'] == 3){
            $groundObj = M('Ground');
            $res = $groundObj->where("project_id='".$id."'")->save($data);
        }

        if($res){
            return true;
        }else{
            echo '{"code":"-1","msg":"mysql error!"}';
            exit;
        }
    }

    /**
    **@auth qianqiang
    **@breif 假删除项目，删除project、housetop、ground、evaluation、component、inverter、pushProject
    **@param id:项目id
    **@date 2016.1.15
    **/
    public function deleteProjectService($id){
        $project = M('Project');
        $objProject = $project->where("id='".$id."' and delete_flag!=9999")->find();
        if(empty($objProject)){
            echo '{"code":"-1","msg":"项目不存在!"}';
            exit;
        }

        $data['delete_flag'] = 9999;
        $data['change_date'] = date("Y-m-d H:i:s",time());
        $res = $project->where("id='".$id."'")->save($data);
        if(!$res){
            echo '{"code":"-1","msg":"project delete error!"}';
            exit;
        }
        if($objProject['project_type'] == 1){
            $housetopObj = M('Housetop');
            $housetopInfo = $housetopObj->where("project_id='".$id."'")->select();
            if(!empty($housetopInfo)){
                $res = $housetopObj->where("project_id='".$id."'")->save($data);
                if(!$res){
                    echo '{"code":"-1","msg":"housetop delete error!"}';
                    exit;
                }
            }
        }elseif($objProject['project_type'] == 2 || $objProject['project_type'] == 3){
            $groundObj = M('Ground');
            $groundInfo = $groundObj->where("project_id='".$id."'")->select();
            if(!empty($groundInfo)){
                $res = $groundObj->where("project_id='".$id."'")->save($data);
                if(!$res){
                    echo '{"code":"-1","msg":"ground delete error!"}';
                    exit;
                }
            }
        }
        $evaluationObj = M('Evaluation'); 
        $evaluationInfo = $evaluationObj->where("project_id='".$id."'")->select();
        if(!empty($evaluationInfo)){
            $res = $evaluationObj->where("project_id='".$id."'")->save($data);
            if(!$res){
                echo '{"code":"-1","msg":"evaluation delete error!"}';
                exit;
            }
        }
        $componentObj = M('Component');
        $componentInfo = $componentObj->where("project_id='".$id."'")->select();
        if(!empty($componentInfo)){
            $res = $componentObj->where("project_id='".$id."'")->save($data);
            if(!$res){
                echo '{"code":"-1","msg":"component delete error!"}';
                exit;
            }
        }
        $inverterObj = M('Inverter');
        $inverterInfo = $inverterObj->where("project_id='".$id."'")->select();
        if(!empty($inverterInfo)){
            $res = $inverterObj->where("project_id='".$id."'")->save($data);
            if(!$res){
                echo '{"code":"-1","msg":"inverter delete error!"}';
                exit;
            }
        }
        $pushProjectObj = M('Pushproject');
        $pushProjectInfo = $pushProjectObj->where("project_code='".$objProject['project_code']."'")->select();
        if(!empty($pushProjectInfo)){
            $res = $pushProjectObj->where("project_code='".$objProject['project_code']."'")->save($data);
            if(!$res){
                echo '{"code":"-1","msg":"pushProject delete error!"}';
                exit;
            }
        }

        return true;
    }

    /**
    **@auth qianqiang
    **@breif 真删除项目，删除project、housetop、ground、evaluation、component、inverter、pushProject
    **@param id:项目id
    **@date 2016.1.16
    **/
    public function dropProjectService($id){
        $project = M('Project');
        $objProject = $project->where("id='".$id."'")->find();
        if(empty($objProject)){
            echo '{"code":"-1","msg":"项目不存在!"}';
            exit;
        }

        $res = $project->where("id='".$id."'")->delete();
        if(!$res){
            echo '{"code":"-1","msg":"project drop error!"}';
            exit;
        }
        if($objProject['project_type'] == 1){
            $housetopObj = M('Housetop');
            $housetopInfo = $housetopObj->where("project_id='".$id."'")->select();
            if(!empty($housetopInfo)){
                $res = $housetopObj->where("project_id='".$id."'")->delete();
                if(!$res){
                    echo '{"code":"-1","msg":"housetop drop error!"}';
                    exit;
                }
            }
        }elseif($objProject['project_type'] == 2 || $objProject['project_type'] == 3){
            $groundObj = M('Ground');
            $groundInfo = $groundObj->where("project_id='".$id."'")->select();
            if(!empty($groundInfo)){
                $res = $groundObj->where("project_id='".$id."'")->delete();
                if(!$res){
                    echo '{"code":"-1","msg":"ground drop error!"}';
                    exit;
                }
            }
        }
        $evaluationObj = M('Evaluation'); 
        $evaluationInfo = $evaluationObj->where("project_id='".$id."'")->select();
        if(!empty($evaluationInfo)){
            $res = $evaluationObj->where("project_id='".$id."'")->delete();
            if(!$res){
                echo '{"code":"-1","msg":"evaluation drop error!"}';
                exit;
            }
        }
        $componentObj = M('Component');
        $componentInfo = $componentObj->where("project_id='".$id."'")->select();
        if(!empty($componentInfo)){
            $res = $componentObj->where("project_id='".$id."'")->delete();
            if(!$res){
                echo '{"code":"-1","msg":"component drop error!"}';
                exit;
            }
        }
        $inverterObj = M('Inverter');
        $inverterInfo = $inverterObj->where("project_id='".$id."'")->select();
        if(!empty($inverterInfo)){
            $res = $inverterObj->where("project_id='".$id."'")->delete();
            if(!$res){
                echo '{"code":"-1","msg":"inverter drop error!"}';
                exit;
            }
        }
        $pushProjectObj = M('Pushproject');
        $pushProjectInfo = $pushProjectObj->where("project_code='".$objProject['project_code']."'")->select();
        if(!empty($pushProjectInfo)){
            $res = $pushProjectObj->where("project_code='".$objProject['project_code']."'")->delete();
            if(!$res){
                echo '{"code":"-1","msg":"pushProject drop error!"}';
                exit;
            }
        }

        return true;
    }

    /**
    **@auth qianqiang
    **@breif 还原项目，还原project、housetop、ground、evaluation、component、inverter、pushProject
    **@param id:项目id
    **@date 2016.1.18
    **/
    public function recoveryProjectService($id){
        $project = M('Project');
        $objProject = $project->where("id='".$id."' and delete_flag=9999")->find();
        if(empty($objProject)){
            echo '{"code":"-1","msg":"项目不可进行恢复操作"}';
            exit;
        }
        $data['delete_flag'] = 0;
        $data['change_date'] = date("Y-m-d H:i:s",time());
        $res = $project->where("id='".$id."'")->save($data);
        if(!$res){
            echo '{"code":"-1","msg":"project recovery error!"}';
            exit;
        }
        if($objProject['project_type'] == 1){
            $housetopObj = M('Housetop');
            $housetopInfo = $housetopObj->where("project_id='".$id."'")->select();
            if(!empty($housetopInfo)){
                $res = $housetopObj->where("project_id='".$id."'")->save($data);
                if(!$res){
                    echo '{"code":"-1","msg":"housetop recovery error!"}';
                    exit;
                }
            }
        }elseif($objProject['project_type'] == 2 || $objProject['project_type'] == 3){
            $groundObj = M('Ground');
            $groundInfo = $groundObj->where("project_id='".$id."'")->select();
            if(!empty($groundInfo)){
                $res = $groundObj->where("project_id='".$id."'")->save($data);
                if(!$res){
                    echo '{"code":"-1","msg":"ground recovery error!"}';
                    exit;
                }
            }
        }
        $evaluationObj = M('Evaluation'); 
        $evaluationInfo = $evaluationObj->where("project_id='".$id."'")->select();
        if(!empty($evaluationInfo)){
            $res = $evaluationObj->where("project_id='".$id."'")->save($data);
            if(!$res){
                echo '{"code":"-1","msg":"evaluation recovery error!"}';
                exit;
            }
        }
        $componentObj = M('Component');
        $componentInfo = $componentObj->where("project_id='".$id."'")->select();
        if(!empty($componentInfo)){
            $res = $componentObj->where("project_id='".$id."'")->save($data);
            if(!$res){
                echo '{"code":"-1","msg":"component recovery error!"}';
                exit;
            }
        }
        $inverterObj = M('Inverter');
        $inverterInfo = $inverterObj->where("project_id='".$id."'")->select();
        if(!empty($inverterInfo)){
            $res = $inverterObj->where("project_id='".$id."'")->save($data);
            if(!$res){
                echo '{"code":"-1","msg":"inverter recovery error!"}';
                exit;
            }
        }
        $pushProjectObj = M('Pushproject');
        $pushProjectInfo = $pushProjectObj->where("project_code='".$objProject['project_code']."'")->select();
        if(!empty($pushProjectInfo)){
            $res = $pushProjectObj->where("project_code='".$objProject['project_code']."'")->save($data);
            if(!$res){
                echo '{"code":"-1","msg":"pushProject recovery error!"}';
                exit;
            }
        }

        return true;
    }

    /**
    **@auth qianqiang
    **@breif 通过提供方id，删除该提供方所有提供的项目信息
    **@param id:用户id
    **@date 2016.1.22
    **/
    public function deleteProjectList($id){
        $project = M('Project');
        $projectList = $project->where("provider_id='".$id."' and delete_flag=9999")->select();
        if(empty($projectList)){
            return true;
        }
        $i = 0;
        while($projectList[$i]){
            $this->deleteProjectService($projectList[$i]['id']);
            $i += 1;
        }
        return true;
    }

    /**
    **@auth qianqiang
    **@breif 通过投资方id，删除该投资方的项目推送信息
    **@param id:用户id
    **@date 2016.1.22
    **/
    public function deletePushProject($id){
        $user = M('User');
        $objUser = $user->where("id='".$id."' and delete_flag=9999")->find();
        if(empty($objUser)){
            echo '{"code":"-1","msg":"用户不存在操作"}';
            exit;
        }
        $pushProjectObj = M('Pushproject');
        $data['delete_flag'] = 9999;
        $data['change_date'] = date("Y-m-d H:i:s",time());
        $res = $pushProjectObj->where("investor_id='".$id."'")->save($data);
        if(!$res){
            echo '{"code":"-1","msg":"push project delete error!"}';
            exit;
        }
        return true;
    }

    /**
    **@auth qianqiang
    **@breif 取消项目高亮标记
    **@param projectCode:项目编码
    **@param userType:用户类型
    **@date 2016.1.30
    **/
    public function cancelProjectHighlight($projectCode, $userType){
        $projectObj = M('Project');
        $condition['project_code'] = $projectCode;
        $condition['highlight_flag'] = 1;
        $condition['delete_flag'] = 0;
        $projectInfo = $projectObj->where($condition)->find();
        if(!empty($projectInfo)){
            if($userType == 2){
                if($projectInfo['status'] == 13 || $projectInfo['status'] == 22 ){
                    return true;
                }
            }elseif($userType == 3){
                if($projectInfo['status'] == 12 || $projectInfo['status'] == 23 || $projectInfo['status'] == 31){
                    return true;
                }
            }
            $data['highlight_flag'] = 0;
            $res = $projectObj->where($condition)->save($data);
            if(!$res){
                echo '{"code":"-1","msg":"cancelProjectHighlight error!"}';
                exit;
            }
        }
        return true;
    }

    /**
    **@auth qianqiang
    **@breif 取消已推送项目高亮标记
    **@param projectCode:项目编码
    **@param email
    **@date 2016.1.30
    **/
    public function cancelPushHighlight($projectCode, $email){
        $user = M('User');
        $objUser = $user->where("email='".$email."' and delete_flag=0")->find();
        if(empty($objUser)){
            echo '{"code":"-1","msg":"用户不存在"}';
            exit;
        }
        $pushObj = M('Pushproject');
        $condition['investor_id'] = $objUser['id'];
        $condition['project_code'] = $projectCode;
        $condition['highlight_flag'] = 1;
        $condition['delete_flag'] = 0;
        $pushInfo = $pushObj->where($condition)->find();
        if(!empty($pushInfo)){
            $data['highlight_flag'] = 0;
            $res = $pushObj->where($condition)->save($data);
            if(!$res){
                echo '{"code":"-1","msg":"cancelPushHighlight error!"}';
                exit;
            }
        }
        return true;
    }

}
