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
            $housetopInfo = $housetop->where("project_id='%s' and status!=9999", $projectId)->select();
            return $housetopInfo[0];
        }elseif($projectType == 2 || $projectType == 3){
            $ground = M("Ground");
            $groundInfo = $ground->where("project_id='%s' and status!=9999", $projectId)->select();
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
            $condition['status'] = $status;
            $housetopInfo = $housetop->where($condition)->select();
            return $housetopInfo[0];
        }elseif($projectType == 2 || $projectType == 3){
            $ground = M("Ground");
            $condition['project_id'] = $projectId;
            $condition['status'] = $status;
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
            $housetopInfo = $housetop->where($condition)->select();
            if(sizeof($housetopInfo) > 0) 
                return $housetopInfo[0];
            else{
                $condition['status'] = array('between','21,29');
                $housetopInfo = $housetop->where($condition)->select();
                return $housetopInfo[0];
            }
        }elseif($projectType == 2 || $projectType == 3){
            $ground = M("Ground");
            $condition['project_id'] = $projectId;
            $condition['status'] = 51;
            $groupInfo = $ground->where($condition)->select();
            if(sizeof($groupInfo) > 0) 
                return $groupInfo[0];
            else{
                $condition['status'] = array('between','21,29');
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
        if($projectInfo['project_type'] == 1){
            $housetop = M('Housetop');
            $resInfo = $housetop->where($condition)->find();
        }elseif($projectInfo['project_type'] == 2 || $projectInfo['project_type'] == 3){
            $ground = M('Ground');
            $resInfo = $ground->where($condition)->find();
        }
        if(empty($resInfo)){
            $resInfo = $this->getProjectDetail($projectInfo['id'], $projectInfo['project_type']);
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
            $userInfo = $user->where("email='".$email."'")->find();
            $condition['provider_id'] = $userInfo['id'];
        }
        if($filter == "committed"){
            $condition['status'] = array('between','12,13');
        }elseif($filter == "uncommitted"){
            $condition['status'] = 11;
        }else{
            $condition['status'] = array('between','11,13');
        }
        $projectInfo = $this->getProjectsInfo($condition, $page, 6);
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
        $res = $obj->where($condition)->delete();
        return $res;
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
            $userInfo = $user->where("email='".$email."'")->find();
            $condition['provider_id'] = $userInfo['id'];
        }
        $condition['status'] = array('between','21,29');
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
            $userInfo = $user->where("email='".$email."'")->find();
            $condition['provider_id'] = $userInfo['id'];
        }
        $condition['status'] = array('between','31,39');
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
            $userInfo = $user->where("email='".$email."'")->find();
            $condition['investor_id'] = $userInfo['id'];
        }else{
            return null;
        }
        $condition['status'] = array('neq',9999);
        $pushPro = M('Pushproject');
        if($page == -1){
            $pushProInfo = $pushPro->where($condition)->select();
        }else{
            $pushProInfo = $pushPro->where($condition)->page($page, 6)->select();
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
            $condition['status'] = array('neq', 9999);
            $projectInfo = $projectObj->where($condition)->find();
            $pushProInfo[$i]['project_type'] = $projectInfo['project_type'];
            $pushProInfo[$i]['status'] = $projectInfo['status'];
            $i += 1;
        }
        return $pushProInfo;
    }

    /**
    **@auth qianqiang
    **@breif 将项目列表中的信息规范化显示
    **@date 2015.12.30
    **/ 
    public function formatProject($projectList){
        if(empty($projectList)) return $projectList;
        $i = 0;
        while($projectList[$i]){
            //状态转换待完成
            if($projectList[$i]['project_type'] == 1){
                $projectList[$i]['type'] = "屋顶分布式";
                $proObj = M('Housetop');
            }
            elseif($projectList[$i]['project_type'] == 2){
                $projectList[$i]['type'] = "地面分布式";
                $proObj = M('Ground');
            }
            elseif($projectList[$i]['project_type'] == 3){
                $projectList[$i]['type'] = "大型地面";
                $proObj = M('Ground');
            }
            $condition['project_id'] = $projectList[$i]['id'];
            $condition['status'] = $projectList[$i]['status'];
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
        $flag = $this->isIntentProject($projectInfo['id'], $projectInfo['project_type']);
        if($flag === false){
            echo '{"code":"-1","msg":"status error, cannot submit intent"}';
            exit;
        }
        $projectDetails = $this->getProjectDetails($projectInfo['id'], 12, $projectInfo['project_type']);//12项目已提交（客服未提交意向书）
        $projectDetails['project_intent'] = $intentText;
        $projectDetails['status'] = 13;
        $projectDetails['change_date'] = date("Y-m-d H:i:s",time());
        if($projectInfo['project_type'] == 1){
            $housetop = M('Housetop');
            $housetopResult = $housetop->where("project_id='".$projectDetails['project_id']."' and status=12")->save($projectDetails);
            if($housetopResult == 0) return false;
            if($this->hasSaveHousetopOrGround($projectInfo['id'], 61, $projectInfo['project_type'])){
                $condition['project_id'] = $projectDetails['project_id'];
                $condition['status'] = 61;
                $housetop->where($condition)->delete();
            }
        }elseif($projectInfo['project_type'] == 2 || $projectInfo['project_type'] == 3){
            $ground = M('Ground');
            $groundResult = $ground->where("project_id='".$projectDetails['project_id']."' and status=12")->save($projectDetails);
            if($groundResult == 0) return false;
            if($this->hasSaveHousetopOrGround($projectInfo['id'], 61, $projectInfo['project_type'])){
                $condition['project_id'] = $projectDetails['project_id'];
                $condition['status'] = 61;
                $ground->where($condition)->delete();
            }
        }
        $project = M("Project");
        $data['status'] = 13;
        $data['change_date'] = date("Y-m-d H:i:s",time());
        $projectResult = $project->where("id='".$projectInfo['id']."' and status=12")->save($data);
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
    public function saveHousetopOrGround($proData, $status, $projectType){
        $result = false;
        if($projectType == 1){
            $housetop = M("Housetop");
            if($this->hasSaveHousetopOrGround($proData['project_id'], $status, $projectType)){
                $condition['project_id'] = $proData['project_id'];
                $condition['status'] = $status;
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
            if($this->hasSaveHousetopOrGround($proData['project_id'], $status, $projectType)){
                $condition['project_id'] = $proData['project_id'];
                $condition['status'] = $status;
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
    public function hasSaveHousetopOrGround($projectId, $status, $projectType){
        $condition["project_id"] = $projectId;
        $condition["status"] = $status;
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
    **@param $status 提交的项目状态，如：进行尽职调查保存，需要传参数为51
    **@param $projectType 项目类型
    **@return 提交成功返回true，失败返回false
    **@date 2015.12.29
    **/ 
    public function submitHousetopOrGround($proData, $status, $projectType){
        if($projectType == 1){
            $housetop = M("Housetop");
            $proData['status'] = 22;
            $proData['change_date'] = date("Y-m-d H:i:s",time());
            $result = $housetop->where("project_id='".$proData['project_id']."' and (status=21 or status=22)")->save($proData);
            if($this->hasSaveHousetopOrGround($proData['project_id'], 51, $projectType)){
                $condition['project_id'] = $proData['project_id'];
                $condition['status'] = 51;
                $housetop->where($condition)->delete();
            }
        }elseif($projectType == 2 || $projectType == 3){
            $ground = M("Ground");
            $proData['status'] = 22;
            $proData['change_date'] = date("Y-m-d H:i:s",time());
            $result = $ground->where("project_id='".$proData['project_id']."' and (status=21 or status=22)")->save($proData);
            if($this->hasSaveHousetopOrGround($proData['project_id'], 51, $projectType)){
                $condition['project_id'] = $proData['project_id'];
                $condition['status'] = 51;
                $ground->where($condition)->delete();
            }
        }
        if($result > 0){
            $project = M("Project");
            $data['status'] = 22;
            $data['change_date'] = date("Y-m-d H:i:s",time());
            $projectResult = $project->where("id='".$proData['project_id']."' and (status=21 or status=22)")->save($data);
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
        $proInfo = $pushProject->where($condition)->select();
        return $proInfo;
    }

    /**
    **@auth qianqiang
    **@breif 推送项目
    **@date 2015.12.30
    **/ 
    public function pushProject($projectCode, $investorList){
        if($this->isPushProject($projectCode) == false){
            echo '{"code":"-1","msg":"该项目不能进行推送操作"}';
            exit;
        }
        $pushProject = D("Pushproject");
        $data = array();
        $data['project_code'] = $projectCode;
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
    **@breif 判断项目是否可以进行推送
    **@return 可以推送返回true，不可以推送返回false
    **@date 2015.12.30
    **/ 
    public function isPushProject($projectCode){
        $projectObj = M("Project");
        $condition["project_code"] = $projectCode;
        $condition["status"] = array('between','21,29');
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
        $condition['project_id'] = $projectId;
        if($projectType == 1){
            $housetop = M('Housetop');
            $res = $housetop->where($condition)->where('status=61 or status=12')->find();
        }elseif($projectType == 2 || $projectType == 3){
            $ground = M('Ground');
            $res = $ground->where($condition)->where('status=61 or status=12')->find();
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
            $projectInfo = $objProject->where($condition)->order('highlight_flag desc')->select();
        }else{
            $projectInfo = $objProject->where($condition)->page($page, $pageSize)->order('highlight_flag desc')->select();
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
        $projectInfo = $project->where($condition)->where("status!=51 and status!=61 and status!=9999")->find();
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
        if($companyType == null || $companyType == '全部'){
            $housetopSql = "select * from enf_project p join enf_housetop h on p.id=h.project_id join enf_user u on p.provider_id=u.id where h.status!=9999 and h.status!=51 and h.status!=61";
            $groundSql = "select * from enf_project p join enf_ground g on p.id=g.project_id join enf_user u on p.provider_id=u.id where g.status!=9999 and g.status!=51 and g.status!=61";
        }else{
            $typeArr = explode("-",$companyType);
            if($typeArr[0] == '屋顶分布式'){
                $housetopSql = "select * from enf_project p join enf_housetop h on p.id=h.project_id join enf_user u on p.provider_id=u.id where h.status!=9999 and h.status!=51 and h.status!=61 and p.project_type=1";
                if($typeArr[1] == '未建'){
                    $housetopSql = $housetopSql." and p.build_state=1";
                }elseif($typeArr[1] == '已建'){
                    $housetopSql = $housetopSql." and p.build_state=2";
                }
            }elseif($typeArr[0] == '地面分布式'){
                $groundSql = "select * from enf_project p join enf_ground g on p.id=g.project_id join enf_user u on p.provider_id=u.id where g.status!=9999 and g.status!=51 and g.status!=61 and p.project_type=2";
                if($typeArr[1] == '未建'){
                    $groundSql = $groundSql." and p.build_state=1";
                }elseif($typeArr[1] == '已建'){
                    $groundSql = $groundSql." and p.build_state=2";
                }
            }elseif($typeArr[0] == '大型地面'){
                $groundSql = "select * from enf_project p join enf_ground g on p.id=g.project_id join enf_user u on p.provider_id=u.id where g.status!=9999 and g.status!=51 and g.status!=61 and p.project_type=3";
                if($typeArr[1] == '未建'){
                    $groundSql = $groundSql." and p.build_state=1";
                }elseif($typeArr[1] == '已建'){
                    $groundSql = $groundSql." and p.build_state=2";
                }
            }
        }
        if(!($companyName == null || $companyName == '全部')){
            if($housetopSql != ""){
                $housetopSql = $housetopSql." and u.company_name='".$companyName."'";
            }
            if($groundSql != ""){
                $groundSql = $groundSql." and u.company_name='".$companyName."'";
            }
        }
        if(!($situation == null || $situation == '全部')){
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
        if(!($status == null || $status == '全部')){
            if($housetopSql != ""){
                if($status == "未提交"){
                    $housetopSql = $housetopSql." and p.status=11";
                }elseif($status == "已提交"){
                    $housetopSql = $housetopSql." and (p.status>=12 and p.status<=13)";
                }elseif($status == "已签意向书"){
                    $housetopSql = $housetopSql." and (p.status>=21 and p.status<=22)";
                }elseif($status == "已尽职调查"){
                    $housetopSql = $housetopSql." and p.status=22";
                }elseif($status == "已签融资合同"){
                    $housetopSql = $housetopSql." and p.status=31";
                }
            }
            if($groundSql != ""){
                if($status == "未提交"){
                    $groundSql = $groundSql." and p.status=11";
                }elseif($status == "已提交"){
                    $groundSql = $groundSql." and (p.status>=12 and p.status<=13)";
                }elseif($status == "已签意向书"){
                    $groundSql = $groundSql." and (p.status>=21 and p.status<=22)";
                }elseif($status == "已尽职调查"){
                    $groundSql = $groundSql." and p.status=22";
                }elseif($status == "已签融资合同"){
                    $groundSql = $groundSql." and p.status=31";
                }
            }
        }
        if(!($cooperationType == null || $cooperationType == '全部')){
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
                $housetopSql = $housetopSql." limit ".$start.",".$pageSize;
            }
            if($groundSql != ""){
                $groundSql = $groundSql." limit ".$start.",".$pageSize;
            }
        }

        header('Content-Type: text/html; charset=utf-8');
        dump($housetopSql);
        dump($groundSql);
        exit;

        $Dao = M();
        $housetopList = $Dao->query($housetopSql);
        $groundList = $Dao->query($groundSql);
        if(!empty($housetopList) && empty($groundList)){
            $projectList = $housetopList;
        }elseif(empty($housetopList) && !empty($groundList)){
            $projectList = $groundList;
        }elseif(!empty($housetopList) && !empty($groundList)){
            $projectList = array_merge($housetopList, $groundList);
        }
        return $projectList;
    }

}
