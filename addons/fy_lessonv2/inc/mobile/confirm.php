<?php
/**
 * 确认下单
 * ============================================================================
 * 版权所有 2015-2018 微课堂团队，并保留所有权利。
 * 网站地址: https://wx.haoshu888.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！不允许对程序代码以任何形式任何目的的再发布，作者将保留
 * 追究法律责任的权力和最终解释权。
 */

checkauth();

$title = "确认下单";
$lesson_id = intval($_GPC['id']); /* 课程id */
$lesson_spec = intval($_GPC['spec_id']);    /* 课程属性ID */
//$lessonurl = $this->createMobileUrl('lesson', array('id'=>$lesson_id));
$uid = $_W['member']['uid'];

$this->check_black_list();/* 检查黑名单操作 */

/* 普通课程检查用户是否完善个人信息 */
$member = pdo_fetch("SELECT a.*,b.avatar,b.mobile,b.realname,b.msn,b.occupation,b.company,b.graduateschool,b.grade,b.address,b.credit1 FROM " .tablename($this->table_member). " a LEFT JOIN " .tablename($this->table_mc_members). " b ON a.uid=b.uid WHERE a.uid=:uid", array(':uid'=>$uid));

$lesson_list = array();

$lesson_price = 0;
$order_price = 0;
$lessons = array();

$single = array();

$meanwhile_id = intval($_GPC['meanwhileid']);
if($meanwhile_id){
    $order_price = pdo_fetchcolumn('SELECT price FROM ' . tablename($this->table_lesson_meanwhile) . ' WHERE id=:id', array(':id' => $meanwhile_id));
    $meanwhile_list = pdo_fetchall('SELECT * FROM ' . tablename($this->table_meanwhile_lesson) . ' WHERE meanwhileid=:id', array(':id' => $meanwhile_id));
    foreach($meanwhile_list as $meanwhile){
        $lesson_list[] = array('id' => $meanwhile['lesson_id'],'spec_id' => $meanwhile['spec_id']);
    }
}else{
    $lesson_list[] = array('id' => $lesson_id, 'spec_id' => intval($_GPC['spec_id']));
}

foreach($lesson_list as $item){
    $id = $item['id'];

    $conditions = 'status>=:status AND uid=:uid AND is_delete=:is_delete AND lesson_ids LIKE :ids';
    $params = array(':status'=>0,':uid'=>$uid,':is_delete'=>0,':ids' => '%,' . $id . ',%');
    $lessonorder = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE " . $conditions . " LIMIT 1", $params);
    //$lessonorder = pdo_fetch("SELECT * FROM " . tablename($this->table_order) . " WHERE lessonid=:lessonid AND status>=:status AND uid=:uid AND is_delete=:is_delete LIMIT 1", array(':lessonid'=>$id, ':status'=>0,':uid'=>$uid,':is_delete'=>0));
    if (!empty($lessonorder)) {
        if ($lessonorder['status'] == 1) {
            if ($lessonorder['validity']==0) {
                message("您已购买该课程，无需重复购买！", $this->createMobileUrl('lesson', array('id' => $id)), "error");
            }else{
                if($lessonorder['validity'] > time()){
                    message("您已购买该课程，无需重复购买！", $this->createMobileUrl('lesson', array('id' => $id)), "error");
                }
            }
        } elseif ($lessonorder['status'] == 0) {
            message("您还有该课程未付款订单！", $this->createMobileUrl('mylesson', array('status' => 0)), "warning");
        }
    }

    /* 课程规格 */
    $spec_id = $item['spec_id'];
    if (empty($spec_id)) {
        message("课程规格不存在！", "", "error");
    }
    $spec = pdo_fetch("SELECT * FROM " .tablename($this->table_lesson_spec). " WHERE uniacid=:uniacid AND spec_id=:spec_id", array(':uniacid'=>$uniacid,':spec_id'=>$spec_id));
    if(empty($spec)){
        message("课程规格不存在！", "", "error");
    }

    $lesson = pdo_fetch("SELECT a.*,b.teacher,b.teacherphoto FROM " .tablename($this->table_lesson_parent). " a LEFT JOIN " .tablename($this->table_teacher). " b ON a.teacherid=b.id WHERE a.uniacid=:uniacid AND a.id=:id AND a.status=1 LIMIT 1", array(':uniacid'=>$uniacid, ':id'=>$id));
    if (empty($lesson)) {
        message("课程不存在或已下架！", "", "error");
    }

    if(!empty($lesson['teacherphoto'])){
        $lesson['teacherphoto'] = $_W['attachurl'].$lesson['teacherphoto'];
        //$teacherphoto = $_W['attachurl'].$lesson['teacherphoto'];
    }else{
        $lesson['teacherphoto'] = MODULE_URL."template/mobile/images/default_avatar.jpg";
        //$teacherphoto = MODULE_URL."template/mobile/images/default_avatar.jpg";
    }

    $lesson['lessonurl'] = $this->createMobileUrl('lesson', array('id'=>$id));

    /* 检查是否开启库存 */
    if($setting['stock_config']==1){
        if($lesson['stock'] <=0 ){
            message("该课程已售罄，下次记得早点来哦~", "", "error");
        }
    }

    if ($setting['mustinfo'] && (!$lesson['lesson_type'] || ($lesson['lesson_type'] && $setting['appoint_mustinfo']))) {
        $user_info = json_decode($setting['user_info']);
        $jumpurl = $this->createMobileUrl('writemsg', array('lessonid'=>$id, 'spec_id'=>$spec_id));

        /*
        if(in_array('mobile',$user_info) && empty($member['mobile'])){
            message("请完善您的个人信息", $jumpurl, "warning");
        }
        if(in_array('realname',$user_info) && empty($member['realname'])){
            message("请完善您的个人信息", $jumpurl, "warning");
        }
        if(in_array('msn',$user_info) && empty($member['msn'])){
            message("请完善您的个人信息", $jumpurl, "warning");
        }
        if(in_array('occupation',$user_info) && empty($member['occupation'])){
            message("请完善您的个人信息", $jumpurl, "warning");
        }
        if(in_array('company',$user_info) && empty($member['company'])){
            message("请完善您的个人信息", $jumpurl, "warning");
        }
        if(in_array('graduateschool',$user_info) && empty($member['graduateschool'])){
            message("请完善您的个人信息", $jumpurl, "warning");
        }
        if(in_array('grade',$user_info) && empty($member['grade'])){
            message("请完善您的个人信息", $jumpurl, "warning");
        }
        if(in_array('address',$user_info) && empty($member['address'])){
            message("请完善您的个人信息", $jumpurl, "warning");
        }
        */
    }

    $lesson_price += $spec['spec_price'];   //课程总金额
    $lesson['spec'] = $spec;
    $lessons[] = $lesson;   //保存到课程列表

    if(empty($meanwhile_id)){
        $single = $lesson;
        /*检查积分抵扣开关和课程是否支持积分抵扣*/
        $market = pdo_fetch("SELECT * FROM " .tablename($this->table_market). " WHERE uniacid=:uniacid", array(':uniacid'=>$uniacid));
        if($market['deduct_switch']==1 && $market['deduct_money']>0 && $lesson['deduct_integral']>0 && $member['credit1']>0){
            $deduct_switch = 1;
            $deduct_integral = $lesson['deduct_integral'] >= $member['credit1'] ? $member['credit1'] : $lesson['deduct_integral'];
            $deduct_money = $deduct_integral*$market['deduct_money'];
        }

        /* 检查会员是否享受折扣 */
        $memberVip_list = pdo_fetchall("SELECT * FROM  " .tablename($this->table_member_vip). " WHERE uid=:uid AND validity>:validity", array(':uid'=>$uid,':validity'=>time()));

        $discount = 100; /* 初始折扣为100，即100% */
        if(!empty($memberVip_list)){
            $isVip = true;
            foreach($memberVip_list as $v){
                if($v['discount'] < $discount) {
                    $discount = $v['discount'];
                }
            }
        }

        /* 检查课程是否参加限时活动 */
        $discount_lesson = pdo_fetch("SELECT * FROM " .tablename($this->table_discount_lesson). " WHERE uniacid=:uniacid AND lesson_id=:lesson_id AND starttime<:time AND endtime>:time", array(':uniacid'=>$uniacid,':lesson_id'=>$id,':time'=>time()));
        if(!empty($discount_lesson)){
            $spec['spec_price'] = round($spec['spec_price']*$discount_lesson['discount']*0.01, 2);
        }

        $price = $spec['spec_price'];
        if ($isVip && $discount!=1) { /* 折扣开启 */
            if ($spec['spec_price'] > 0) {
                if ($lesson['isdiscount'] == 1) {/* 课程开启折扣 */
                    if(empty($discount_lesson) || (!empty($discount_lesson) && $discount_lesson['member_discount'])){
                        if ($lesson['vipdiscount'] > 0) {/* 使用课程单独折扣 */
                            $price = round($spec['spec_price'] * $lesson['vipdiscount'] * 0.01, 2);
                        } else {/* 使用VIP等级最低折扣 */
                            $price = round($spec['spec_price'] * $discount * 0.01, 2);
                        }
                    }
                }
            } else {
                $price = 0;
            }
        } else {
            $price = $spec['spec_price']; /* 课程全局关闭折扣 */
        }

        $vipCoupon = $spec['spec_price'] - $price;

        /*判断可用优惠券*/
        if($lesson['support_coupon']==1){
            $coupon_list = pdo_fetchall("SELECT * FROM " .tablename($this->table_member_coupon). " WHERE uid=:uid AND conditions<=:conditions AND validity>=:validity AND status=:status ORDER BY amount DESC,validity ASC", array(':uid'=>$uid, ':conditions'=>$price, ':validity'=>time(), ':status'=>0));
            foreach($coupon_list as $k=>$v){
                $category = pdo_fetch("SELECT name FROM " .tablename($this->table_category). " WHERE id=:id", array(':id'=>$v['category_id']));
                $coupon_list[$k]['category_name'] = $category['name'] ? "仅限[".$category['name']."]分类的课程" : "全部课程分类";
                unset($category);

                if($v['category_id']>0){
                    if($lesson['pid']!=$v['category_id']){
                        unset($coupon_list[$k]);
                    }
                }
            }
        }

        /* 报名课程附加个人信息 */
        $appoint_info = json_decode($lesson['appoint_info'], true);

        /* 报名课程且会员为VIP免费时，报名价格为0 */
        if(!empty($memberVip_list) && $lesson['lesson_type']==1){
            foreach($memberVip_list as $v){
                if(in_array($v['level_id'], json_decode($lesson['vipview']))){
                    $apply_price = $price;
                    $price = 0;
                    break;
                }
            }
        }

        $order_price = $price;
    }
}

print_r("&nbsp;");
include $this->template('confirm');


?>