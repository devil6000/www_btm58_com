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
  `addtime` int(6) null,
  primary key (`id`)
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
  PRIMARY KEY (`id`)
);

/**
讨论信息列表
 */
create table `ims_fy_discuss_content`(
  `id` int(11) not null auto_increment,
  `uniacid` int(11) not null,
  `cid` int(11) not null comment '讨论话题ID',
  `openid` varchar(100) not null,
  `addtime` int(6) default 0,
  `content` text comment '内容',
  primary key (`id`)
);