<?php
/**
 * 讨论管理
 * Created by PhpStorm.
 * User: appleimac
 * Date: 18/12/10
 * Time: 3:46
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
        message('课程不存在或已被删除！', '', 'error');
    }

    $section = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_son) . ' WHERE uniacid=:uniacid AND id=:id LIMIT 1', array(':uniacid' => $uniacid, ':id' => $cid));
    if(empty($section)){
        message('章节不存在或已删除', '', 'error');
    }

    $discuss = pdo_fetch('SELECT * FROM ' . tablename($this->table_discuss) . ' WHERE uniacid=:uniacid AND parentid=:pid AND chapterid=:cid AND id=:id LIMIT 1', array(':pid' => $pid, ':cid' => $cid, ':id' => $id));

    if(checksubmit('submit')){
        $data = array('uniacid' => $uniacid, 'parentid' => $pid, 'chapterid' => $cid, 'content' => $_GPC['content'], 'title' => $_GPC['title'], 'addtime' => time());
        if(empty($id)){
            pdo_insert($this->table_discuss, $data);
        }else{
            unset($data['uniacid'], $data['addtime']);
            pdo_update($this->table_discuss, $data, array('id' => $id, 'uniacid' => $uniacid));
        }
        message('编辑课程名称：' . $lesson['bookname'] . '章节名称：' . $section['title'] . '的话题讨论内容');
    }

}

include $this->template('web/discuss');