<?php
/**
 * 会员管理
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/30
 * Time: 12:36
 */


if($op == 'display'){
    $pindex = max(1,intval($_GPC['page']));
    $psize = 20;

    $conditions = 'uniacid=:uniacid';
    $params[':uniacid'] = $uniacid;

    if($_GPC['keyword']){
        $conditions .= ' AND (nickname LIKE :keyword OR realname LIKE :keywrod)';
        $params[':keyword'] = "'%" . $_GPC['keyword'] . "%'";
    }

    $total = pdo_fetchcolumn('SELECT COUNT(uid) FROM ' . tablename($this->table_mc_members) . ' WHERE ' . $conditions, $params);
    $pager = pagination($total, $pindex, $psize);

    $order = 'uid DESC';
    $limit = ($pindex - 1) * $psize . ',' . $psize;

    $list = pdo_fetchall('SELECT * FROM ' . tablename($this->table_mc_members) . ' WHERE ' . $conditions . ' ORDER BY ' . $order . ' LIMIT ' . $limit, $params);
    foreach($list as $key => $item){
        if(empty($item['avatar'])){
            $avatar = MODULE_URL."template/mobile/images/default_avatar.jpg";
        }else{
            $inc = strstr($member['avatar'], "http://") || strstr($member['avatar'], "https://");
            $avatar = $inc ? $member['avatar'] : $_W['attachurl'].$member['avatar'];
        }

        $list[$key]['avatar'] = $avatar;
    }

    include $this->template('web/member');
} elseif($op == 'recharge'){
    $uid = intval($_GPC['uid']);
    $type = $_GPC['type'];

    $name = $type == 'credit1' ? '积分' : '余额';

    $member = pdo_fetch('SELECT * FROM ' . tablename($this->table_mc_members) . ' WHERE uid=:id', array(':id' => $uid));
    if(checksubmit('submit')){
        $val = $_GPC['credit'];
        if(empty($_GPC['remark'])){
            if($val >= 0){
               $remark = '系统后台：添加' . $val . $name;
            }else{
                $remark = '系统后台：减少' . $val . $name;
            }
        }else{
            $remark = $_GPC['remark'];
        }

        $updata[$type] = $member[$type] + $val;
        if(pdo_update($this->table_mc_members, $updata, array('uid' => $uid))){
            $data['uid'] = $uid;
            $data['uniacid'] = $uniacid;
            $data['credittype'] = $type;
            $data['num'] = $val;
            $data['operator'] = 1;
            $data['module'] = system;
            $data['clerk_id'] = $val < 0 ? 0 : 1;
            $data['clerk_type'] = $val < 0 ? 1 : 2;
            $data['createtime'] = time();
            $data['remark'] = $remark;
            $data['real_uniacid'] = $uniacid;

            pdo_insert('mc_credits_record', $data);

            message('操作会员' . $name . '成功！','','success');
        }
    }

    include $this->template('web/member');
} elseif($op == 'detail'){
    $uid = intval($_GPC['uid']);

    $member = pdo_fetch('SELECT * FROM ' . tablename($this->table_mc_members) . ' WHERE uid=:id', array(':id' => $uid));

    if(checksubmit('submit')){

    }

    include $this->template('web/member');
} elseif($op == 'log'){
    $uid = intval($_GPC['uid']);
    $type = $_GPC['type'];

    $name = $type == 'credit1' ? '积分' : '余额';

    $pindex = max(1,intval($_GPC['page']));
    $psize = 20;

    $conditions = 'uniacid=:uniacid AND uid=:uid AND credittype=:type';
    $params = array(':uniacid' => $uniacid, ':uid' => $uid, ':type' => $type);

    $total = pdo_fetchcolumn('SELECT COUNT(id) FROM ' . tablename('mc_credits_record') . ' WHERE ' . $conditions, $params);
    $pager = pagination($total, $pindex, $psize);

    $order = 'id DESC';
    $limit = ($pindex - 1) * $psize . ',' . $psize;
    $list = pdo_fetchall('SELECT * FROM ' . tablename('mc_credits_record') . ' WHERE ' . $conditions . ' ORDER BY ' . $order . ' LIMIT ' . $limit, $params);

    include $this->template('web/member');
}