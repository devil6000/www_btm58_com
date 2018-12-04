<?php
/**
 * 课程海报
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */
 
checkauth();

$title = "课程海报";
$uid = $_W['member']['uid'];
$lessonid = intval($_GPC['lessonid']);/* 课程id */

$member = pdo_fetch("SELECT a.*,b.avatar,b.nickname AS mc_nickname FROM " .tablename($this->table_member). " a LEFT JOIN ".tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.uid=:uid", array(':uniacid'=>$uniacid,':uid'=>$uid));

if(empty($member['avatar'])){
	$avatar = MODULE_URL."template/mobile/images/default_avatar.jpg";
}else{
	$inc = strstr($member['avatar'], "http://") || strstr($member['avatar'], "https://");
	$avatar = $inc ? $member['avatar'] : $_W['attachurl'].$member['avatar'];
}

$lesson = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_parent). " WHERE uniacid=:uniacid AND id=:id AND status!=:status LIMIT 1", array(':uniacid'=>$uniacid, ':id'=>$lessonid, ':status'=>0));

if(empty($lesson)){
	message("该课程已下架，您可以看看其他课程~", "", "error");
}

/* 海报配置参数 */
$poster = json_decode($lesson['poster_config'], true);
if(!empty($poster['nickname_fontcolor'])){
	$font_color = $this->hexTorgb($poster['nickname_fontcolor']);
}else{
	$font_color['r'] = $font_color['g'] = $font_color['b'] = 0;
}
$nickname_fontsize = intval($poster['nickname_fontsize']) ? intval($poster['nickname_fontsize']) : '18';

/* 检查目录是否存在 */
$dirpath = "../attachment/images/{$uniacid}/fy_lessonv2/";
$this->checkdir($dirpath);
$dirpath .="poster/";
$this->checkdir($dirpath);

if($_GPC['op']=='delete'){
	$qrcodeImages = $dirpath."lesson_{$lessonid}_uid_{$uid}_ok.png";
	unlink($qrcodeImages);
	header("Location:".$this->createMobileUrl('lessonqrcode', array('lessonid'=>$lessonid)));
}

$imagepath = $dirpath."lesson_{$lessonid}_uid_{$uid}_ok.png";
if(!file_exists($imagepath) || $comsetting['qrcode_cache']==0){
	set_time_limit(60); 
	ignore_user_abort(true);
	include(IA_ROOT."/framework/library/qrcode/phpqrcode.php");

	if(empty($poster['images'])){
		/* $bgimg = "../addons/fy_lessonv2/template/mobile/images/lesson_posterbg.jpg"; */
	}else{
		$bgimg = $dirpath."lesson_{$lessonid}_posterbg.jpg";
		if(!file_exists($bgimg)){
			$this->saveImage($_W['attachurl'].$poster['images'], $dirpath."lesson_{$lessonid}_posterbg.", '');
		}	
	}
	
	/* 二维码图片 */
	$errorCorrectionLevel = 'L';  /* 纠错级别：L、M、Q、H */
	$matrixPointSize = 4;  /* 点的大小：1到10 */
	$qrcode = $dirpath."lesson_{$lessonid}_uid_{$uid}.png"; /* 生成的文件名 */
	$lessonUrl = $_W['siteroot'] .'app/'. $this->createMobileUrl('lesson', array('id'=>$lessonid,'uid'=>$uid));

	QRcode::png($lessonUrl, $qrcode, $errorCorrectionLevel, $matrixPointSize, 2);
	$savefield = $this->img_water_mark($bgimg, $qrcode, $dirpath, "lesson_{$lessonid}_uid_{$uid}.png", intval($poster['qrcode_left']), intval($poster['qrcode_top']));

	/* 合成头像 */
	if($poster['avatar_left'] && $poster['avatar_top']){
		if(empty($member['avatar'])){
			$avatar = MODULE_URL."template/mobile/images/default_avatar.jpg";
		}else{
			$inc = strstr($member['avatar'], "http://") || strstr($member['avatar'], "https://");
			$avatar = $inc ? $member['avatar'] : $_W['attachurl'].$member['avatar'];
		}
		
		$suffix = $this->saveImage($avatar, $dirpath."avatar_{$uid}.", 'avatar');

		$avatar_size = filesize($dirpath."avatar_{$uid}.".$suffix);
		if($avatar_size==0){
			message("获取头像失败，请在个人中心点击头像更新", $this->createMobileUrl('self'), "error");
		}

		if($suffix=='png'){
			$im = imagecreatefrompng($dirpath."avatar_{$uid}.".$suffix);
		}elseif($suffix=='jpeg' || $suffix=='jpg'){
			$im = imagecreatefromjpeg($dirpath."avatar_{$uid}.".$suffix);
		}else{
			$im = imagecreatefromjpeg(MODULE_URL."template/mobile/images/default_avatar.jpg");
		}
		imagejpeg($im, $dirpath."avatar_{$uid}.jpg");
		imagedestroy($im);
		
		$this->resize($dirpath."avatar_{$uid}.jpg", $dirpath."avatar_{$uid}.jpg", "100", "100", "100");
		$savefield = $this->img_water_mark($savefield, $dirpath."avatar_{$uid}.jpg", $dirpath, "lesson_{$lessonid}_uid_{$uid}.png", $poster['avatar_left'], $poster['avatar_top']);
	}

	$info = getimagesize($savefield);
	/* 通过编号获取图像类型 */
	$type = image_type_to_extension($info[2],false);
	/* 图片复制到内存 */
	$image = imagecreatefromjpeg($savefield);

	/* 合成昵称 */
	if($poster['nickname_left'] && $poster['nickname_top']){
		/* 设置字体的路径 */
		$font = MODULE_ROOT."/template/mobile/ttf/SourceHanSansK-Bold.ttf";
		/* 设置字体颜色和透明度 */
		$color = imagecolorallocatealpha($image, $font_color['r'], $font_color['g'], $font_color['b'], 0);
		/* 写入文字 */
		$fun = $dirpath."lesson_{$lessonid}_uid_{$uid}.png";
		imagettftext($image, $nickname_fontsize, 0, $poster['nickname_left'], $poster['nickname_top'], $color, $font, $member['mc_nickname']);
	}

	/* 保存图片 */
	$fun = "image".$type;
	$okfield = $dirpath."lesson_{$lessonid}_uid_{$uid}_ok.png";
	$fun($image, $okfield);  
	/*销毁图片*/  
	imagedestroy($image);

	/* 删除多余文件 */
	unlink($dirpath."lesson_{$lessonid}_uid_{$uid}.png");
	unlink($dirpath."avatar_{$uid}.".$suffix);
}

$imagepath .= "?v=".time();



include $this->template('lessonqrcode');

?>