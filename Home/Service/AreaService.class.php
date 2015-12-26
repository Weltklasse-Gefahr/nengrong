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
}