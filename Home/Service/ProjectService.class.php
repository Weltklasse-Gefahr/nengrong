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
    **@breif 查询已签意向书项目
    **@date 2015.12.24
    **/ 
    public function getAgreementProject($email){
        if(!empty($email)){
            $user = D('User');
            $userInfo = $user->where("email='".$email."'")->find();
            $condition['provider_id'] = $userInfo['id'];
        }
        $condition['status'] = array('between','21,29');
        $projectInfo = $this->getProjectsInfo($condition);
        return $projectInfo; 
    }

    /**
    **@auth qianqiang
    **@breif 查询已签融资合同项目
    **@date 2015.12.24
    **/ 
    public function getxxxxx($email){
        if(!empty($email)){
            $user = D('User');
            $userInfo = $user->where("email='".$email."'")->find();
            $condition['provider_id'] = $userInfo['id'];
        }
        $condition['status'] = array('between','31,39');
        $projectInfo = $this->getProjectsInfo($condition);
        return $projectInfo; 
    }

    /**
    **@auth qianqiang
    **@breif 尽职调查保存时，保存项目信息
    **@return 保存成功返回true，失败返回false
    **@date 2015.12.23
    **/ 
    public function saveHousetopProject($proData){
        $housetop = M("Housetop");
        if($this->hasSaveHousetopProject($proData['project_id']) == 1){
            $housetop->where("project_id='".$proData['project_id']."' and status=51")->save($proData);
        }else{
            $proData['status'] = 51;
            $proData['create_date'] = date("Y-m-d H:i:s",time());
            $housetop->add($proData);
        }
        $housetopInfo = $housetop->where($proData)->where("status=51")->select();
        if(sizeof($housetopInfo) == 1)
            return true;
        else
            return false;
    }

    /**
    **@auth qiujinhan
    **@breif 更新Housetop，orGround 如果已经存在就更新，不存在就插入
    **@return 保存成功返回true，失败返回false
    **@date 2015.12.23
    **/ 
    public function saveHousetopOrGround($proData, $status, $table){
        $housetoporGround = M("$table");
        if($this->hasSavehousetoporGround($proData['project_id'], $table) == true){
            $ret = $housetoporGround->where("project_id='".$proData['project_id'])->save($proData);
        }else{
            $proData['create_date'] = date("Y-m-d H:i:s",time());
            $ret = $housetoporGround->add($proData);
        }
        return $ret;
    }

    /**
    **@auth qiujinhan
    **@breif 判断是否有保存的saveHousetopOrGround
    **@return 存在返回true，不存在返回false
    **@date 2015.12.23
    **/ 
    public function hasSaveHousetopProject($projectId, $table){
        $objProject = D("$table");
        $condition["project_id"] = $projectId;
        $proInfo = $objProject->where($condition)->select();
        if(empty($proInfo))
            return false;
        else
            return true;
    }


    /**
    **@auth qianqiang
    **@breif 尽职调查提交时，提交项目信息
    **@return 提交成功返回true，失败返回false
    **@date 2015.12.23
    **/ 
    public function submitHousetopProject($proData){
        //更新项目资料
        //如果有save数据，进行删除
        $housetop = M("Housetop");
        $proData['status'] = 22;
        $proData['change_date'] = date("Y-m-d H:i:s",time());
        $housetopInfo = $housetop->where("project_id='".$proData['project_id']."' and status=21")->save($proData);
        if($this->hasSaveHousetopProject($proData['project_id']) == 1){
            $condition['project_id'] = $proData['project_id'];
            $condition['status'] = 51;
            $housetop->where($condition)->delete();
        }
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
    **@breif 查询project表信息
    **@param condition 数组，查询的条件
    **@return 一个数组
    **@date 2015.12.24
    **/
    public function getProjectsInfo($condition){
        $objProject = new \Home\Model\ProjectModel(); 
        $projectInfo = $objProject->where($condition)->order('highlight_flag desc')->select();
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
        $ret = $project->where("project_code = ".$project_code)->save($proData);
        $condition["project_code"] = $project_code;
        $projectInfo = $project->where($condition)->where("status!=51")->select();
        return !empty($projectInfo) ? $projectInfo[0]["id"]:false;
    }

}
