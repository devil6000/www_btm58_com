<?php
/**
 * 讨论管理
 * Created by PhpStorm.
 * User: appleimac
 * Date: 18/12/10
 * Time: 下午3:46
 */
if(empty($setting)){
    message("请先配置相关参数！", $this->createWebUrl('setting'), "error");
}

if($op == 'display'){
    $pid = intval($_GPC['pid']);
    $cid = intval($_GPC['cid']);
    $id = intval($_GPC['id']);

    $lesson = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_parent) . ' WHERE uniacid=:uniacid AND id=:id LIMIT 1', array(':uniacid' => $uniacid, ':id' => $pid));
    if(empty($lesson)){
        message('课程已删除或不存在！', '', 'error');
    }

    $section = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_son) . ' WHERE uniacid=:uniacid AND id=:id LIMIT 1', array(':uniacid' => $uniacid, ':id' => $cid));
    if(empty($section)){
        message('章节已删除或不存在！', '', 'error');
    }

    $discuss = pdo_fetch('SELECT * FROM ' . tablename($this->table_discuss) . ' WHERE uniacid=:uniacid AND parentid=:pid AND chapterid=:cid AND id=:id LIMIT 1', array(':pid' => $pid, ':cid' => $cid, ':id' => $id));

    if(checksubmit('submit')){

    }

}

include $this->template('web/discuss');