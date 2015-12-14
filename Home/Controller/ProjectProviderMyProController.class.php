<?php
namespace Home\Controller;

use Think\Controller;

class ProjectProviderMyProController extends Controller {
    
    /**
    **@auth qiujinhan@gmail.com
    **@breif 项目提供方->项目信息未填写入口
    **@date 2015.12.05
    **/
    public function projectInfoNew()
    {
    	$this->display("ProjectProvider:projectInfoNew");
    }


    /**
    **@auth qiujinhan@gmail.com
    **@breif 项目提供方->待评估项目入口
    **@date 2015.12.13
    **/
    public function awaitingAssessment()
    {
        $this->display("ProjectProvider:awaitingAssessment");
    }


    /**
    **@auth qiujinhan@gmail.com
    **@breif 项目提供方->项目信息编辑入口
    **@date 2015.12.05
    **/
	public function projectInfoEdit()
    {
    	//操作类型为1是插入和保存数据
    	$optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
    	if ( $optype == "save" && $rtype == 1)
    	{
            //项目保存
            //接收前端表单过来的参数
            //上传图片是调用一个函数，返回一个url
            //上传doc等文件，调用一个函数，返回一个url
            //拼接url插入数据
            //这里数据只保持到xxx_sava编码中
	    }
        elseif ( $optype == "commit" && $rtype == 1)
        {
            //项目提交
            //这里数据只保持到xxx编码中
        }
	    elseif ( $optype == "delete" && $rtype == 1)
	    {
            //项目删除
            //这里删除以xxx开头的编号数据
	    }
        elseif($rtype != 1)
        {
            //这个分支是做数据的显示
            $projectCode = $_POST['project_code'] ? $_POST['project_code']:$_GET['project_code'];
            $display     =$_GET['display'];
            //根据project_code去查询项目信息
            $objProject  = D("Project","Service");
            $projectInfo = $objProject->getProjectInfo($projectCode);
            if ($display=="json")
            {
                echo json_encode($projectInfo);
                exit;
            }
            $this->assign('project',$projectInfo);
            $this->display();
        }
    }


}