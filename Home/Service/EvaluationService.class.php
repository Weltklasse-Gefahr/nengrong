<?php
namespace Home\Service;

use Think\Model;

class evaluationService extends Model(){

    /**
    **@auth qianqiang
    **@breif 根据项目ID查询尽职调查详细信息
    **@date 2015.12.21
    **/ 
	public function getEvaluationInfo($projectId){
		$objEvaluation = D("Evaluation");
		$condition["project_id"] = $projectId;
		$condition["status"] = array('neq',9999);
		$evaluationInfo = $objEvaluation->where($condition)->select();
		return $evaluationInfo[0];
	}

	/**
    **@auth qianqiang
    **@breif 保存尽职调查
    **@return 保存成功返回true，失败返回false
    **@date 2015.12.23
    **/ 
	public function saveEvaluationInfo($evaluationInfo){
		$evaluation = M("Evaluation");
        if($this->hasEvaluation($evaluationInfo['projectId']) == 1){
            $evaluation->where("project_id='".$evaluationInfo['projectId']."' and status=51")->save($evaluationInfo);
        }else{
            $evaluationInfo['status'] = 51;
            $evaluationInfo['create_date'] = date("Y-m-d H:i:s",time());
            $evaluation->add($evaluationInfo);
        }
        $evaInfo = $evaluation->where($evaluationInfo)->where("status=51")->select();
        if(sizeof($evaInfo) == 1)
            return true;
        else
            return false;
	}

	/**
    **@auth qianqiang
    **@breif 提交尽职调查
    **@return 提交成功返回true，失败返回false
    **@date 2015.12.23
    **/ 
	public function submitEvaluationInfo($evaluationInfo){
		//存储，如果有save数据，进行删除
	}

	/**
    **@auth qianqiang
    **@breif 判断是否有保存的尽职调查
    **@return 存在返回true，不存在返回false
    **@date 2015.12.23
    **/ 
    public function hasEvaluation($projectId){
        $objEvaluation = D("Evaluation");
        $condition["project_id"] = $projectId;
        $condition["status"] = 51;
        $evaluationInfo = $objEvaluation->where($condition)->select();
        if(sizeof($evaluationInfo) == 1)
            return true;
        else
            return false;
    }
}