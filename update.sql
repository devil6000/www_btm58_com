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
  primary key (`id`)
);