<?php 
namespace Home\Service;

use Think\Model;

class CommonService{

	/**
    **@auth qianqiang
    **@breif 获取用户企业类型
    **@date 2016.1.28
	**/
	public function getUserCompanyType($no){
		if($no == null){
			return null;
		}
		if(1 == $no){
			$companyTypeStr = '央企国企';
		}elseif(2 == $no){
			$companyTypeStr = '中外合资';
		}elseif(3 == $no){
			$companyTypeStr = '外商独资';
		}elseif(4 == $no){
			$companyTypeStr = '大型民营';
		}elseif(5 == $no){
			$companyTypeStr = '小型民营';
		}else{
			$companyTypeStr = '其他';
		}
		return $companyTypeStr;
	}

	/**
    **@auth qianqiang
    **@breif 获取项目企业类型
    **@date 2016.1.28
	**/
	public function getProjectCompanyType($no){
		if($no == null){
			return null;
		}
		if(1 == $no){
			$companyTypeStr = '国企（上市公司）';
		}elseif(2 == $no){
			$companyTypeStr = '外企（上市公司）';
		}elseif(3 == $no){
			$companyTypeStr = '私企（上市公司）';
		}elseif(4 == $no){
			$companyTypeStr = '国企（非上市公司）';
		}elseif(5 == $no){
			$companyTypeStr = '外企（非上市公司）';
		}elseif(6 == $no){
			$companyTypeStr = '私企（非上市公司）';
		}else{
			$companyTypeStr = '其他';
		}
		return $companyTypeStr;
	}

	/**
    **@auth qianqiang
    **@breif 获取并网方式
    **@date 2016.1.28
	**/
	public function getSynchronizeType($no){
		if($no == null){
			return null;
		}
		if(1 == $no){
			$synchronizeTypeStr = '全部自发自用';
		}elseif(2 == $no){
			$synchronizeTypeStr = '全额上网';
		}elseif(3 == $no){
			$synchronizeTypeStr = '自发自用，余额上网';
		}else{
			$synchronizeTypeStr = '其他';
		}
		return $synchronizeTypeStr;
	}

	/**
    **@auth qianqiang
    **@breif 获取融资方式
    **@date 2016.1.28
	**/
	public function getFinancingType($no){
		if($no == null){
			return null;
		}
		if(1 == $no){
			$financingTypeStr = '融资租赁（直租）';
		}elseif(2 == $no){
			$financingTypeStr = '融资租赁（回租）';
		}elseif(3 == $no){
			$financingTypeStr = '股权融资';
		}else{
			$financingTypeStr = '其他';
		}
		return $financingTypeStr;
	}

	/**
    **@auth qianqiang
    **@breif 获取电价结算方式
    **@date 2016.1.28
	**/
	public function getElectricityClearType($no){
		if($no == null){
			return null;
		}
		if(1 == $no){
			$str = '峰谷平电价打折';
		}elseif(2 == $no){
			$str = '平均电价打折';
		}elseif(3 == $no){
			$str = '固定电价';
		}else{
			$str = '其他';
		}
		return $str;
	}

	/**
    **@auth qianqiang
    **@breif 获取土地性质
    **@date 2016.1.28
	**/
	public function getGroundProperty($no){
		if($no == null){
			return null;
		}
		if(1 == $no){
			$str = '一般农田';
		}elseif(2 == $no){
			$str = '林地';
		}elseif(3 == $no){
			$str = '荒地';
		}elseif(4 == $no){
			$str = '鱼塘';
		}elseif(5 == $no){
			$str = '基本农田';
		}else{
			$str = '其他';
		}
		return $str;
	}

	/**
    **@auth qianqiang
    **@breif 获取土地平整情况
    **@date 2016.1.28
	**/
	public function getGroundCondition($no){
		if($no == null){
			return null;
		}
		if(1 == $no){
			$str = '平地';
		}elseif(2 == $no){
			$str = '山坡';
		}elseif(3 == $no){
			$str = '水面';
		}else{
			$str = '其他';
		}
		return $str;
	}

	/**
    **@auth qianqiang
    **@breif 获取计量点
    **@date 2016.1.28
	**/
	public function getMeasurePoint($no){
		if($no == null){
			return null;
		}
		if(1 == $no){
			$str = '站内';
		}elseif(2 == $no){
			$str = '变电站';
		}else{
			$str = '其他';
		}
		return $str;
	}

	/**
    **@auth qianqiang
    **@breif 获取项目支架类型
    **@date 2016.1.28
	**/
	public function getProjectHolderType($no){
		if($no == null){
			return null;
		}
		if(1 == $no){
			$str = '地面固定式';
		}elseif(2 == $no){
			$str = '单轴';
		}elseif(3 == $no){
			$str = '双轴';
		}else{
			$str = '其他';
		}
		return $str;
	}

	/**
    **@auth qianqiang
    **@breif 获取土地项目类型
    **@date 2016.1.28
	**/
	public function getGroundProjectType($no){
		if($no == null){
			return null;
		}
		if(1 == $no){
			$str = '地面';
		}elseif(2 == $no){
			$str = '农光互补';
		}elseif(3 == $no){
			$str = '鱼光互补';
		}else{
			$str = '其他';
		}
		return $str;
	}

	/**
    **@auth qianqiang
    **@breif 获取屋顶类型
    **@date 2016.1.28
	**/
	public function getHousetopType($no){
		if($no == null){
			return null;
		}
		if(1 == $no){
			$str = '混凝土';
		}elseif(2 == $no){
			$str = '彩钢瓦';
		}else{
			$str = '其他';
		}
		return $str;
	}

	/**
    **@auth qianqiang
    **@breif 获取屋顶朝向
    **@date 2016.1.28
	**/
	public function getHousetopDirection($no){
		if($no == null){
			return null;
		}
		if(1 == $no){
			$str = '正南';
		}elseif(2 == $no){
			$str = '东西向';
		}else{
			$str = '其他';
		}
		return $str;
	}

	/**
    **@auth qianqiang
    **@breif 计算附件大小
    **@date 2016.1.30
	**/
	public function getFileSize($size){
		$i = 1;
		$tempSize = $size/1024;
		while($tempSize<0 || $tempSize>1024){
			$size = $tempSize;
			$tempSize = $size/1024;
			$i += 1;
		}
		if($i == 1){
			$str = round($tempSize)."B";
		}elseif($i == 2){
			$str = round($tempSize)."KB";
		}elseif($i == 3){
			$str = round($tempSize)."M";
		}else{
			$str = round($tempSize)."G";
		}
		return $str;
	}

	/**
    **@auth qianqiang
    **@breif 获取与能融网合作方式
    **@param arrayStr合作方式数组
    **@date 2016.1.31
	**/
	public function getCooperationType($arrayStr){
		$typeStr = "";
		$i = 0;
		while($arrayStr[$i]){
			if($arrayStr[$i] == 1){
				$typeStr = $typeStr."EPC ";
			}elseif($arrayStr[$i] == 2){
				$typeStr = $typeStr."申请融资 ";
			}elseif($arrayStr[$i] == 3){
				$typeStr = $typeStr."推介项目 ";
			}elseif($arrayStr[$i] == 4){
				$typeStr = $typeStr."转让 ";
			}
			$i += 1;
		}
		return $typeStr;
	}

	/**
    **@auth qianqiang
    **@breif 获取有无遮挡,有无污染物
    **@date 2016.1.31
	**/
	public function getHasShelterOrPollution($no){
		if($no == null){
			return null;
		}
		if(1 == $no){
			$str = '有';
		}else{
			$str = '无';
		}
		return $str;
	}
}