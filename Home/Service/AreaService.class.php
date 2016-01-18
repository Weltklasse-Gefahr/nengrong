<?php 
namespace Home\Service;

use Think\Model;

class AreaService extends Model{

	/**
    **@auth qianqiang
    **@breif 根据id得到省市县字符串,如：传入市中区，返回山东省济南市市中区
    **@param areaId 区域id
    **@return 省市县字符串
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
    **@breif 根据低级地区id得到地区本级及高级地区的id数组
    **@param areaId 区域id，如天桥区
    **@return 省市县的id数组+省市区字符串，如array(0=>山东省，1=>济南市，2=>天桥区，3=>省市县) 
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
		$areaArray[$i] = $this->getAreaById($areaArray[$i-1]);
		return $areaArray;
	}

	/**
    **@auth qianqiang
    **@breif 根据高级地区id得到地区本级及低级地区的id数组
    **@param areaId 区域id，如山东省
    **@return 省市县的id数组，如array(0=>山东省，1=>济南市，2=>青岛市...*=>天桥区...)
    **@date 2016.1.12
	**/
	public function getAreaArrayByHighLevelId($areaId){
		$area = M("Area");
		$areaArray = array();
		$tempArray = array();
		$i = 0;
		$areaInfo = $area->where("id='".$areaId."'")->find();
		array_push($tempArray, $areaInfo);
		while(sizeof($tempArray) != 0){
			$tempArea = array_shift($tempArray);
			$areaArray[$i++] = $tempArea['id'];
			$areaList = $area->where("parent_id='".$tempArea['id']."'")->select();
			$j=0;
			while($areaList[$j]){
				array_push($tempArray, $areaList[$j]);
				$j += 1;
			}			
		}
		return $areaArray;
	}

	/**
    **@auth qianqiang
    **@breif 根据地区描述和父级地区描述得到地区id，避免有重名的区(尽有区可能重名，如高新区)
    **@param area 区域描述，如海淀区
    **@param parentArea 父级区域描述，如北京市
    **@return 地区id
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