
-- 导出  表 jobs.jobs_admin 结构
CREATE TABLE IF NOT EXISTS `jobs_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `zh_username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
  `salt` char(10) NOT NULL DEFAULT '' COMMENT '密码盐',
  `last_login` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态，1正常 0禁用',
  `create_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_user_name` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- 正在导出表  jobs.jobs_admin 的数据：~2 rows (大约)
DELETE FROM `jobs_admin`;
/*!40000 ALTER TABLE `jobs_admin` DISABLE KEYS */;
INSERT INTO `jobs_admin` (`id`, `username`, `zh_username`, `password`, `email`, `salt`, `last_login`, `last_ip`, `status`, `create_at`, `update_at`) VALUES
	(1, 'admin', 'admin','21232f297a57a5a743894a0e4a801fc3', '', '', 1531147072, 3232243201, 1, 0, 1531243580),
	(2, 'demo', 'demo','fe01ce2a7fbac8fafaed7c982a04e229', 'test@test.com', '', 13333333333, 3232243201, 1, 1531641360, 1531755647);
/*!40000 ALTER TABLE `jobs_admin` ENABLE KEYS */;

-- 导出  表 jobs.jobs_auth_access_log 结构
CREATE TABLE IF NOT EXISTS `jobs_auth_access_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `access_path` varchar(50) NOT NULL DEFAULT '',
  `access_data` varchar(500) NOT NULL DEFAULT '',
  `create_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='行为日志';

-- 正在导出表  jobs.jobs_auth_access_log 的数据：~0 rows (大约)
DELETE FROM `jobs_auth_access_log`;
/*!40000 ALTER TABLE `jobs_auth_access_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs_auth_access_log` ENABLE KEYS */;

-- 导出  表 jobs.jobs_auth_group 结构
CREATE TABLE IF NOT EXISTS `jobs_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `rules` char(80) NOT NULL DEFAULT '',
  `create_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- 正在导出表  jobs.jobs_auth_group 的数据：2 rows
DELETE FROM `jobs_auth_group`;
/*!40000 ALTER TABLE `jobs_auth_group` DISABLE KEYS */;
INSERT INTO `jobs_auth_group` (`id`, `title`, `status`, `rules`, `create_at`, `update_at`) VALUES
	(1, '超级管理员', 1, '6,5,4,3,2,1', 0, 0),
	(2, '操作员', 1, '7,6,5,4', 0, 0);
/*!40000 ALTER TABLE `jobs_auth_group` ENABLE KEYS */;

-- 导出  表 jobs.jobs_auth_group_access 结构
CREATE TABLE IF NOT EXISTS `jobs_auth_group_access` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 正在导出表  jobs.jobs_auth_group_access 的数据：3 rows
DELETE FROM `jobs_auth_group_access`;
/*!40000 ALTER TABLE `jobs_auth_group_access` DISABLE KEYS */;
INSERT INTO `jobs_auth_group_access` (`uid`, `group_id`) VALUES
	(1, 1),
	(2, 0),
	(2, 2);
/*!40000 ALTER TABLE `jobs_auth_group_access` ENABLE KEYS */;

-- 导出  表 jobs.jobs_auth_rule 结构
CREATE TABLE IF NOT EXISTS `jobs_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文名称',
  `type` char(80) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  `condition` char(100) NOT NULL DEFAULT '' COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',
  `create_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- 正在导出表  jobs.jobs_auth_rule 的数据：7 rows
DELETE FROM `jobs_auth_rule`;
/*!40000 ALTER TABLE `jobs_auth_rule` DISABLE KEYS */;
INSERT INTO `jobs_auth_rule` (`id`, `name`, `title`, `type`, `status`, `condition`, `create_at`, `update_at`) VALUES
	(1, 'App\\Jobs\\Jobs\\AuthGroup', '用户组管理', 'Jobs', 1, '', 0, 1531649589),
	(2, 'App\\Jobs\\Jobs\\AuthRule', '规则权限', 'Jobs', 1, '', 0, 1531649584),
	(3, 'App\\Jobs\\Jobs\\Admin', '用户管理', 'Jobs', 1, '', 1531069725, 1531649557),
	(4, 'App\\Jobs\\Jobs\\Task', '任务列表', 'Jobs', 1, '', 1531641117, 1531649551),
	(5, 'App\\Jobs\\Jobs\\TaskGroup', '任务分组', 'Jobs', 1, '', 1531641140, 1531649544),
	(6, 'App\\Jobs\\Jobs\\TaskLog', '任务日志', 'Jobs', 1, '', 1531641172, 1531649537),
	(7, 'App\\Jobs\\Jobs\\AuthAccessLog', '行为日志', 'Jobs', 1, '', 1531759248, 1531759248);
/*!40000 ALTER TABLE `jobs_auth_rule` ENABLE KEYS */;

-- 导出  表 jobs.jobs_task 结构
CREATE TABLE IF NOT EXISTS `jobs_task` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `server_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '服务器资源ID',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分组ID',
  `task_name` varchar(50) NOT NULL DEFAULT '' COMMENT '任务名称',
  `task_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '任务类型:1-常驻类型，0-定时类型',
  `description` varchar(200) NOT NULL DEFAULT '' COMMENT '任务描述',
  `cron_spec` varchar(100) NOT NULL DEFAULT '' COMMENT '时间表达式',
  `single` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否只允许一个实例',
  `command` varchar(500) NOT NULL DEFAULT '' COMMENT '命令详情',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0停用 1启用',
  `timeout` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '超时设置',
  `execute_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '累计执行次数',
  `prev_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上次执行时间',
  `create_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_group_id` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- 正在导出表  jobs.jobs_task 的数据：~9 rows (大约)
DELETE FROM `jobs_task`;
/*!40000 ALTER TABLE `jobs_task` DISABLE KEYS */;
INSERT INTO `jobs_task` (`id`, `user_id`, `server_id`, `group_id`, `task_name`, `task_type`, `description`, `cron_spec`, `single`, `command`, `status`, `timeout`, `execute_times`, `prev_time`, `create_at`, `update_at`) VALUES
	(1, 0, 0, 14, '每分钟触发', 0, '每分钟触发', '* * * * *', 1, '/usr/local/php/bin/php /home/www/test/index.php', 1, 600, 5700, 1531760763, 1529684716, 1531760763),
	(2, 0, 0, 14, '每 10 分钟触发', 0, '每 10 分钟触发', '*/10 * * * *', 1, '/usr/local/php/bin/php /home/www/test/index.php', 1, 600, 1147, 1531760703, 1529684716, 1531760703),
	(3, 0, 0, 14, '每小时的 20 分触发', 0, '每小时的 20 分触发', '20 * * * *', 1, '/usr/local/php/bin/php /home/www/test/index.php', 1, 600, 89, 1531758003, 1529684716, 1531758003),
	(4, 0, 0, 14, '每12 小时触发', 0, '每12 小时触发', '0 */12 * * * *', 1, '/usr/local/php/bin/php /home/www/test/index.php', 1, 600, 17, 1531756803, 1529684716, 1531756803),
	(5, 0, 0, 14, '每小时触发', 0, '每小时触发', '0 * * * *', 1, '/usr/local/php/bin/php /home/www/test/index.php', 1, 600, 98, 1531760402, 1529684716, 1531760402),
	(9, 0, 0, 14, '每天触发', 0, '每天触发', '0 0 * * *', 1, '/usr/local/php/bin/php /home/www/test/index.php', 1, 3600, 14, 1531756803, 1529734196, 1531756803),
	(10, 0, 0, 14, '每周触发', 0, '每周触发', '0 0 * * 0', 1, '/usr/local/php/bin/php /home/www/test/index.php', 1, 3600, 1, 1531584003, 1529734202, 1531584003),
	(11, 0, 0, 14, '每月触发', 0, '每月触发', '0 0 1 * *', 1, '/usr/local/php/bin/php /home/www/test/index.php', 1, 3600, 0, 0, 1529734206, 1529769372),
	(12, 0, 0, 14, '每年触发', 0, '每年触发', '0 0 1 1 *', 1, '/usr/local/php/bin/php /home/www/test/index.php', 1, 3600, 0, 0, 1529734299, 1529769364);
/*!40000 ALTER TABLE `jobs_task` ENABLE KEYS */;

-- 导出  表 jobs.jobs_task_group 结构
CREATE TABLE IF NOT EXISTS `jobs_task_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `group_name` varchar(50) NOT NULL DEFAULT '' COMMENT '组名',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '说明',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `create_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- 正在导出表  jobs.jobs_task_group 的数据：~3 rows (大约)
DELETE FROM `jobs_task_group`;
/*!40000 ALTER TABLE `jobs_task_group` DISABLE KEYS */;
INSERT INTO `jobs_task_group` (`id`, `user_id`, `group_name`, `description`, `is_del`, `create_at`, `update_at`) VALUES
	(14, 0, '测试任务', '测试任务', 0, 1528248151, 1529734452),
	(15, 0, '预发布任务', '预发布任务', 0, 1529734469, 1529734469),
	(16, 0, '上线任务', '上线任务', 0, 1529734502, 1529734502);
/*!40000 ALTER TABLE `jobs_task_group` ENABLE KEYS */;

-- 导出  表 jobs.jobs_task_log 结构
CREATE TABLE IF NOT EXISTS `jobs_task_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '任务ID',
  `command` varchar(500) NOT NULL DEFAULT '' COMMENT '执行命令',
  `output` varchar(5000) NOT NULL DEFAULT '' COMMENT '任务输出',
  `error` varchar(5000) NOT NULL DEFAULT '' COMMENT '错误信息',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `process_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '消耗时间/秒',
  `create_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_task_id` (`task_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- 正在导出表  jobs.jobs_task_log 的数据：~3 rows (大约)
DELETE FROM `jobs_task_log`;
/*!40000 ALTER TABLE `jobs_task_log` DISABLE KEYS */;
INSERT INTO `jobs_task_log` (`id`, `task_id`, `output`, `error`, `status`, `process_time`, `create_at`, `update_at`) VALUES
	(1, 1, 'good luck', '', 0, 3, 1531760523, 1531760523),
	(2, 1, 'good luck', '', 0, 3, 1531760583, 1531760583),
	(3, 1, 'good luck', '', 0, 3, 1531760643, 1531760643),
	(4, 1, 'good luck', '', 0, 3, 1531760703, 1531760703),
	(5, 2, 'good luck', '', 0, 3, 1531760703, 1531760703),
	(6, 1, 'good luck', '', 0, 3, 1531760763, 1531760763);
/*!40000 ALTER TABLE `jobs_task_log` ENABLE KEYS */;

-- 导出  表 jobs.jobs_task_server 结构
CREATE TABLE IF NOT EXISTS `jobs_task_server` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `server_name` varchar(64) NOT NULL DEFAULT '0' COMMENT '服务器名称',
  `server_account` varchar(32) NOT NULL DEFAULT 'root' COMMENT '账户名称',
  `server_ip` varchar(20) NOT NULL DEFAULT '0' COMMENT '服务器IP',
  `port` int(4) unsigned NOT NULL DEFAULT '22' COMMENT '服务器端口',
  `password` varchar(64) NOT NULL DEFAULT '0' COMMENT '服务器密码',
  `private_key_src` varchar(128) NOT NULL DEFAULT '0' COMMENT '私钥文件地址',
  `public_key_src` varchar(128) NOT NULL DEFAULT '0' COMMENT '公钥地址',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '登录类型：0-密码登录，1-私钥登录',
  `detail` varchar(255) NOT NULL DEFAULT '0' COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0-正常，1-删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务器列表';