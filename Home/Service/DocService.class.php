<?php
namespace Home\Service;

use Think\Model;

class DocService extends Model{
	/**
    **@auth qiujinhan
    **@breif 上传成功后插入一条文件信息
    **@date 2015.12.19
    **@return 插入成功返回id，失败返回false
    **/
	public function insert($pictureName, $pictureUrl){
        $Doc = M("Doc"); 
        $data['file_name'] = $pictureName;
        $data['file_rename'] = $pictureUrl;
        $data['update_date'] = date("Y-m-d H:i:s" ,time());
        $res = $Doc->add($data);
        //查询刚才插入的id
        $condition['file_rename'] = $pictureUrl;
        $docInfo = $Doc->where($condition)->select();
        return !empty($docInfo) ? $docInfo[0]["id"]:false;
	}

    /**
    **@auth qiujinhan
    **@breif 根据id获取文件信息
    **@param condition 数组，查询的条件
    **@return 一个数组
    **@date 2015.12.05
    **/
    public function getDocInfo($condition){
        $objUser = M("Doc");
        $userInfo = $objUser->where($condition)->select();
        return $userInfo;
    }

}
