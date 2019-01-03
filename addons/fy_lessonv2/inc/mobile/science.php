<?php
/**
 * 科普
 * Created by PhpStorm.
 * User: appleimac
 * Date: 18/12/25
 * Time: 下午3:25
 */

checkauth();

/* 标题 */
$title = '科普视频';

$uid = $_W['member']['uid'];

/* 科普列表 */
$pindex = max(1,intval($_GPC['page']));
$psize = 10;

$list = pdo_fetchall('SELECT * FROM ' . tablename($this->table_lesson_science) . ' WHERE uniacid=:uniacid ORDER BY addtime DESC,displayorder DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':uniacid' => $_W['uniacid']));

if($op == 'display'){
    include $this->template('science');
}elseif ($op == 'ajaxgetlist'){
    echo json_encode($list);
    exit;
}elseif($op == 'watch'){
    $id = intval($_GPC['id']);

    $section = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_science) . ' WHERE id=:id', array(':id' => $id));

    if(empty($section)){
        message("该科普视频不存在或已被删除！", "", "error");
    }

    $title = $section['title'];

    /**
     * 视频课程格式
     * @sectiontype 1.视频章节 2.图文章节 3.音频课程 4、外链章节
     * @savetype	0.其他存储 1.七牛存储 2.内嵌播放代码模式 3.腾讯云存储
     */
    if(in_array($section['sectiontype'], array('1','3'))){
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
            $systemType = $this->checkSystenType();
        }
        if($section['savetype']==1){
            $qiniu = unserialize($setting['qiniu']);
            if($qiniu['https']==1){
                $section['videourl'] = str_replace("http://", "https://", $section['videourl']);
            }
            $section['videourl'] = $this->privateDownloadUrl($qiniu['access_key'],$qiniu['secret_key'],$section['videourl']);

        }elseif($section['savetype']==3){
            $qcloud		 = unserialize($setting['qcloud']);
            if($qcloud['https']==1){
                $section['videourl'] = str_replace("http://", "https://", $section['videourl']);
            }
            $section['videourl'] = $this->tencentDownloadUrl($qcloud, $section['videourl']);

        }elseif($section['savetype']==4){
            $aliyun = unserialize($setting['aliyun']);
            $aliyunVod = new AliyunVod($aliyun['region_id'],$aliyun['access_key_id'],$aliyun['access_key_secret']);

            $file = pdo_get($this->table_aliyun_upload, array('uniacid'=>$uniacid,'videoid'=>$section['videourl']), array('name'));
            $suffix = substr(strrchr($file['name'], '.'), 1);
            $audio = strtolower($suffix)=='mp3' ? true : false;

            try {
                $response = $aliyunVod->getVideoPlayAuth($section['videourl']);
                $playAuth = $response->PlayAuth;
            } catch (Exception $e) {
                message("播放失败，错误原因:".$e->getMessage(), "", "error");
            }
        }elseif($section['savetype']==5){
            $qcloudvod = unserialize($setting['qcloudvod']);
            $newqcloudVod = new QcloudVod($qcloudvod['secret_id'], $qcloudvod['secret_key']);
            try {
                $exper = '';
                if($section['is_free'] && $section['test_time']){
                    $exper = $section['test_time'];
                }
                $qcloudVodRes = $newqcloudVod->getPlaySign($qcloudvod['safety_key'], $qcloudvod['appid'], $section['videourl'], $exper);
            } catch (Exception $e) {
                message("播放失败，错误原因:".$e->getMessage(), "", "error");
            }
        }
    }

    if($section['sectiontype']==4){
        header("Location:".$section['videourl']);
    }

    include $this->template('sciencewatch');
}