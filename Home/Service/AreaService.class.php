<?php 
namespace Home\Service;

use Think\Model;

class AreaService extends Model{

	/**
    **@auth qianqiang
    **@breif 根据id得到省市县字符串,如：山东省济南市市中区
    **@date 2015.12.23
	**/
	public function getAreaById($areaId){
		$area = M("Area");
		$areaStr = "";
		$areaInfo = $area->where("id='".$areaId."'")->find();
		while($areaInfo['parent_id'] != "1" && $areaInfo != null){
			$areaStr = $areaInfo['area'].$areaStr;
			$areaInfo = $area->where("id='".$areaInfo['parent_id']."'")->find();
		}
		$areaStr = $areaInfo['area'].$areaStr;
		return $areaStr;
	}

	/**
    **@auth qianqiang
    **@breif 根据地区id得到地区省市县id数组
    **@date 2015.12.28
	**/
	public function getAreaArrayById($areaId){
		$area = M("Area");
		$areaArray = array();
		$tempArray = array();
		$i = 0;
		$areaInfo = $area->where("id='".$areaId."'")->find();
		while($areaInfo['parent_id'] != "1" && $areaInfo != null){
			array_push($tempArray,$areaInfo['id']);
			$areaInfo = $area->where("id='".$areaInfo['parent_id']."'")->find();
		}
		$areaArray[$i++] = $areaInfo['id'];
		while(!empty($tempArray)){
			$areaArray[$i++] = array_pop($tempArray);
		}
		return $areaArray;
	}

	/**
    **@auth qianqiang
    **@breif 根据地区描述和父级地区描述得到地区id
    **@date 2015.12.28
	**/
	public function getIdByAreaAndParentArea($area, $parentArea){
		$areaObj = M("Area");
		$parentId = $this->getIdByArea($parentArea);
		$condition['area'] = $area;
		$condition['parent_id'] = $parentId;
		$areaInfo = $areaObj->where($condition)->find();
		return $areaInfo['id'];
	}

	/**
    **@auth qianqiang
    **@breif 根据地区描述得到地区id，区级单位有重名的如高新区，返回查到的第一个数据
    **@date 2015.12.28
	**/
	public function getIdByArea($area){
		$areaObj = M("Area");
		$areaInfo = $areaObj->where("area='".$area."'")->find();
		return $areaInfo['id'];
	}
}