<?php
/**
 * 讨论
 * Created by PhpStorm.
 * User: appleimac
 * Date: 18/12/14
 * Time: 上午9:52
 */

if($op == 'display'){

    checkauth();

    $uid = intval($_W['member']['uid']);
    $pid = intval($_GPC['pid']);
    $cid = intval($_GPC['cid']);

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

    $discuss = pdo_fetch('SELECT * FROM ' . tablename($this->table_discuss) . ' WHERE uniacid=:uniacid AND parentid=:pid AND chapterid=:cid AND status=1', array(':uniacid' => $uniacid, ':pid' => $pid, ':cid' => $cid));
    if(empty($discuss)){
        message("该章节的讨论话题不存在或已删除，您可以看看其他话题", "", "error");
    }

}elseif($op == 'uploader'){
    load()->func('file');
    $field = $_GPC["file"];
    if (!empty($_FILES[$field]["name"])){
        if ($_FILES[$field]["error"] != 0){
            $result["message"] = "上传失败，请重试！";
            print(json_encode($result));
            exit;
        }
        $path = "../attachment/images/fy_lessonv2/{$uniacid}";
        if (!is_dir($path)) {
            mkdirs($path);
        }
        $_W["uploadsetting"]                        = array();
        $_W["uploadsetting"]["image"]["folder"]     = $path;
        $_W["uploadsetting"]["image"]["extentions"] = $_W["config"]["upload"]["image"]["extentions"];
        $_W["uploadsetting"]["image"]["limit"]      = $_W["config"]["upload"]["image"]["limit"];
        $file                                       = file_upload($_FILES[$field], "image");
        if (is_error($file)) {
            $result["message"] = $file["message"];
            print(json_encode($result));
            exit;
        }
        if (function_exists("file_remote_upload")) {
            $remote = file_remote_upload($file["path"]);
            if (is_error($remote)) {
                $result["message"] = $remote["message"];
                print(json_encode($result));
                exit;
            }
        }
        $result["status"]   = "success";
        $result["url"]      = $file["url"];
        $result["error"]    = 0;
        $result["filename"] = $file["path"];
        $result["url"]      = '/attachment/' . $result['filename'];
        pdo_insert("core_attachment", array(
            "uniacid" => $uniacid,
            "uid" => $_W["member"]["uid"],
            "filename" => $_FILES[$field]["name"],
            "attachment" => $result["filename"],
            "type" => 1,
            "createtime" => time(),
            "group_id" => -1
        ));
        print(json_encode($result));
        exit;
    }else{
        $result["message"] = "请选择要上传的图片！";
        print(json_encode($result));
        exit;
    }
}elseif ($op == 'remove'){
    load()->func('file');
    $file = $_GPC["file"];
    file_delete($file);
    echo 1;
    exit;
}elseif ($op == 'save'){
    $uid = intval($_W['member']['uid']);
    $cid = intval($_GPC['cid']);
    $comments = $_GPC['comments'];

    $insert_data = array(
        'uniacid' => $uniacid,
        'uid' => $uid,
        'content' => $comments['content'],
        'addtime' => time(),
        'imgs' => serialize($comments['images'])
    );

    pdo_insert($this->table_discuss_content,$insert_data);
    echo 1;
    exit;
}


include $this->template('discuss');