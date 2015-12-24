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
            return $housetop[0];
        }elseif($projectType == 2 || $projectType == 3){
            $ground = M("Ground");
            $groundInfo = $ground->where("project_id='%s' and status!=9999", $projectId)->select();
            return $groundInfo[0];
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
        //增加排序！！
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
        if($this->hasSaveHousetopProject($proData['projectId']) == 1){
            $housetop->where("project_id='".$proData['projectId']."' and status=51")->save($proData);
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
    **@auth qianqiang
    **@breif 尽职调查提交时，提交项目信息
    **@return 提交成功返回true，失败返回false
    **@date 2015.12.23
    **/ 
    public function submitHousetopProject($proData){
        //保存记录，如果有save数据，进行删除
        //如果没有保存记录，判断是否有提交记录，有则更新，无则添加
        $housetop = M("Housetop");
        if($this->hasSaveHousetopProject($proData['projectId']) == 1){

        }
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
        //增加排序
        $projectInfo = $objProject->where($condition)->select();
        return $projectInfo;
    }
}
