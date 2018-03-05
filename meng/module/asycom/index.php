<?php
header('Content-type:text/json');     

switch ($act) {
	case 'index':
        $result = array();
        $callback = "indexComplete";
	    if($_REQUEST["pid"]){
            //从商品详情页跳回定制页
	        $sql = "SELECT p.brand_id,pd.* from ".dbpre.'product p left join '.dbpre.'product_dz pd on p.product_id = pd.pid where p.product_id='.$_REQUEST["pid"];
            $dz_pic_info = $db->sql_selectall($sql);
            $result['brand_id'] = $dz_pic_info[0]['brand_id'];
            $result["curpic"] = $dz_pic_info[0]['ip_img_ads'];
            $result['dz_info'] = $dz_pic_info[0];
//            echo "<pre>";
//            print_r($dz_pic_info);die;
        }else{
            if($_REQUEST["brand_pic_id"]){
                //从IP列表跳到定制页
                $brand_pic_id = $_REQUEST["brand_pic_id"];
                $brandList = $db->pe_selectall('brand_pic', array('brand_pic_id'=>$brand_pic_id), 'brand_pic_addr,brand_pic_bid', array(1));
//            print_r($brandList);die;
                $result["curpic"] = $brandList[0]['brand_pic_addr'];
                $result['brand_id'] = $brandList[0]['brand_pic_bid'];
            }else{
                //直接从在线定制页进入
                $result['brand_id'] = 4;//默认图写死品牌
            }

        }
		$category_indexlist = array();
		$ipname = $_REQUEST["ipLabel"];
		$category_id = $_REQUEST["category_id"];
//		$brand_pic_id = $_REQUEST["brand_pic_id"];
        foreach((array)$cache_category_arr[0] as $k=>$v)
		{
			$v['models'] = $db->pe_selectall('canvas', array('category_id'=>category_cidarr($v['category_id'])), 'canvas_logo,canvas_model,canvas_mask', array(3));
			$category_indexlist[] = $v;
		}
		$result["category"] = $category_indexlist;
		$result["curid"] = $category_id;
		$result["curname"] = $ipname;
        $sql = "select canvas_model,canvas_id,canvas_mask from ".dbpre."canvas c left join ".dbpre."category g on c.category_id = g.category_id where g.category_name = '{$ipname}'";
        if(!empty($category_id))
        {
            $sql = "select canvas_model,canvas_id,canvas_mask from ".dbpre."c   anvas where category_id = $category_id";
        }
		$result["canvasList"] = $db->sql_selectall($sql);
//		$result["curpic"] = $db->pe_selectall('brand_pic', array('brand_pic_id'=>$brand_pic_id), 'brand_pic_addr', array(1))[0]['brand_pic_addr'];
		$brand_list = array();
		foreach((array)$cache_brand as $k=>$v)
		{
			array_push($brand_list,$v);
		}
		$result["brand"] = $brand_list;
//        var_dump($result);die;
		echo $callback.'('.json_encode($result).')';
		break;
	case 'ipselect':
		$result = array();
		$callback = "ipSelect";
		$page = isset($_REQUEST["p"])?$_REQUEST["p"]:1;
		$ipname = $_REQUEST["ipLabel"];
		//echo $ipname;
        $num = 20;
		$result['piclist'] = $db->pe_selectall('brand_pic', array('brand_pic_bname'=>$ipname), 'brand_pic_id,brand_pic_addr,brand_pic_bid', array($num,$page));
		$result['page'] = $page;
        $table = dbpre.'brand_pic';
        $result['countNum'] = $db->sql_num("SELECT count(1) from `{$table}` WHERE brand_pic_bname='{$ipname}'");
        $result['countpage'] = ceil($result['countNum']/$num);
		//var_dump($result);
		echo $callback.'('.json_encode($result).')';
		break;
	case 'createImage':
		$result = array();
		$callback = "createImage";
        if (!pe_login('user')) {
            echo $callback.'('.json_encode(array('result'=>false, 'show'=>'请先登录...')).')';
            die();
        }
		$drawImage = $_REQUEST["di"];
		$baseImage = $_REQUEST["bi"];
		$maskImage = $_REQUEST["mi"];
		$imageRotation = $_REQUEST["imageRotation"];
		$dix = $_REQUEST["x"];
		$diy = $_REQUEST["y"];
        $dscalex = $_REQUEST["dscalex"];
        $dscaley = $_REQUEST["dscaley"];
        $basex = $_REQUEST["basex"];
        $basey = $_REQUEST["basey"];
        $fontSize = $_REQUEST["fontsize"];
        $fontColor = $_REQUEST["fontColor"];
        $fontText = $_REQUEST["fontText"];
        $rotation = intval($_REQUEST["rotation"]);
        $fontx = $_REQUEST["fontx"];
        $fonty = $_REQUEST["fonty"];
        $category_id = $_REQUEST['category_id'];
        $path_1 = $_SERVER['DOCUMENT_ROOT'].$drawImage;
        $path_2 = $_SERVER['DOCUMENT_ROOT'].$maskImage;
        $path_3 = $_SERVER['DOCUMENT_ROOT'].$baseImage;

        $image_1 = new imagick($path_1);
        $image_2 = new imagick($path_2);
        $image_3 = new imagick($path_3);
        //将人物和装备图片分别取到两个画布中
        if(($dscalex*1) != 1){
            //获取原图的宽高
            $image_1_width = $image_1->getImageWidth();
            $image_1_height = $image_1->getImageHeight();
            //根据缩放比例获取宽高
            $new_image_1_width = $image_1_width*$dscalex;
            $new_image_1_height = $image_1_height*$dscaley;
/*        var_dump(['dscalex'=>$dscalex,'dscaley'=>$dscaley,'width'=>$image_1_width,'height'=>$image_1_height,'new_width'=>$image_1_width,'new_height'=>$new_image_1_height]);die;*/
        $image_1->scaleImage($new_image_1_width, $new_image_1_height, true);
        }
        $image_1->rotateImage(new ImagickPixel(), $imageRotation);
        //将人物图像与base图片合并
        $image_3->compositeImage($image_1,imagick::COMPOSITE_MULTIPLY,$dix,$diy);

        //实例化一个绘画对象，绘制文本信息嵌入图片中
        $draw = new ImagickDraw();
        //设置文本字体，要求ttf或者ttc字体，可以绝对或者相对路径
        $draw->setFont('love.ttf');
        //设置字号
        $draw->setFontSize($fontSize);
        //文字填充颜色
        $draw->setFillColor($fontColor);
        $image_3->annotateImage($draw,$fontx,$fonty+$fontSize,$rotation,$fontText);
        //将镂空的图片覆盖在上面
        $image_3->compositeImage($image_2,imagick::COMPOSITE_ATOP,0,0);
        $image_3->thumbnailImage(500,500, true, true);
//        $image_3->cropImage(600, 600,90,70);
        $image_name = md5($drawImage.$imageRotation.$baseImage.$maskImage.$dix.$diy.$fontx.$fonty.$rotation.$dscalex.$dscaley.$fontColor);
        $image_3->writeImage($_SERVER['DOCUMENT_ROOT'].'/data/upload/product/'.$image_name.'.jpg');
        $result['result'] = true;
        $result['image'] = "data/upload/product/".$image_name.".jpg";
		echo $callback.'('.json_encode($result).')';
		break;
	default:
		break;
}


?>