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
}