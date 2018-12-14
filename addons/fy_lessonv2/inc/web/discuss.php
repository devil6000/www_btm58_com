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

    $lesson = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_parent) . ' WHERE uniacid=:uniacid AND id=:id LIMIT 1', array(':uniacid' => $uniacid, ':id' => $pid));
    if(empty($lesson)){
        message('课程不存在或已被删除！', '', 'error');
    }

    $section = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_son) . ' WHERE uniacid=:uniacid AND id=:id LIMIT 1', array(':uniacid' => $uniacid, ':id' => $cid));
    if(empty($section)){
        message('章节不存在或已删除', '', 'error');
    }

    if (checksubmit('submit')) { /* 排序 */
        if (is_array($_GPC['sectionorder'])) {
            foreach ($_GPC['sectionorder'] as $sid => $val) {
                $data = array('displayorder' => intval($_GPC['sectionorder'][$sid]));
                pdo_update($this->table_discuss, $data, array('id' => $sid));
            }
        }

        message('操作成功!', referer, 'success');
    }

    $pindex = max(1, intval($_GPC['page']));
    $psize = 25;

    $condition = " uniacid=:uniacid AND parentid=:parentid AND chapterid=:chapterid";
    $params[':uniacid'] = $uniacid;
    $params[':parentid'] = $pid;
    $params[':chapterid'] = $cid;

    $discussList = pdo_fetchall('SELECT * FROM ' . tablename($this->table_discuss) . ' WHERE ' . $condition . ' ORDER BY displayorder DESC,id ASC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);

    $total = pdo_fetchcolumn("SELECT COUNT(id) FROM " .tablename($this->table_discuss). " WHERE {$condition}", $params);
    $pager = pagination($total, $pindex, $psize);

}elseif ($op == 'postdiscuss'){
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

    $discuss = pdo_fetch('SELECT * FROM ' . tablename($this->table_discuss) . ' WHERE uniacid=:uniacid AND parentid=:pid AND chapterid=:cid AND id=:id LIMIT 1', array(':pid' => $pid, ':cid' => $cid, ':id' => $id, ':uniacid' => $uniacid));

    if(checksubmit('submit')){
        $data = array('uniacid' => $uniacid, 'parentid' => $pid, 'chapterid' => $cid, 'content' => $_GPC['content'], 'videourl' => $_GPC['videourl'], 'title' => $_GPC['title'], 'status' => intval($_GPC['status']), 'addtime' => time(), 'displayorder' => intval($_GPC['']));
        if($data['status'] == 1){
            $tmp = pdo_fetch('SELECT * FROM ' . tablename($this->table_discuss) . ' WHERE uniacid=:uniacid AND parentid=:pid AND chapterid=:cid AND status=1', array(':pid' => $pid, ':cid' => $cid, ':uniacid' => $uniacid));
            if(!empty($tmp)){
                if(empty($id) || $id != $tmp['id']){
                    message('已存在开启的话题，当前话题不能开启，请先关闭开启话题。',referer, 'error');
                }
            }
        }

        if(empty($id)){
            pdo_insert($this->table_discuss, $data);
        }else{
            unset($data['uniacid'], $data['addtime']);
            pdo_update($this->table_discuss, $data, array('id' => $id, 'uniacid' => $uniacid));
        }
        message('新增或编辑课程名称：' . $lesson['bookname'] . '章节名称：' . $section['title'] . '的话题讨论内容成功!',referer,'success');
    }
}

include $this->template('web/discuss');