<?php
/*
 * 付费阅读同步回调
 */
include '../../../config.inc.php';
require_once 'libs/payjs.php';
date_default_timezone_set('Asia/Shanghai');

$db = Typecho_Db::get();
$options = Typecho_Widget::widget('Widget_Options');
$option=$options->plugin('TleQiTao');

switch($option->tleqitaopaytype){
	case "payjs":
		$out_trade_no = isset($_GET['id']) ? addslashes($_GET['id']) : 0;
		$url = isset($_GET['url']) ? addslashes(base64_decode($_GET['url'])) : "";
		?>
		<!doctype html>
		<html class="no-js fixed-layout">
		<head>
		  <meta charset="utf-8">
		  <meta http-equiv="X-UA-Compatible" content="IE=edge">
		  <title>付款结果</title>
		  <meta name="description" content="">
		  <meta name="keywords" content="">
		  <meta name="viewport" content="width=device-width, initial-scale=1">
		  <meta name="format-detection" content="telephone=no">
		  <meta http-equiv="Cache-Control" content="no-siteapp" />
		  <meta http-equiv="X-UA-Compatible" content="IE=edge">
		  <meta name="renderer" content="webkit">
		  <meta http-equiv="Cache-Control" content="no-siteapp" />
		  <link rel="icon" type="image/png" href="https://wx3.sinaimg.cn/large/005V7SQ5ly1fykchvb7s5j300s00s744.jpg">
		  <meta name="apple-mobile-web-app-title" content="Amaze UI" />
		  <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
		  <script src="https://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
		</head>
		<body style="background:url('https://wx2.sinaimg.cn/large/a15b4afegy1fpp139ax3wj200o00g073.jpg');">
			<div class="container" style="padding-top:20px;">
				<center>
					<div class="alert alert-success">
						等待<span id="countdown" style="color:red;">10</span>秒自动返回页面
						<a href="<?php echo $url;?>">点击返回</a>
					</div>
					<div class="alert alert-success">
						<?=$option->tleqitao_ad_return?$option->tleqitao_ad_return:"广告位";?>
					</div>
				</center>
			</div>
			<script>
			var count = 10; 
			var inl = setInterval (function () {
				count -= 1;
				$("#countdown").html(count);
				if (count <= 0) {
					clearInterval(inl);
					location.href="<?php echo $url;?>";
				}
			}, 1000);
			</script>
		</body>
		</html>
		<?php
		exit;
		break;
}
?>