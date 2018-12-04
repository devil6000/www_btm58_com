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
$id = intval($_GPC['id']);/* 课程id */
$pid = intval($_GPC['pid']); /* 题目ID */

if($uid>0){
    $member = pdo_fetch("SELECT a.*,b.follow,c.avatar,c.nickname FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename($this->table_fans). " b ON a.uid=b.uid LEFT JOIN " .tablename($this->table_mc_members). " c ON a.uid=c.uid WHERE a.uid=:uid", array(':uid'=>$uid));
}
if(empty($member['avatar'])){
    $avatar = MODULE_URL."template/mobile/images/default_avatar.jpg";
}else{
    $inc = strstr($member['avatar'], "http://") || strstr($member['avatar'], "https://");
    $avatar = $inc ? $member['avatar'] : $_W['attachurl'].$member['avatar'];
}

$lesson = pdo_fetch("SELECT a.*,b.teacher,b.qq,b.qqgroup,b.qqgroupLink,b.weixin_qrcode,b.teacherphoto,b.teacherdes FROM " .tablename($this->table_lesson_parent). " a LEFT JOIN " .tablename($this->table_teacher). " b ON a.teacherid=b.id WHERE a.uniacid=:uniacid AND a.id=:id AND a.status!=:status LIMIT 1", array(':uniacid'=>$uniacid, ':id'=>$id, ':status'=>0));
if(empty($lesson)){
    message("该课程已下架，您可以看看其他课程~", "", "error");
}

$praxisList = pdo_fetchall('SELECT * FROM ' . tablename($this->table_lesson_praxis) . ' WHERE parentid=:pid AND chapterid=:cid', array(':pid' => $id, ':cid' => $id));