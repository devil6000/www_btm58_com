<?php
/**
 * 腾讯云点播
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */

$qcloudvod = unserialize($setting['qcloudvod']);
$newqcloudVod = new QcloudVod($qcloudvod['secret_id'], $qcloudvod['secret_key']);
$qcloud_array = array('upQcloudVod','preview');

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

	$list = pdo_fetchall("SELECT * FROM " .tablename($this->table_qcloudvod_upload). " WHERE {$condition} ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
	foreach($list as $key=>$value){

		$list[$key]['suffix'] = strtolower($tmp[count($tmp)-1]);
	}

	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_qcloudvod_upload). " WHERE {$condition}", $params);
	$pager = pagination($total, $pindex, $psize);

}elseif($op=='getUploadInfo'){
	$signature = $newqcloudVod->getUploadSign();

	$data = array(
		'signature' => $signature
	);

	echo json_encode($data);
	exit();

}elseif($op=='saveVideo'){

	$data = array(
		'uniacid'	=> $uniacid,
		'uid'		=> '',
		'teacherid'	=> '',
		'name'		=> trim($_GPC['filename']),
		'videoid'	=> trim($_GPC['videoid']),
		'videourl'	=> trim($_GPC['videourl']),
		'size'		=> $_GPC['size'],
		'addtime'	=> time(),
	);
	$res = pdo_insert($this->table_qcloudvod_upload, $data);
	if($res){
		echo '写入数据库成功';
	}else{
		pdo_debug();
	}
	exit();

}elseif($op=='delVideo'){
	$id = intval($_GPC['id']);
	$file = pdo_fetch("SELECT * FROM " .tablename($this->table_qcloudvod_upload). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid, ':id'=>$id));
	if(empty($file)){
		message("该文件不存在!", "", "error");
	}

	$paramArray = array(
		'Action' => 'DeleteVodFile',
		'fileId' => $file['videoid'],
		'priority' => 0,
	);

	$deleteUrl = $newqcloudVod->generateUrl($paramArray, 'GET', 'vod.api.qcloud.com', '/v2/index.php');
	$response = $this->http_request($deleteUrl);
	$res = json_decode($response, true);
	if($res['code']=='0'){
		if(pdo_delete($this->table_qcloudvod_upload, array('id'=>$id))){
			message("删除成功", $this->createWebUrl('qcloudvod'), "success");
		}else{
			message("删除失败，请稍后重试", "", "error");
		}
	}else{
		message('删除失败，腾讯云点播返回信息：'.$res['message']);
	}

}elseif($op=='preview'){
	$id = intval($_GPC['id']);
	$file = pdo_fetch("SELECT * FROM " .tablename($this->table_qcloudvod_upload). " WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$uniacid, ':id'=>$id));
	if(empty($file)){
		message("该文件不存在!", "", "error");
	}

	try {
		$res = $newqcloudVod->getPlaySign($qcloudvod['safety_key'], $qcloudvod['appid'], $file['videoid'], $exper);
	} catch (Exception $e) {
		message("播放失败，错误原因:".$e->getMessage(), "", "error");
	}
	
}


include $this->template('web/qcloudVod');