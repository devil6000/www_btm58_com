<?php
/**
 * 阿里云点播
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */

/* 阿里云点播全局配置 */
include_once MODULE_ROOT."/inc/common/AliyunVod.php";
$aliyun = unserialize($setting['aliyun']);
$aliyunVod = new AliyunVod($aliyun['region_id'],$aliyun['access_key_id'],$aliyun['access_key_secret']);

if($op=='display'){
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;

	$condition = " uniacid=:uniacid AND teacherid=:teacherid ";
	$params[':uniacid'] = $uniacid;
	$params[':teacherid'] = 0;
	if(!empty($_GPC['keyword'])){
		$condition .= " AND name LIKE :name ";
		$params[':name'] = "%".trim($_GPC['keyword'])."%";
	}
	if (!empty($_GPC['time']['start'])) {
		$starttime = strtotime($_GPC['time']['start']);
		$endtime = strtotime($_GPC['time']['end']);
		$endtime = !empty($endtime) ? $endtime + 86399 : 0;
		if (!empty($starttime)) {
			$condition .= " AND addtime>=:starttime ";
			$params[':starttime'] = $starttime;
		}
		if (!empty($endtime)) {
			$condition .= " AND addtime<=:endtime ";
			$params[':endtime'] = $endtime;
		}
	}

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_aliyun_upload). " WHERE {$condition} ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	foreach($list as $key=>$value){

		$list[$key]['suffix'] = strtolower($tmp[count($tmp)-1]);
	}

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_aliyun_upload). " WHERE {$condition}", $params);
	$pager = pagination($total, $pindex, $psize);

}elseif($op=='getUploadInfo'){
	$filename = trim($_GPC['filename']);
	$suffix = substr(strrchr($filename, '.'), 1);
	$title = str_replace(".".$suffix, "", $filename);

	$response = $aliyunVod->create_upload_video($title, $filename);

	echo json_encode($response);
	exit();

}elseif($op=='saveVideo'){

	$data = array(
		'uniacid'	=> $uniacid,
		'uid'		=> '',
		'teacherid'	=> '',
		'name'		=> trim($_GPC['filename']),
		'videoid'	=> trim($_GPC['videoId']),
		'object'	=> trim($_GPC['object']),
		'size'		=> $_GPC['size'],
		'addtime'	=> time(),
	);
	pdo_insert($this->table_aliyun_upload, $data);

}elseif($op=='delVideo'){
	$id = intval($_GPC['id']);
	$file = pdo_fetch("SELECT * FROM " .tablename($this->table_aliyun_upload). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid, ':id'=>$id));
	if(empty($file)){
		message("该文件不存在!", "", "error");
	}

	try {
		pdo_delete($this->table_aliyun_upload, array('id'=>$id));
		$aliyunVod->delete_videos($file['videoid']);

		message("删除成功!", $this->createWebUrl('aliyunvod'), "success");
	} catch (Exception $e) {
		message("删除失败：".print $e->getMessage(), "", "error");
	}

}elseif($op=='preview'){
	$id = intval($_GPC['id']);
	$file = pdo_fetch("SELECT * FROM " .tablename($this->table_aliyun_upload). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid, ':id'=>$id));
	if(empty($file)){
		message("该文件不存在!", "", "error");
	}

	$suffix = substr(strrchr($file['name'], '.'), 1);
	$audio = strtolower($suffix)=='mp3' ? true : false;

	
	try {
		$response = $aliyunVod->getVideoPlayAuth($file['videoid']);
		$playAuth = $response->PlayAuth;
	} catch (Exception $e) {
		message("播放失败，错误原因:".$e->getMessage(), "", "error");
	}
	
}


include $this->template('web/aliyunVod');