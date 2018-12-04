create table `ims_fy_lesson_praxis`(
  `id` int(11) not null auto increment,
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