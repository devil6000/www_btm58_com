/**
习题
 */
create table `ims_fy_lesson_praxis`(
  `id` int(11) not null auto_increment,
  `uniacid` int(11) not null,
  `parentid` int(11) not null,
  `chapterid` int(11) not null comment '章节ID',
  `displayorder` int(4) null default 0 comment '排序',
  `voidtype` tinyint(1) null default 0 comment '视频保存方式',
  `voideurl` text comment '视频地址',
  `audiotype` tinyint(1) null default 0 comment '音频保存方式',
  `audiourl` text comment '音频地址',
  `subject` text comment '题目',
  `answer_a` text comment '答案A',
  `answer_b` text comment '答案B',
  `answer_c` text comment '答案C',
  `answer_d` text comment '答案D',
  `correct` varchar(2) not null comment '正确答案',
  `correct_mark` VARCHAR(500) null comment '答案说明',
  `addtime` int(6) null,
  `score` int(4) null default 0 comment '分数',
  primary key (`id`)
);
/**
答题列表
 */
create table `ims_fy_lesson_praxis_score`(
  `id` int(11) not null auto_increment,
  `uniacid` int(11) not null,
  `uid` int(11) not null,
  `parentid` int(11) not null comment '课程ID',
  `chapterid` int(11) not null comment '章节ID',
  `praxisid` int(11) not null comment '习题ID',
  `score` int(10) not null default 0 comment '分数',
  `addtime` int(6) default 0 comment '添加时间',
  `correct` tinyint(1) default 0 comment '是否正确1正确',
  primary key(`id`)
);


/**
讨论内容表
 */
create table `ims_fy_discuss`(
  `id` int(11) not null auto_increment,
  `uniacid` int(11) not null,
  `parentid` int(11) not null,
  `chapterid` int(11) not null comment '章节ID',
  `videotype` tinyint(1) default 0 comment '视频保存方式',
  `videourl` text comment '视频地址',
  `content` text comment '说明',
  `title` varchar(300) null comment '标题',
  `addtime` int(6) default 0,
  `status` tinyint(1) null default 0 comment '是否开启',
  `displayorder` int(4) null default 0 comment '排序',
  `is_credit` tinyint(1) null default 0 comment '是否开启讨论获取积分或余额，1获取积分，2获取余额',
  `credit` decimal(10,2) null default 0 comment '积分或余额值',
  PRIMARY KEY (`id`)
);

/**
讨论信息列表
 */
create table `ims_fy_discuss_content`(
  `id` int(11) not null auto_increment,
  `uniacid` int(11) not null,
  `cid` int(11) not null comment '讨论话题ID',
  `uid` int(11) not null comment '讨论会员ID',
  `addtime` int(6) default 0,
  `content` text comment '内容',
  `imgs` text comment '上传图片',
  primary key (`id`)
);

/**
资料上传表
 */
create table `ims_fy_lesson_material`(
  `id` int(11) not null auto_increment,
  `uniacid` int(11) not null,
  `parentid` int(11) not null comment '课程ID',
  `types` tinyint(1) null default 0 comment '素材类型0文档，1视频',
  `url` text comment '路径',
  `title` varchar(300) not null comment '素材标题',
  `addtime` int(6) null,
  `down_num` int(10) null default 0 comment '下载次数',
  `filename` varchar(300) not null comment '素材名称',
  primary key(`id`)
);

/**
已下载素材
 */
create table `ims_fy_material_download`(
  `id` int(11) not null auto_increment,
  `uid` int(11) not null,
  `uniacid` int(11) not null,
  `materialid` int(11) not null,
  `addtime` int(6) not null,
  primary key(`id`)
);

/**
我的学习
 */
create table `ims_fy_mystudy`(
  `id` int(11) not null auto_increment,
  `uniacid` int(11) not null,
  `uid` int(11) not null,
  `lessonid` int(11) not null comment '课程id',
  `addtime` int(6) null,
  `status` tinyint(1) null default 0 comment '状态0学习中，1已学完',
  primary key(`id`)
);

/**
学习进度
 */
create table `ims_fy_mystudy_rate`(
  `id` int(11) not null auto_increment,
  `uniacid` int(11) not null,
  `studyid` int(11) not null,
  `uid` int(11) not null,
  `sectionid` int(11) not null,
  `status` tinyint(1) null default 0 comment '学习状态0未学习',
  `addtime` int(6) null,
  primary key(`id`)
);

/**
参与的讨论
 */
create table `ims_fy_mydiscuss`(
  `id` int(11) not null auto_increment,
  `uniacid` int(11) not null,
  `discussid` int(11) not null,
  `uid` int(11) not null,
  `addtime` int(6) null,
  primary key(`id`)
);

/**
分享表
 */
create table `ims_fy_lesson_share`(
  `id` int(11) not null auto_increment,
  `uniacid` int(11) not null,
  `lessonid` int(11) not null,
  `uid` int(11) not null comment '分享ID',
  `mid` int(11) not null comment '会员ID',
  `addtime` int(6) null default 0,
  `status` tinyint(1) null default 0 comment '购买状态0未购买',
  primary key(`id`)
);

/**
分享购买次数
 */
create table `ims_fy_lesson_share_userd`(
  `id` int(11) not null auto_increment,
  `uniacid` int(11) not null,
  `lessonid` int(11) not null,
  `uid` int(11) not null,
  `addtime` tinyint(6) default 0,
  primary key(`id`)
);


/**
会员管理
 */
insert into `ims_modules_bindings`(`module`,`entry`,`title`,`do`,`direct`,`displayorder`) values('fy_lessonv2','menu','会员管理','member',0,0);

/**
课程分类
 */
alter table `ims_fy_lesson_son` add `rid` int(11) not null default 0 comment '章节分类ID';

/**
章节分类
 */
create table `ims_fy_lesson_reclassify`(
  `id` int(11) not null auto_increment,
  `uniacid` int(4) null,
  `name` varchar(100) not null,
  `addtime` int(6) null,
  primary key(`id`)
);


/**
同时购
*/
create table `ims_fy_lesson_meanwhile`(
  `id` int(11) not null auto_increment,
  `uniacid` int(4) not null,
  `title` varchar(200) null,
  `price` decimal(10,2) not null,
  `displayorder` int(4) null default 0 comment '排序',
  `status` tinyint(1) null default 0,
  `addtime` int(6) null,
  `thumb` varchar(255) null,
  `markprice` decimal(10,2) null default 0,
  primary key(`id`)
);

/**
 同时购课程
 */
create table `ims_fy_lesson_meanwhile_lesson`(
  `id` int(11) not null auto_increment,
  `uniacid` int(4) not null,
  `meanwhileid` int(11) not null,
  `lesson_id` int(11) not null,
  `addtime` int(6) null,
  `spec_id` int(11) null default 0,
  `prce` decimal(10,2) null,
  primary key(`id`)
);

/**
订单商品
 */
create table `ims_fy_lesson_order_parent`(
  `id` int(11) not null auto_increment,
  `uniacid` int(4) null,
  `order_id` int(11) not null,
  `lessonid` int(11) not null,
  `bookname` varchar(255) not null,
  `marketprice` decimal(10,2) not null,
  `price` decimal(10,2) not null,
  `spec_day` int(4) null,
  `teacherid` int(11) null,
  `validity` int(11) not null,
  `lesson_type` tinyint(1) not null,
  `appoint_info` text,
  `spec_name` varchar(255) null,
  primary key(`id`)
);

/**
订单
 */
alter table `ims_fy_lesson_order` add `is_meanwhile` tinyint(1) null default 0 comment '是否同时购订单1是';
alter table `ims_fy_lesson_order` add `lesson_ids` varchar(255) not null comment '课程ID列表；用，隔开';