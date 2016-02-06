<?php
namespace Home\Controller;

use Think\Controller;

class ProjectInvestorMyProController extends Controller {
	
    /**
    **@auth qiujinhan
    **@breif 在客服中导出一个项目的信息
    **@date 2016.1.17
    **@参数 ?c=InnerStaff&a=export&no=sss&token=xxxx&datatype=proinfo
    **/
    public function export()
    {
        
        isLogin($_COOKIE['email'],$_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 4);
        $projectCode  = $_POST['no']     ? $_POST['no']:$_GET['no'];
        $mProjectCode = $_POST['token']  ? $_POST['token']:$_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode); 
        $fatherUrl  = $_SERVER['HTTP_REFERER'];
        $getJsonFlag = 1;
        $innerToken = "InternalCall";
        import("Org.Util.PHPExcel");
        $obpe = new \PHPExcel();
        $obpe_pro = $obpe->getProperties();
        $obpe->getProperties()->setCreator('qiujinhan')//设置创建者
             ->setLastModifiedBy('2013/2/16 15:00')//设置时间
             ->setTitle('data')//设置标题
             ->setSubject('beizhu')//设置备注
             ->setDescription('miaoshu')//设置描述
             ->setKeywords('keyword')//设置关键字 | 标记
             ->setCategory('catagory');//设置类别
            //设置当前sheet索引,用于后续的内容操作
            //一般用在对个Sheet的时候才需要显示调用
            //缺省情况下,PHPExcel会自动创建第一个SHEET被设置SheetIndex=0
            //设置SHEET

 
        //-----------------------------------------导出---项目信息导出开始------------------------------------------//
        if(false !== strpos($fatherUrl,'a=projectInfoView'))
        {
            //获取项目信息
            $obj   = new ProjectProviderMyProController();
            $data = $obj->projectInfoEdit($projectCode, null, $getJsonFlag, $innerToken);
            $objProject  = D("Project","Service");
            $data['typeStr'] = $objProject->getTypeAndStateStr($data['project_type'], $data['build_state']);
            //处理一下信息
            $areaObj = D('Area', 'Service');
            $areaInfo = $data['county']?$data['county']:$data['city'];
            $areaInfo = $areaInfo?$areaInfo:$data['province'];
            $data['areaStr'] = $areaObj->getAreaById($areaInfo);
            $common = D("Common","Service");
            $data['companyType'] = $common->getProjectCompanyType($data['company_type']);
            $data['housetopType'] = $common->getHousetopType($data['housetop_type']);
            $data['synchronizeType'] = $common->getSynchronizeType($data['synchronize_type']);
            $data['financingType'] = $common->getFinancingType($data['financing_type']);
            $data['electricityClearType'] = $common->getElectricityClearType($data['electricity_clear_type']);
            $data['groundProperty'] = $common->getGroundProperty($data['ground_property']);
            $data['groundCondition'] = $common->getGroundProperty($data['ground_condition']);
            $data['measurePoint'] = $common->getMeasurePoint($data['measure_point']);
            $data['projectHolderType'] = $common->getProjectHolderType($data['project_holder_type']);
            $data['groundProjectType'] = $common->getGroundProjectType($data['ground_project_type']);
            $data['housetopDirection'] = $common->getHousetopDirection($data['housetop_direction']);
            $data  = array('projectInfo' => $data);
            //echo jj;
            //echo json_encode($data);exit;
            $objActSheet = $obpe->setactivesheetindex(0);
            //设置SHEET的名字
            $obpe->getActiveSheet()->setTitle('项目信息');   

            //设置列宽
            $obpe->getActiveSheet()->getColumnDimension('B')->setWidth(50);
            $obpe->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $obpe->createSheet();
            //靠左对齐
            $obpe->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            //对第一列的信息字体飘红
            $obpe->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A2:A3')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A4:A5')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A6:A7')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A8:A9')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A10:A11')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A12:A13')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A14:A15')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A16:A17')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A18:A19')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A20:A21')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A22:A23')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A24:A25')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A26:A27')->getFont()->getColor()->setARGB('D94600');


            //先处理好全部的第一列死信息
            $obpe->getactivesheet()->setcellvalue('A1', "项目类型");
            $obpe->getactivesheet()->setcellvalue('A2', "项目地点");
            //这个需要填充颜色 屋顶业主信息 6个

            $obpe->getActiveSheet()->getStyle( 'A3')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $obpe->getActiveSheet()->getStyle( 'A3')->getFill()->getStartColor()->setARGB('FFC78E');
            $obpe->getactivesheet()->setcellvalue('A3', "屋顶业主信息");   //这个需要填充颜色
            $obpe->getactivesheet()->setcellvalue('A4', "屋顶业主名称");
            $obpe->getactivesheet()->setcellvalue('A5', "企业类型");
            $obpe->getactivesheet()->setcellvalue('A6', "注册资本金");
            $obpe->getactivesheet()->setcellvalue('A7', "年用电量");
            $obpe->getactivesheet()->setcellvalue('A8', "电费");
            //屋顶情况  5个
            $obpe->getActiveSheet()->getStyle( 'A9')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $obpe->getActiveSheet()->getStyle( 'A9')->getFill()->getStartColor()->setARGB('FFC78E');
            $obpe->getactivesheet()->setcellvalue('A9', "屋顶情况 ");//这个需要填充颜色
            $obpe->getactivesheet()->setcellvalue('A10', "屋顶类型");
            $obpe->getactivesheet()->setcellvalue('A11', "屋顶面积");
            $obpe->getactivesheet()->setcellvalue('A12', "屋顶使用寿命");
            $obpe->getactivesheet()->setcellvalue('A13', "屋顶朝向");
            //电网接入 4个
            $obpe->getActiveSheet()->getStyle( 'A14')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $obpe->getActiveSheet()->getStyle( 'A14')->getFill()->getStartColor()->setARGB('FFC78E');
            $obpe->getactivesheet()->setcellvalue('A14', "电网接入"); //这个需要填充颜色
            $obpe->getactivesheet()->setcellvalue('A15', "上级变压器容量");
            $obpe->getactivesheet()->setcellvalue('A16', "并网电压等级");
            $obpe->getactivesheet()->setcellvalue('A17', "并网方式");
            //项目情况 5个
            $obpe->getActiveSheet()->getStyle( 'A18')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $obpe->getActiveSheet()->getStyle( 'A18')->getFill()->getStartColor()->setARGB('FFC78E');
            $obpe->getactivesheet()->setcellvalue('A18', "项目情况"); //这个需要填充颜色
            $obpe->getactivesheet()->setcellvalue('A19', "拟建设容量");
            $obpe->getactivesheet()->setcellvalue('A20', "与能融网合作方式");
            $obpe->getactivesheet()->setcellvalue('A21', "拟融资金额");
            $obpe->getactivesheet()->setcellvalue('A22', "融资方式");
            //项目文件 5个
            $obpe->getActiveSheet()->getStyle( 'A23')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $obpe->getActiveSheet()->getStyle( 'A23')->getFill()->getStartColor()->setARGB('FFC78E');
            $obpe->getactivesheet()->setcellvalue('A23', "项目文件"); //项目文件
            $obpe->getactivesheet()->setcellvalue('A24', "租赁年限");
            $obpe->getactivesheet()->setcellvalue('A25', "租赁租金");
            $obpe->getactivesheet()->setcellvalue('A26', "电价结算方式");
            $obpe->getactivesheet()->setcellvalue('A27', "结算电价");
            $obpe->getactivesheet()->setcellvalue('A29', "注:相关上传的图片资料,请到页面点击下载");


            //第二列的数据
            $obpe->getactivesheet()->setcellvalue('B1', $data['projectInfo']['typeStr']); //项目类型
            $obpe->getactivesheet()->setcellvalue('B2', $data['projectInfo']['areaStr']); //项目地点
            //$obpe->getactivesheet()->setcellvalue('B3', $data['projectInfo']['housetop_owner']);//屋顶业主信息
            $obpe->getactivesheet()->setcellvalue('B4', $data['projectInfo']['housetop_owner']);//屋顶业主名称
            $obpe->getactivesheet()->setcellvalue('B5', $data['projectInfo']['companyType']);//企业类型
            $obpe->getactivesheet()->setcellvalue('B6', $data['projectInfo']['company_capital'].'万元');//注册资本金
            $obpe->getactivesheet()->setcellvalue('B7', $data['projectInfo']['electricity_total'].'万度');//年用电量
            $obpe->getactivesheet()->setcellvalue('B8', $data['projectInfo']['electricity_pay'].'万元');//电费
            //$obpe->getactivesheet()->setcellvalue('B9', data);//屋顶情况
            $obpe->getactivesheet()->setcellvalue('B10', $data['projectInfo']['housetopType']);//屋顶类型
            $obpe->getactivesheet()->setcellvalue('B11', $data['projectInfo']['housetop_area'].'m2');//屋顶面积
            $obpe->getactivesheet()->setcellvalue('B12', $data['projectInfo']['housetop_age'].'年');//屋顶使用寿命
            $obpe->getactivesheet()->setcellvalue('B13', $data['projectInfo']['housetopDirection']); //屋顶朝向
            //$obpe->getactivesheet()->setcellvalue('B14', data);//电网接入
            $obpe->getactivesheet()->setcellvalue('B15', $data['projectInfo']['transformer_capacity'].'kVa');//上级变压器容量
            $obpe->getactivesheet()->setcellvalue('B16', $data['projectInfo']['voltage_level'].'kV'); //并网电压等级
            $obpe->getactivesheet()->setcellvalue('B17', $data['projectInfo']['synchronizeType']);//并网方式
            //$obpe->getactivesheet()->setcellvalue('B18', data);//项目情况
            $obpe->getactivesheet()->setcellvalue('B19', $data['projectInfo']['plan_build_volume'].'KW');//拟建设容量
            $obpe->getactivesheet()->setcellvalue('B20', $data['projectInfo']['cooperation_type']);//与能融网合作方式
            $obpe->getactivesheet()->setcellvalue('B21', $data['projectInfo']['plan_financing'].'万元');//拟融资金额
            $obpe->getactivesheet()->setcellvalue('B22', $data['projectInfo']['financingType']);//融资方式
            //$obpe->getactivesheet()->setcellvalue('B23', data);//项目文件
            $obpe->getactivesheet()->setcellvalue('B24', $data['projectInfo']['rent_time'].'年');//租赁年限
            $obpe->getactivesheet()->setcellvalue('B25', $data['projectInfo']['rent_pay'].'元/年');//租赁租金
            $obpe->getactivesheet()->setcellvalue('B26', $data['projectInfo']['electricityClearType']);//电价结算方式
            $obpe->getactivesheet()->setcellvalue('B27', $data['projectInfo']['electricity_clear']);//结算电价



            /*import("Org.Util.PHPExcel.IOFactory");  
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$projectCode.'_项目信息.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::CreateWriter($obpe,"Excel2007");
            $objWriter->save('php://output');*/
            //-----------------------------------------导出---项目信息导出结束------------------------------------------//
        }



        //-----------------------------------------导出---尽职调查导出开始------------------------------------------//
        if(false !== strpos($fatherUrl,'a=projectInfoView'))
        {
            //获取尽职调查
            $obj   = new InnerStaffController();
            list($picture,$docListInfo,$projectDetail,$areaArray,$evaluationInfo) = 
                $obj->dueDiligence($projectCode, null, $getJsonFlag,$innerToken);


            $objActSheet = $obpe->setactivesheetindex(1);
            //设置SHEET的名字
            $obpe->getActiveSheet()->setTitle('尽职调查信息');   

            //设置列宽
            $obpe->getActiveSheet()->getColumnDimension('B')->setWidth(50);
            $obpe->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $obpe->createSheet();
            //靠左对齐
            $obpe->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            //对第一列的信息字体飘红
            $obpe->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A2:A3')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A4:A5')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A6:A7')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A8:A9')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A10:A11')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A12:A13')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A14:A15')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A16:A17')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A18:A19')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A20:A21')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A22:A23')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A24:A25')->getFont()->getColor()->setARGB('D94600');


            //先处理好全部的第一列死信息
            //总体信息 8个
            $obpe->getActiveSheet()->getStyle( 'A1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $obpe->getActiveSheet()->getStyle( 'A1')->getFill()->getStartColor()->setARGB('FFC78E');
            $obpe->getactivesheet()->setcellvalue('A1', "总体信息");  //这个需要填充颜色
            $obpe->getactivesheet()->setcellvalue('A2', "内部收益率IRR");
            $obpe->getactivesheet()->setcellvalue('A3', "评价结果");
            $obpe->getactivesheet()->setcellvalue('A4', "静态投资回收年");
            $obpe->getactivesheet()->setcellvalue('A5', "动态投资回收年");
            $obpe->getactivesheet()->setcellvalue('A6', "LCOE");
            $obpe->getactivesheet()->setcellvalue('A7', "净现值npv");
            $obpe->getactivesheet()->setcellvalue('A8', "电站资产累计现值");
            //这个需要填充颜色 基本信息 9个
            $obpe->getActiveSheet()->getStyle( 'A11')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $obpe->getActiveSheet()->getStyle( 'A11')->getFill()->getStartColor()->setARGB('FFC78E');
            $obpe->getactivesheet()->setcellvalue('A11', "基本信息");   //这个需要填充颜色
            $obpe->getactivesheet()->setcellvalue('A12', "屋顶业主名称");
            $obpe->getactivesheet()->setcellvalue('A13', "拟建设容量");
            $obpe->getactivesheet()->setcellvalue('A14', "屋顶类型");
            $obpe->getactivesheet()->setcellvalue('A15', "拟融资金额");
            $obpe->getactivesheet()->setcellvalue('A16', "企业类型");
            $obpe->getactivesheet()->setcellvalue('A17', "项目地点");
            $obpe->getactivesheet()->setcellvalue('A18', "并网方式");
            $obpe->getactivesheet()->setcellvalue('A19', "融资方式");
            //评价内容  1个
            $obpe->getActiveSheet()->getStyle( 'A20')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $obpe->getActiveSheet()->getStyle( 'A20')->getFill()->getStartColor()->setARGB('FFC78E');
            $obpe->getactivesheet()->setcellvalue('A20', "评价内容 ");//这个需要填充颜色
            //评价情况 4个
            //$obpe->getActiveSheet()->getStyle( 'A21')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            //$obpe->getActiveSheet()->getStyle( 'A21')->getFill()->getStartColor()->setARGB('FFC78E');
            $obpe->getactivesheet()->setcellvalue('A21', "评价情况A,B,C"); //这个需要填充颜色
            $obpe->getactivesheet()->setcellvalue('A22', "A、文件审查");
            $obpe->getactivesheet()->setcellvalue('A23', "B、工程建设可行性");
            $obpe->getactivesheet()->setcellvalue('A24', "C、项目经济收益情况");
 
            $obpe->getactivesheet()->setcellvalue('A26', "注:相关上传的图片资料,请到页面点击下载");


            //第二列的数据
            $obpe->getactivesheet()->setcellvalue('B2', $evaluationInfo['irr']); //内部收益率IRR
            $obpe->getactivesheet()->setcellvalue('B3', $evaluationInfo['evaluation_result']);//评价结果
            $obpe->getactivesheet()->setcellvalue('B4', $evaluationInfo['static_payback_time']);//静态投资回收年
            $obpe->getactivesheet()->setcellvalue('B5', $evaluationInfo['dynamic_payback_time']);//动态投资回收年
            $obpe->getactivesheet()->setcellvalue('B6', $evaluationInfo['lcoe']);//LCOE
            $obpe->getactivesheet()->setcellvalue('B7', $evaluationInfo['npv'].'万元');//净现值npv
            $obpe->getactivesheet()->setcellvalue('B8', $evaluationInfo['power_asset_current_value']);//电站资产累计现值
            $obpe->getactivesheet()->setcellvalue('B12', $projectDetail['housetop_owner']);//屋顶业主名称
            $obpe->getactivesheet()->setcellvalue('B13', $projectDetail['plan_build_volume'].'kW'); //拟建设容量
            $obpe->getactivesheet()->setcellvalue('B14', $projectDetail['housetopType']);//屋顶类型
            $obpe->getactivesheet()->setcellvalue('B15', $projectDetail['plan_financing'].'万元');//拟融资金额
            $obpe->getactivesheet()->setcellvalue('B16', $projectDetail['companyType']); //企业类型
            $obpe->getactivesheet()->setcellvalue('B17', $projectDetail['project_address']);//项目地点
            $obpe->getactivesheet()->setcellvalue('B18', $projectDetail['synchronizeType']);//并网方式
            $obpe->getactivesheet()->setcellvalue('B19', $projectDetail['financingType']);//融资方式

            //处理下html代码
            $evaluationInfo['evaluation_content'] = str_replace('</p>','',$evaluationInfo['evaluation_content']);
            $evaluationInfo['evaluation_content'] = str_replace('<p>','',$evaluationInfo['evaluation_content']);
            $evaluationInfo['evaluation_content'] = str_replace('<br/>',"\n",$evaluationInfo['evaluation_content']);
            $evaluationInfo['document_review'] = str_replace('</p>','',$evaluationInfo['document_review']);
            $evaluationInfo['document_review'] = str_replace('<p>','',$evaluationInfo['document_review']);
            $evaluationInfo['document_review'] = str_replace('<br/>',"\n",$evaluationInfo['document_review']);
            $evaluationInfo['project_quality_situation'] = str_replace('</p>','',$evaluationInfo['project_quality_situation']);
            $evaluationInfo['project_quality_situation'] = str_replace('<p>','',$evaluationInfo['project_quality_situation']);
            $evaluationInfo['project_quality_situation'] = str_replace('<br/>',"\n",$evaluationInfo['project_quality_situation']);
            $evaluationInfo['project_earnings_situation'] = str_replace('</p>','',$evaluationInfo['project_earnings_situation']);
            $evaluationInfo['project_earnings_situation'] = str_replace('<p>','',$evaluationInfo['project_earnings_situation']);
            $evaluationInfo['project_earnings_situation'] = str_replace('<br/>',"\n",$evaluationInfo['project_earnings_situation']);

            $obpe->getActiveSheet()->getColumnDimension('B')->setWidth(100); 
            //大容量的文档
            $obpe->getActiveSheet()->getStyle('B20')->getAlignment()->setWrapText(true);
            $obpe->getActiveSheet()->getStyle('B23')->getAlignment()->setWrapText(true);
            $obpe->getActiveSheet()->getStyle('B24')->getAlignment()->setWrapText(true);
            $obpe->getActiveSheet()->getStyle('B22')->getAlignment()->setWrapText(true);
            $obpe->getactivesheet()->setcellvalue('B20', $evaluationInfo['evaluation_content']);//评价内容
            $obpe->getactivesheet()->setcellvalue('B22', $evaluationInfo['document_review']);//A、文件审查
            $obpe->getactivesheet()->setcellvalue('B23', $evaluationInfo['project_quality_situation']);//B、工程建设可行性
            $obpe->getactivesheet()->setcellvalue('B24', $evaluationInfo['project_earnings_situation']);//C、项目经济收益情况
            //删除掉多余的行
            $obpe->getActiveSheet()->removeRow(9, 2);

            import("Org.Util.PHPExcel.IOFactory");  
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$projectCode.'_项目信息.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::CreateWriter($obpe,"Excel2007");
            $objWriter->save('php://output');
            //-----------------------------------------导出---尽职调查导出结束------------------------------------------//
        }


    }

    /**
    **@auth qianqiang
    **@breif 项目投资方->项目管理->推荐项目
    **@date 2016.1.5
    **/
	public function recommendedProject(){
		isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 4);
		$email = $_COOKIE['email'];
		// $email = 'qianqiang@qq.com';
        $loginFlag = $_GET['r'];//登录后调用标记
        isDataComplete($email, $loginFlag);
        $page = $_GET['page'];
        if(empty($page)) $page=1;
        $pageSize = 6;
		$objProject = D("Project", "Service");
        $listProject = $objProject->getPushProject($email, $page);
        $listTotal = $objProject->getPushProject($email, -1);
        $data = array();
        $data["list"] = $listProject;
        $data["page"] = $page;
        $data["count"] = sizeof($listTotal);
        $data["totalPage"] = ceil($data["count"]/$pageSize+1);
        $data["endPage"] = ceil($data["count"]/$pageSize);
        if($_GET['display']=="json"){
            header('Content-Type: text/html; charset=utf-8');
            dump($data);
            exit;
        }
        $this->assign('arrData', $data);
        $this->display("ProjectInvestor:recommendedProject");
	}

	/**
    **@auth qianqiang
    **@breif 项目投资方->项目管理->已投资项目
    **@date 2016.1.5
    **/
	public function investmentProject(){
		isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 4);
		$email = $_COOKIE['email'];
        isDataComplete($email);
		//是否需要做？需要建立项目投资表还是加一个字段
		$this->display("ProjectInvestor:investmentProject");
	}
/**
    **@auth qiujinhan
    **@breif 项目投资方->查看项目信息进度
    **@date 2016.01.03
    **/
    public function projectInfoView()
    {
        //判断登陆，并且获取用户名的email
        isLogin($_COOKIE['email'],$_COOKIE['mEmail']);     
        authentication($_COOKIE['email'], 4);
        //接收参数
        $projectCode  = $_POST['no']     ? $_POST['no']:$_GET['no'];
        $mProjectCode = $_POST['token']  ? $_POST['token']:$_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode);
        $optype       = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $rtype        = $_POST['rtype']  ? $_POST['rtype']:$_GET['rtype'];
        //签署意向书的同意按钮，其实是去project和Housetop两个表中更新下status字段就可以了
        $objProject  = D("Project","Service");
        $objProject->cancelPushHighlight($projectCode, $_COOKIE['email']);
        $getJsonFlag = 1;
        //获取项目信息
        $obj   = new ProjectProviderMyProController();
        $innerToken = "InternalCall";
        $data = $obj->projectInfoEdit($projectCode, null, $getJsonFlag,$innerToken);
        $common = D("Common","Service");
        $areaObj = D('Area', 'Service');
        $data['areaStr'] = $areaObj->getAreaById($data['project_area']).$data['project_address'];
        $data['companyType'] = $common->getProjectCompanyType($data['company_type']);
        $data['housetopType'] = $common->getHousetopType($data['housetop_type']);
        $data['synchronizeType'] = $common->getSynchronizeType($data['synchronize_type']);
        $data['financingType'] = $common->getFinancingType($data['financing_type']);
        $data['electricityClearType'] = $common->getElectricityClearType($data['electricity_clear_type']);
        $data['groundProperty'] = $common->getGroundProperty($data['ground_property']);
        $data['groundCondition'] = $common->getGroundCondition($data['ground_condition']);
        $data['measurePoint'] = $common->getMeasurePoint($data['measure_point']);
        $data['projectHolderType'] = $common->getProjectHolderType($data['project_holder_type']);
        $data['groundProjectType'] = $common->getGroundProjectType($data['ground_project_type']);
        $data['housetopDirection'] = $common->getHousetopDirection($data['housetop_direction']);
        $data['cooperationType'] = $common->getCooperationType($data['cooperation_type']);
        $data['hasShelter'] = $common->getHasShelterOrPollution($data['has_shelter']);
        $data['hasPollute'] = $common->getHasShelterOrPollution($data['has_pollute']);
        $data['hasPollution'] = $common->getHasShelterOrPollution($data['has_pollution']);
        // dump($data);exit;
        //echo $data['project_type'];exit;
        //获取签署意向书信息 
        if($optype == 'agree' &&  $rtype == 1)
        {
            $res = $objProject->updateProjectStatus($data["id"]);
        }

        $projectInfoForIntent = $objProject->getProjectDetail($data["id"], $data['project_type']);
        $status = $projectInfoForIntent["status"];//
        //echo $projectCode;exit;
        //echo $data['project_type'];exit;
        //echo json_encode($projectInfoForIntent);exit;
        //获取强哥的尽职调查信息
        $obj   = new InnerStaffController();
        list($picture,$docListInfo,$projectDetail,$areaArray,$evaluationInfo) = $obj->dueDiligence($projectCode, null, $getJsonFlag,$innerToken);

        //先判断一下当前进度的状态
        //12项目已提交（客服未提交意向书）、13项目已提交（客服已提交意向书）、
        //21签意向合同（客服未提交尽职调查）、22签意向合同（客服已提交尽职调查）
        //"state":"dueDiligence" // projectInfo, intent, dueDiligence
        //substate":"submited" // wait, submited, signed
        if ($status == 12) //12项目已提交（已提交）
        {
            $strStatus = "dueDiligence";
            $substate  = "wait";
        }
        elseif($status == 22)  //13已提交意向书（已提交）
        {
            $strStatus = "dueDiligence";
            $substate  = "submited";
        }
        elseif($status == 13)  //13已提交意向书（已提交）
        {
            $strStatus = "dueDiligence";
            $substate  = "submited";
        }
        else   //21已签意向书
        {
            $strStatus = "dueDiligence";
            $substate  = "submited";
        }


        //拼接大json
        $bigArr = array();
        $bigArr['step'] = array();
        $bigArr['step']['state'] = $strStatus;
        $bigArr['step']['substate'] = $substate;
        $bigArr['projectInfo'] = $data;//echo json_encode($bigArr);exit;
        $bigArr['intent'] = $projectInfoForIntent['project_intent'];
        //$picture,$docListInfo,$projectDetail,$areaArray,$evaluationInfo

        $bigArr['dueDiligence'] = array();
        $bigArr['dueDiligence']['picture'] = $picture;
        $bigArr['dueDiligence']['docListInfo'] = $docListInfo;
        $bigArr['dueDiligence']['areaArray'] = $areaArray;
        $bigArr['dueDiligence']['projectDetail'] = $projectDetail;
        $bigArr['dueDiligence']['evaluationInfo'] = $evaluationInfo;
        //echo json_encode($bigArr);exit;


        //判断显示哪个前端页面


        //$bigJson = '{"step":{"state":"dueDiligence","substate":"submited"},"projectInfo":{},"intent":{},"dueDiligence":{}}';
        //$bigJson = json_encode($bigArr);
        //echo json_encode($bigArr);exit;
        $this->assign('data', $bigArr);
        if($data['project_type'] == 1){
            if($data['build_state'] == 1){
                $this->display("ProjectInvestor:projectInfoView_housetop_nonbuild");
            }elseif($data['build_state'] == 2){
                $this->display("ProjectInvestor:projectInfoView_housetop_build");
            }
        }elseif($data['project_type'] == 2 || $data['project_type'] == 3){
            if($data['build_state'] == 1){
                $this->display("ProjectInvestor:projectInfoView_ground_nonbuild");
            }elseif($data['build_state'] == 2){
                $this->display("ProjectInvestor:projectInfoView_ground_build");
            }
        }

    }

}