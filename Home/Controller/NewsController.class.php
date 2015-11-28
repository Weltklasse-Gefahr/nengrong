<?php
namespace Home\Controller;
use Think\Controller;
class NewsController extends Controller {
    public function index(){
        $this->display();
    }
    public function newsList(){
        //$baseDir = '/home/ubuntu/enetfPlatform/nengrong/Home/View/';
        //$this->assign('baseDir',$baseDir);
        $page  = $_GET['page'];
        if(empty($page)) 
        {
            $page=1;
        }
        $mysql_server_name="localhost"; //数据库服务器名称
        $mysql_username="root"; // 连接数据库用户名
        $mysql_password=""; // 连接数据库密码
        $mysql_database="nengrongweb"; // 数据库的名字
        $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
        $start = $page*5-5;
        $end = 5;
        $strsql="SELECT id,title,time FROM news order by id desc limit $start,$end";
        $result=mysql_db_query($mysql_database, $strsql, $conn);
        $data = array();
        while($row=mysql_fetch_row($result))
        {
            $data["list"][]=$row;
            //echo json_encode($row);
        }
        $data["page"] = $page;
        $strsql="SELECT count(*) FROM news";
        $result=mysql_db_query($mysql_database, $strsql, $conn);
        $row=mysql_fetch_row($result);
        //var_dump($row);
        $data["count"] = $row['0'];
        $data["totalPage"] = ceil($data["count"]/5+1);
        $data["endPage"] = ceil($data["count"]/5);
        //echo json_encode($data);
        $this->assign('arrData',$data);
        $this->display();
    }
    public function newsDelete(){
        //$baseDir = '/home/ubuntu/enetfPlatform/nengrong/Home/View/';
        //$this->assign('baseDir',$baseDir);
        $page  = $_GET['page'];
        if(empty($page)) 
        {
            $page=1;
        }
        $mysql_server_name="localhost"; //数据库服务器名称
        $mysql_username="root"; // 连接数据库用户名
        $mysql_password=""; // 连接数据库密码
        $mysql_database="nengrongweb"; // 数据库的名字
        $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
        $start = $page*5-5;
        $end = 5;
        $strsql="SELECT id,title,time FROM news order by id desc limit $start,$end";
        $result=mysql_db_query($mysql_database, $strsql, $conn);
        $data = array();
        while($row=mysql_fetch_row($result))
        {
            $data["list"][]=$row;
            //echo json_encode($row);
        }
        $data["page"] = $page;
        $strsql="SELECT count(*) FROM news";
        $result=mysql_db_query($mysql_database, $strsql, $conn);
        $row=mysql_fetch_row($result);
        //var_dump($row);
        $data["count"] = $row['0'];
        $data["totalPage"] = ceil($data["count"]/5+1);
        $data["endPage"] = ceil($data["count"]/5);
        //echo json_encode($data);
        $this->assign('arrData',$data);
        $this->display();
    }
    public function newsContent(){
        $id  = $_GET['id'];
        if(empty($id))
        {
            $id=1;
        }
        $mysql_server_name="localhost"; //数据库服务器名称
        $mysql_username="root"; // 连接数据库用户名
        $mysql_password=""; // 连接数据库密码
        $mysql_database="nengrongweb"; // 数据库的名字
        $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
        $strsql="SELECT * FROM news where id=$id";
        //mysql_query(”SET NAMES ‘utf-8’”);
        $result=mysql_db_query($mysql_database, $strsql, $conn);
        $row=mysql_fetch_row($result);
        $data = array();
        //新闻阅读加一
        $strsql = "update news set readcount=readcount+1";
        $result=mysql_db_query($mysql_database, $strsql, $conn);
        $data["list"] = $row;
        if($id ==1)
        {
            $preNews = null;
        }
        else
        {
            $preNews = $id;
        }
        $data["preNews"] = $preNews;
        $data["nextNews"] = $id+1;
        //echo json_encode($data);
        //echo $data["list"][2];
        $this->assign('arrData',$data);
        $this->display();
    }
    public function docList(){
        $page  = $_GET['page'];
        if(empty($page))
        {
            $page=1;
        }
        $mysql_server_name="localhost"; //数据库服务器名称
        $mysql_username="root"; // 连接数据库用户名
        $mysql_password=""; // 连接数据库密码
        $mysql_database="nengrongweb"; // 数据库的名字
        $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
        $start = $page*5-5;
        $end = 5;
        $strsql="SELECT id,title,time FROM doc order by time desc limit $start,$end";
        $result=mysql_db_query($mysql_database, $strsql, $conn);
        $data = array();
        while($row=mysql_fetch_row($result))
        {
            $data["list"][]=$row;
            //echo json_encode($row);
        }
        $data["page"] = $page;
        $strsql="SELECT count(*) FROM doc";
        $result=mysql_db_query($mysql_database, $strsql, $conn);
        $row=mysql_fetch_row($result);
        //var_dump($row);
        $data["count"] = $row['0'];
        $data["totalPage"] = ceil($data["count"]/5+1);
        $data["endPage"] = ceil($data["count"]/5);
        //echo json_encode($data);
        $this->assign('arrData',$data);
        $this->display();
    }
    public function docDelete(){
        $page  = $_GET['page'];
        if(empty($page))
        {
            $page=1;
        }
        $mysql_server_name="localhost"; //数据库服务器名称
        $mysql_username="root"; // 连接数据库用户名
        $mysql_password=""; // 连接数据库密码
        $mysql_database="nengrongweb"; // 数据库的名字
        $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
        $start = $page*5-5;
        $end = 5;
        $strsql="SELECT id,title,time FROM doc order by time desc limit $start,$end";
        $result=mysql_db_query($mysql_database, $strsql, $conn);
        $data = array();
        while($row=mysql_fetch_row($result))
        {
            $data["list"][]=$row;
            //echo json_encode($row);
        }
        $data["page"] = $page;
        $strsql="SELECT count(*) FROM doc";
        $result=mysql_db_query($mysql_database, $strsql, $conn);
        $row=mysql_fetch_row($result);
        //var_dump($row);
        $data["count"] = $row['0'];
        $data["totalPage"] = ceil($data["count"]/5+1);
        $data["endPage"] = ceil($data["count"]/5);
        //echo json_encode($data);
        $this->assign('arrData',$data);
        $this->display();
    }
    public function downloadDoc(){
         $docId  = $_GET['docid'];

         $mysql_server_name="localhost"; //数据库服务器名称
         $mysql_username="root"; // 连接数据库用户名
         $mysql_password=""; // 连接数据库密码
         $mysql_database="nengrongweb"; // 数据库的名字
         $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
         $strsql="SELECT title FROM doc where id=$docId";
         $result=mysql_db_query($mysql_database, $strsql, $conn); 
         $row=mysql_fetch_row($result);
         $docName=$row['0'];
         $path_name = '/home/ubuntu/enetfPlatform/nengrong/UploadDocument/'.$docName;
         $save_name= $docName;
         ob_end_clean();
         $hfile = fopen($path_name, "rb");// or die("Can not find file: $path_name\n");
         Header("Content-type: application/pdf");
         Header("Content-Transfer-Encoding: binary");
         Header("Accept-Ranges: bytes");
         Header("Content-Length: ".filesize($path_name));
         Header("Content-Disposition: attachment; filename=\"$save_name\"");
         while (!feof($hfile)) {
            echo fread($hfile, 32768);
         }
         fclose($hfile);
    }
    public function uploadnews(){
         $title  = $_POST['title'];
         $token  = $_POST['token'];
         if ($token != "nengrong20151101")
         {
              echo '{"code":"-1","msg":"token不对哦，你没有权限!"}';
              exit;
         }
         $time  = date('Y-m-d');
         $readcount  = '4';
         $fromplace  = $_POST['fromplace'];
         $newscontent  = $_POST['newscontent'];
         if (empty($title) || empty($time)|| empty($fromplace)||empty($newscontent))
         {
             echo '{"code":"-1","msg":"缺少参数!"}';
             exit;
         }
         $mysql_server_name="localhost"; //数据库服务器名称
         $mysql_username="root"; // 连接数据库用户名
         $mysql_password=""; // 连接数据库密码
         $mysql_database="nengrongweb"; // 数据库的名字
         $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
         $strsql="insert into news values('','".$title."','".$time."', '".$readcount."', '".$fromplace."','".$newscontent."')";
         $result=mysql_db_query($mysql_database, $strsql, $conn); 
         if ($result)
         {
             echo '{"code":"0","msg":""}';
             
         }
         else
         {
             echo '{"code":"-1","msg":"mysql err!"}';
         }
    }
    public function deletenews(){
         $id  = $_POST['id'];
         $token  = $_POST['token'];
         if ($token != "nengrong20151101")
         {
              echo '{"code":"-1","msg":"token不对哦，你没有权限!"}';
              exit;
         }
         if (empty($id))
         {
             echo '{"code":"-1","msg":"缺少参数!"}';
             exit;
         }
         $mysql_server_name="localhost"; //数据库服务器名称
         $mysql_username="root"; // 连接数据库用户名
         $mysql_password=""; // 连接数据库密码
         $mysql_database="nengrongweb"; // 数据库的名字
         $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
         $strsql= "delete from news where id=".$id;
         $result=mysql_db_query($mysql_database, $strsql, $conn); 
         if ($result)
         {
             echo '{"code":"0","msg":""}';
             
         }
         else
         {
             echo '{"code":"-1","msg":"mysql err!"}';
         }
    }
    public function deletedoc(){
         $id  = $_POST['id'];
         $token  = $_POST['token'];
         if ($token != "nengrong20151101")
         {
              echo '{"code":"1","errmsg":"token不对哦，你没有权限!"}';
              exit;
         }
         if (empty($id))
         {
             echo '{"code":"1","errmsg":"缺少参数!"}';
             exit;
         }
         $mysql_server_name="localhost"; //数据库服务器名称
         $mysql_username="root"; // 连接数据库用户名
         $mysql_password=""; // 连接数据库密码
         $mysql_database="nengrongweb"; // 数据库的名字
         $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
         $strsql= "delete from doc where id=".$id;
         $result=mysql_db_query($mysql_database, $strsql, $conn); 
         if ($result)
         {
             echo '{"code":"0","errmsg":""}';
             
         }
         else
         {
             echo '{"code":"1","errmsg":"mysql err!"}';
         }
    }
    public function uploaddoc(){
         $title  = $_FILES["files"]["name"];
         $title  = str_replace(" ","_",$title);
         $time  = date('Y-m-d');
         $token  = $_POST['token'];
         if ($token != "nengrong20151101")
         {
              echo '{"code":"1","errmsg":"token不对哦，你没有权限!"}';
              exit;
         }
         if (empty($title) || empty($time))
         {
             echo '{"code":"1","errmsg":"缺少参数!"}';
             exit;
         }
         if ($_FILES["files"]["type"] != "application/pdf")
         {
             echo '{"code":"1","errmsg":"请上传PDF文件哦!"}';
             exit;
         }
         $strDirFilename = "/home/ubuntu/enetfPlatform/nengrong/UploadDocument/".$title;
         if (file_exists($strDirFilename))
         {
             echo '{"code":"1","errmsg":"文件'.$title.'已经存在了哦!"}';
             exit;
         }
         move_uploaded_file($_FILES["files"]["tmp_name"],$strDirFilename);
         $mysql_server_name="localhost"; //数据库服务器名称
         $mysql_username="root"; // 连接数据库用户名
         $mysql_password=""; // 连接数据库密码
         $mysql_database="nengrongweb"; // 数据库的名字
         $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
         $strsql='insert into doc values("","'.$title.'","'.$time.'")';
         $result=mysql_db_query($mysql_database, $strsql, $conn); 
         if ($result)
         {
             echo '{"code":"0","msg":""}';
             
         }
         else
         {
             echo '{"code":"1","errmsg":"mysql err!"}';
         }
    }
    public function docInsert(){
        $this->display();
    }
    public function newsInsert(){
        $this->display();
    }
    public function uploadpic(){
	    if ($_FILES["file"]["size"] < 20000)
	    {
		    if ($_FILES["file"]["error"] > 0)
		    {
			    echo '{"code":"-1","msg":"上传图片失败!"}';
		    }
		    else
		    {
                            $nowtime =  time();
			    if (file_exists("/var/www/enetf/WebContent/EnergyFe/news/" . $nowtime.$_FILES["file"]["name"]))
			    {
				    echo $_FILES["file"]["name"] . " already exists. ";
			    }
			    else
			    {
				    move_uploaded_file($_FILES["file"]["tmp_name"],"/var/www/enetf/WebContent/EnergyFe/news/" . $nowtime.$_FILES["file"]["name"]);
                                    echo '{"code":"0","msg":"/EnergyFe/news/.$nowtime.$_FILES["file"]["name"]."}';
			    }
		    }
	    }
	    else
	    {
		    echo '{"code":"-1","msg":"图片太大啦!"}';
	    }
    }
}
