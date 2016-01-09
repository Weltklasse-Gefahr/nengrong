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
        $objDoc = M("Doc");
        $docInfo = $objDoc->where($condition)->select();
        return $docInfo;
    }

    /**
    **@auth qiujinhan
    **@breif 上传图片或者文档
    **@param arrPhotosAndFile 数组，要上传的图片和文件名称
    **@param arrFile 数组，文档的名称
    **@return 一个数组，里面是图片名对于的附件表id，例如$arrDocId = array("business_license"=>"12","financial_audit"=>"13")
    **@date 2015.12.05
    **/
    public function uploadFileAndPictrue($arrPhotosAndFile, $arrFile){
        //上传图片资料上传,
        $hiddenId = "_hiddenId";
        $arrDocId = array();
        foreach($arrPhotosAndFile as $val)
        {
            //xxx_hiddenId是前端用来控制图片删除和改进状态的，可以见文件上传接口设计.jpg
            if(!empty($_POST[$val.$hiddenId])) //business_license_hiddenId
            {
                //xxx_hiddenId有值说明当前已经有图片存在，点击保存时候没有做任何操作
                continue;
            }
            if(empty($_POST[$val.$hiddenId])) //business_license_hiddenId
            {
                $arrDocId[$val] = "";
            }
            if(!empty($_FILES[$val]))  //business_license
            {
                if(in_array($val, $arrFile))
                {
                    //这个是处理文档的分支
                    $res = uploadFileOne($_FILES[$val], "ProjectProvider".$email);
                    //文档的保持路径url，中文名，和上传时间，保存到ENF_Doc表中
                    $pictureUrl = "/userdata/file/".$res; 
                    $pictureName =  $_FILES[$val]["name"];
                    $objUser = D("Doc","Service");
                    $returnId = $objUser->insert($pictureName, $pictureUrl);
                }
                else
                {
                    //这个是处理图片的分支
                    $res = uploadPicOne($_FILES[$val], "ProjectProvider".$email);
                    //图片的保持路径url，中文名，和上传时间，保存到ENF_Doc表中
                    $pictureUrl = "/userdata/img/".$res; 
                    $pictureName =  $_FILES[$val]["name"];
                    $objUser = D("Doc","Service");
                    $returnId = $objUser->insert($pictureName, $pictureUrl);
                }
                if($returnId == false)
                {
                    echo '{"code":"-1","msg":"更新失败！"}';
                    exit;
                }
                $arrDocId[$val] = $returnId;
            }
        }
        return $arrDocId;
    }

    /**
    **@auth qianqiang
    **@breif 获取附件数组
    **@param docList附件id数组
    **@return 一个数组
    **@date 2016.1.9
    **/
    public function getAllDocInfo($docList){
        $i = 0;
        while($docList[$i]){
            $condition['id'] = $docList[$i];
            $objDoc = M("Doc");
            $docInfo = $objDoc->where($condition)->select();
            $docListInfo[$i] = $docInfo;
            $i += 1;
        }
        return $docListInfo;
    }

}
