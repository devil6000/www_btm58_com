<?php
/**
 * 异步上传图片
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */

load()->func('file');


/* 以base64格式上传 */
if($_GPC['type']=='base64'){
	$path = "../attachment/images/{$uniacid}/";
	$this->checkdir($path);
	$path .= date('Y', time())."/";
	$this->checkdir($path);
	$path .= date('m', time())."/";
	$this->checkdir($path);

	if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $_GPC['imageData'], $result)){
		$type = $result[2];
		$new_file = $path.random(30).".{$type}";

		if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $_GPC['imageData'])))){
			$res['path'] = str_replace("../attachment/", "", $new_file);
		}else{
			exit(json_encode('图片数据错误，请重试'));
		}
	}else{
		exit(json_encode('图片数据错误，请重试'));
	}

/* 以图片文件上传 */
}else{
	$res = file_upload($_FILES['uploadFile']);
	/* 按比例压缩图片 */
	$imagePath = ATTACHMENT_ROOT."/".$res['path'];
	$this->resize($imagePath, $imagePath, "400", "400", "80");
}

if (!empty($_W['setting']['remote']['type'])) {
	$remotestatus = file_remote_upload($res['path']);
	if (is_error($remotestatus)) {
		exit(json_encode("远程附件上传失败，请联系管理员检查配置"));
	}
}

exit(json_encode($res));