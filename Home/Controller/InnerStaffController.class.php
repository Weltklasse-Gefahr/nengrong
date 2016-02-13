<?php
namespace Home\Controller;

use Think\Controller;

class InnerStaffController extends Controller {
 

    /**
    **@auth qiujinhan
    **@breif 在客服中导出一个项目的信息
    **@date 2016.1.17
    **@参数 ?c=InnerStaff&a=export&no=sss&token=xxxx&datatype=proinfo
    **/
    public function export()
    {
        
        isLogin($_COOKIE['email'],$_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2);
        $projectCode  = $_POST['no']     ? $_POST['no']:$_GET['no'];
        $mProjectCode = $_POST['token']  ? $_POST['token']:$_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode); 
        $fatherUrl  = $_SERVER['HTTP_REFERER'];
        $getJsonFlag = 1;
        $innerToken = "InternalCall";
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.PDF");
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

        //-----------------------------------------导出---项目投资方信息开始------------------------------------------//
        //获取项目投资方信息
        if(false !== strpos($fatherUrl,'a=getProjectProviderInfo'))
        {  
            list($userInfo,$areaStr,$docData) = $this->getProjectProviderInfo($projectCode, null, $getJsonFlag,$innerToken);
            $objActSheet = $obpe->setactivesheetindex(0);
            //设置SHEET的名字
            $obpe->getActiveSheet()->setTitle('项目投资方信息');
                       
            $obpe->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $obpe->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $obpe->createSheet();
            //填充颜色
            $obpe->getActiveSheet()->getStyle( 'A1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $obpe->getActiveSheet()->getStyle( 'A1')->getFill()->getStartColor()->setARGB('FFC78E');
            $obpe->getactivesheet()->setcellvalue('A1', "账户详细信息");
            $obpe->getActiveSheet()->getStyle( 'B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $obpe->getActiveSheet()->getStyle('A2:A3')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A4:A5')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A6:A7')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A8:A9')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A10:A11')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('A12:A13')->getFont()->getColor()->setARGB('D94600');
            $obpe->getactivesheet()->setcellvalue('A2', "E-mail");
            $obpe->getactivesheet()->setcellvalue('A3', "联系人");
            $obpe->getactivesheet()->setcellvalue('A4', "联系人手机");
            $obpe->getactivesheet()->setcellvalue('A5', "所在地区");
            $obpe->getactivesheet()->setcellvalue('A6', "详细地址");
            $obpe->getactivesheet()->setcellvalue('A7', "企业注册资本");

            $obpe->getactivesheet()->setcellvalue('B2', $userInfo["email"]);
            $obpe->getactivesheet()->setcellvalue('B3', $userInfo["company_contacts"]);
            $obpe->getactivesheet()->setcellvalue('B4', $userInfo["company_contacts_phone"]);

            $obpe->getactivesheet()->setcellvalue('B5', $areaStr);
            $obpe->getactivesheet()->setcellvalue('B6', $userInfo["company_address"]);
            $obpe->getactivesheet()->setcellvalue('B7', $userInfo["company_capital"]);

            $obpe->getactivesheet()->setcellvalue('A8', "企业名称");
            $obpe->getactivesheet()->setcellvalue('A9', "企业类型");
            $obpe->getactivesheet()->setcellvalue('A10', "公司传真");
            $obpe->getactivesheet()->setcellvalue('A11', "其他手机");
            $obpe->getactivesheet()->setcellvalue('A12', "座机");
            $obpe->getactivesheet()->setcellvalue('A13', "企业法人");

            $obpe->getactivesheet()->setcellvalue('B8', $userInfo["company_name"]);
            $obpe->getactivesheet()->setcellvalue('B9', $userInfo["company_type"]);
            $obpe->getactivesheet()->setcellvalue('B10', $userInfo["company_fax"]);
            $obpe->getactivesheet()->setcellvalue('B11', $userInfo['company_telephone']);
            $obpe->getactivesheet()->setcellvalue('B12', $userInfo["company_phone"]);
            $obpe->getactivesheet()->setcellvalue('B13', $userInfo["company_person"]);  
            $obpe->getactivesheet()->setcellvalue('A16', "注:相关上传的图片资料,请到页面点击下载");
            import("Org.Util.PHPExcel.IOFactory");  
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$projectCode.'_项目提供方信息.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::CreateWriter($obpe,"Excel2007");
            $objWriter->save('php://output');
            //-----------------------------------------导出---项目投资方信息结束------------------------------------------//
        }


        //-----------------------------------------导出---意向书导出开始------------------------------------------//
        //获取意向书
        if(false !== strpos($fatherUrl,'a=intent'))
        {
            //获取项目信息
            $obj   = new ProjectProviderMyProController();
            $data = $obj->projectInfoEdit($projectCode, null, $getJsonFlag, $innerToken);
            //echo json_encode($userInfo);exit;
            //然后在获取意向书
            $objProject  = D("Project","Service");
            $projectInfoForIntent = $objProject->getProjectDetail($data["id"], $data['project_type']);

            //意向书内容
            $intent = $projectInfoForIntent['project_intent'];
            //签署状态23代表已经签署
            $status = $projectInfoForIntent["status"];
            if($status == 23)
            {
                $tips = '项目意向书状态：已经签署';
            }
            else
            {
                //$tips = "你好\nhello";
                $tips = '项目意向书状态：未签署';
            }
            //处理一下换行符号 </p><p><br/> 去掉</p><p> 把<br/> 替换为\n
            $intent = str_replace('</p>','',$intent);
            $intent = str_replace('<p>','',$intent);
            $intent = str_replace('<br/>',"\n",$intent);
            //$intent = '"'.$intent.'"';
            $objActSheet = $obpe->setactivesheetindex(0);
            //设置SHEET的名字
            $obpe->getActiveSheet()->setTitle('意向书内容');   

            $obpe->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $obpe->createSheet();
            $obpe->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true); 
            $obpe->getActiveSheet()->getStyle('A3')->getAlignment()->setWrapText(true);
            $obpe->getactivesheet()->setcellvalue('A1', $intent);
            $obpe->getActiveSheet()->getStyle('A3')->getFont()->getColor()->setARGB('D94600');
            $obpe->getactivesheet()->setcellvalue('A3', $tips);            
            import("Org.Util.PHPExcel.IOFactory");  
            //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            /*header('Content-Disposition: attachment;filename="'.$projectCode.'_项目意向书.xls"');
            header('Cache-Control: max-age=0');*/
            //Vendor('\PhpExcel.PHPExcel.Writer.PDF.DomPDF');
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
            header("Content-Type:application/force-download");
            header("Content-Type: application/pdf");
            header("Content-Type:application/octet-stream");
            header("Content-Type:application/download");
            $fileName  = $projectCode.'_项目意向书.pdf';
            header("Content-Disposition:attachment;filename=".$fileName);
            header("Content-Transfer-Encoding:binary");

            //$objWriter = \PHPExcel_IOFactory::CreateWriter($obpe,"PDF");
            //$objWriter =new \PHPExcel_Writer_PDF($obpe);

            //$objWriter->save('php://output');
            $html2 ='数字'; 
            $intent= $intent."\n".$tips;

            header("Content-type: application/octet-stream"); 
            header("Accept-Ranges: bytes"); 
            header("Accept-Length: ".strlen($html2));
            header('Content-Disposition: attachment;filename="'.$projectCode.'_项目意向书.doc"'); 
            header("Pragma:no-cache"); 
            header("Expires:0"); 
            echo $intent;
            //-----------------------------------------导出---意向书导出结束------------------------------------------//
        }





        //-----------------------------------------导出---推送项目开始------------------------------------------//
        //获取推送项目
        if(false !== strpos($fatherUrl,'a=pushProject'))
        {  
            $data = $this->pushProject($projectCode, null, $getJsonFlag, $innerToken);
            if(empty($data)){
                echo "<script type='text/javascript'>alert('没有推送记录');</script>";
                exit;
            }
            //echo json_encode($data);exit;
            $objActSheet = $obpe->setactivesheetindex(0);
            //设置SHEET的名字
            $obpe->getActiveSheet()->setTitle('推送项目');

            //左靠对齐
            $obpe->getActiveSheet()->getStyle( 'C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            //设置列宽
            $obpe->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $obpe->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $obpe->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $obpe->createSheet();
            //设置列的颜色
            $obpe->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB('D94600'); 
            $obpe->getActiveSheet()->getStyle('B1')->getFont()->getColor()->setARGB('D94600');
            $obpe->getActiveSheet()->getStyle('C1')->getFont()->getColor()->setARGB('D94600');

            $obpe->getactivesheet()->setcellvalue('A1', "融资机构");
            $obpe->getactivesheet()->setcellvalue('B1', "推送状态"); 
            $obpe->getactivesheet()->setcellvalue('C1', "联系电话"); 
            $i = 1; 
            foreach($data['list'] as $val)
            {
                $i = $i +1;
                $obpe->getactivesheet()->setcellvalue('A'.$i, $val['company_name']);
                $obpe->getactivesheet()->setcellvalue('B'.$i, $val['push_flag']);
                $obpe->getactivesheet()->setcellvalue('C'.$i, $val['company_contacts_phone']);
            }          
            import("Org.Util.PHPExcel.IOFactory");  
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$projectCode.'_推送项目.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::CreateWriter($obpe,"Excel2007");
            $objWriter->save('php://output');
            //-----------------------------------------导出---推送项目结束------------------------------------------//
        }




        //-----------------------------------------导出---项目信息导出开始------------------------------------------//
        if(false !== strpos($fatherUrl,'a=projectInfo'))
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
            import("Org.Util.PHPExcel.IOFactory");  
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$projectCode.'_项目信息.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::CreateWriter($obpe,"Excel2007");
            $objWriter->save('php://output');
            //-----------------------------------------导出---项目信息导出结束------------------------------------------//
        }



        //-----------------------------------------导出---尽职调查导出开始------------------------------------------//
        if(false !== strpos($fatherUrl,'a=dueDiligence'))
        {
            //获取尽职调查
            $obj   = new InnerStaffController();
            list($picture,$docListInfo,$projectDetail,$areaArray,$evaluationInfo,$data) = 
                $obj->dueDiligence($projectCode, null, $getJsonFlag,$innerToken);


            $objActSheet = $obpe->setactivesheetindex(0);
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
    **@breif 客服->项目信息
    **@date 2015.12.26
    **/
    public function projectInfo(){
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2);
        $projectCode  = $_POST['no']     ? $_POST['no']:$_GET['no'];
        $mProjectCode = $_POST['token']  ? $_POST['token']:$_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode);
        $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
        $objProject  = D("Project","Service");
        $objProject->cancelProjectHighlight($projectCode, 2);
        $projectInfo = $objProject->getProjectInfo($projectCode);
        if($rtype == 1){
            $proData['comment'] = $_POST['comment'];
            // $proData['comment'] = "sldfjiofnosdkfj是的发生的";
            $res = $objProject->saveProjectDetail($projectCode, $projectInfo['project_type'], $proData);
            if($res > 0){
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"0","msg":"保存成功"}';
            }else{
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"保存失败"}';
            }
        }else{
            $common = D("Common","Service");
            //获取项目信息
            $getJsonFlag = 1;
            $obj   = new ProjectProviderMyProController();
            $innerToken = "InternalCall";
            $data = $obj->projectInfoEdit($projectCode, null, $getJsonFlag, $innerToken);
            $data['typeStr'] = $objProject->getTypeAndStateStr($data['project_type'], $data['build_state']);
            $areaObj = D('Area', 'Service');
            // $areaInfo = $data['county']?$data['county']:$data['city'];
            // $areaInfo = $areaInfo?$areaInfo:$data['province'];
            // $data['areaStr'] = $areaObj->getAreaById($areaInfo);
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
            $dataBig  = array('projectInfo' => $data);
            $this->assign('data',$dataBig);
            if ($_GET['display'] == 'json') {
                header('Content-Type: text/html; charset=utf-8');
                dump($dataBig);
                exit;
            }
            if($data['project_type'] == 1){
                if($data['build_state'] == 1){
                    $this->display("InnerStaff:projectInfo_housetop_nonbuild");
                }elseif($data['build_state'] == 2){
                    $this->display("InnerStaff:projectInfo_housetop_build");
                }
            }elseif($data['project_type'] == 2 || $data['project_type'] == 3){
                if($data['build_state'] == 1){
                    $this->display("InnerStaff:projectInfo_ground_nonbuild");
                }elseif($data['build_state'] == 2){
                    $this->display("InnerStaff:projectInfo_ground_build");
                }
            }
        }
    }

    /**
    **@auth qianqiang
    **@breif 客服->项目提供方信息（账户向详细信息）
    **@date 2015.12.19
    **/
    public function getProjectProviderInfo($projectCode=null, $rtype=null, $getJsonFlag=null, $innerToken=null){
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2, $innerToken);

        if($projectCode == null){
            $projectCode  = $_POST['no']     ? $_POST['no']:$_GET['no'];
            $mProjectCode = $_POST['token']  ? $_POST['token']:$_GET['token'];
            isProjectCodeRight($projectCode, $mProjectCode);
        }

        $objProject = D("Project", "Service");
        $objProjectInfo = $objProject->getProjectInfo($projectCode);
        $providerId = $objProjectInfo['provider_id'];
        $userObj = D("User", "Service");
        $userInfo = $userObj->getUserINfoById($providerId);
        $common = D("Common", "Service");
        $userInfo[0]['companyType'] = $common->getUserCompanyType($userInfo[0]['company_type']);

        $areaObj = D("Area", "Service");
        $areaStr = $areaObj->getAreaById($userInfo[0]['company_area']);
        $docObj = D("Doc", "Service");
        $condition['id'] = $userInfo[0]['business_license'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['business_license']['id'] = $docInfo[0]['id'];
        $docData['business_license']['file_name'] = $docInfo[0]['file_name'];
        $docData['business_license']['file_rename'] = $docInfo[0]['file_rename'];
        $docFileInfo = explode(".",$docData['business_license']['file_name']);
        if($docFileInfo[1] == "" || $docFileInfo[1] == "jpg" || $docFileInfo[1] == "jpeg" || $docFileInfo[1] == "png" || $docFileInfo[1] == "gif" || $docFileInfo[1] == "bmp" || $docFileInfo[1] == "ico"){
            $docData['business_license']['img_file_rename'] = $docData['business_license']['file_rename'];
        }else{
            $docData['business_license']['img_file_rename'] = "/EnergyFe/img/".$docFileInfo[1].".png";
        }
        
        $condition['id'] = $userInfo[0]['organization_code'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['organization_code']['id'] = $docInfo[0]['id'];
        $docData['organization_code']['file_name'] = $docInfo[0]['file_name'];
        $docData['organization_code']['file_rename'] = $docInfo[0]['file_rename'];
        $docFileInfo = explode(".",$docData['organization_code']['file_name']);
        if($docFileInfo[1] == "" || $docFileInfo[1] == "jpg" || $docFileInfo[1] == "jpeg" || $docFileInfo[1] == "png" || $docFileInfo[1] == "gif" || $docFileInfo[1] == "bmp" || $docFileInfo[1] == "ico"){
            $docData['organization_code']['img_file_rename'] = $docData['organization_code']['file_rename'];
        }else{
            $docData['organization_code']['img_file_rename'] = "/EnergyFe/img/".$docFileInfo[1].".png";
        }
        
        $condition['id'] = $userInfo[0]['national_tax_certificate'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['national_tax_certificate']['id'] = $docInfo[0]['id'];
        $docData['national_tax_certificate']['file_name'] = $docInfo[0]['file_name'];
        $docData['national_tax_certificate']['file_rename'] = $docInfo[0]['file_rename'];
        $docFileInfo = explode(".",$docData['national_tax_certificate']['file_name']);
        if($docFileInfo[1] == "" || $docFileInfo[1] == "jpg" || $docFileInfo[1] == "jpeg" || $docFileInfo[1] == "png" || $docFileInfo[1] == "gif" || $docFileInfo[1] == "bmp" || $docFileInfo[1] == "ico"){
            $docData['national_tax_certificate']['img_file_rename'] = $docData['national_tax_certificate']['file_rename'];
        }else{
            $docData['national_tax_certificate']['img_file_rename'] = "/EnergyFe/img/".$docFileInfo[1].".png";
        }
        
        $condition['id'] = $userInfo[0]['local_tax_certificate'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['local_tax_certificate']['id'] = $docInfo[0]['id'];
        $docData['local_tax_certificate']['file_name'] = $docInfo[0]['file_name'];
        $docData['local_tax_certificate']['file_rename'] = $docInfo[0]['file_rename'];
        $docFileInfo = explode(".",$docData['local_tax_certificate']['file_name']);
        if($docFileInfo[1] == "" || $docFileInfo[1] == "jpg" || $docFileInfo[1] == "jpeg" || $docFileInfo[1] == "png" || $docFileInfo[1] == "gif" || $docFileInfo[1] == "bmp" || $docFileInfo[1] == "ico"){
            $docData['local_tax_certificate']['img_file_rename'] = $docData['local_tax_certificate']['file_rename'];
        }else{
            $docData['local_tax_certificate']['img_file_rename'] = "/EnergyFe/img/".$docFileInfo[1].".png";
        }

        $condition['id'] = $userInfo[0]['identity_card_front'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['identity_card_front']['id'] = $docInfo[0]['id'];
        $docData['identity_card_front']['file_name'] = $docInfo[0]['file_name'];
        $docData['identity_card_front']['file_rename'] = $docInfo[0]['file_rename'];
        $docFileInfo = explode(".",$docData['identity_card_front']['file_name']);
        if($docFileInfo[1] == "" || $docFileInfo[1] == "jpg" || $docFileInfo[1] == "jpeg" || $docFileInfo[1] == "png" || $docFileInfo[1] == "gif" || $docFileInfo[1] == "bmp" || $docFileInfo[1] == "ico"){
            $docData['identity_card_front']['img_file_rename'] = $docData['identity_card_front']['file_rename'];
        }else{
            $docData['identity_card_front']['img_file_rename'] = "/EnergyFe/img/".$docFileInfo[1].".png";
        }

        $condition['id'] = $userInfo[0]['identity_card_back'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['identity_card_back']['id'] = $docInfo[0]['id'];
        $docData['identity_card_back']['file_name'] = $docInfo[0]['file_name'];
        $docData['identity_card_back']['file_rename'] = $docInfo[0]['file_rename'];
        $docFileInfo = explode(".",$docData['identity_card_back']['file_name']);
        if($docFileInfo[1] == "" || $docFileInfo[1] == "jpg" || $docFileInfo[1] == "jpeg" || $docFileInfo[1] == "png" || $docFileInfo[1] == "gif" || $docFileInfo[1] == "bmp" || $docFileInfo[1] == "ico"){
            $docData['identity_card_back']['img_file_rename'] = $docData['identity_card_back']['file_rename'];
        }else{
            $docData['identity_card_back']['img_file_rename'] = "/EnergyFe/img/".$docFileInfo[1].".png";
        }

        $condition['id'] = $userInfo[0]['financial_audit'];
        $docInfo = $docObj->getDocInfo($condition);
        $docData['financial_audit']['id'] = $docInfo[0]['id'];
        $docData['financial_audit']['file_name'] = $docInfo[0]['file_name'];
        $docData['financial_audit']['file_rename'] = $docInfo[0]['file_rename'];
        $docData['financial_audit']['token'] = md5(addToken($docInfo[0]["id"]));

        if ($_GET['display'] == 'json') {
            header('Content-Type: text/html; charset=utf-8');
            dump($userInfo);
            dump($areaStr);
            dump($docData);
            exit;
        }

        //直接截断了,返回json了
        if ($getJsonFlag == 1)
        {
          return array($userInfo[0],$areaStr,$docData);
        }      
          
        $this->assign('userInfo', $userInfo[0]);
        $this->assign('areaStr', $areaStr);
        $this->assign('docData', $docData);
        $this->display("InnerStaff:providerInfo");
    }

    /**
    **@auth qianqiang
    **@breif 客服->尽职调查
    **@date 2015.12.19
    **/
    public function dueDiligence($projectCode=null, $rtype=null, $getJsonFlag=null, $innerToken=null){
    	isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2, $innerToken);
    	$optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        if($rtype == null){
            $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
        }
        if($optype == "upload" && $rtype == 1){
            $docFile = array(
                "attachment",
                );
            $doc = D("Doc", "Service");
            $docInfo = $doc->uploadFileAndPictrue($docFile, $docFile);
            if(!empty($docInfo)){
                echo '{"code":"0","msg":"success","id":"'.$docInfo['attachment'].'"}';
            }else{
                echo '{"code":"-1","msg":"上传失败！"}';
            }
        }elseif($optype == "save" && $rtype == 1){
            if($projectCode == null){
                $projectCode = $_POST['no'] ? $_POST['no']:$_GET['no'];
                $mProjectCode = $_POST['token'] ? $_POST['token']:$_GET['token'];
                isProjectCodeRight($projectCode, $mProjectCode);
            }

    		$objProject = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];

    		$proData = array();
            if($objProjectInfo['project_type'] == 1){
                $proData['project_id'] = $projectId;
                $proData['housetop_owner'] = $_POST['housetop_owner']; 
                $proData['company_type'] = $_POST['company_type'];
                $proData['plan_build_volume'] = $_POST['plan_build_volume']==""?null:$_POST['plan_build_volume'];
                // $proData['project_area'] = $_POST['county'];
                $proArea = $_POST['county']?$_POST['county']:$_POST['city'];
                $proArea = $proArea?$proArea:$_POST['province'];
                $proData['project_area'] = $proArea;
                $proData['project_address'] = $_POST['project_address'];
                $proData['housetop_type'] = $_POST['housetop_type'];
                $proData['housetop_type_other'] = $_POST['housetop_type_other'];
                $proData['synchronize_type'] = $_POST['synchronize_type'];
                $proData['plan_financing'] = $_POST['plan_financing']==""?null:$_POST['plan_financing'];
                $proData['financing_type'] = $_POST['financing_type'];
            }elseif($objProjectInfo['project_type'] == 2 || $objProjectInfo['project_type'] == 3){
                $proData['project_id'] = $projectId;
                $proData['plan_build_volume'] = $_POST['plan_build_volume']==""?null:$_POST['plan_build_volume'];
                // $proData['project_area'] = $_POST['county'];
                $proArea = $_POST['county']?$_POST['county']:$_POST['city'];
                $proArea = $proArea?$proArea:$_POST['province'];
                $proData['project_area'] = $proArea;
                $proData['project_address'] = $_POST['project_address'];
                $proData['project_name'] = $_POST['project_name'];
                $proData['ground_project_type'] = $_POST['ground_project_type'];
                $proData['project_finish_date'] = $_POST['project_finish_date'];
                $proData['project_electricity_price'] = $_POST['project_electricity_price']==""?null:$_POST['project_electricity_price'];
                $proData['project_investment'] = $_POST['project_investment']==""?null:$_POST['project_investment'];
                $proData['ground_condition'] = $_POST['ground_condition'];
                $proData['ground_property'] = $_POST['ground_property'];
                $proData['ground_property_other'] = $_POST['ground_property_other'];
                $proData['ground_area'] = $_POST['ground_area']==""?null:$_POST['ground_area'];
                $proData['plan_financing'] = $_POST['plan_financing']==""?null:$_POST['plan_financing'];
                $proData['financing_type'] = $_POST['financing_type'];
            }
    		
            $evaData = array();
            $evaData['project_id'] = $projectId;
            $evaData['IRR'] = $_POST['IRR']==""?null:$_POST['IRR'];
            $evaData['evaluation_result'] = $_POST['evaluation_result'].",".$_POST['evaluation_result_text'];
            $evaData['static_payback_time'] = $_POST['static_payback_time']==""?null:$_POST['static_payback_time'];
            $evaData['dynamic_payback_time'] = $_POST['dynamic_payback_time']==""?null:$_POST['dynamic_payback_time'];
            $evaData['LCOE'] = $_POST['LCOE']==""?null:$_POST['LCOE'];
            $evaData['npv'] = $_POST['npv']==""?null:$_POST['npv'];
            $evaData['power_asset_current_value'] = $_POST['power_asset_current_value']==""?null:$_POST['power_asset_current_value'];
            $evaData['evaluation_content'] = $_POST['evaluation_content'];
            $evaData['document_review'] = $_POST['document_review'];
            $evaData['project_quality_situation'] = $_POST['project_quality_situation'];
            $evaData['project_invest_situation'] = $_POST['project_invest_situation'];
            $evaData['project_earnings_situation'] = $_POST['project_earnings_situation'];
            $evaData['duty_person'] = $_POST['duty_person'];
            $evaData['doc_mul'] = implode(",", $_POST['doc_mul']);

            $res = $objProject->saveHousetopOrGround($proData, 51, $objProjectInfo['project_type']);
            if($res == true){
            	$objEvaluation = D("Evaluation", "Service");
            	$res = $objEvaluation->saveEvaluationInfo($evaData);
            	if($res == true){
            		echo '{"code":"0","msg":"success"}';
            	}else{
            		echo '{"code":"-1","msg":"Evaluation更新失败！"}';
            	}
            }else{
            	echo '{"code":"-1","msg":"Housetop更新失败！"}';
            }
    	}elseif($optype == "submit" && $rtype == 1){
            if($projectCode == null){
                $projectCode = $_POST['no'] ? $_POST['no']:$_GET['no'];
                $mProjectCode = $_POST['token'] ? $_POST['token']:$_GET['token'];
                isProjectCodeRight($projectCode, $mProjectCode);
            }

    		$objProject  = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];

    		$proData = array();
            if($objProjectInfo['project_type'] == 1){
                $proData['project_id'] = $projectId;
                $proData['housetop_owner'] = $_POST['housetop_owner']; 
                $proData['company_type'] = $_POST['company_type'];
                $proData['plan_build_volume'] = $_POST['plan_build_volume']==""?null:$_POST['plan_build_volume'];
                // $proData['project_area'] = $_POST['county'];
                $proArea = $_POST['county']?$_POST['county']:$_POST['city'];
                $proArea = $proArea?$proArea:$_POST['province'];
                $proData['project_area'] = $proArea;
                $proData['project_address'] = $_POST['project_address'];
                $proData['housetop_type'] = $_POST['housetop_type'];
                $proData['housetop_type_other'] = $_POST['housetop_type_other'];
                $proData['synchronize_type'] = $_POST['synchronize_type'];
                $proData['plan_financing'] = $_POST['plan_financing']==""?null:$_POST['plan_financing'];
                $proData['financing_type'] = $_POST['financing_type'];
            }elseif($objProjectInfo['project_type'] == 2 || $objProjectInfo['project_type'] == 3){
                $proData['project_id'] = $projectId;
                $proData['plan_build_volume'] = $_POST['plan_build_volume']==""?null:$_POST['plan_build_volume'];
                // $proData['project_area'] = $_POST['county'];
                $proArea = $_POST['county']?$_POST['county']:$_POST['city'];
                $proArea = $proArea?$proArea:$_POST['province'];
                $proData['project_area'] = $proArea;
                $proData['project_address'] = $_POST['project_address'];
                $proData['project_name'] = $_POST['project_name'];
                $proData['ground_project_type'] = $_POST['ground_project_type'];
                $proData['project_finish_date'] = $_POST['project_finish_date'];
                $proData['project_electricity_price'] = $_POST['project_electricity_price']==""?null:$_POST['project_electricity_price'];
                $proData['project_investment'] = $_POST['project_investment']==""?null:$_POST['project_investment'];
                $proData['ground_condition'] = $_POST['ground_condition'];
                $proData['ground_property'] = $_POST['ground_property'];
                $proData['ground_property_other'] = $_POST['ground_property_other'];
                $proData['ground_area'] = $_POST['ground_area']==""?null:$_POST['ground_area'];
                $proData['plan_financing'] = $_POST['plan_financing']==""?null:$_POST['plan_financing'];
                $proData['financing_type'] = $_POST['financing_type'];
            }

            $evaData = array();
            $evaData['project_id'] = $projectId;
            $evaData['IRR'] = $_POST['IRR']==""?null:$_POST['IRR'];
            $evaData['evaluation_result'] = $_POST['evaluation_result'].",".$_POST['evaluation_result_text'];
            $evaData['static_payback_time'] = $_POST['static_payback_time']==""?null:$_POST['static_payback_time'];
            $evaData['dynamic_payback_time'] = $_POST['dynamic_payback_time']==""?null:$_POST['dynamic_payback_time'];
            $evaData['LCOE'] = $_POST['LCOE']==""?null:$_POST['LCOE'];
            $evaData['npv'] = $_POST['npv']==""?null:$_POST['npv'];
            $evaData['power_asset_current_value'] = $_POST['power_asset_current_value']==""?null:$_POST['power_asset_current_value'];
            $evaData['evaluation_content'] = $_POST['evaluation_content'];
            $evaData['document_review'] = $_POST['document_review'];
            $evaData['project_quality_situation'] = $_POST['project_quality_situation'];
            $evaData['project_invest_situation'] = $_POST['project_invest_situation'];
            $evaData['project_earnings_situation'] = $_POST['project_earnings_situation'];
            $evaData['duty_person'] = $_POST['duty_person'];
            $evaData['doc_mul'] = implode(",", $_POST['doc_mul']);

            $projectInfo = $objProject->where("id=".$proData['project_id']." and delete_flag!=9999")->find();
            if($projectInfo['status'] == 21){
                $sta = 23;
            }else{
                $sta = 22;
            }
			$res = $objProject->submitHousetopOrGround($proData, $sta, $objProjectInfo['project_type']);
            if($res == true){
            	$objEvaluation = D("Evaluation", "Service");
            	$res = $objEvaluation->submitEvaluationInfo($evaData);
            	if($res == true){
            		echo '{"code":"0","msg":"success"}';
            	}else{
            		echo '{"code":"-1","msg":"Evaluation更新失败！"}';
            	}
            }else{
            	echo '{"code":"-1","msg":"Housetop更新失败！"}';
            }
    	}elseif($rtype != 1){
            if($projectCode == null){
                $projectCode = $_POST['no'] ? $_POST['no']:$_GET['no'];
                $mProjectCode = $_POST['token'] ? $_POST['token']:$_GET['token'];
                isProjectCodeRight($projectCode, $mProjectCode);
            }

    		$objProject  = D("Project", "Service");
    		$objProjectInfo = $objProject->getProjectInfo($projectCode);
    		$projectId = $objProjectInfo['id'];
    		$projectDetail = $objProject->getProjectInEvaluation($projectId, $objProjectInfo['project_type']);
            $projectDetail['state_type'] = $objProject->getTypeAndStateStr($objProjectInfo['project_type'], $objProjectInfo['build_state']);
            
            $common = D("Common","Service");
            $projectDetail['companyType'] = $common->getProjectCompanyType($projectDetail['company_type']);
            $projectDetail['housetopType'] = $common->getHousetopType($projectDetail['housetop_type']);
            $projectDetail['synchronizeType'] = $common->getSynchronizeType($projectDetail['synchronize_type']);
            $projectDetail['financingType'] = $common->getFinancingType($projectDetail['financing_type']);
            // $projectDetail['electricityClearType'] = $common->getElectricityClearType($projectDetail['electricity_clear_type']);
            $projectDetail['groundProperty'] = $common->getGroundProperty($projectDetail['ground_property']);
            $projectDetail['groundCondition'] = $common->getGroundCondition($projectDetail['ground_condition']);
            // $projectDetail['measurePoint'] = $common->getMeasurePoint($projectDetail['measure_point']);
            // $projectDetail['projectHolderType'] = $common->getProjectHolderType($projectDetail['project_holder_type']);
            $projectDetail['groundProjectType'] = $common->getGroundProjectType($projectDetail['ground_project_type']);
            // $projectDetail['housetopDirection'] = $common->getHousetopDirection($projectDetail['housetop_direction']);
            if($projectDetail['project_finish_date'] == null){
                $projectDetail['project_finish_date'] = null;
            }else{
                $projectDetail['project_finish_date'] = date('Y-m-d', strtotime($projectDetail['project_finish_date']));
            }

            $area = D("Area", "Service");
            $areaArray = $area->getAreaArrayById($projectDetail['project_area']);
			
			$objEvaluation = D("Evaluation", "Service");
			$evaluationInfo = $objEvaluation->getEvaluation($projectId);
            $evaluationResult = explode(',', $evaluationInfo['evaluation_result']);
            $evaluationInfo['evaluation_result'] = $evaluationResult[0];
            $evaluationInfo['evaluation_result_text'] = $evaluationResult[1];

            $objDoc = D("Doc", "Service");
            $condition['id'] = $projectDetail['picture_full'];
            $picture = $objDoc->getDocInfo($condition);
            $docList = explode(',', $evaluationInfo['doc_mul']);
            $docListInfo = $objDoc->getAllDocInfo($docList);

            if ($_GET['display'] == 'json') {
                header('Content-Type: text/html; charset=utf-8');
                dump($picture);
                dump($docListInfo);
                dump($projectDetail);
                dump($areaArray);
                dump($evaluationInfo);
                exit;
            }
            //给项目进度用,直接截断了,返回json了
            if ($getJsonFlag == 1)
            {
              return array($picture[0]['file_rename'],$docListInfo,$projectDetail,$areaArray,$evaluationInfo,$objProjectInfo);
            }

            $this->assign('picture', $picture[0]['file_rename']);
            $this->assign('docListInfo', $docListInfo);
    		$this->assign('projectDetail', $projectDetail);
            $this->assign('areaArray', $areaArray);
    		$this->assign('evaluationInfo', $evaluationInfo);

            if($objProjectInfo['project_type'] == 1){
                if($objProjectInfo['build_state'] == 1){
                    $this->display("InnerStaff:dueDiligence_housetop_nonbuild");
                }elseif($objProjectInfo['build_state'] == 2){
                    $this->display("InnerStaff:dueDiligence_housetop_build");
                }
            }elseif($objProjectInfo['project_type'] == 2 || $objProjectInfo['project_type'] == 3){
                if($objProjectInfo['build_state'] == 1){
                    $this->display("InnerStaff:dueDiligence_ground_nonbuild");
                }elseif($objProjectInfo['build_state'] == 2){
                    $this->display("InnerStaff:dueDiligence_ground_build");
                }
            }
    	}
    }

    /**
    **@auth qianqiang
    **@breif 客服->意向书
    **@date 2015.12.28
    **/
    public function intent(){
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2);
        $projectCode = $_POST['no'] ? $_POST['no']:$_GET['no'];
        $mProjectCode = $_POST['token'] ? $_POST['token']:$_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode);
        $optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
        if($optype == "save" && $rtype == 1){
            $intentText = $_POST["yixiangshu"];
            if($intentText == "" || $intentText == null){
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"意向书不能为空"}';
            }
            $project = D("Project", "Service");
            //echo $projectCode;echo jj;exit;
            $result = $project->saveIntent($projectCode, $intentText);
            if($result === true)
                echo '{"code":"0","msg":"save success"}';
            else
                echo '{"code":"-1","msg":"save error"}';
        }elseif($optype == "submit" && $rtype == 1){
            $intentText = $_POST["yixiangshu"];
            if($intentText == "" || $intentText == null){
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"意向书不能为空"}';
            }
            $project = D("Project", "Service");
            $result = $project->submitIntent($projectCode, $intentText);
            if($result === true)
                echo '{"code":"0","msg":"save success"}';
            else
                echo '{"code":"-1","msg":"save error"}';
        }elseif($rtype != 1){
            $project = D("Project", "Service");
            $projectInfo = $project->getIntent($projectCode);
            //dump($projectInfo);exit;
            if($_GET['display']=="json"){
                header('Content-Type: text/html; charset=utf-8');
                dump($projectInfo);
                exit;
            }
            $this->assign("projectInfo", $projectInfo);
            $this->display("InnerStaff:intent");
        }
    }

    /**
    **@auth qianqiang
    **@breif 客服->推送项目
    **@date 2015.12.30
    **/
    public function pushProject($projectCode=null, $rtype=null, $getJsonFlag=null, $innerToken=null){
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2,$innerToken);
        if($rtype == null)
        {
            $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
        }
        if($projectCode == null)
        {
            $projectCode  = $_POST['no']     ? $_POST['no']:$_GET['no'];
            $mProjectCode = $_POST['token']  ? $_POST['token']:$_GET['token'];
            isProjectCodeRight($projectCode, $mProjectCode);
        }
        $projectObj = D('Project', 'Service');
        $investors = $_POST['investors'];
        $investorStr = substr($investors, 0, strlen($investors)-1);
        $investorList = explode(",",$investorStr);
        if($rtype == 1){
            $result = $projectObj->pushProject($projectCode, $investorList);
            if($result === true)
                echo '{"code":"0","msg":"push project success"}';
            else
                echo '{"code":"-1","msg":"push project error"}';
        }else{
            // if($projectObj->isPushProject($projectCode) === true){
            $page = $_GET['page'];
            if(empty($page)) $page=1;
            $pageSize = 6;
            $investor = D('User', 'Service');
            $investorList = $investor->getInvestorPush($projectCode, $page);
            $investorTotal = $investor->getInvestorPush($projectCode, -1);
            $data = array();
            $data["list"] = $investorList;
            $data["page"] = $page;
            $data["count"] = sizeof($investorTotal);
            $data["totalPage"] = ceil($data["count"]/$pageSize+1);
            $data["endPage"] = ceil($data["count"]/$pageSize);
            if($_GET['display']=="json"){
                header('Content-Type: text/html; charset=utf-8');
                dump($data);
                exit;
            }

            //直接截断了,返回json了
            if ($getJsonFlag == 1)
            {
              return $data;
            }
            $this->assign("arrData", $data);
            // }
            $this->display("InnerStaff:pushProject");
        }
    }

    /**
    **@auth qianqiang
    **@breif 客服->综合查询
    **@date 2016.1.8
    **/
    public function search(){
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2);
        $rtype = $_POST['rtype'] ? $_POST['rtype']:$_GET['rtype'];
        $optype = $_POST['optype'] ? $_POST['optype']:$_GET['optype'];
        $projectObj = D("Project", "Service");
        $userObj = D("User", "Service");
        if($rtype == 1 && $optype == 'change'){
            $projectCode = $_GET['no'];
            $mProjectCode = $_GET['token'];
            isProjectCodeRight($projectCode, $mProjectCode);
            $oldStatus = $_GET['oldStatus'];
            $status = $_GET['status'];
            if($status == 11){//未提交
                $newStatus = 11;
            }elseif($status == 12){//已提交
                $newStatus = 12;
            }elseif($status == 14){//已尽职调查
                if($oldStatus == 12){
                    echo '{"code":"-1","msg":"项目不可修改为已尽职调查"}';
                    exit;
                }
                $newStatus = 22;
            }elseif($status == 13){//已签意向书
                if($oldStatus == 12 || $oldStatus == 13 || $oldStatus == 22){
                    echo '{"code":"-1","msg":"项目不可修改为已签意向书"}';
                    exit;
                }
                $newStatus = 23;
            }elseif($status == 15){//已签融资合同
                if($oldStatus == 12 || $oldStatus == 13 || $oldStatus == 22 || $oldStatus == 23){
                    echo '{"code":"-1","msg":"项目不可修改为已签融资合同"}';
                    exit;
                }
                $newStatus = 31;
            }
            $res = $projectObj->changeProjectStatus($projectCode, $oldStatus, $newStatus);
            if($res === true){
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"0","msg":"修改成功！"}';
            }else{
                header('Content-Type: text/html; charset=utf-8');
                echo '{"code":"-1","msg":"'.$res.'"}';
            }
        }else{
            $companyName = $_GET['company_name'];
            $companyType = $_GET['project_type'];
            $situation = $_GET['province'];
            $startDate = $_GET['startDate'];
            $endDate = $_GET['endDate'];
            $status = $_GET['status'];
            $cooperationType = $_GET['cooperation_type'];
            $page = $_GET['page'];
            if(empty($page)) $page=1;
            $pageSize = 6;

            $projectList = $projectObj->searchService($companyName, $companyType, $situation, $startDate, $endDate, $status, $cooperationType, $page);
            $projectTotal = $projectObj->searchService($companyName, $companyType, $situation, $startDate, $endDate, $status, $cooperationType, -1);

            $companyNameList = $userObj->getAllCompanyName();
            $data = array();
            $data["list"] = $projectList;
            $data["campanyName"] = $companyNameList;
            $data["page"] = $page;
            $data["count"] = sizeof($projectTotal);
            $data["totalPage"] = ceil($data["count"]/$pageSize+1);
            $data["endPage"] = ceil($data["count"]/$pageSize);
            $data["searchInfo"]["companyName"] = $companyName;
            $data["searchInfo"]["companyType"] = $companyType;
            $data["searchInfo"]["province"] = $situation;
            $data["searchInfo"]["startDate"] = $startDate;
            $data["searchInfo"]["endDate"] = $endDate;
            $data["searchInfo"]["status"] = $status;
            $data["searchInfo"]["cooperationType"] = $cooperationType;
            if($_GET['display']=="json"){
                header('Content-Type: text/html; charset=utf-8');
                dump($data);
                exit;
            }
            $this->assign("arrData", $data);
            $this->display("InnerStaff:search");
        }
    }

    /**
    **@auth qianqiang
    **@breif 客服->删除项目
    **@date 2016.1.20
    **/
    public function delete(){
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2);
        $projectCode = $_GET['no'];
        $mProjectCode = $_GET['token'];
        isProjectCodeRight($projectCode, $mProjectCode);
        $projectObj = D("Project", "Service");
        $condition['project_code'] = $projectCode;
        $condition["delete_flag"] = array('neq',9999);
        $proInfo = $projectObj->where($condition)->find();
        $res = $projectObj->deleteProjectService($proInfo['id']);
        if($res){
            header('Content-Type: text/html; charset=utf-8');
            echo '{"code":"0","msg":"删除成功！"}';
        }else{
            header('Content-Type: text/html; charset=utf-8');
            echo '{"code":"-1","msg":"删除失败！"}';
        }
    }

    /**
    **@auth qianqiang
    **@breif 客服->修改密码
    **@date 2015.12.05
    **/
    public function securityCenter()
    {
        isLogin($_COOKIE['email'], $_COOKIE['mEmail']);
        authentication($_COOKIE['email'], 2);
        if($_POST['rtype'] == 1 || $_GET['rtype'] == 1){
            $email = $_COOKIE['email'];
            $pwd = $_POST['password'];
            $newPwd = $_POST['newPassword'];
            if ( empty($pwd) || empty($newPwd) ) {
                echo '{"code":"-1","msg":"新旧密码不可为空！"}';
                exit;
            }

            $user = D('User','Service');
            $objUser = $user->changePasswordService($email, $pwd, $newPwd);
            if ($_GET['display'] == 'json') {
                dump($objUser);
                exit;
            }
            $user->logoutService();
            echo '{"code":"0","msg":"success"}';
        }else{
            $this->display("InnerStaff:securityCenter");
        }
    }
}