<?php
/**
 * 习题
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4
 * Time: 22:32
 */

checkauth();

if($op == 'display'){
    $uid = $_W['member']['uid'];
    $id = intval($_GPC['id']);/* 题目ID */
    $pid = intval($_GPC['pid']); /* 课程章节ID */
    $cid = intval($_GPC['cid']); /* 章节ID */
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

    $title = $lesson['bookname'] . '测试';

    if($cid > 0){
        $section = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_son) . ' WHERE id=:id AND status=1', array(':id' => $cid));
        if(empty($section)){
            message("该章节已下架，您可以看看其他章节~", "", "error");
        }
        $title = $section['title'] . '测试';
    }

    $count = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename($this->table_lesson_praxis) . ' WHERE parentid=:pid AND uniacid=:uniacid AND chapterid=:cid', array(':pid' => $pid, ':uniacid' => $uniacid, ':cid' => $cid));

    if($page > $count){ $page = $count;}

    if(!empty($id)){
        $praxis = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_praxis) . ' WHERE id=:id AND uniacid=:uniacid ', array(':id' => $id, ':uniacid' => $uniacid));
    }else{
        $praxis = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_praxis) . ' WHERE parentid=:pid AND uniacid=:uniacid AND chapterid=:cid ORDER BY id ASC LIMIT ' . ($page - 1) * $pageSize . ',' . $pageSize, array(':pid' => $pid, ':uniacid' => $uniacid, ':cid' => $cid));
    }

    if(empty($praxis)){
        message('课程没有习题。', "", 'error');
        exit;
    }

    $resubmit = false;
    $prev = false;
    $next = false;

    //判断是否已经测试
    $ceshi = pdo_fetch('SELECT * FROM ' . tablename($this->table_praxis_score) . ' WHERE uid=:uid AND praxisid=:pid AND chapterid=:cid', array(':uid' => $uid, ':pid' => $praxis['id'], ':cid' => $cid));
    if(!empty($ceshi)){
        $resubmit = true;
    }

}elseif ($op == 'ajaxpost'){
    $uid = intval($_W['member']['uid']);
    $id = intval($_GPC['id']);
    $answer = trim($_GPC['answer']);
    $praxis = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_praxis) . ' WHERE id=:id', array(':id' => $id));
    $correct = 0;   //是否正确
    if(strtolower($praxis['correct']) == strtolower($answer)){
        $correct = 1;
    }

    $answers = pdo_fetch('SELECT * FROM ' . tablename($this->table_praxis_score) . ' WHERE praxisid=:pid AND uniacid=:uniacid AND uid=:uid', array(':pid' => $praxis['id'], ':uniacid' => $uniacid, ':uid' => $uid));

    $answer = array(
        'uniacid' => $uniacid,
        'uid' => $uid,
        'parentid' => $praxis['parentid'],
        'chapterid' => $praxis['chapterid'],
        'praxisid' => $praxis['id'],
        'score' => empty($correct) ? 0 : $praxis['score'],
        'correct' => $correct,
        'addtime' => time()
    );

    if(empty($answers)){
        pdo_insert($this->table_praxis_score, $answer);
    }else{
        unset($answer['uniacid'],$answer['addtime']);
        pdo_update($this->table_praxis_score, $answer, array('id' => $answers['id']));
    }

    echo $correct;
    exit;
}elseif ($op == 'score'){
    $title = '测验结果';

    $uid = intval($_W['member']['uid']);
    $pid = intval($_GPC['pid']);
    $cid = intval($_GPC['cid']);

    $lesson = pdo_fetch("SELECT a.*,b.teacher,b.qq,b.qqgroup,b.qqgroupLink,b.weixin_qrcode,b.teacherphoto,b.teacherdes FROM " .tablename($this->table_lesson_parent). " a LEFT JOIN " .tablename($this->table_teacher). " b ON a.teacherid=b.id WHERE a.uniacid=:uniacid AND a.id=:id AND a.status!=:status LIMIT 1", array(':uniacid'=>$uniacid, ':id'=>$pid, ':status'=>0));
    if(empty($lesson)){
        message("该课程已下架，您可以看看其他课程~", "", "error");
    }

    $scoreList = pdo_fetchall('SELECT * FROM ' . tablename($this->table_praxis_score) . ' WHERE parentid=:pid AND chapterid=:cid AND uniacid=:uniacid AND uid=:uid ORDER BY praxisid ASC', array(':pid' => $pid,':uniacid' => $uniacid, ':uid' => $uid, ':cid' => $cid));

    $score = 0;
    foreach ($scoreList as $item){
        $score += $item['score'];
    }
}

if($op == 'score'){
    include $this->template('praxis_score');
}else{
    include $this->template('praxis');
}

/**
 * 视频课程格式
 * @savetype	0.其他存储 1.七牛存储 2.内嵌播放代码模式 3.腾讯云存储
 */
/*
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
*/