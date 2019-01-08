-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2019-01-08 17:09:53
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
(2, 10001, 10002, 1),
(4, 10003, 10002, 2),
(30, 10003, 10001, 2),
(31, 10001, 10003, 1);

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
(1, 10001, '默认分组'),
(2, 10003, '测试分组');

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
(10000, 10001, 'php交流群', 'https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq/it/u=4049759186,3681816205&fm=58&bpow=353&bpoh=373'),
(10001, 10001, '娱乐', 'uploads/avatar/20190108/5c3441bd6d03e.jpeg'),
(10002, 10001, '123', 'uploads/avatar/20190108/5c34420450b18.png'),
(10003, 10001, '213', 'uploads/avatar/20190108/5c344215e166a.png'),
(10004, 10001, 'asd', 'uploads/avatar/20190108/5c3444490c21c.png'),
(10005, 10001, 'asd', 'uploads/avatar/20190108/5c3444490c21c.png'),
(10006, 10001, '12', 'uploads/avatar/20190108/5c3444f362882.png'),
(10007, 10001, '2233333333333', 'uploads/avatar/20190108/5c34454047cb1.png');

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
(4, 10000, 10002),
(5, 10000, 10003),
(7, 10000, 10001),
(8, 10001, 10001),
(9, 10002, 10001),
(10, 10003, 10001),
(11, 10004, 10001),
(12, 10005, 10001),
(13, 10006, 10001),
(14, 10007, 10001);

-- --------------------------------------------------------

--
-- 表的结构 `c_system_message`
--

CREATE TABLE `c_system_message` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '接收用户id',
  `from_id` int(11) NOT NULL COMMENT '来源相关用户id',
  `remark` varchar(255) NOT NULL COMMENT '添加好友附言',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未处理 1已处理 用来判断显示同意拒绝按钮',
  `read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未读 1已读，用来显示消息盒子数量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统消息表';

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
  `status` varchar(255) NOT NULL DEFAULT 'hide' COMMENT 'online在线 hide隐身 offline离线'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

--
-- 转存表中的数据 `c_user`
--

INSERT INTO `c_user` (`id`, `avatar`, `nickname`, `username`, `password`, `sign`, `status`) VALUES
(10001, 'https://avatars3.githubusercontent.com/u/25681022?s=460&v=4', 'woann', 'woann', '$2y$10$GXL4bjaaD2CwRZE40rA54.Mj/q0w7.PuM6BzZoM914D4GVvD/X7m.', 'PHP is the best language in the world', 'offline'),
(10002, 'https://iocaffcdn.phphub.org/uploads/avatars/24116_1544858599.jpg!/both/100x100', '哈哈哈', 'asd', '$2y$10$M3cWs2latiKEMKrSwhNLFeo7sepKzq3CYW9RbFS90iUbj10S6ikOC', '', 'offline'),
(10003, 'uploads/avatar/20190107/5c330024b9bf5.jpg', 'test1', 'test', '$2y$10$GAH4HFWW0/LW9xmRTgEVie24mvMqWplOae2Fc5iITAVZGP5KMy2f6', 'asd', 'offline');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- 使用表AUTO_INCREMENT `c_friend_group`
--
ALTER TABLE `c_friend_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `c_group`
--
ALTER TABLE `c_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10008;

--
-- 使用表AUTO_INCREMENT `c_group_member`
--
ALTER TABLE `c_group_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `c_system_message`
--
ALTER TABLE `c_system_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `c_user`
--
ALTER TABLE `c_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
