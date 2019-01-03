<?php
/**
 * 科普管理
 * Created by PhpStorm.
 * User: appleimac
 * Date: 18/12/25
 * Time: 下午2:08
 */
if(empty($setting)){
    message("请先配置相关参数！", $this->createWebUrl('setting'), "error");
}

if($op == 'display'){
    if (checksubmit('submit')) { /* 排序 */
        if (is_array($_GPC['lessonorder'])) {
            foreach ($_GPC['lessonorder'] as $pid => $val) {
                $data = array('displayorder' => intval($_GPC['lessonorder'][$pid]));
                pdo_update($this->table_lesson_parent, $data, array('id' => $pid));
            }
        }
        if (is_array($_GPC['sectionorder'])) {
            foreach ($_GPC['sectionorder'] as $sid => $val) {
                $data = array('displayorder' => intval($_GPC['sectionorder'][$sid]));
                pdo_update($this->table_lesson_son, $data, array('id' => $sid));
            }
        }
        message('操作成功!', referer, 'success');
    }

    $pindex = max(1, intval($_GPC['page']));
    $psize = 25;

    $condition = " uniacid=:uniacid";
    $params[':uniacid'] = $uniacid;

    $section_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_lesson_science). " WHERE {$condition} ORDER BY displayorder DESC,id ASC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " .tablename($this->table_lesson_science). " WHERE {$condition}", $params);
    $pager = pagination($total, $pindex, $psize);
}elseif ($op == 'postscience'){
    $typeStatus = new TypeStatus();
    $saveList = $typeStatus->sectionSaveType();

    $id = intval($_GPC['id']);
    if($id){
        $science = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_science) . ' WHERE uniacid=:uniacid AND id=:id', array(':uniacid' => $uniacid, ':id' => $id));
        if(empty($science)){
            message('该科普视频不存在或已删除', '', 'error');
        }
        $sectionUrl = $_W['siteroot'].'app/'.str_replace("./", "", $this->createMobileUrl('science', array('id'=>$id)));
    }
    
    /* 存储方式 */
    $qiniu = unserialize($setting['qiniu']);
    if(substr($qiniu['url'],0,7)!='http://'){
        $qiniu['url'] = "http://".$qiniu['url']."/";
    }

    $qcloud = unserialize($setting['qcloud']);
    if(substr($qcloud['url'],0,7)!='http://'){
        $qcloud['url'] = "http://".$qcloud['url']."/";
    }

    if(checksubmit('submit')){
        $data = array();
        $data['uniacid']		= $uniacid;
        $data['title']			= $_GPC['title'];
        $data['images']			= trim($_GPC['images']);
        $data['sectiontype']	= intval($_GPC['sectiontype']);
        $data['savetype']		= trim($_GPC['savetype']);
        $data['videourl']		= trim($_GPC['videourl']);
        $data['content']		= $_GPC['content'];
        $data['displayorder']	= intval($_GPC['displayorder']);
        $data['addtime']		= time();

        if(empty($data['title'])){
            message("请填写章节名称");
        }
        if(empty($data['sectiontype'])){
            message("请选择章节类型");
        }
        if($data['sectiontype']==1 && empty($data['videourl'])){
            message("请填写章节视频URL");
        }
        if($data['sectiontype']==3 && in_array($data['savetype'], array('2','4','5'))){
            message("音频章节存储方式只能其他存储、七牛云存储和腾讯云存储");
        }
        if($data['savetype']==2){//内嵌代码存储方式保留内容的空格
            $data['videourl'] = $_GPC['videourl'];
        }
        if($data['sectiontype']==4){//外链章节的url保存在videourl里
            $data['videourl'] = $_GPC['linkurl'];
        }

        if(empty($id)){
            pdo_insert($this->table_lesson_science, $data);
            $id = pdo_insertid();
            if($id){
                $this->addSysLog($_W['uid'], $_W['username'], 1, "科普视频", "新增ID:{$id}的视频");
            }

            message("添加科普视频成功！", $this->createWebUrl('science'), "success");
        }else{
            unset($data['addtime']);
            $res = pdo_update($this->table_lesson_science, $data, array('uniacid'=>$uniacid, 'id'=>$id));
            if($res){
                $this->addSysLog($_W['uid'], $_W['username'], 3, "科普视频", "编辑ID:{$id}的视频");
            }

            $refurl = $_GPC['refurl']?$_GPC['refurl']:$this->createWebUrl('science');
            message("编辑科普视频成功！", $refurl, "success");
        }
    }
}elseif($op == 'delete'){
    $id = intval($_GPC['id']);
    $science = pdo_fetch('SELECT * FROM ' . tablename($this->table_lesson_science) . ' WHERE uniacid=:uniacid AND id=:id', array(':uniacid' => $uniacid, ':id' => $id));
    if(empty($science)){
        message('该科普视频不存在或已删除', '', 'error');
    }
    pdo_delete($this->table_lesson_science, array('uniacid'=>$uniacid, 'id'=>$id));
    $this->addSysLog($_W['uid'], $_W['username'], 2, "科普视频", "删除ID:{$id}的科普");
    message("删除科普成功！", referer, "success");
}

include $this->template('web/science');
