<?php
namespace Home\Service;

use Think\Model;

class evaluationService extends Model{

    /**
    **@auth qianqiang
    **@breif 根据项目ID查询尽职调查详细信息
    **@date 2015.12.21
    **/ 
	public function getEvaluationInfo($projectId){
		$objEvaluation = D("Evaluation");
		$condition["project_id"] = $projectId;
		$condition["delete_flag"] = array('neq',9999);
		$evaluationInfo = $objEvaluation->where($condition)->select();
		return $evaluationInfo[0];
	}

	/**
    **@auth qianqiang
    **@breif 查看尽职调查信息，读取顺序：保存的、提交的、空
    **@date 2015.12.25
    **/ 
	public function getEvaluation($projectId){
		$objEvaluation = D("Evaluation");
		$condition["project_id"] = $projectId;
		$condition["status"] = array('between', '51,52');
        $condition["delete_flag"] = array('neq',9999);
		$evaluationInfo = $objEvaluation->where($condition)->order('status asc')->select();
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
        if($this->hasEvaluation($evaluationInfo['project_id'], 51) == 1){
            $evaluationInfo['change_date'] = date("Y-m-d H:i:s",time());
            $result = $evaluation->where("project_id='".$evaluationInfo['project_id']."' and status=51")->save($evaluationInfo);
        }else{
            $evaluationInfo['status'] = 51;
            $evaluationInfo['create_date'] = date("Y-m-d H:i:s",time());
            $evaluationInfo['change_date'] = date("Y-m-d H:i:s",time());
            $result = $evaluation->add($evaluationInfo);
        }
        if($result == false)
            return false;
        else
            return true;
	}

	/**
    **@auth qianqiang
    **@breif 提交尽职调查
    **@return 提交成功返回true，失败返回false
    **@date 2015.12.23
    **/ 
	public function submitEvaluationInfo($evaluationInfo){
		//如果没有保存记录，判断是否有提交记录，有则更新，无则添加
		//如果有save数据，进行删除
		$evaluation = M("Evaluation");
		if($this->hasEvaluation($evaluationInfo['project_id'], 52) == 1){
            $evaluationInfo['change_date'] = date("Y-m-d H:i:s",time());
            $result = $objEvaluation = $evaluation->where("project_id='".$evaluationInfo['project_id']."' and status=52")->save($evaluationInfo);
        }else{
            $evaluationInfo['status'] = 52;
            $evaluationInfo['create_date'] = date("Y-m-d H:i:s",time());
            $evaluationInfo['change_date'] = date("Y-m-d H:i:s",time());
            $result = $objEvaluation = $evaluation->add($evaluationInfo);
        }
        if($result == true){
            if($this->hasEvaluation($evaluationInfo['project_id'], 51) == 1){
                $condition['project_id'] = $evaluationInfo['project_id'];
                $condition['status'] = 51;
                $evaluation->where($condition)->delete();
            }
            return true;
        }else{
            return false;
        }
    }

	/**
    **@auth qianqiang
    **@breif 判断是否有保存的尽职调查
    **@return 存在返回true，不存在返回false
    **@date 2015.12.23
    **/ 
    public function hasEvaluation($projectId, $status){
        $objEvaluation = D("Evaluation");
        $condition["project_id"] = $projectId;
        $condition["status"] = $status;
        $condition["delete_flag"] = array('neq',9999);
        $evaluationInfo = $objEvaluation->where($condition)->select();
        if(sizeof($evaluationInfo) == 1)
            return true;
        else
            return false;
    }
}