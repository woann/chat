-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2019-01-09 14:59:09
-- 服务器版本： 5.5.60-log
-- PHP 版本： 7.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `woann_chat`
--

-- --------------------------------------------------------

--
-- 表的结构 `c_friend`
--

CREATE TABLE `c_friend` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `friend_group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='好友表';

--
-- 转存表中的数据 `c_friend`
--

INSERT INTO `c_friend` (`id`, `user_id`, `friend_id`, `friend_group_id`) VALUES
(46, 10001, 10008, 3),
(47, 10008, 10001, 7),
(52, 10001, 10005, 3),
(53, 10005, 10001, 4),
(54, 10001, 10006, 3),
(55, 10006, 10001, 5),
(66, 10001, 10007, 3),
(67, 10007, 10001, 6);

-- --------------------------------------------------------

--
-- 表的结构 `c_friend_group`
--

CREATE TABLE `c_friend_group` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `groupname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `c_friend_group`
--

INSERT INTO `c_friend_group` (`id`, `user_id`, `groupname`) VALUES
(3, 10001, '默认分组'),
(4, 10005, '默认分组'),
(5, 10006, '默认分组'),
(6, 10007, '默认分组'),
(7, 10008, '默认分组');

-- --------------------------------------------------------

--
-- 表的结构 `c_group`
--

CREATE TABLE `c_group` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '群组所属用户id,群主',
  `groupname` varchar(255) NOT NULL COMMENT '群名',
  `avatar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='群组';

--
-- 转存表中的数据 `c_group`
--

INSERT INTO `c_group` (`id`, `user_id`, `groupname`, `avatar`) VALUES
(10008, 10001, 'PHP交流群', 'uploads/avatar/20190109/5c358bcaa77e3.jpeg'),
(10009, 10006, '屌丝集中营', 'uploads/avatar/20190109/5c358c05aa1cc.jpg'),
(10010, 10007, '萌宠', 'uploads/avatar/20190109/5c358c35a8043.jpg');

-- --------------------------------------------------------

--
-- 表的结构 `c_group_member`
--

CREATE TABLE `c_group_member` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `c_group_member`
--

INSERT INTO `c_group_member` (`id`, `group_id`, `user_id`) VALUES
(17, 10008, 10001),
(18, 10009, 10006),
(19, 10010, 10007),
(20, 10009, 10007),
(21, 10008, 10007),
(22, 10008, 10005),
(23, 10009, 10005),
(24, 10010, 10005),
(25, 10009, 10001),
(26, 10010, 10001),
(27, 10008, 10008),
(28, 10009, 10008),
(29, 10010, 10008);

-- --------------------------------------------------------

--
-- 表的结构 `c_system_message`
--

CREATE TABLE `c_system_message` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '接收用户id',
  `from_id` int(11) NOT NULL COMMENT '来源相关用户id',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '添加好友附言',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0好友请求 1请求结果通知',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未处理 1同意 2拒绝',
  `read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未读 1已读，用来显示消息盒子数量',
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统消息表';

--
-- 转存表中的数据 `c_system_message`
--

INSERT INTO `c_system_message` (`id`, `user_id`, `from_id`, `group_id`, `remark`, `type`, `status`, `read`, `time`) VALUES
(33, 10001, 10008, 7, '', 0, 1, 1, 1547013257),
(34, 10008, 10001, 0, '', 1, 1, 1, 1547013357),
(37, 10001, 10005, 4, '', 0, 1, 1, 1547013795),
(38, 10005, 10001, 0, '', 1, 1, 0, 1547013800),
(39, 10001, 10006, 5, '', 0, 1, 1, 1547013821),
(40, 10006, 10001, 0, '', 1, 1, 0, 1547013826),
(51, 10001, 10007, 6, '', 0, 1, 1, 1547016259),
(52, 10007, 10001, 0, '', 1, 1, 1, 1547016263);

-- --------------------------------------------------------

--
-- 表的结构 `c_user`
--

CREATE TABLE `c_user` (
  `id` int(11) NOT NULL,
  `avatar` varchar(255) NOT NULL COMMENT '头像',
  `nickname` varchar(255) NOT NULL COMMENT '昵称',
  `username` varchar(255) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL,
  `sign` varchar(255) NOT NULL COMMENT '签名',
  `status` varchar(255) NOT NULL DEFAULT 'online' COMMENT 'online在线 hide隐身 offline离线'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

--
-- 转存表中的数据 `c_user`
--

INSERT INTO `c_user` (`id`, `avatar`, `nickname`, `username`, `password`, `sign`, `status`) VALUES
(10001, 'uploads/avatar/20190109/5c3587fb5da9e.jpeg', 'woann', 'woann', '$2y$10$9Jo4A0nxzH8sLckJzIW9v.6wf4/ZizPs2rshK3.VUIOday1BEEj/y', 'php是世界上最好的语言', 'offline'),
(10005, 'uploads/avatar/20190109/5c358aa30d122.jpg', '苦逼程序员', 'test01', '$2y$10$DGjWpUFuBU/SnBFG3w6IHOHyV94OP2bgjTNJmgrvka2ieR9lOAi72', '我是旋涡鸣人', 'offline'),
(10006, 'uploads/avatar/20190109/5c358ae10c4d0.jpeg', '狗der产品', 'test02', '$2y$10$uQJ.ShZMJ2MHsVVVmauzluFsImWuszMS963XUEE/u7C8xRPZMfm1S', '有钱真的可以为所欲为', 'offline'),
(10007, 'uploads/avatar/20190109/5c358b05874c2.jpg', '服务架构师', 'test03', '$2y$10$RYwAgHBdfXqaE8nLo3scq.HB9vnxHhYI2P8f3aaNh0CSdykdmFuVq', '技术流就是我', 'online'),
(10008, 'uploads/avatar/20190109/5c358b4b578e1.jpg', '前端攻城狮', 'test04', '$2y$10$5QgXxaoDVkERj5pJA8B81e4ByORwSZQ8ABZRqGue0sHOatzUFtLN6', '前端好苦逼', 'offline');

--
-- 转储表的索引
--

--
-- 表的索引 `c_friend`
--
ALTER TABLE `c_friend`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `c_friend_group`
--
ALTER TABLE `c_friend_group`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `c_group`
--
ALTER TABLE `c_group`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `c_group_member`
--
ALTER TABLE `c_group_member`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `c_system_message`
--
ALTER TABLE `c_system_message`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `c_user`
--
ALTER TABLE `c_user`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `c_friend`
--
ALTER TABLE `c_friend`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- 使用表AUTO_INCREMENT `c_friend_group`
--
ALTER TABLE `c_friend_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用表AUTO_INCREMENT `c_group`
--
ALTER TABLE `c_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10011;

--
-- 使用表AUTO_INCREMENT `c_group_member`
--
ALTER TABLE `c_group_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- 使用表AUTO_INCREMENT `c_system_message`
--
ALTER TABLE `c_system_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- 使用表AUTO_INCREMENT `c_user`
--
ALTER TABLE `c_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10009;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
