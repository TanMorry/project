<?php
//$menumark = 'index';
switch($act) {
    //#####################@ 设计提交页面 @#####################//
    default:
        if (isset($_p_pesubmit)) {
            var_dump($_FILES);
            $sql_des['canvas_id'] =$_p_canvas;
            $sql_des['brand_pic_id'] =$_p_ip;
            $sql_des['status'] = 0;
            $sql_des['author_id'] = $_SESSION['designer_id'];
            $sql_des['up_time'] = time();
            $sql_des['name'] = $_p_name;
            $sql_des['intro'] = $_p_intro;
            if ($_FILES['design_img']['size']) {
                pe_lead('include/class/upload.class.php');
                $upload = new upload($_FILES['design_img'], 'data/design/', array('filename'=>$_s_designer_id.time()));
                $sql_des['pic_addr'] = $upload->filehost;
//                $sqlset = "when 'web_qrcode' then '{$upload->filehost}'";
            }
            //var_dump($sql_des);die;
            if($db->pe_insert('design', pe_dbhold($sql_des))){
                pe_success('上传成功！');
            }else{
                die(mysql_error());
                pe_error('上传失败');
            }

        }
        $canvas_info = $db->pe_selectall('canvas','','canvas_id,canvas_model');
        $ip_list = $db->pe_selectall('brand','','brand_id,brand_name');
        include(pe_tpl('index.html'));
        break;
}
?>