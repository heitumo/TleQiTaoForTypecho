<?php
include '../../../config.inc.php';
require_once 'libs/ispay/lib/Ispay.class.php';

$db = Typecho_Db::get();
$prefix = $db->getPrefix();
$options = Typecho_Widget::widget('Widget_Options');
$option=$options->plugin('TleQiTao');
$plug_url = $options->pluginUrl;

$type = isset($_POST['payChannel']) ? addslashes($_POST['payChannel']) : '';
$price = isset($_POST['Money']) ? addslashes($_POST['Money']) : '';
$attachData = isset($_POST['attachData']) ? addslashes($_POST['attachData']) : '';
$returnurl = isset($_POST['returnurl']) ? addslashes($_POST['returnurl']) : '';

$Ispay = new ispayService($option->tleqitaoispayid, $option->tleqitaoispaykey);
date_default_timezone_set('Asia/Shanghai');
$Request=array();
$Request['payId'] = $option->tleqitaoispayid;
$Request['payChannel'] = $type;
$Request['Subject'] = "乞讨";
$Request['Money'] = $price*100;
$Request['orderNumber'] = date("YmdHis") . rand(100000, 999999);
$Request['attachData'] = $returnurl;
$Request['Notify_url'] = $plug_url."/TleQiTao/notify_url.php";
$Request['Return_url'] = $plug_url."/TleQiTao/return_url.php";
$Request['Sign'] = $Ispay -> Sign($Request);

if($Request['orderNumber']==''||$Request['payChannel']==''||$Request['Money']==''){
	header("location:http://127.0.0.1");
	exit;
}

$data = array(
	'orderNumber'   =>  $Request['orderNumber'],
	'payChannel'   =>  $Request['payChannel'],
	'Money'=>$Request['Money']/100,
	'attachData'     =>  $attachData,
	'status'=>'n',
	'instime'=>date('Y-m-d H:i:s',Typecho_Date::time())
);
$insert = $db->insert('table.tleqitao_item')->rows($data);
$insertId = $db->query($insert);

switch($Request['payChannel']){
	case "alipay":
	case "wxpay":
	case "qqpay":
	case "bank_pc":
		echo '
			<script src="http://apps.bdimg.com/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
			<link rel="alternate icon" type="image/png" href="http://www.tongleer.com/wp-content/themes/D8/img/favicon.png">
			<form id="orderform" method="post" action="https://pay.ispay.cn/core/api/request/pay/">
				<input type="hidden" name="payChannel" value="'.$Request['payChannel'].'" />
				<input type="hidden" name="payId" value="'.$Request['payId'].'" />
				<input type="hidden" name="Subject" value="'.$Request['Subject'].'">
				<input type="hidden" name="attachData" value="'.$Request['attachData'].'">
				<input type="hidden" name="Money" value="'.$Request['Money'].'">
				<input type="hidden" name="orderNumber" value="'.$Request['orderNumber'].'">
				<input type="hidden" name="Notify_url" value="'.$Request['Notify_url'].'">
				<input type="hidden" name="Return_url" value="'.$Request['Return_url'].'">
				<input type="hidden" name="Sign" value="'.$Request['Sign'].'">
			</form>
			<script>
				$(function() {
					$("#orderform").submit();
				});
			</script>
		';
		break;
}
?>