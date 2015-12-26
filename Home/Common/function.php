<?php 
/**
 * code()是验证码函数
 * @access public
 * @param int $_width 验证码的长度：如果要6位长度推荐75+50；如果要8位，推荐75+50+50，依次类推
 * @param int $_height 验证码的高度
 * @param int $_rnd_code 验证码的位数
 * @param bool $_flag 验证码是否需要边框：true有边框， false无边框（默认）
 * @return void 这个函数执行后产生一个验证码
 */
function code($_width = 75,$_height = 25,$_rnd_code = 4,$_flag = false) {
    //创建随机码
    for ($i=0;$i<$_rnd_code;$i++) {
        $_nmsg .= dechex(mt_rand(0,15));
    }
    //保存在session
    //$_SESSION['code'] = $_nmsg;
    //把加密后的动态码保存到cookie
    $mDynamicCode = MD5($_nmsg."ENFENF");
    setcookie("mDynamicCode", $email, time()+3600);
    

    //创建一张图像
    $_img = imagecreatetruecolor($_width,$_height);
    //白色
    $_white = imagecolorallocate($_img,255,255,255);
    //填充
    imagefill($_img,0,0,$_white);
    if ($_flag) {
        //黑色,边框
        $_black = imagecolorallocate($_img,0,0,0);
        imagerectangle($_img,0,0,$_width-1,$_height-1,$_black);
    }
    //随即画出6个线条
    for ($i=0;$i<6;$i++) {
        $_rnd_color = imagecolorallocate($_img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
        imageline($_img,mt_rand(0,$_width),mt_rand(0,$_height),mt_rand(0,$_width),mt_rand(0,$_height),$_rnd_color);
    }
    //随即雪花
    for ($i=0;$i<100;$i++) {
        $_rnd_color = imagecolorallocate($_img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
        imagestring($_img,1,mt_rand(1,$_width),mt_rand(1,$_height),'*',$_rnd_color);
    }
    //输出验证码
    for ($i=0;$i<strlen($_SESSION['code']);$i++) {
        $_rnd_color = imagecolorallocate($_img,mt_rand(0,100),mt_rand(0,150),mt_rand(0,200));
        imagestring($_img,5,$i*$_width/$_rnd_code+mt_rand(1,10),mt_rand(1,$_height/2),$_SESSION['code'][$i],$_rnd_color);
    }
    //输出图像
    // 建立 PNG 图型。
    // int imagepng(int im, string [filename]);
    // 本函数用来建立一张 PNG 格式图形。参数 im 为使用 ImageCreate() 所建立的图片代码。
    // 参数 filename 可省略，若无本参数 filename，则会将图片指接送到浏览器端，
    // 记得在送出图片之前要先送出使用 Content-type: image/png 的标头字符串 (header) 到浏览器端，以顺利传输图片。
    header('Content-Type: image/png');
    imagepng($_img);
    //销毁
    imagedestroy($_img);

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
    // 设置附件上传大小
    $upload->maxSize   =     3145728 ;
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
    // 设置附件上传大小
    $upload->maxSize   =     3145728 ;
    // 设置附件上传类型doc .docx .xls .xlsx .ppt .pptx .txt .pdf
    $upload->exts      =     array('pdf', 'doc', 'excel', 'txt', 'docx', 'xlsx', 'xls', 'ppt', 'pptx');
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
**@breif 登陆状态判断
**@param $userName 用户名
**@param mUserName  加密后的用户名
**@return 如果登陆了就返回true 如果没有登陆就弹框提示，并且跳转到登陆页面
**@date 2015.12.12
**/
function isLogin($userName, $mUserName){
    return true;
    if (empty($userName) || empty($mUserName)) {
        //没有登陆，弹框提示，并且跳转到登陆页
        header('Content-Type: text/html; charset=utf-8');
        echo "<script type='text/javascript'>alert('没有登录');location.href='?c=User&a=login'</script>";
        exit;
    }
    if (!($mUserName == MD5(addToken($userName)))) {
        //登录信息错误，弹框提示，并且跳转到登陆页
        header('Content-Type: text/html; charset=utf-8');
        echo "<script type='text/javascript'>alert('登录信息错误');location.href='?c=User&a=login'</script>";
        exit;
    }
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
    // return true;
    if (empty($userName) || empty($mUserName)) {
        //没有登陆，弹框提示，并且跳转到登陆页
        header('Content-Type: text/html; charset=utf-8');
        echo "<script type='text/javascript'>alert('没有登录');location.href='?c=Admin&a=login'</script>";
        exit;
    }
    if (!($mUserName == MD5(addToken($userName)))) {
        //登录信息错误，弹框提示，并且跳转到登陆页
        header('Content-Type: text/html; charset=utf-8');
        echo "<script type='text/javascript'>alert('登录信息错误');location.href='?c=Admin&a=login'</script>";
        exit;
    }
}

/**
**@auth qianqiang
**@breif 判断必填资料是否填写完成
**@param $email 用户邮箱
**@return 如果填写了就返回true 如果没有没有填写就弹框提示，并且跳转到我的资料页面
**@date 2015.12.12
**/
function isDataComplete($email){
    return true;
    $user = M("User");
    $objUser = $user->where("email='".$email."'")->find();
    //dump($objUser);
    //只有项目提供方有必填项：企业名称、联系人、联系人手机
    if($objUser["user_type"] == 3){
        if($objUser["company_name"] == NULL || $objUser["company_contacts"] == NULL || $objUser["company_contacts_phone"] == NULL){
            header('Content-Type: text/html; charset=utf-8');
            echo "<script type='text/javascript'>alert('请先完善个人资料');location.href='?c=ProjectProviderMyInfo&a=myInformation'</script>";
            exit;
        }
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

?>