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
        if(1)//false !== strpos($fatherUrl,'a=projectInfo'))
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
            //echo json_encode($data);exit;
            //屋顶 未建---------------------------项目信息------------------------开始build_state
            if($data['projectInfo']['project_type'] == 1 && $data['projectInfo']['build_state'] == 1)
            {
                //用一种新的方式啊，写一个数组，全部的内容到这个数组，然后写两个for循环就ok
                $baseInfo = array(
                    "项目信息" => 'color',  //如果是color的话就要填充颜色
                    "项目名称" => $data['projectInfo']['project_name'],
                    "项目类型" => $data['projectInfo']['typeStr'],
                    "项目地点" => $data['projectInfo']['areaStr'],
                    "联系人信息" => 'color',
                    "联系人" => $data['projectInfo']['contacts_name'],
                    "联系方式" => $data['projectInfo']['contacts_phone'],
                    "邮件地址" => $data['projectInfo']['contacts_email'],
                    "屋顶业主信息" => 'color',
                    "屋顶业主名称" => $data['projectInfo']['housetop_owner'],
                    "企业类型" => $data['projectInfo']['companyType'],
                    "注册资本金" => $data['projectInfo']['company_capital'].'万元',
                    "年用电量" => $data['projectInfo']['electricity_total'].'万度',
                    "电费" => $data['projectInfo']['electricity_total'].'万度',
                    "屋顶情况" => 'color',
                    "屋顶类型" => $data['projectInfo']['housetop_type_other'].$data['projectInfo']['housetopType'],
                    "屋顶面积" => $data['projectInfo']['housetop_area'].'m2',
                    "屋顶使用寿命" => $data['projectInfo']['housetop_age'].'年',
                    "屋顶朝向" => $data['projectInfo']['housetop_direction_other'].$data['projectInfo']['housetopDirection'],
                    "电网接入" => 'color',
                    "上级变压器容量" => $data['projectInfo']['transformer_capacity'].'kVA',
                    "并网电压等级" => $data['projectInfo']['voltage_level'].'kV',
                    "并网方式" => $data['projectInfo']['synchronizeType'],
                    "项目情况" => 'color',
                    "拟建设容量" => $data['projectInfo']['plan_build_volume'].'kW',
                    "与能融网合作方式" => $data['projectInfo']['cooperationType'],
                    "拟融资金额" => $data['projectInfo']['plan_financing'].'万元',
                    "融资方式" => $data['projectInfo']['financingType'],
                    "项目文件信息" => 'color',
                    "租赁年限" => $data['projectInfo']['rent_time'].'年',
                    "租赁租金" => $data['projectInfo']['rent_pay'].'元/年',
                    "电价结算方式" => $data['projectInfo']['electricityClearType'],
                    "结算电价" => $data['projectInfo']['electricity_clear'],
                );
                //第一个for循环搞出全部数据
                $indexA = 1;
                foreach($baseInfo as $k=>$v)
                {
                     if($v == 'color')
                     {
                        $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                        $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->getStartColor()->setARGB('FFC78E');
                        $obpe->getactivesheet()->setcellvalue('A'.$indexA, $k); //项目文件
                     }
                     else
                     {
                          $obpe->getactivesheet()->setcellvalue('A'.$indexA, $k);
                          $obpe->getactivesheet()->setcellvalue('B'.$indexA, $v);
                     }
                     $indexA = $indexA + 1;
                }
                //对第一列的信息字体飘红
                for($i=1; $i<=$indexA ;$i++)
                {
                    $obpe->getActiveSheet()->getStyle('A'.$i)->getFont()->getColor()->setARGB('D94600');
                } 

                $indexA = $indexA + 1;
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "注:相关上传的图片资料,请到页面点击下载");
            }
            //屋顶 未建---------------------------项目信息------------------------结束


            //屋顶 已建--------------------------项目信息-------------------------开始
            if ($data['projectInfo']['project_type'] == 1 && $data['projectInfo']['build_state'] == 2)
            {
                //用一种新的方式啊，写一个数组，全部的内容到这个数组，然后写两个for循环就ok
                $baseInfo = array(
                    "项目信息" => 'color',  //如果是color的话就要填充颜色
                    "项目名称" => $data['projectInfo']['project_name'],
                    "项目类型" => $data['projectInfo']['typeStr'],
                    "项目地点" => $data['projectInfo']['areaStr'],
                    "联系人信息" => 'color',
                    "联系人" => $data['projectInfo']['contacts_name'],
                    "联系方式" => $data['projectInfo']['contacts_phone'],
                    "邮件地址" => $data['projectInfo']['contacts_email'],
                    "屋顶业主信息" => 'color',
                    "屋顶业主名称" => $data['projectInfo']['housetop_owner'],
                    "企业类型" => $data['projectInfo']['companyType'],
                    "注册资本金" => $data['projectInfo']['company_capital'].'万元',
                    "年用电量" => $data['projectInfo']['electricity_total'].'万度',
                    "电费" => $data['projectInfo']['electricity_total'].'万度',
                    "屋顶情况" => 'color',
                    "屋顶类型" => $data['projectInfo']['housetop_type_other'].$data['projectInfo']['housetopType'],
                    "屋顶面积" => $data['projectInfo']['housetop_area'].'m2',
                    "屋顶防水周期" => $data['projectInfo']['housetop_waterproof_time'].'年',
                    "屋顶使用寿命" => $data['projectInfo']['housetop_age'].'年',
                    "屋顶朝向" => $data['projectInfo']['housetop_direction_other'].$data['projectInfo']['housetopDirection'],
                    "屋顶活载荷" => $data['projectInfo']['housetop_load'].'kN/m2',
                    "附近有无遮挡" => $data['projectInfo']['hasShelter'],
                    "有无污染源" => $data['projectInfo']['hasPollution'],
                    "电网接入" => 'color',
                    "上级变压器容量" => $data['projectInfo']['transformer_capacity'].'kVA',
                    "并网电压等级" => $data['projectInfo']['voltage_level'].'kV',
                    "电网接入点距离" => $data['projectInfo']['electricity_distance'].'m',
                    "并网方式" => $data['projectInfo']['synchronizeType'],
                    //"xxx" => $data['projectInfo']['xxxxxxxx'],
                    "项目情况" => 'color',
                    "建设容量" => $data['projectInfo']['plan_build_volume'].'kW',
                    "单位投资" => $data['projectInfo']['company_invest'].'元/W',
                    "EPC厂家" => $data['projectInfo']['company_epc'],
                    "资质等级" => $data['projectInfo']['capacity_level'],
                    "并网时间" => $data['projectInfo']['synchronize_date'],
                    "历史发电量数据（最近一年）" => $data['projectInfo']['electricity_data'].'万度',
                    "与能融网合作方式" => $data['projectInfo']['cooperationType'],
                    "拟融资金额" => $data['projectInfo']['plan_financing'].'万元',
                    "融资方式" => $data['projectInfo']['financingType'],
                    "项目文件信息" => 'color',
                    "租赁年限" => $data['projectInfo']['rent_time'].'年',
                    "租赁租金" => $data['projectInfo']['rent_pay'].'元/年',
                    "电价结算方式" => $data['projectInfo']['electricityClearType'],
                    "结算电价" => $data['projectInfo']['electricity_clear'],
                );
                //第一个for循环搞出全部数据
                $indexA = 1;
                foreach($baseInfo as $k=>$v)
                {
                     if($v == 'color')
                     {
                        $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                        $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->getStartColor()->setARGB('FFC78E');
                        $obpe->getactivesheet()->setcellvalue('A'.$indexA, $k); //项目文件
                     }
                     else
                     {
                          $obpe->getactivesheet()->setcellvalue('A'.$indexA, $k);
                          $obpe->getactivesheet()->setcellvalue('B'.$indexA, $v);
                     }
                     $indexA = $indexA + 1;
                }
                $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "组件"); //这个需要填充颜色
                $indexA = $indexA + 1;
                foreach($data['projectInfo']['components'] as $k=>$v)
                {     
                    $obpe->getactivesheet()->setcellvalue('A'.$indexA, "组件厂家 ".$v['component_company']);
                    $obpe->getactivesheet()->setcellvalue('B'.$indexA, "规格型号 ".$v['component_type'].' , '.' 数量 '.$v['component_count'].'个');
                    $indexA=$indexA+1;
                }
                //逆变器
                $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "逆变器"); //这个需要填充颜色
                $indexA = $indexA+1;
                foreach($data['projectInfo']['inverters'] as $k=>$v)
                {
                    //$indexA = 36;
                    $obpe->getactivesheet()->setcellvalue('A'.$indexA, "逆变器厂家 ".$v['inverter_company']);
                    $obpe->getactivesheet()->setcellvalue('B'.$indexA, "规格型号 ".$v['inverter_type'].' , '.'数量 '.$v['inverter_count'].'个');
                    $indexA  = $indexA +1;  
                }
                //对第一列的信息字体飘红
                for($i=1; $i<=$indexA ;$i++)
                {
                    $obpe->getActiveSheet()->getStyle('A'.$i)->getFont()->getColor()->setARGB('D94600');
                } 

                $indexA = $indexA + 1;
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "注:相关上传的图片资料,请到页面点击下载");
            }
            //屋顶 已建--------------------------项目信息-------------------------结束

            //地面 未建----------------------项目信息-----------------------------开始
            if (($data['projectInfo']['project_type'] == 2 || $data['projectInfo']['project_type'] == 3) && $data['projectInfo']['build_state'] == 1)
            {
                //echo jj;exit;
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
                $obpe->getactivesheet()->setcellvalue('A1', "项目名称");
                $obpe->getactivesheet()->setcellvalue('A2', "项目类型");
                $obpe->getactivesheet()->setcellvalue('A3', "项目地点");
                //这个需要填充颜色 联系人信息4个
                $obpe->getActiveSheet()->getStyle( 'A4')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A4')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A4', "联系人信息");   //这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A5', "联系人");
                $obpe->getactivesheet()->setcellvalue('A6', "联系方式");
                $obpe->getactivesheet()->setcellvalue('A7', "邮件地址");               
                //土地情况  6个
                $obpe->getActiveSheet()->getStyle( 'A8')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A8')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A8', "土地情况");//这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A9', "土地性质租");
                $obpe->getactivesheet()->setcellvalue('A10', "凭土地面积");
                $obpe->getactivesheet()->setcellvalue('A11', "租凭年限");
                $obpe->getactivesheet()->setcellvalue('A12', "租凭资金");
                $obpe->getactivesheet()->setcellvalue('A13', "土地平整情况");               
                //电网接入 5个
                $obpe->getActiveSheet()->getStyle( 'A14')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A14')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A14', "电网接入"); //这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A15', "上级变压器容量");
                $obpe->getactivesheet()->setcellvalue('A16', "并网电压等级");
                $obpe->getactivesheet()->setcellvalue('A17', "电网接入点距离");
                $obpe->getactivesheet()->setcellvalue('A18', "计量点");
                //项目情况 7个
                $obpe->getActiveSheet()->getStyle( 'A19')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A19')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A19', "项目情况"); //这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A20', "拟建设容量");
                $obpe->getactivesheet()->setcellvalue('A21', "项目支架类型");
                $obpe->getactivesheet()->setcellvalue('A22', "项目类型");
                $obpe->getactivesheet()->setcellvalue('A23', "与能融网合作方式");
                $obpe->getactivesheet()->setcellvalue('A24', "拟融资金额");
                $obpe->getactivesheet()->setcellvalue('A25', "融资方式");
                $obpe->getactivesheet()->setcellvalue('A27', "注:相关上传的图片资料,请到页面点击下载");
                //第二列的数据
                $obpe->getactivesheet()->setcellvalue('B1', $data['projectInfo']['project_name']); //项目名称
                $obpe->getactivesheet()->setcellvalue('B2', $data['projectInfo']['typeStr']); //项目类型
                $obpe->getactivesheet()->setcellvalue('B3', $data['projectInfo']['areaStr']);//项目地点
                //$obpe->getactivesheet()->setcellvalue('B4', $data['projectInfo']['housetop_owner']);//联系人信息
                $obpe->getactivesheet()->setcellvalue('B5', $data['projectInfo']['contacts_name']);//联系人
                $obpe->getactivesheet()->setcellvalue('B6', $data['projectInfo']['contacts_phone']);//联系方式
                $obpe->getactivesheet()->setcellvalue('B7', $data['projectInfo']['contacts_email']);//邮件地址
                //$obpe->getactivesheet()->setcellvalue('B8', $data['projectInfo']['electricity_pay'].'万元');//土地情况
                $obpe->getactivesheet()->setcellvalue('B9', $data['projectInfo']['ground_property_other'].$data['projectInfo']['groundProperty']);//土地性质租
                $obpe->getactivesheet()->setcellvalue('B10', $data['projectInfo']['ground_area'].'m2');//凭土地面积
                $obpe->getactivesheet()->setcellvalue('B11', $data['projectInfo']['rent_time'].'年');//租凭年限
                $obpe->getactivesheet()->setcellvalue('B12', $data['projectInfo']['rent_pay'].'元/年');//租凭资金
                $obpe->getactivesheet()->setcellvalue('B13', $data['projectInfo']['groundCondition']); //土地平整情况
                //$obpe->getactivesheet()->setcellvalue('B14', data);//电网接入
                $obpe->getactivesheet()->setcellvalue('B15', $data['projectInfo']['transformer_capacity'].'kVA');//上级变压器容量
                $obpe->getactivesheet()->setcellvalue('B16', $data['projectInfo']['voltage_level'].'kV'); //并网电压等级
                $obpe->getactivesheet()->setcellvalue('B17', $data['projectInfo']['electricity_distance'].'m');//电网接入点距离
                $obpe->getactivesheet()->setcellvalue('B18', $data['projectInfo']['measurePoint']);//计量点
                //$obpe->getactivesheet()->setcellvalue('B19', $data['projectInfo']['plan_build_volume'].'KW');//项目情况
                $obpe->getactivesheet()->setcellvalue('B20', $data['projectInfo']['plan_build_volume'].'KW');//拟建设容量
                $obpe->getactivesheet()->setcellvalue('B21', $data['projectInfo']['projectHolderType']);//项目支架类型
                $obpe->getactivesheet()->setcellvalue('B22', $data['projectInfo']['groundProjectType']);//项目类型
                $obpe->getactivesheet()->setcellvalue('B23', $data['projectInfo']['groundProjectType']);//与能融网合作方式
                $obpe->getactivesheet()->setcellvalue('B24', $data['projectInfo']['plan_financing'].'万元');//拟融资金额
                $obpe->getactivesheet()->setcellvalue('B25', $data['projectInfo']['financingType']);//融资方式

            }
            //地面 未建----------------------项目信息-----------------------------结束

            //地面 已建----------------------项目信息-----------------------------开始
            if (($data['projectInfo']['project_type'] == 2 || $data['projectInfo']['project_type'] == 3) && $data['projectInfo']['build_state'] == 2)
            {

                //先处理好全部的第一列死信息
                $obpe->getactivesheet()->setcellvalue('A1', "项目名称");
                $obpe->getactivesheet()->setcellvalue('A2', "项目类型");
                $obpe->getactivesheet()->setcellvalue('A3', "项目地点");
                //这个需要填充颜色 联系人信息4个
                $obpe->getActiveSheet()->getStyle( 'A4')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A4')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A4', "联系人信息");   //这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A5', "联系人");
                $obpe->getactivesheet()->setcellvalue('A6', "联系方式");
                $obpe->getactivesheet()->setcellvalue('A7', "邮件地址");               
                //土地情况  6个
                $obpe->getActiveSheet()->getStyle( 'A8')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A8')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A8', "土地情况");//这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A9', "土地性质租");
                $obpe->getactivesheet()->setcellvalue('A10', "凭土地面积");
                $obpe->getactivesheet()->setcellvalue('A11', "租凭年限");
                $obpe->getactivesheet()->setcellvalue('A12', "租凭资金");
                $obpe->getactivesheet()->setcellvalue('A13', "土地平整情况"); 
                //已建比未建多了，4个   中控室建筑面积   出让金额  附近有无遮挡   有无污染源
                $obpe->getactivesheet()->setcellvalue('A14', "中控室建筑面积");     
                $obpe->getactivesheet()->setcellvalue('A15', "出让金额"); 
                $obpe->getactivesheet()->setcellvalue('A16', "附近有无遮挡"); 
                $obpe->getactivesheet()->setcellvalue('A17', "有无污染源"); 
                //电网接入 5个
                $obpe->getActiveSheet()->getStyle( 'A18')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A18')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A18', "电网接入"); //这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A19', "上级变压器容量");
                $obpe->getactivesheet()->setcellvalue('A20', "并网电压等级");
                $obpe->getactivesheet()->setcellvalue('A21', "电网接入点距离");
                $obpe->getactivesheet()->setcellvalue('A22', "计量点");
                //项目情况 12个
                $obpe->getActiveSheet()->getStyle( 'A23')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A23')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A23', "项目情况"); //这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A24', "建设容量");
                $obpe->getactivesheet()->setcellvalue('A25', "单位投资");
                $obpe->getactivesheet()->setcellvalue('A26', "EPC厂家");
                $obpe->getactivesheet()->setcellvalue('A27', "资质等级");
                $obpe->getactivesheet()->setcellvalue('A28', "并网时间");
                $obpe->getactivesheet()->setcellvalue('A29', "历史发电量数据（最近一年）");
                $obpe->getactivesheet()->setcellvalue('A30', "项目支架类型");
                $obpe->getactivesheet()->setcellvalue('A31', "项目类型");
                $obpe->getactivesheet()->setcellvalue('A32', "与能融网合作方式");
                $obpe->getactivesheet()->setcellvalue('A33', "拟融资金额");
                $obpe->getactivesheet()->setcellvalue('A34', "融资方式");
                //这里加上组件和逆变器，循环体啊,  发现上面 的代码写得太恶心了，应该用for循环啊
                $obpe->getActiveSheet()->getStyle( 'A35')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A35')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A35', "组件"); //这个需要填充颜色
                $indexA = 36;
                foreach($data['projectInfo']['components'] as $k=>$v)
                {     
                    $obpe->getactivesheet()->setcellvalue('A'.$indexA, "组件厂家 ".$v['component_company']);
                    $obpe->getactivesheet()->setcellvalue('B'.$indexA, "规格型号 ".$v['component_type'].' , '.' 数量 '.$v['component_count'].'个');
                    $indexA=$indexA+1;
                }
                //逆变器
                $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "逆变器"); //这个需要填充颜色
                $indexA = $indexA+1;
                foreach($data['projectInfo']['inverters'] as $k=>$v)
                {
                    //$indexA = 36;
                    $obpe->getactivesheet()->setcellvalue('A'.$indexA, "逆变器厂家 ".$v['inverter_company']);
                    $obpe->getactivesheet()->setcellvalue('B'.$indexA, "规格型号 ".$v['inverter_type'].' , '.'数量 '.$v['inverter_count'].'个');
                    $indexA  = $indexA +1;  
                }
                //对第一列的信息字体飘红
                for($i=1; $i<=$indexA ;$i++)
                {
                    $obpe->getActiveSheet()->getStyle('A'.$i)->getFont()->getColor()->setARGB('D94600');
                }

                $indexA = $indexA+2;
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "注:相关上传的图片资料,请到页面点击下载");
                //第二列的数据
                $obpe->getactivesheet()->setcellvalue('B1', $data['projectInfo']['project_name']); //项目名称
                $obpe->getactivesheet()->setcellvalue('B2', $data['projectInfo']['typeStr']); //项目类型
                $obpe->getactivesheet()->setcellvalue('B3', $data['projectInfo']['areaStr']);//项目地点
                //$obpe->getactivesheet()->setcellvalue('B4', $data['projectInfo']['housetop_owner']);//联系人信息
                $obpe->getactivesheet()->setcellvalue('B5', $data['projectInfo']['contacts_name']);//联系人
                $obpe->getactivesheet()->setcellvalue('B6', $data['projectInfo']['contacts_phone']);//联系方式
                $obpe->getactivesheet()->setcellvalue('B7', $data['projectInfo']['contacts_email']);//邮件地址
                //$obpe->getactivesheet()->setcellvalue('B8', $data['projectInfo']['electricity_pay'].'万元');//土地情况
                $obpe->getactivesheet()->setcellvalue('B9', $data['projectInfo']['ground_property_other'].$data['projectInfo']['groundProperty']);//土地性质租
                $obpe->getactivesheet()->setcellvalue('B10', $data['projectInfo']['ground_area'].'m2');//凭土地面积
                $obpe->getactivesheet()->setcellvalue('B11', $data['projectInfo']['rent_time'].'年');//租凭年限
                $obpe->getactivesheet()->setcellvalue('B12', $data['projectInfo']['rent_pay'].'元/年');//租凭资金
                $obpe->getactivesheet()->setcellvalue('B13', $data['projectInfo']['groundCondition']); //土地平整情况
                //已建比未建多了，4个   中控室建筑面积   出让金额  附近有无遮挡   有无污染源
                $obpe->getactivesheet()->setcellvalue('B14', $data['projectInfo']['control_room_area'].'m2'); //    中控室建筑面积
                $obpe->getactivesheet()->setcellvalue('B15', $data['projectInfo']['sell_sum'].'元'); //出让金额
                $obpe->getactivesheet()->setcellvalue('B16', $data['projectInfo']['hasShelter']);// 附近有无遮挡
                $obpe->getactivesheet()->setcellvalue('B17', $data['projectInfo']['hasPollute']); //有无污染源
                //$obpe->getactivesheet()->setcellvalue('B18', data);//电网接入
                $obpe->getactivesheet()->setcellvalue('B19', $data['projectInfo']['transformer_capacity'].'kVA'); //上级变压器容量
                $obpe->getactivesheet()->setcellvalue('B20', $data['projectInfo']['voltage_level'].'kV'); //并网电压等级
                $obpe->getactivesheet()->setcellvalue('B21', $data['projectInfo']['electricity_distance'].'m'); //电网接入点距离
                $obpe->getactivesheet()->setcellvalue('B22', $data['projectInfo']['measurePoint']); //计量点
                //$obpe->getactivesheet()->setcellvalue('B23', $data['projectInfo']['xxx']); //xxx
                $obpe->getactivesheet()->setcellvalue('B24', $data['projectInfo']['plan_build_volume'].'kW'); //建设容量
                $obpe->getactivesheet()->setcellvalue('B25', $data['projectInfo']['company_invest'].'元/W'); //单位投资
                $obpe->getactivesheet()->setcellvalue('B26', $data['projectInfo']['company_epc']); //EPC厂家
                $obpe->getactivesheet()->setcellvalue('B27', $data['projectInfo']['capacity_level']); //资质等级
                $obpe->getactivesheet()->setcellvalue('B28', $data['projectInfo']['synchronize_date']); //并网时间
                $obpe->getactivesheet()->setcellvalue('B29', $data['projectInfo']['electricity_data'].'万度'); //历史发电量数据（最近一年）
                $obpe->getactivesheet()->setcellvalue('B30', $data['projectInfo']['projectHolderType']); //项目支架类型
                $obpe->getactivesheet()->setcellvalue('B31', $data['projectInfo']['groundProjectType']); //项目类型
                $obpe->getactivesheet()->setcellvalue('B32', $data['projectInfo']['cooperationType']); //与能融网合作方式
                $obpe->getactivesheet()->setcellvalue('B33', $data['projectInfo']['plan_financing'].'万元'); //拟融资金额
                $obpe->getactivesheet()->setcellvalue('B34', $data['projectInfo']['financingType']); //融资方式


            }
            //地面 已建---------------------------------------------------开始
            
            //echo oo;exit;
            /*import("Org.Util.PHPExcel.IOFactory");  
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$projectCode.'_项目信息.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::CreateWriter($obpe,"Excel2007");
            $objWriter->save('php://output');*/
            //-----------------------------------------导出---项目信息导出结束------------------------------------------//
        }



        //-----------------------------------------导出---尽职调查导出开始------------------------------------------//
        if(1)//false !== strpos($fatherUrl,'a=dueDiligence'))
        {
            //获取尽职调查
            $obj   = new InnerStaffController();
            list($picture,$docListInfo,$projectDetail,$areaArray,$evaluationInfo,$data) = 
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

            //屋顶 未建----------------尽职调查-----------------------------------开始
            if ($data['project_type'] == 1 && $data['build_state'] == 1)
            {


                //先处理好全部的第一列死信息
                //总体信息 8个
                $obpe->getActiveSheet()->getStyle( 'A1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A1')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A1', "项目经济收益测算");  //这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A2', "内部收益率IRR");
                $obpe->getactivesheet()->setcellvalue('A3', "评价结果");
                $obpe->getactivesheet()->setcellvalue('A4', "静态投资回收年");
                $obpe->getactivesheet()->setcellvalue('A5', "动态投资回收年");
                $obpe->getactivesheet()->setcellvalue('A6', "LCOE");
                $obpe->getactivesheet()->setcellvalue('A7', "净现值npv");
                $obpe->getactivesheet()->setcellvalue('A8', "电站资产累计现值");
               

                //这个需要填充颜色 基本信息 11个
                $obpe->getActiveSheet()->getStyle( 'A9')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A9')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A9', "基本信息");   //这个需要填充颜色
                $indexA = 10;
                $baseInfo = array(
                    "屋顶业主名称" => $projectDetail['housetop_owner'],
                    "企业类型" => $projectDetail['companyType'],
                    "拟建设容量" => $projectDetail['plan_build_volume'].'kW',
                    "项目地点" => $projectDetail['project_address'],
                    "屋顶类型" => $projectDetail['housetop_type_other'].$projectDetail['housetopType'],
                    "并网方式" => $projectDetail['synchronizeType'],
                    "拟融资金额" => $projectDetail['plan_financing'].'万元',
                    "融资方式" => $projectDetail['financingType'],
                );
                foreach($baseInfo as $k=>$v)
                {
                    $obpe->getactivesheet()->setcellvalue('A'.$indexA, $k);
                    $obpe->getactivesheet()->setcellvalue('B'.$indexA, $v);
                    $indexA = $indexA + 1;
                }
                //处理下html代码

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
                //评价情况 4个
                $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "评价情况 ");//这个需要填充颜色
                $indexA = $indexA + 1;
                $obpe->getActiveSheet()->getStyle('B'.$indexA)->getAlignment()->setWrapText(true);
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "A、文件审查");
                $obpe->getactivesheet()->setcellvalue('B'.$indexA, $evaluationInfo['document_review']);//A、文件审查
                $indexA = $indexA + 1;
                $obpe->getActiveSheet()->getStyle('B'.$indexA)->getAlignment()->setWrapText(true);
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "B、工程建设可行性");
                $obpe->getactivesheet()->setcellvalue('B'.$indexA, $evaluationInfo['project_quality_situation']);//B、工程建设可行性
                $indexA = $indexA + 1;
                $obpe->getActiveSheet()->getStyle('B'.$indexA)->getAlignment()->setWrapText(true);
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "C、项目经济收益情况"); 
                $obpe->getactivesheet()->setcellvalue('B'.$indexA, $evaluationInfo['project_earnings_situation']);//C、项目经济收益情况
                $indexA = $indexA + 1;    
                //对第一列的信息字体飘红
                for($i=1; $i<=$indexA ;$i++)
                {
                    $obpe->getActiveSheet()->getStyle('A'.$i)->getFont()->getColor()->setARGB('D94600');
                }
                $indexA = $indexA + 2;   
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "注:相关上传的图片资料,请到页面点击下载");


                //第二列的数据
                $obpe->getactivesheet()->setcellvalue('B2', $evaluationInfo['irr']); //内部收益率IRR
                $obpe->getactivesheet()->setcellvalue('B3', $evaluationInfo['evaluation_result']);//评价结果
                $obpe->getactivesheet()->setcellvalue('B4', $evaluationInfo['static_payback_time']);//静态投资回收年
                $obpe->getactivesheet()->setcellvalue('B5', $evaluationInfo['dynamic_payback_time']);//动态投资回收年
                $obpe->getactivesheet()->setcellvalue('B6', $evaluationInfo['lcoe']);//LCOE
                $obpe->getactivesheet()->setcellvalue('B7', $evaluationInfo['npv'].'万元');//净现值npv
                $obpe->getactivesheet()->setcellvalue('B8', $evaluationInfo['power_asset_current_value']);//电站资产累计现值
            }
            //屋顶 未建-------------------------尽职调查--------------------------结束
            

            //屋顶 已建---------------------------------------------------开始
            if ($data['project_type'] == 1 && $data['build_state'] == 2)
            {


                //先处理好全部的第一列死信息
                //总体信息 8个
                $obpe->getActiveSheet()->getStyle( 'A1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A1')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A1', "项目经济收益测算");  //这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A2', "内部收益率IRR");
                $obpe->getactivesheet()->setcellvalue('A3', "评价结果");
                $obpe->getactivesheet()->setcellvalue('A4', "静态投资回收年");
                $obpe->getactivesheet()->setcellvalue('A5', "动态投资回收年");
                $obpe->getactivesheet()->setcellvalue('A6', "LCOE");
                $obpe->getactivesheet()->setcellvalue('A7', "净现值npv");
                $obpe->getactivesheet()->setcellvalue('A8', "电站资产累计现值");
               

                //这个需要填充颜色 基本信息 11个
                $obpe->getActiveSheet()->getStyle( 'A9')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A9')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A9', "基本信息");   //这个需要填充颜色
                $indexA = 10;
                $baseInfo = array(
                    "屋顶业主名称" => $projectDetail['housetop_owner'],
                    "企业类型" => $projectDetail['companyType'],
                    "拟建设容量" => $projectDetail['plan_build_volume'].'kW',
                    "项目地点" => $projectDetail['project_address'],
                    "屋顶类型" => $projectDetail['housetop_type_other'].$projectDetail['housetopType'],
                    "并网方式" => $projectDetail['synchronizeType'],
                    "拟融资金额" => $projectDetail['plan_financing'].'万元',
                    "融资方式" => $projectDetail['financingType'],
                );
                foreach($baseInfo as $k=>$v)
                {
                    $obpe->getactivesheet()->setcellvalue('A'.$indexA, $k);
                    $obpe->getactivesheet()->setcellvalue('B'.$indexA, $v);
                    $indexA = $indexA + 1;
                }
                //处理下html代码

                $evaluationInfo['document_review'] = str_replace('</p>','',$evaluationInfo['document_review']);
                $evaluationInfo['document_review'] = str_replace('<p>','',$evaluationInfo['document_review']);
                $evaluationInfo['document_review'] = str_replace('<br/>',"\n",$evaluationInfo['document_review']);
                $evaluationInfo['project_quality_situation'] = str_replace('</p>','',$evaluationInfo['project_quality_situation']);
                $evaluationInfo['project_quality_situation'] = str_replace('<p>','',$evaluationInfo['project_quality_situation']);
                $evaluationInfo['project_quality_situation'] = str_replace('<br/>',"\n",$evaluationInfo['project_quality_situation']);
                $evaluationInfo['project_earnings_situation'] = str_replace('</p>','',$evaluationInfo['project_earnings_situation']);
                $evaluationInfo['project_earnings_situation'] = str_replace('<p>','',$evaluationInfo['project_earnings_situation']);
                $evaluationInfo['project_earnings_situation'] = str_replace('<br/>',"\n",$evaluationInfo['project_earnings_situation']);
                $evaluationInfo['project_invest_situation'] = str_replace('</p>','',$evaluationInfo['project_invest_situation']);
                $evaluationInfo['project_invest_situation'] = str_replace('<p>','',$evaluationInfo['project_invest_situation']);
                $evaluationInfo['project_invest_situation'] = str_replace('<br/>',"\n",$evaluationInfo['project_invest_situation']);

                $obpe->getActiveSheet()->getColumnDimension('B')->setWidth(100); 
                //评价情况 4个
                $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "评价情况 ");//这个需要填充颜色
                $indexA = $indexA + 1;
                $obpe->getActiveSheet()->getStyle('B'.$indexA)->getAlignment()->setWrapText(true);
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "A、文件审查");
                $obpe->getactivesheet()->setcellvalue('B'.$indexA, $evaluationInfo['document_review']);//A、文件审查
                $indexA = $indexA + 1;
                $obpe->getActiveSheet()->getStyle('B'.$indexA)->getAlignment()->setWrapText(true);
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "B、工程建设可行性");
                $obpe->getactivesheet()->setcellvalue('B'.$indexA, $evaluationInfo['project_quality_situation']);//B、工程建设可行性
                $indexA = $indexA + 1;
                $obpe->getActiveSheet()->getStyle('B'.$indexA)->getAlignment()->setWrapText(true);
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "C、项目建设投资情况"); 
                $obpe->getactivesheet()->setcellvalue('B'.$indexA, $evaluationInfo['project_invest_situation']);//C、项目经济收益情况
                $indexA = $indexA + 1;    
                $obpe->getActiveSheet()->getStyle('B'.$indexA)->getAlignment()->setWrapText(true);
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "D、项目经济收益情况"); 
                $obpe->getactivesheet()->setcellvalue('B'.$indexA, $evaluationInfo['project_earnings_situation']);//C、项目经济收益情况
                
                //对第一列的信息字体飘红
                for($i=1; $i<=$indexA ;$i++)
                {
                    $obpe->getActiveSheet()->getStyle('A'.$i)->getFont()->getColor()->setARGB('D94600');
                }
                $indexA = $indexA + 2;   
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "注:相关上传的图片资料,请到页面点击下载");
                //第二列的数据
                $obpe->getactivesheet()->setcellvalue('B2', $evaluationInfo['irr']); //内部收益率IRR
                $obpe->getactivesheet()->setcellvalue('B3', $evaluationInfo['evaluation_result']);//评价结果
                $obpe->getactivesheet()->setcellvalue('B4', $evaluationInfo['static_payback_time']);//静态投资回收年
                $obpe->getactivesheet()->setcellvalue('B5', $evaluationInfo['dynamic_payback_time']);//动态投资回收年
                $obpe->getactivesheet()->setcellvalue('B6', $evaluationInfo['lcoe']);//LCOE
                $obpe->getactivesheet()->setcellvalue('B7', $evaluationInfo['npv'].'万元');//净现值npv
                $obpe->getactivesheet()->setcellvalue('B8', $evaluationInfo['power_asset_current_value']);//电站资产累计现值 
            }
            //屋顶 已建------------------------尽职调查---------------------------结束

            //地面 未建-------------------------尽职调查--------------------------开始
            if (($data['project_type'] == 2 || $data['project_type'] == 3) && $data['build_state'] == 1)
            {
                //对第一列的信息字体飘红
                for($i=1; $i<=23 ;$i++)
                {
                    $obpe->getActiveSheet()->getStyle('A'.$i)->getFont()->getColor()->setARGB('D94600');
                }

                //先处理好全部的第一列死信息
                //总体信息 8个
                $obpe->getActiveSheet()->getStyle( 'A1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A1')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A1', "项目经济收益测算");  //这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A2', "内部收益率IRR");
                $obpe->getactivesheet()->setcellvalue('A3', "评价结果");
                $obpe->getactivesheet()->setcellvalue('A4', "静态投资回收年");
                $obpe->getactivesheet()->setcellvalue('A5', "动态投资回收年");
                $obpe->getactivesheet()->setcellvalue('A6', "LCOE");
                $obpe->getactivesheet()->setcellvalue('A7', "净现值npv");
                $obpe->getactivesheet()->setcellvalue('A8', "电站资产累计现值");
                //这个需要填充颜色 基本信息 11个
                $obpe->getActiveSheet()->getStyle( 'A9')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A9')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A9', "基本信息");   //这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A10', "项目名称");
                $obpe->getactivesheet()->setcellvalue('A11', "项目类型");
                $obpe->getactivesheet()->setcellvalue('A12', "建设容量");
                $obpe->getactivesheet()->setcellvalue('A13', "项目地点");
                $obpe->getactivesheet()->setcellvalue('A14', "项目完工时间");
                $obpe->getactivesheet()->setcellvalue('A15', "项目电价");
                $obpe->getactivesheet()->setcellvalue('A16', "土地性质");
                $obpe->getactivesheet()->setcellvalue('A17', "租赁土地面积");
                $obpe->getactivesheet()->setcellvalue('A18', "拟融资金额");
                $obpe->getactivesheet()->setcellvalue('A19', "融资方式");
                //评价情况 4个
                $obpe->getActiveSheet()->getStyle( 'A20')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A20')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A20', "评价情况 ");//这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A21', "A、文件审查");
                $obpe->getactivesheet()->setcellvalue('A22', "B、工程建设可行性");
                $obpe->getactivesheet()->setcellvalue('A23', "C、项目经济收益情况");     
                $obpe->getactivesheet()->setcellvalue('A25', "注:相关上传的图片资料,请到页面点击下载");
                //第二列的数据
                $obpe->getactivesheet()->setcellvalue('B2', $evaluationInfo['irr']); //内部收益率IRR
                $obpe->getactivesheet()->setcellvalue('B3', $evaluationInfo['evaluation_result']);//评价结果
                $obpe->getactivesheet()->setcellvalue('B4', $evaluationInfo['static_payback_time']);//静态投资回收年
                $obpe->getactivesheet()->setcellvalue('B5', $evaluationInfo['dynamic_payback_time']);//动态投资回收年
                $obpe->getactivesheet()->setcellvalue('B6', $evaluationInfo['lcoe']);//LCOE
                $obpe->getactivesheet()->setcellvalue('B7', $evaluationInfo['npv'].'万元');//净现值npv
                $obpe->getactivesheet()->setcellvalue('B8', $evaluationInfo['power_asset_current_value']);//电站资产累计现值                
                //基本信息
                //$obpe->getactivesheet()->setcellvalue('B9', $projectDetail['']);
                $obpe->getactivesheet()->setcellvalue('B10', $projectDetail['project_name']);//项目名称
                $obpe->getactivesheet()->setcellvalue('B11', $projectDetail['groundProjectType']);//项目类型
                $obpe->getactivesheet()->setcellvalue('B12', $projectDetail['plan_build_volume'].'kW');//建设容量
                $obpe->getactivesheet()->setcellvalue('B13', $areaArray[3].$projectDetail['project_address']); //项目地点
                $obpe->getactivesheet()->setcellvalue('B14', $projectDetail['project_finish_date']);//项目完工时间
                $obpe->getactivesheet()->setcellvalue('B15', $projectDetail['project_electricity_price'].'元/kWh');//项目电价
                $obpe->getactivesheet()->setcellvalue('B16', $projectDetail['ground_property_other'].$projectDetail['groundProperty']); //土地性质
                $obpe->getactivesheet()->setcellvalue('B17', $projectDetail['ground_area'].'m2');//租赁土地面积
                $obpe->getactivesheet()->setcellvalue('B18', $projectDetail['plan_financing'].'万元');//拟融资金额
                $obpe->getactivesheet()->setcellvalue('B19', $projectDetail['financingType']);//融资方式
                //$obpe->getactivesheet()->setcellvalue('B20', $projectDetail['']);//

                //处理下html代码

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
                $obpe->getActiveSheet()->getStyle('B21')->getAlignment()->setWrapText(true);
                $obpe->getActiveSheet()->getStyle('B22')->getAlignment()->setWrapText(true);
                $obpe->getActiveSheet()->getStyle('B23')->getAlignment()->setWrapText(true);
                
                $obpe->getactivesheet()->setcellvalue('B21', $evaluationInfo['document_review']);//A、文件审查
                $obpe->getactivesheet()->setcellvalue('B22', $evaluationInfo['project_quality_situation']);//B、工程建设可行性
                $obpe->getactivesheet()->setcellvalue('B23', $evaluationInfo['project_earnings_situation']);//C、项目经济收益情况

            }
            //地面 未建-----------------------尽职调查----------------------------结束

            //地面 已建-----------------------尽职调查----------------------------开始
            if (($data['project_type'] == 2 ||$data['project_type'] == 3) && $data['build_state'] == 2)
            {
                //对第一列的信息字体飘红
                for($i=1; $i<=26 ;$i++)
                {
                    $obpe->getActiveSheet()->getStyle('A'.$i)->getFont()->getColor()->setARGB('D94600');
                }

                //先处理好全部的第一列死信息
                //总体信息 8个
                $obpe->getActiveSheet()->getStyle( 'A1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A1')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A1', "项目经济收益测算");  //这个需要填充颜色
                $obpe->getactivesheet()->setcellvalue('A2', "内部收益率IRR");
                $obpe->getactivesheet()->setcellvalue('A3', "评价结果");
                $obpe->getactivesheet()->setcellvalue('A4', "静态投资回收年");
                $obpe->getactivesheet()->setcellvalue('A5', "动态投资回收年");
                $obpe->getactivesheet()->setcellvalue('A6', "LCOE");
                $obpe->getactivesheet()->setcellvalue('A7', "净现值npv");
                $obpe->getactivesheet()->setcellvalue('A8', "电站资产累计现值");
               

                //这个需要填充颜色 基本信息 11个
                $obpe->getActiveSheet()->getStyle( 'A9')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A9')->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A9', "基本信息");   //这个需要填充颜色
                $indexA = 10;
                $baseInfo = array(
                    "项目名称" => $projectDetail['project_name'],
                    "项目类型" => $projectDetail['groundProjectType'],
                    "拟建设容量" => $projectDetail['plan_build_volume'].'kW',
                    "项目地点" => $projectDetail['project_address'],
                    "项目完工时间" => $projectDetail['project_finish_date'],
                    "项目电价" => $projectDetail['project_electricity_price'].'元/kWh',
                    "项目总投资" => $projectDetail['project_investment'].'万元',
                    "土地平整情况" => $projectDetail['groundCondition'],
                    "土地性质" => $projectDetail['ground_property_other'].$projectDetail['groundProperty'],
                    "租赁土地面积" => $projectDetail['ground_area'].'m2',
                    "拟融资金额" => $projectDetail['plan_financing'].'万元',
                    "融资方式" => $projectDetail['financingType'],
                );
                foreach($baseInfo as $k=>$v)
                {
                    $obpe->getactivesheet()->setcellvalue('A'.$indexA, $k);
                    $obpe->getactivesheet()->setcellvalue('B'.$indexA, $v);
                    $indexA = $indexA + 1;
                }
                //处理下html代码

                $evaluationInfo['document_review'] = str_replace('</p>','',$evaluationInfo['document_review']);
                $evaluationInfo['document_review'] = str_replace('<p>','',$evaluationInfo['document_review']);
                $evaluationInfo['document_review'] = str_replace('<br/>',"\n",$evaluationInfo['document_review']);
                $evaluationInfo['project_quality_situation'] = str_replace('</p>','',$evaluationInfo['project_quality_situation']);
                $evaluationInfo['project_quality_situation'] = str_replace('<p>','',$evaluationInfo['project_quality_situation']);
                $evaluationInfo['project_quality_situation'] = str_replace('<br/>',"\n",$evaluationInfo['project_quality_situation']);
                $evaluationInfo['project_earnings_situation'] = str_replace('</p>','',$evaluationInfo['project_earnings_situation']);
                $evaluationInfo['project_earnings_situation'] = str_replace('<p>','',$evaluationInfo['project_earnings_situation']);
                $evaluationInfo['project_earnings_situation'] = str_replace('<br/>',"\n",$evaluationInfo['project_earnings_situation']);
                $evaluationInfo['project_invest_situation'] = str_replace('</p>','',$evaluationInfo['project_invest_situation']);
                $evaluationInfo['project_invest_situation'] = str_replace('<p>','',$evaluationInfo['project_invest_situation']);
                $evaluationInfo['project_invest_situation'] = str_replace('<br/>',"\n",$evaluationInfo['project_invest_situation']);

                $obpe->getActiveSheet()->getColumnDimension('B')->setWidth(100); 
                //评价情况 4个
                $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                $obpe->getActiveSheet()->getStyle( 'A'.$indexA)->getFill()->getStartColor()->setARGB('FFC78E');
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "评价情况 ");//这个需要填充颜色
                $indexA = $indexA + 1;
                $obpe->getActiveSheet()->getStyle('B'.$indexA)->getAlignment()->setWrapText(true);
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "A、文件审查");
                $obpe->getactivesheet()->setcellvalue('B'.$indexA, $evaluationInfo['document_review']);//A、文件审查
                $indexA = $indexA + 1;
                $obpe->getActiveSheet()->getStyle('B'.$indexA)->getAlignment()->setWrapText(true);
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "B、工程建设可行性");
                $obpe->getactivesheet()->setcellvalue('B'.$indexA, $evaluationInfo['project_quality_situation']);//B、工程建设可行性
                $indexA = $indexA + 1;
                $obpe->getActiveSheet()->getStyle('B'.$indexA)->getAlignment()->setWrapText(true);
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "C、项目建设投资情况"); 
                $obpe->getactivesheet()->setcellvalue('B'.$indexA, $evaluationInfo['project_invest_situation']);//C、项目经济收益情况
                $indexA = $indexA + 1;    
                $obpe->getActiveSheet()->getStyle('B'.$indexA)->getAlignment()->setWrapText(true);
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "D、项目经济收益情况"); 
                $obpe->getactivesheet()->setcellvalue('B'.$indexA, $evaluationInfo['project_earnings_situation']);//C、项目经济收益情况
                $indexA = $indexA + 2;   
                $obpe->getactivesheet()->setcellvalue('A'.$indexA, "注:相关上传的图片资料,请到页面点击下载");
                //第二列的数据
                $obpe->getactivesheet()->setcellvalue('B2', $evaluationInfo['irr']); //内部收益率IRR
                $obpe->getactivesheet()->setcellvalue('B3', $evaluationInfo['evaluation_result']);//评价结果
                $obpe->getactivesheet()->setcellvalue('B4', $evaluationInfo['static_payback_time']);//静态投资回收年
                $obpe->getactivesheet()->setcellvalue('B5', $evaluationInfo['dynamic_payback_time']);//动态投资回收年
                $obpe->getactivesheet()->setcellvalue('B6', $evaluationInfo['lcoe']);//LCOE
                $obpe->getactivesheet()->setcellvalue('B7', $evaluationInfo['npv'].'万元');//净现值npv
                $obpe->getactivesheet()->setcellvalue('B8', $evaluationInfo['power_asset_current_value']);//电站资产累计现值 

            }
            //地面 已建-------------------------尽职调查--------------------------开始      

            import("Org.Util.PHPExcel.IOFactory");  
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$projectCode.'_尽职调查信息.xls"');
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