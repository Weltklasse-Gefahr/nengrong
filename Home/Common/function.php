<?php 
require_once dirname(dirname(__FILE__)).'/library/class.phpmailer.php';
require_once dirname(dirname(__FILE__)).'/library/PHPMailerAutoload.php';


/**
 * @auth qiujinhan
 * *brief 这里可以做一些配置文件  使用方法就是$res =  qiangge('12');
 * @date 2016.1.7
 */
 function qiangge($num){

    $aa = array(
        '11' => '未提交', 
        '12' => '提交', 
        );  
    return $aa[$num];  
}

/**
 * @auth qianqiang
 * @breif 系统邮件发送函数
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题 
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
 * @return boolean 
 * @date 2016.1.7
 */
 function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null){
    $config = C('THINK_EMAIL');
    //vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
    $mail             = new PHPMailer(); //PHPMailer对象
    $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();  // 设定使用SMTP服务
    $mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
                                               // 1 = errors and messages
                                               // 2 = messages only
    $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl';                 // 使用安全协议
    $mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
    $mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
    $mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
    $mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
    $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
    $replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
    $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject    = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($to, $name);
    if(is_array($attachment)){ // 添加附件
        foreach ($attachment as $file){
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
 }

/**
**@auth qiujinhan
**@breif 上传一张图片
**@param $file 上传文件的二进制流
**@param savePath  上传的子目录
**@return 更新成功返回图片地址  上传失败返回false
**@date 2015.12.05
**/
function uploadPicOne($photo, $savePath = ''){
    // 实例化上传类
    $upload = new \Think\Upload();
    // 设置附件上传大小30M
    $upload->maxSize   =     3145728 * 10 ;
    // 设置附件上传类型 .jpg .jpeg .gif .png .bmp .psd
    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg', 'bmp', 'psd');
    // 设置附件上传根目录
    $dirNengrongUserDataImg = dirname(dirname(dirname(__FILE__))).'/userdata/img/';
    $upload->rootPath  =      $dirNengrongUserDataImg; 
    //图片的保持名字
    $upload->saveName = array('uniqid','');
    // 开启子目录保存 并调用自定义函数get_user_id生成子目录
    $upload->autoSub = true;
    $upload->subName = "img";

    // 设置附件上传（子）目录
    $upload->savePath  =       $savePath; 
    // 上传单个文件 
    $info   =   $upload->uploadOne($photo);
    if(!$info) {
         // 上传错误提示错误信息
        echo '{"code":"-1","msg":"更新失败！原因：'.$upload->getError().'"}';
        exit;
    }
    else{
         // 上传成功 获取上传文件信息
         return  $info['savepath'].$info['savename'];
    }
}

/**
**@auth qiujinhan
**@breif 上传文件
**@param $file 上传文件的二进制流
**@param savePath  上传的子目录
**@return 更新成功返回文件的存储路径  上传失败返回false
**@date 2015.12.05
**/
function uploadFileOne($file, $savePath = ''){
    // 实例化上传类
    $upload = new \Think\Upload();
    // 设置附件上传大小30M
    $upload->maxSize   =     3145728 * 10 ;
    // 设置附件上传类型doc .docx .xls .xlsx .ppt .pptx .txt .pdf
    $upload->exts      =     array('pdf', 'doc', 'excel', 'txt', 'docx', 'xlsx', 'xls', 'ppt', 'pptx','jpg', 'gif', 'png', 'jpeg', 'bmp', 'psd');
    // 设置附件上传根目录
    $dirNengrongUserDataDoc = dirname(dirname(dirname(__FILE__))).'/userdata/doc/'; 
    $upload->rootPath  =      $dirNengrongUserDataDoc; 
    //doc的文件不变
    $upload->saveName =  array('uniqid','');
    // 开启子目录保存 并调用自定义函数get_user_id生成子目录
    $upload->autoSub = true;
    $upload->subName = "file";
    // 设置附件上传（子）目录
    $upload->savePath  =       $savePath; 
    // 上传单个文件 
    $info   =   $upload->uploadOne($file);
    if(!$info) {
         // 上传错误提示错误信息
        echo '{"code":"-1","msg":"更新失败！原因：'.$upload->getError().'"}';
        exit;
    }
    else{
         // 上传成功 获取上传文件信息
         return  $info['savepath'].$info['savename'];
    }
}

/**
**@auth qiujinhan
**@breif 生成项目编号
**@param $projectType 项目类型
**@param $area 地区  
**@param $financingType 融资模式  
**@return 更新成功返回文件的存储路径  上传失败返回false
**@date 2015.12.05
**/
function getProjectCode($projectType, $area, $financingType){
    //项目类型
    if($projectType == 1) //屋顶
    {
        $proType = "R";
    }
    elseif($projectType == 2) //地面
    {
        $proType = "G";
    }
    elseif($projectType == 3) //大型
    {
        $proType = "L";
    }
    else
    {
        $proType = "X"; //其他
    }
    //行业
    $industry = array(
        '1' => "I", 
        '2' => "C", 
        '3' => "A", 
        '4' => "R", 
        '5' => "F", 
        '6' => "X", 
        );
    $index = rand(1,6);
    $industryType =  $industry[$index];
    //地区
    //$areaType = empty($area)?"0101":$area;
    $areaType = rand(1000,9999);
    //项目规模
    $projectScale = rand(100,999)."K";
    //年份
    $year = substr(date('Y'),2,2);
    //融资模式
    if($financingType == 1) //直租
    {
        $proType = "D";
    }
    elseif($financingType == 2) //回租
    {
        $proType = "B";
    }
    elseif($financingType == 3) //股权融
    {
        $proType = "E";
    }
    else
    {
        $proType = "X"; //其他
    }
    //客户号yu 序列号
    $mul = rand(100000,999999);
    return $proType.$industryType.$areaType.'-'.$projectScale.'-'.$proType.$year.'-'.$mul;

}

/**
**@auth qiujinhan
**@breif 登陆状态判断
**@param $userName 用户名
**@param mUserName  加密后的用户名
**@return 如果登陆了就返回true 如果没有登陆跳转到登陆页面 如果是登录时flag=1，返回false
**@date 2015.12.12
**/
function isLogin($userName, $mUserName, $flag=0){
    // return true;
    if (empty($userName) || empty($mUserName)) {
        //没有登陆，弹框提示，并且跳转到登陆页
        header('Content-Type: text/html; charset=utf-8');
        echo "<script type='text/javascript'>alert('没有登录');location.href='?c=User&a=login'</script>";
        exit;
    }
    if (!($mUserName == MD5(addToken($userName)))) {
        //登录信息错误，弹框提示，并且跳转到登陆页
        setcookie("email", "", time()-3600*24*7);
        setcookie("mEmail", "", time()-3600*24*7);
        setcookie("userType", "", time()-3600*24*7);
        setcookie("userName", "", time()-3600*24*7);
        header('Content-Type: text/html; charset=utf-8');
        echo "<script type='text/javascript'>alert('登录信息错误');location.href='?c=User&a=login'</script>";
        exit;
    }
    if($flag == 1){
        return false;
    }
    $user = M('User');
    $condition['email'] = $userName;
    $condition['delete_flag'] = 0;
    $res = $user->where($condition)->find();
    if(!$res){//用户被删除
        setcookie("email", "", time()-3600*24*7);
        setcookie("mEmail", "", time()-3600*24*7);
        setcookie("userType", "", time()-3600*24*7);
        setcookie("userName", "", time()-3600*24*7);
        header('Content-Type: text/html; charset=utf-8');
        echo "<script type='text/javascript'>alert('没有登录');location.href='?c=User&a=login'</script>";
        exit;
    }

    return true;
}

/**
**@auth qianqiang
**@breif 管理员登陆状态判断
**@param $userName 用户名
**@param mUserName  加密后的用户名
**@return 如果登陆了就返回true 如果没有登陆就弹框提示，并且跳转到登陆页面
**@date 2015.12.17
**/
function isAdminLogin($userName, $mUserName){
    if(empty($userName) || empty($mUserName)){
        //没有登陆，弹框提示，并且跳转到登陆页
        header('Content-Type: text/html; charset=utf-8');
        echo "<script type='text/javascript'>alert('没有登录');location.href='?c=Admin&a=login'</script>";
        exit;
    }
    if(!($mUserName == MD5(addToken($userName)))){
        //登录信息错误，弹框提示，并且跳转到登陆页
        header('Content-Type: text/html; charset=utf-8');
        echo "<script type='text/javascript'>alert('登录信息错误');location.href='?c=Admin&a=login'</script>";
        exit;
    }
    $obj = M('Admin');
    $condition['user_name'] = $userName;
    $res = $obj->where($condition)->select();
    if(empty($res)){
        header('Content-Type: text/html; charset=utf-8');
        echo "<script type='text/javascript'>alert('用户权限错误');location.href='?c=User&a=login'</script>";
        exit;
    }
    return true;
}

/**
**@auth qianqiang
**@breif 判断项目编码是否正确，不正确返回登录界面
**@param pc 项目编码
**@param mpc  加密后的项目编码
**@return 如果正确就返回true
**@date 2015.12.17
**/
function isProjectCodeRight($pc, $mpc){
    // return true;
    if(empty($pc) || empty($mpc)){
        header('Content-Type: text/html; charset=utf-8');
        echo '{"code":"-1","msg":"项目信息验证错误"}';
        // echo "<script type='text/javascript'>alert('项目错误，重新登录');location.href='?c=User&a=login'</script>";
        exit;
    }
    if(!($mpc == MD5(addToken($pc)))){
        header('Content-Type: text/html; charset=utf-8');
        echo '{"code":"-1","msg":"项目信息验证错误"}';
        // echo "<script type='text/javascript'>alert('项目错误，重新登录');location.href='?c=User&a=login'</script>";
        exit;
    }
    $projectObj = M('Project');
    $condition['project_code'] = $pc;
    $condition['delete_flag'] = 0;
    $res = $projectObj->where($condition)->select();
    if(empty($res)){
        echo "<script type='text/javascript'>location.href='?c=User&a=login'</script>";
    }
    return true;
}

/**
**@auth qianqiang
**@breif 判断必填资料是否填写完成
**@param $email 用户邮箱
**@return 如果填写了就返回true 如果没有没有填写就弹框提示，并且跳转到我的资料页面
**@date 2015.12.12
**/
function isDataComplete($email, $flag=0){
    //return true;
    $user = M("User");
    $objUser = $user->where("email='".$email."' and delete_flag!=9999")->find();
    //dump($objUser);
    //项目提供方/项目投资方有必填项：企业名称、联系人、联系人手机
    if($objUser["user_type"] == 3){
        if($objUser["company_name"] == NULL || $objUser["company_contacts"] == NULL || $objUser["company_contacts_phone"] == NULL){
            header('Content-Type: text/html; charset=utf-8');
            if($flag == 1){
                echo "<script type='text/javascript'>location.href='?c=ProjectProviderMyInfo&a=myInformation'</script>";
            }else{
                echo "<script type='text/javascript'>alert('请先完善个人资料');location.href='?c=ProjectProviderMyInfo&a=myInformation'</script>";
            }
            exit;
        }
    }elseif($objUser["user_type"] == 4){
        if($objUser["company_name"] == NULL || $objUser["company_contacts"] == NULL || $objUser["company_contacts_phone"] == NULL){
            header('Content-Type: text/html; charset=utf-8');
            if($flag == 1){
                echo "<script type='text/javascript'>location.href='?c=ProjectInvestorMyInfo&a=myInformation'</script>";
            }else{
                echo "<script type='text/javascript'>alert('请先完善个人资料');location.href='?c=ProjectInvestorMyInfo&a=myInformation'</script>";
            }
            exit;
        }
    }
    return true;
}

/**
**@auth qianqiang
**@breif 鉴权用户身份，身份错误提示后跳转
**@param $email 用户邮箱
**@param $userType 用户类型
**@param $innerToken 内部调用就不用判断了
**@date 2016.1.19
**/
function authentication($email, $userType, $innerToken=null){//return true;
    if($innerToken == "InternalCall")
    {
        return true;
    }
    $user = M("User");
    $objUser = $user->where("email='".$email."' and delete_flag!=9999")->find();
    // dump($userType);
    // dump($objUser);
    // dump($objUser["user_type"] != $userType);
    // exit;
    if(intval($objUser["user_type"]) != $userType){
        header('Content-Type: text/html; charset=utf-8');
        echo "<script type='text/javascript'>alert('用户权限错误');location.href='?c=User&a=login'</script>";
        exit;
    }
    return true;
}

/**
**@auth qianqiang
**@breif 生成加密前的加密串
**@param $str 需要加密的串
**@return 返回需要加密的串
**@date 2015.12.17
**/
function addToken($str){
    return $str."ENFESDFSDNDLFJddddsssefOWEMDJDJ23392222KKSKSNF";
}

/**
**@auth qianqiang
**@breif 获取加密秘钥
**@date 2015.12.17
**/
function getKey(){
    return "ENFESDFSrwdsccxh33922@&@##22KKSKSNF";
}

// /**
// **@auth qianqiang
// **@breif 加密
// **@date 2016.1.7
// **/
// function encrypt($data, $key)  
// {  
//     $key    =   md5($key);  
//     $x      =   0;  
//     $len    =   strlen($data);  
//     $l      =   strlen($key);  
//     for ($i = 0; $i < $len; $i++)  
//     {  
//         if ($x == $l)   
//         {  
//             $x = 0;  
//         }  
//         $char .= $key{$x};  
//         $x++;  
//     }  
//     for ($i = 0; $i < $len; $i++)  
//     {  
//         $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);  
//     }  
//     return base64_encode($str);  
// }  

// /**
// **@auth qianqiang
// **@breif 解密
// **@date 2016.1.7
// **/
// function decrypt($data, $key)  
// {  
//     $key = md5($key);  
//     $x = 0;  
//     $data = base64_decode($data);  
//     $len = strlen($data);  
//     $l = strlen($key);  
//     for ($i = 0; $i < $len; $i++)  
//     {  
//         if ($x == $l)   
//         {  
//             $x = 0;  
//         }  
//         $char .= substr($key, $x, 1);  
//         $x++;  
//     }  
//     for ($i = 0; $i < $len; $i++)  
//     {  
//         if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))  
//         {  
//             $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));  
//         }  
//         else  
//         {  
//             $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));  
//         }  
//     }  
//     return $str;  
// }  

?>