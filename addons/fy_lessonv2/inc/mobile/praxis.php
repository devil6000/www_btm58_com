<?php
/**
 * 习题
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4
 * Time: 22:32
 */

$userAgent = $this->checkUserAgent();
$login_visit = json_decode($setting['login_visit']);
if((!empty($login_visit) && in_array('praxis', $login_visit)) || $userAgent){
    checkauth();
}

$uid = $_W['member']['uid'];
$id = intval($_GPC['id']);/* 题目ID */
$pid = intval($_GPC['pid']); /* 课程章节ID */
$page = max(1,intval($_GPC['page'])); //页码
$pageSize = 1;

if($uid>0){
    $member = pdo_fetch("SELECT a.*,b.follow,c.avatar,c.nickname FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename($this->table_fans). " b ON a.uid=b.uid LEFT JOIN " .tablename($this->table_mc_members). " c ON a.uid=c.uid WHERE a.uid=:uid", array(':uid'=>$uid));
}
if(empty($member['avatar'])){
    $avatar = MODULE_URL."template/mobile/images/default_avatar.jpg";
}else{
    $inc = strstr($member['avatar'], "http://") || strstr($member['avatar'], "https://");
    $avatar = $inc ? $member['avatar'] : $_W['attachurl'].$member['avatar'];
}

$lesson = pdo_fetch("SELECT a.*,b.teacher,b.qq,b.qqgroup,b.qqgroupLink,b.weixin_qrcode,b.teacherphoto,b.teacherdes FROM " .tablename($this->table_lesson_parent). " a LEFT JOIN " .tablename($this->table_teacher). " b ON a.teacherid=b.id WHERE a.uniacid=:uniacid AND a.id=:id AND a.status!=:status LIMIT 1", array(':uniacid'=>$uniacid, ':id'=>$pid, ':status'=>0));
if(empty($lesson)){
    message("该课程已下架，您可以看看其他课程~", "", "error");
}

$praxis = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_praxis) . ' WHERE parentid=:pid AND uniacid=:uniacid ORDER BY id ASC LIMIT ' . ($page - 1) * $pageSize . ',' . $pageSize, array(':pid' => $pid, ':uniacid' => $uniacid));

if($praxis){
    /**
     * 视频课程格式
     * @savetype	0.其他存储 1.七牛存储 2.内嵌播放代码模式 3.腾讯云存储
     */
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
        $systemType = $this->checkSystenType();
    }

    if($praxis['voideurl']){
        if($praxis['voidtype']==1){
            $qiniu = unserialize($setting['qiniu']);
            if($qiniu['https']==1){
                $praxis['videourl'] = str_replace("http://", "https://", $praxis['videourl']);
            }
            $praxis['videourl'] = $this->privateDownloadUrl($qiniu['access_key'],$qiniu['secret_key'],$praxis['videourl']);

        }elseif($praxis['voidtype']==3){
            $qcloud		 = unserialize($setting['qcloud']);
            if($qcloud['https']==1){
                $praxis['videourl'] = str_replace("http://", "https://", $praxis['videourl']);
            }
            $praxis['videourl'] = $this->tencentDownloadUrl($qcloud, $praxis['videourl']);

        }elseif($praxis['voidtype']==4){
            $aliyun = unserialize($setting['aliyun']);
            $aliyunVod = new AliyunVod($aliyun['region_id'],$aliyun['access_key_id'],$aliyun['access_key_secret']);

            $file = pdo_get($this->table_aliyun_upload, array('uniacid'=>$uniacid,'videoid'=>$praxis['videourl']), array('name'));
            $suffix = substr(strrchr($file['name'], '.'), 1);
            $audio = strtolower($suffix)=='mp3' ? true : false;

            try {
                $response = $aliyunVod->getVideoPlayAuth($praxis['videourl']);
                $playAuth = $response->PlayAuth;
            } catch (Exception $e) {
                message("播放失败，错误原因:".$e->getMessage(), "", "error");
            }
        }elseif($praxis['voidtype']==5){
            $qcloudvod = unserialize($setting['qcloudvod']);
            $newqcloudVod = new QcloudVod($qcloudvod['secret_id'], $qcloudvod['secret_key']);
            try {
                $exper = '';
                $qcloudVodRes = $newqcloudVod->getPlaySign($qcloudvod['safety_key'], $qcloudvod['appid'], $praxis['videourl'], $exper);
            } catch (Exception $e) {
                message("播放失败，错误原因:".$e->getMessage(), "", "error");
            }
        }
    }

    if($praxis['audiourl']){
        if($praxis['audiotype']==1){
            $qiniu = unserialize($setting['qiniu']);
            if($qiniu['https']==1){
                $praxis['audiourl'] = str_replace("http://", "https://", $praxis['audiourl']);
            }
            $praxis['audiourl'] = $this->privateDownloadUrl($qiniu['access_key'],$qiniu['secret_key'],$praxis['audiourl']);

        }elseif($praxis['audiotype']==3){
            $qcloud		 = unserialize($setting['qcloud']);
            if($qcloud['https']==1){
                $praxis['audiourl'] = str_replace("http://", "https://", $praxis['audiourl']);
            }
            $praxis['audiourl'] = $this->tencentDownloadUrl($qcloud, $praxis['audiourl']);

        }
    }
}

include $this->template('praxis');