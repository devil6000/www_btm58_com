<?php
/**
 * 章节分类
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/30
 * Time: 18:19
 */

if ($operation == 'display'){

    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;

    $condition = " uniacid=:uniacid ";
    $params[':uniacid'] = $uniacid;

    $list = pdo_fetchall('SELECT * FROM ' . tablename('fy_lesson_reclassify') . ' WHERE ' . $condition . ' LIMIT '  . ($pindex - 1) * $psize . ',' . $psize, $params);

    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(fy_lesson_reclassify) . " WHERE {$condition}", $params);
    $pager = pagination($total, $pindex, $psize);
} elseif($op == 'post'){
    $id = intval($_GPC['id']);
    $reclassify = pdo_fetch('SELECT * FROM ' . tablename('fy_lesson_reclassify') . ' WHERE id=:id', array(':id' => $id));
    if(checksubmit('submit')){
        $data = array(
            'uniacid' => $uniacid,
            'name' => $_GPC['name'],
            'addtime' => time()
        );

        if(empty($id)){
            pdo_insert('fy_lesson_reclassify', $data);
        }else{
            unset($data['addtime']);
            pdo_update('fy_lesson_reclassify', $data, array('id' => $id));
        }

        message('编辑或添加章节分类成功', '', 'success');
    }

} elseif($op == 'delete'){
    $id = intval($_GPC['id']);
    $reclassify = pdo_fetch('SELECT * FROM ' . tablename('fy_lesson_reclassify') . ' WHERE id=:id', array(':id' => $id));
    if(empty($reclassify)){
        message('该章节分类不存在或已删除.','', 'error');
    }

    pdo_delete('fy_lesson_reclassify', array('id' => $id));

    pdo_update($this->table_lesson_son, array('rid' => 0), array('rid' => $id));

    message('删除章节分类成功！', $this->createWebUrl('reclassify'), 'success');
}

include $this->template('web/reclassify');