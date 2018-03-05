<?php
//$menumark = 'index';
switch($act) {
    case 'getipimg':
        $ipid = $_GET['ipid'];
        $info = $db->pe_selectall('brand_pic',array('brand_pic_bid'=>$ipid),'brand_pic_id,brand_pic_addr');
        //var_dump($info);
        echo (json_encode($info));
        break;

    //#####################@ ？？？？？ @#####################//
    default:
        $canvas_info = $db->pe_selectall('canvas','','canvas_id,canvas_model');
        $ip_list = $db->pe_selectall('brand','','brand_id,brand_name');
        include(pe_tpl('index.html'));
        break;
}
?>