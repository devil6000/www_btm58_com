<?php
defined('IN_IA') or exit('Access Denied');

class Fy_lessonv2ModuleProcessor extends WeModuleProcessor {
	public $table_article = 'fy_lesson_article';
    public $table_blacklist = 'fy_lesson_blacklist';
    public $table_cashlog = 'fy_lesson_cashlog';
    public $table_category = 'fy_lesson_category';
	public $table_lesson_collect = 'fy_lesson_collect';
	public $table_commission_level = 'fy_lesson_commission_level';
	public $table_commission_log = 'fy_lesson_commission_log';
	public $table_commission_setting = 'fy_lesson_commission_setting';
	public $table_coupon = 'fy_lesson_coupon';
    public $table_evaluate = 'fy_lesson_evaluate';
    public $table_lesson_history = 'fy_lesson_history';
	public $table_inform = 'fy_lesson_inform';
	public $table_inform_fans = 'fy_lesson_inform_fans';
	public $table_market = 'fy_lesson_market';
	public $table_mcoupon = 'fy_lesson_mcoupon';
    public $table_member = 'fy_lesson_member';
	public $table_member_coupon = 'fy_lesson_member_coupon';
    public $table_member_order = 'fy_lesson_member_order';
	public $table_member_vip = 'fy_lesson_member_vip';
    public $table_order = 'fy_lesson_order';
    public $table_lesson_parent = 'fy_lesson_parent';
    public $table_playrecord = 'fy_lesson_playrecord';
	public $table_qcloud_upload = 'fy_lesson_qcloud_upload';
	public $table_qiniu_upload = 'fy_lesson_qiniu_upload';
    public $table_recommend = 'fy_lesson_recommend';
    public $table_setting = 'fy_lesson_setting';
    public $table_lesson_son = 'fy_lesson_son';
	public $table_lesson_spec = 'fy_lesson_spec';
	public $table_static = 'fy_lesson_static';
    public $table_syslog = 'fy_lesson_syslog';
    public $table_teacher = 'fy_lesson_teacher';
    public $table_teacher_income = 'fy_lesson_teacher_income';
	public $table_tplmessage = 'fy_lesson_tplmessage';
    public $table_vip_level = 'fy_lesson_vip_level';
    public $table_vipcard = 'fy_lesson_vipcard';
	public $table_mc_members = 'mc_members';
	public $table_fans = 'mc_mapping_fans';
    public $table_core_cache = 'core_cache';
	public $table_core_paylog = 'core_paylog';
    public $table_users = 'users';
    public $table_lesson_praxis = 'fy_lesson_praxis';


    public function respond() {
		global $_W;

        $content = trim($this->message['content']);
		if(in_array($content, array('推广海报', '二维码海报', '专属海报'))){
			$setting = $this->readCache(1);
			$comsetting = $this->readCache(2);
			if($comsetting['is_sale']==0){
				return $this->respText('抱歉，系统未开启该功能.');
			}

			$uniacid = $_W['uniacid'];
			$uid = $_W['member']['uid'];
			$member = pdo_fetch("SELECT a.*,b.avatar,b.nickname AS mc_nickname FROM " .tablename($this->table_member). " a LEFT JOIN ".tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uniacid=:uniacid AND a.uid=:uid", array(':uniacid'=>$uniacid,':uid'=>$uid));

			$memberVip = pdo_fetchall("SELECT * FROM " .tablename($this->table_member_vip). " WHERE uid=:uid AND validity>:validity", array(':uid'=>$uid,':validity'=>time()));
			if($comsetting['sale_rank']==2 && empty($memberVip)){
				return $this->respText('抱歉，您不是VIP会员，无法访问该功能.');
			}

			if($member['status']!=1){
				return $this->respText('抱歉，您的分销身份未激活.');
			}

			/* 海报配置参数 */
			$poster = json_decode($setting['poster_config'], true);
			$qrcode_left = intval($poster['qrcode_left'])>0 ? $poster['qrcode_left'] : 473;
			$qrcode_top = intval($poster['qrcode_top'])>0 ? $poster['qrcode_top'] : 733;
			$avatar_left = intval($poster['avatar_left'])>0 ? $poster['avatar_left'] : 22;
			$avatar_top = intval($poster['avatar_top'])>0 ? $poster['avatar_top'] : 698;
			$nickname_left = intval($poster['nickname_left'])>0 ? $poster['nickname_left'] : 210;
			$nickname_top = intval($poster['nickname_top'])>0 ? $poster['nickname_top'] : 728;
			$nickname_fontsize = intval($poster['nickname_fontsize'])>0 ? $poster['nickname_fontsize'] : 24;
			if(!empty($poster['nickname_fontcolor'])){
				$font_color = $this->hexTorgb($poster['nickname_fontcolor']);
			}else{
				$font_color['r'] = $font_color['g'] = $font_color['b'] = 255;
			}

			$this->checkdir(IA_ROOT."/attachment/images/fy_lessonv2/");
			$this->checkdir(IA_ROOT."/attachment/images/fy_lessonv2/{$uniacid}/");
			$dirpath = IA_ROOT."/attachment/images/fy_lessonv2/{$uniacid}/";

			$imagepath = $dirpath.$uniacid."_".$uid."_ok.png";
			if(!file_exists($imagepath) || $comsetting['qrcode_cache']==0 || filectime($imagepath) > time()+7*86400){
				set_time_limit(80); 
				ignore_user_abort(true); 
				include(IA_ROOT."/framework/library/qrcode/phpqrcode.php");

				/* 背景图片 */
				if(empty($setting['posterbg'])){
					$bgimg = MODULE_URL."template/mobile/images/posterbg.jpg";
				}else{
					$bgimg = IA_ROOT."/attachment/images/fy_lessonv2/{$uniacid}/posterbg.jpg";
					if(!file_exists($bgimg)){
						$this->saveImage($_W['attachurl'].$setting['posterbg'], $dirpath."posterbg.", '');
					}
				}

				/* 二维码图片 */
				if($setting['poster_type']==2){
					$barcode = array (
						'expire_seconds' => 2592000,
						'action_name' => QR_STR_SCENE,
						'action_info' => array (
							'scene' => array (
								'scene_id' => "uid_{$uid}",
							),
						),
					);
					$account_api = WeAccount::create();
					$res = $account_api->barCodeCreateDisposable($barcode);

					if(empty($res['ticket'])){
						return $this->respText('获取二维码失败，错误信息:'.$res['errcode'].'，'.$res['errmsg']);
					}
					$qrcodeurl = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$res['ticket'];
					$qrcode_suffix = $this->saveImage($qrcodeurl, $dirpath.$uniacid."_".$uid."_qrcode.");
					$this->resize($dirpath.$uniacid."_".$uid."_qrcode.jpg", $dirpath.$uniacid."_".$uid."_qrcode.jpg", "150", "150", "100");
					$qrcode = $dirpath.$uniacid."_".$uid."_qrcode.".$qrcode_suffix;
				}else{
					$errorCorrectionLevel = 'L';  /* 纠错级别：L、M、Q、H */
					$matrixPointSize = 4;  /* 点的大小：1到10 */
					
					$infourl = $_W['siteroot'] .'app/'. $this->createMobileUrl('index', array('uid'=>$uid));
					$qrcode = $dirpath.$uniacid."_".$uid."_qrcode.png"; /* 生成的文件名 */
					QRcode::png($infourl, $qrcode, $errorCorrectionLevel, $matrixPointSize, 2);
				}
				
				/* 合成二维码 */
				$savefield = $this->img_water_mark($bgimg, $qrcode, $dirpath, $uniacid."_".$uid.".png", $qrcode_left, $qrcode_top);
				
				/* 合成头像 */
				if($poster['avatar_show']==1){
					if(empty($member['avatar'])){
						$avatar = MODULE_URL."template/mobile/images/default_avatar.jpg";
					}else{
						$inc = strstr($member['avatar'], "http://") || strstr($member['avatar'], "https://");
						$avatar = $inc ? $member['avatar'] : $_W['attachurl'].$member['avatar'];
					}
					
					$suffix = $this->saveImage($avatar, $dirpath.$uniacid."_".$uid."_avatar.");

					$avatar_size = filesize($dirpath.$uniacid."_".$uid."_avatar.".$suffix);
					if($avatar_size==0){
						return $this->respText('获取头像失败，请在个人中心点击头像更新.');
					}

					if($suffix=='png'){
						$im = imagecreatefrompng($dirpath.$uniacid."_".$uid."_avatar.".$suffix);
					}elseif($suffix=='jpeg' || $suffix=='jpg'){
						$im = imagecreatefromjpeg($dirpath.$uniacid."_".$uid."_avatar.".$suffix);
					}else{
						$im = imagecreatefromjpeg(MODULE_URL."template/mobile/images/default_avatar.jpg");
					}
					imagejpeg($im, $dirpath.$uniacid."_".$uid."_avatar.jpg");
					imagedestroy($im);
					
					$this->resize($dirpath.$uniacid."_".$uid."_avatar.jpg", $dirpath.$uniacid."_".$uid."_avatar.jpg", "100", "100", "100");
					$savefield = $this->img_water_mark($savefield, $dirpath.$uniacid."_".$uid."_avatar.jpg", $dirpath, $uniacid."_".$uid."_ok.png", $avatar_left, $avatar_top);
				}

				$info = getimagesize($savefield);
				/* 通过编号获取图像类型 */
				$type = image_type_to_extension($info[2],false);
				/* 图片复制到内存 */
				$image = imagecreatefromjpeg($savefield);
				
				/* 合成昵称 */
				if($poster['nickname_show']==1){
					/* 设置字体的路径 */
					$font = MODULE_ROOT."/template/mobile/ttf/SourceHanSansK-Bold.ttf";
					/* 设置字体颜色和透明度 */
					$color = imagecolorallocatealpha($image, $font_color['r'], $font_color['g'], $font_color['b'], 0);
					/* 写入文字 */
					$fun = $dirpath.$uniacid."_".$uid.".png";
					imagettftext($image, $nickname_fontsize, 0, $nickname_left, $nickname_top, $color, $font, $member['mc_nickname']);
				}

				/* 保存图片 */
				$fun = "image".$type;
				$okfield = $dirpath.$uniacid."_".$uid."_ok.png";
				$fun($image, $okfield);  
				/*销毁图片*/  
				imagedestroy($image);
				
				/* 删除多余文件 */
				unlink($dirpath.$uniacid."_".$uid.".png");
				unlink($dirpath.$uniacid."_".$uid."_qrcode.png");
				unlink($dirpath.$uniacid."_".$uid."_qrcode.jpg");
				unlink($dirpath.$uniacid."_".$uid."_avatar.jpg");
				if($suffix!='jpg'){
					unlink($dirpath.$uniacid."_".$uid."_avatar.".$suffix);
				}
			}


			/* 发送海报给粉丝 */				
			$acc = WeAccount::create($_W['acid']);
			$imagepath = $dirpath.$uniacid."_".$uid."_ok.png";
			$data = $acc->uploadMedia($imagepath);

			$send = array();
			$send['touser']  = $_W['openid'];
			$send['fromuser']  = $_W['openid'];
			$send['createtime']  = time();
			$send['msgtype'] = 'image';
			$send['image'] = array('media_id' => $data['media_id']);
			if($_W['acid']) {
				$result = $acc->sendCustomNotice($send);
			}

			if(!is_error($result)){
				return $this->respText('您的专属海报已成功生成并下发，可转发给好友.');
			}else{
				return $this->respText('抱歉，生成海报失败，请稍候重试.');
			}
		}
    }



	/* 读取缓存
	  * $type 读取缓存类型 1.全局设置表 2.分销设置表
	  */
	private function readCache($type){
		global $_W;

		if($type==1){
			$setting = cache_load('fy_lessonv2_setting_'.$_W['uniacid']);
			if(empty($setting)){
				$setting = $this->getSetting();
				cache_write('fy_lessonv2_setting_'.$_W['uniacid'], $setting);
			}
			return $setting;

		}elseif($type==2){
			$comsetting = cache_load('fy_lessonv2_commission_setting_'.$_W['uniacid']);
			if(empty($comsetting)){
				$comsetting = $this->getComsetting();
				cache_write('fy_lessonv2_commission_setting_'.$_W['uniacid'], $comsetting);
			}
			return $comsetting;
		}
	}
	
	/* 获取基本设置参数 */
	private function getSetting(){
		global $_W;
		return pdo_fetch("SELECT * FROM " .tablename($this->table_setting). " WHERE uniacid=:uniacid", array(':uniacid'=>$_W['uniacid']));
	}

	/* 获取分销设置参数 */
	private function getComsetting(){
		global $_W;
		return pdo_fetch("SELECT * FROM " .tablename($this->table_commission_setting). " WHERE uniacid=:uniacid", array(':uniacid'=>$_W['uniacid']));
	}

	/* 十六进制颜色转为RGB */
	function hexTorgb($hexColor) {
		$color = str_replace('#', '', $hexColor);
		if (strlen($color) > 3) {
			$rgb = array(
				'r' => hexdec(substr($color, 0, 2)),
				'g' => hexdec(substr($color, 2, 2)),
				'b' => hexdec(substr($color, 4, 2))
			);
		} else {
			$color = $hexColor;
			$r = substr($color, 0, 1) . substr($color, 0, 1);
			$g = substr($color, 1, 1) . substr($color, 1, 1);
			$b = substr($color, 2, 1) . substr($color, 2, 1);
			$rgb = array(
				'r' => hexdec($r),
				'g' => hexdec($g),
				'b' => hexdec($b)
			);
		}
		return $rgb;
	}

	/**
     * 图片加水印（适用于png/jpg/gif格式）
     * 
     * @author flynetcn
     *
     * @param $srcImg 原图片
     * @param $waterImg 水印图片
     * @param $savepath 保存路径
     * @param $savename 保存名字
     * @param $positon 水印位置 
     * 
     * @return 成功 -- 加水印后的新图片地址
     *          失败 -- -1:原文件不存在, -2:水印图片不存在, -3:原文件图像对象建立失败
     *          -4:水印文件图像对象建立失败 -5:加水印后的新图片保存失败
     */
    public function img_water_mark($srcImg, $waterImg, $savepath = null, $savename = null, $x, $y, $alpha = 100) {
        $temp = pathinfo($srcImg);
        $name = $temp['basename'];
        $path = $temp['dirname'];
        $exte = $temp['extension'];
        $savename = $savename ? $savename : $name;
        $savepath = $savepath ? $savepath : $path;
        $savefile = $savepath . '/' . $savename;
        $srcinfo = @getimagesize($srcImg);
        if (!$srcinfo) {
            return -1; /* 原文件不存在 */
        }
        $waterinfo = @getimagesize($waterImg);
        if (!$waterinfo) {
            return -2; /* 水印图片不存在 */
        }
        $srcImgObj = $this->image_create_from_ext($srcImg);
        if (!$srcImgObj) {
            return -3; /* 原文件图像对象建立失败 */
        }
        $waterImgObj = $this->image_create_from_ext($waterImg);
        if (!$waterImgObj) {
            return -4; /* 水印文件图像对象建立失败 */
        }

        imagecopymerge($srcImgObj, $waterImgObj, $x, $y, 0, 0, $waterinfo[0], $waterinfo[1], $alpha);
        switch ($srcinfo[2]) {
            case 1: imagegif($srcImgObj, $savefile);
                break;
            case 2: imagejpeg($srcImgObj, $savefile);
                break;
            case 3: imagepng($srcImgObj, $savefile);
                break;
            default: return -5; /* 保存失败 */
        }
        imagedestroy($srcImgObj);
        imagedestroy($waterImgObj);
        return $savefile;
    }

	public function image_create_from_ext($imgfile) {
        $info = getimagesize($imgfile);
        $im = null;
        switch ($info[2]) {
            case 1: $im = imagecreatefromgif($imgfile);
                break;
            case 2: $im = imagecreatefromjpeg($imgfile);
                break;
            case 3: $im = imagecreatefrompng($imgfile);
                break;
        }
        return $im;
    }

	/* 获取远程图片保存到本地 */
    public function saveImage($url, $image_path) {
		$header = array(     
			'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0',
			'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
			'Accept-Encoding: gzip, deflate'
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		$data = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		//把URL格式的图片转成base64_encode格式
		if ($code == 200) {
			$imgBase64Code = "data:image/jpeg;base64," . base64_encode($data);
		}
		$img_content = $imgBase64Code;
		if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_content, $result)){
			$type = $result[2]; //图片类型
			if($type=='jpeg'){
				$type = 'jpg';
			}
			$new_file = $image_path.$type;   
			if (!file_put_contents($new_file, base64_decode(str_replace($result[1], '', $img_content)))){
				echo '获取头像失败，请在个人中心点击头像更新';
			}else{
				return $type;
			}
		}
    }

	/**
     * 等比缩放 
     * @param unknown_type $srcImage   源图片路径 
     * @param unknown_type $toFile     目标图片路径 
     * @param unknown_type $maxWidth   最大宽 
     * @param unknown_type $maxHeight  最大高 
     * @param unknown_type $imgQuality 图片质量 
     * @return unknown 
     */
    function resize($srcImage, $toFile, $maxWidth = 1024, $maxHeight = 1024, $imgQuality = 100) {

        list($width, $height, $type, $attr) = getimagesize($srcImage);
        if ($width < $maxWidth || $height < $maxHeight)
            return;
        switch ($type) {
            case 1: $img = imagecreatefromgif($srcImage);
                break;
            case 2: $img = imagecreatefromjpeg($srcImage);
                break;
            case 3: $img = imagecreatefrompng($srcImage);
                break;
        }
        $scale = min($maxWidth / $width, $maxHeight / $height); //求出绽放比例 

        if ($scale < 1) {
            $newWidth = floor($scale * $width);
            $newHeight = floor($scale * $height);
            $newImg = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($newImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            $newName = "";
            $toFile = preg_replace("/(.gif|.jpg|.jpeg|.png)/i", "", $toFile);

            switch ($type) {
                case 1: if (imagegif($newImg, "$toFile$newName.gif", $imgQuality))
                        return "$newName.gif";
                    break;
                case 2: if (imagejpeg($newImg, "$toFile$newName.jpg", $imgQuality))
                        return "$newName.jpg";
                    break;
                case 3: if (imagepng($newImg, "$toFile$newName.png", $imgQuality))
                        return "$newName.png";
                    break;
                default: if (imagejpeg($newImg, "$toFile$newName.jpg", $imgQuality))
                        return "$newName.jpg";
                    break;
            }
            imagedestroy($newImg);
        }
        imagedestroy($img);
        return false;
    }

	/**
     *  检查目录是否存在
     */
    private function checkdir($path) {
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
    }

}