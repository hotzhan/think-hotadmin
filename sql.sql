-- MySQL dump 10.13  Distrib 5.7.44, for Linux (x86_64)
--
-- Host: localhost    Database: admin_tp8_com
-- ------------------------------------------------------
-- Server version	5.7.44-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ha_admin`
--

DROP TABLE IF EXISTS `ha_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父ID',
  `username` varchar(20) DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) DEFAULT '' COMMENT '昵称',
  `password` varchar(32) DEFAULT '' COMMENT '密码',
  `salt` varchar(30) DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) DEFAULT '' COMMENT '头像',
  `email` varchar(100) DEFAULT '' COMMENT '电子邮箱',
  `mobile` varchar(11) DEFAULT '' COMMENT '手机号码',
  `login_failure` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '失败次数',
  `login_time` bigint(16) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) DEFAULT NULL COMMENT '登录IP',
  `token` varchar(59) DEFAULT NULL COMMENT 'Session标识',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `update_time` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_admin`
--

LOCK TABLES `ha_admin` WRITE;
/*!40000 ALTER TABLE `ha_admin` DISABLE KEYS */;
INSERT INTO `ha_admin` VALUES (1,0,'admin','开发管理员','128190c1b339afa82c901962b234ae76','xzHrKX','/storage/uploads/20240322/1d1b1342f8d1922eb398bd118ace1eb7.png','admin@admin.com','17522334455',0,1748432248,'127.0.0.1','71426cb4-dcb9-4b51-ae00-676c3b61989f',1,1491635035,1748432248,0),(2,1,'admin2','超级管理员','adb38760014615b8076b936b3f6c34dc','Jh3jXZ','/static/img/avatar.png','','',0,1708517981,NULL,'abea1c46-c18c-44fc-ab50-ee24c067cd32',1,1708484149,1711105927,0),(3,2,'admin3','Adm3','e52ba33aa2dcb8333c02e21eaf0e6050','Yda2UX','/static/img/avatar.png','','13599809908',0,NULL,NULL,'',1,1708486699,1711097506,0),(4,1,'admin4','Ad4323344DD','b5872552b81102561687ef90f6c8fa45','tqPXyW','/static/img/avatar.png','','13599809908',0,NULL,NULL,'',1,1708505181,1711097506,0),(5,1,'admin666','Admin233','2022f67c79a66d8a061fe587768b4063','sXv8md','/static/img/avatar.png','123123@qq.com','13599809908',0,NULL,NULL,'',1,1710043091,1711097320,0);
/*!40000 ALTER TABLE `ha_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ha_article`
--

DROP TABLE IF EXISTS `ha_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `category_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT '栏目',
  `title` varchar(255) DEFAULT '' COMMENT '标题',
  `img` varchar(255) DEFAULT '' COMMENT '标题图片',
  `content` text COMMENT '内容',
  `uid` int(10) unsigned NOT NULL COMMENT '作者',
  `is_top` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '置顶',
  `is_recommend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '推荐',
  `is_swiper` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '轮播',
  `sort_number` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `create_time` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `update_time` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `delete_time` bigint(16) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='文章表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_article`
--

LOCK TABLES `ha_article` WRITE;
/*!40000 ALTER TABLE `ha_article` DISABLE KEYS */;
INSERT INTO `ha_article` VALUES (1,6,'这是一篇测试文章','/storage/uploads/20250528/b25d1bff926700af766a5a51cf2e5983.webp','<p><span style=\"font-size: 1rem;\">5月28日，外交部发言人毛宁主持例行记者会。</span></p><p>路透社记者提问，日本防卫省表示，<span style=\"font-weight: bolder;\"><font color=\"#ff0000\">辽宁舰航母</font></span>经过日本南部岛屿并正在驶向太平洋的西部，日方正在密切监视这一举动。中方航母为何如此靠近日本？这是否在向日美以及<span style=\"background-color: rgb(255, 255, 0);\">台湾</span>地区发出信号？</p><p><img src=\"http://admin.tp8.com/storage/uploads/20250528/b25d1bff926700af766a5a51cf2e5983.webp\" style=\"width: 204.656px;\"></p>',1,1,1,0,1000,1,1748423798,1748425718,0),(2,2,'测试文章2','','<p>测试文章2内容</p>',1,0,0,0,1000,0,1748425434,1748425718,0);
/*!40000 ALTER TABLE `ha_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ha_attachment`
--

DROP TABLE IF EXISTS `ha_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_attachment` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `cate` varchar(50) DEFAULT NULL COMMENT '类别',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `url` varchar(255) DEFAULT '' COMMENT '物理路径',
  `filename` varchar(100) DEFAULT '' COMMENT '文件名称',
  `filetype` varchar(16) DEFAULT NULL,
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `mimetype` varchar(100) DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) DEFAULT '' COMMENT '透传数据',
  `storage` varchar(100) NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) DEFAULT NULL COMMENT '文件 sha1编码',
  `description` varchar(100) DEFAULT NULL COMMENT '文件备注信息，可用于搜索等',
  `create_time` int(11) DEFAULT NULL COMMENT '创建日期',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COMMENT='附件表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_attachment`
--

LOCK TABLES `ha_attachment` WRITE;
/*!40000 ALTER TABLE `ha_attachment` DISABLE KEYS */;
INSERT INTO `ha_attachment` VALUES (1,NULL,1,0,'/storage/uploads/20240322/1d1b1342f8d1922eb398bd118ace1eb7.png','avatar4.png','image',13543,'image/png','','local','0e7d20d57fcba12f7eb8d8e7e702b1c4898c2db9',NULL,1711099323,1711099323,0),(2,NULL,1,0,'/storage/uploads/20250527/bbe9f7f44cd52a8725183e0d577665fe.jpg','user2-160x160.jpg','image',6905,'image/jpeg','','local','d3f9e84ed9d642b6a0374c85e626d917001c16c6',NULL,1748339357,1748339357,0),(3,NULL,1,0,'/storage/uploads/20250528/82e1853c40995a550a8404e8f4404d37.jpg','photo4.jpg','image',1145510,'image/jpeg','','local','6d0d33a2b82ba14c01169f94d5c26dcfab2b59a0',NULL,1748416571,1748417861,1748417861),(4,NULL,1,0,'/storage/uploads/20250528/ee0e3d81109d817fb56c250db43b7eda.jpg','user3-128x128.jpg','image',3398,'image/jpeg','','local','129c9917b123c09885d0d46584a7c0edb94b8fd6',NULL,1748416915,1748416915,0),(5,NULL,1,0,'/storage/uploads/20250528/f8161e70a192cd363b964fbcb9b937cc.jpg','user1-128x128.jpg','image',2750,'image/jpeg','','local','599406b7429a78a667e163212c0dedae2165b0d6',NULL,1748418325,1748418325,0),(6,NULL,1,0,'/storage/uploads/20250528/b25d1bff926700af766a5a51cf2e5983.webp','279759ee3d6d55fb31f25400dc79a54520a4dd4f.webp','image',20018,'image/webp','','local','3f1b07e1d1666fe5dd24012c21479e2175afef76',NULL,1748423458,1748423458,0);
/*!40000 ALTER TABLE `ha_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ha_auth_group`
--

DROP TABLE IF EXISTS `ha_auth_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父id',
  `name` varchar(100) DEFAULT '' COMMENT '组名',
  `rules` text COMMENT '规则ID',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='分组表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_auth_group`
--

LOCK TABLES `ha_auth_group` WRITE;
/*!40000 ALTER TABLE `ha_auth_group` DISABLE KEYS */;
INSERT INTO `ha_auth_group` VALUES (1,0,'开发管理员','*',1,1,1707215622,1709908731,0),(2,1,'超级管理员','1,2,3,4,5,6,8,10,12,19,20,21,22,23,24,25,26,27,28,29,30,35,39,40,41,46,47,48,49,58,59,69,70,72',1,1,1707216046,1708499774,0),(3,2,'管理员','0,2,3,4,10,12,19,20,21,22,23,24,25,26,46,47,48,49,58,59',1,1,1708503453,1711097510,0);
/*!40000 ALTER TABLE `ha_auth_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ha_auth_group_access`
--

DROP TABLE IF EXISTS `ha_auth_group_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '级别ID',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限分组表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_auth_group_access`
--

LOCK TABLES `ha_auth_group_access` WRITE;
/*!40000 ALTER TABLE `ha_auth_group_access` DISABLE KEYS */;
INSERT INTO `ha_auth_group_access` VALUES (1,1,0,0,0),(2,2,0,0,0),(3,3,0,0,0),(4,3,0,0,0);
/*!40000 ALTER TABLE `ha_auth_group_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ha_auth_rule`
--

DROP TABLE IF EXISTS `ha_auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) DEFAULT '' COMMENT '图标',
  `rule` varchar(100) DEFAULT NULL COMMENT '规则URL',
  `condition` varchar(255) DEFAULT '' COMMENT '条件',
  `is_menu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `is_show` tinyint(1) NOT NULL DEFAULT '0',
  `menu_type` enum('addtabs','blank','dialog','ajax') DEFAULT NULL COMMENT '菜单类型',
  `sort_number` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`rule`),
  KEY `pid` (`parent_id`),
  KEY `weigh` (`sort_number`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COMMENT='节点表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_auth_rule`
--

LOCK TABLES `ha_auth_rule` WRITE;
/*!40000 ALTER TABLE `ha_auth_rule` DISABLE KEYS */;
INSERT INTO `ha_auth_rule` VALUES (1,0,'控制台','fas fa-tachometer-alt','index/index','',1,1,NULL,1000,1,1706946983,1709969595,0),(2,0,'常规管理','fas fa-archive','general','',1,1,NULL,1000,1,1706947832,1706950915,0),(3,2,'附件管理','fas fa-paperclip','general/attachment','',1,1,NULL,1000,1,1706949208,1709974442,0),(4,2,'个人资料','fas fa-file-alt','general/profile','',1,1,NULL,1000,1,1706952823,1706965996,0),(5,0,'权限管理','fas fa-address-card','auth','',1,1,NULL,1000,1,1706952853,1710996128,0),(6,5,'账户管理','fas fa-user-circle','auth/admin','',1,1,NULL,1000,1,1706952895,1710230233,0),(7,5,'账户日志','fas fa-book','auth/admin_log','',1,1,NULL,1000,1,1706954050,1710230386,0),(8,5,'角色分组','far fa-object-group','auth/auth_group','',1,1,NULL,1000,1,1706954071,1706954217,0),(9,5,'菜单规则','fas fa-bars','auth/auth_rule','',1,1,NULL,1000,1,1706954088,1706954237,0),(10,0,'会员管理','fas fa-users','user','',1,1,NULL,1000,1,1706954275,1711090657,0),(12,10,'会员管理','fas fa-user','user/user','',1,1,NULL,1000,1,1706954988,1706973139,0),(13,10,'会员分组','fas fa-search','user/user_group','',1,1,NULL,1000,1,1706955213,1706955213,0),(14,10,'会员规则','fas fa-search','user/user_rule','',1,1,NULL,1000,1,1706955229,1706955229,0),(15,0,'设置管理','fas fa-cog','setting','',1,1,NULL,2000,1,1706955260,1748356483,0),(16,15,'配置管理','fas fa-wrench','setting/config','',1,1,NULL,1000,1,1706955288,1706973406,0),(17,15,'配置分组','fas fa-cogs','setting/config_group','',1,1,NULL,1000,1,1706955322,1748428084,0),(19,3,'查看','far fa-circle','general/attachment/index','',0,0,NULL,1000,1,1706965714,1711032596,0),(20,3,'添加','far fa-circle','general/attachment/add','',0,0,NULL,1000,1,1706965788,1711032608,0),(21,3,'编辑','far fa-circle','general/attachment/edit','',0,0,NULL,1000,1,1706965818,1711032619,0),(22,3,'删除','far fa-circle','general/attachment/del','',0,0,NULL,1000,1,1706965851,1711032630,0),(23,4,'查看','far fa-circle','general/profile/index','',0,0,NULL,1000,1,1706966359,1706966359,0),(24,4,'添加','far fa-circle','general/profile/add','',0,0,NULL,1000,1,1706966383,1706966383,0),(25,4,'编辑','far fa-circle','general/profile/edit','',0,0,NULL,1000,1,1706966458,1706966458,0),(26,4,'删除','far fa-circle','general/profile/del','',0,0,NULL,1000,1,1706966505,1706966505,0),(27,6,'查看','far fa-circle','auth/admin/index','',0,0,NULL,1000,1,1706967720,1706967720,0),(28,6,'添加','far fa-circle','auth/admin/add','',0,0,NULL,1000,1,1706967785,1706967785,0),(29,6,'编辑','far fa-circle','auth/admin/edit','',0,0,NULL,1000,1,1706967859,1706967918,0),(30,6,'删除','far fa-circle','auth/admin/del','',0,0,NULL,1000,1,1706967936,1706967936,0),(31,7,'查看','far fa-circle','auth/admin_log/index','',0,0,NULL,1000,1,1706972560,1706972560,0),(32,7,'添加','far fa-circle','auth/admin_log/add','',0,0,NULL,1000,1,1706972560,1706972560,0),(33,7,'编辑','far fa-circle','auth/admin_log/edit','',0,0,NULL,1000,1,1706972560,1706972560,0),(34,7,'删除','far fa-circle','auth/admin_log/del','',0,0,NULL,1000,1,1706972560,1706972560,0),(35,8,'查看','far fa-circle','auth/auth_group/index','',0,0,NULL,1000,1,1706972644,1706973074,0),(39,8,'添加','far fa-circle','auth/auth_group/add','',0,0,NULL,1000,1,1706973022,1706973022,0),(40,8,'编辑','far fa-circle','auth/auth_group/edit','',0,0,NULL,1000,1,1706973022,1706973022,0),(41,8,'删除','far fa-circle','auth/auth_group/del','',0,0,NULL,1000,1,1706973022,1706973022,0),(42,9,'查看','far fa-circle','auth/auth_rule/index','',0,0,NULL,1000,1,1706973087,1706973087,0),(43,9,'添加','far fa-circle','auth/auth_rule/add','',0,0,NULL,1000,1,1706973087,1706973087,0),(44,9,'编辑','far fa-circle','auth/auth_rule/edit','',0,0,NULL,1000,1,1706973087,1706973087,0),(45,9,'删除','far fa-circle','auth/auth_rule/del','',0,0,NULL,1000,1,1706973087,1706973087,0),(46,12,'查看','far fa-circle','user/user/index','',0,0,NULL,1000,1,1706973155,1706973155,0),(47,12,'添加','far fa-circle','user/user/add','',0,0,NULL,1000,1,1706973181,1706973181,0),(48,12,'编辑','far fa-circle','user/user/edit','',0,0,NULL,1000,1,1706973202,1706973202,0),(49,12,'删除','far fa-circle','user/user/del','',0,0,NULL,1000,1,1706973220,1706973220,0),(50,13,'查看','far fa-circle','user/user_group/index','',0,0,NULL,1000,1,1706973230,1709983776,0),(51,13,'添加','far fa-circle','user/user_group/add','',0,0,NULL,1000,1,1706973230,1706973230,0),(52,13,'编辑','far fa-circle','user/user_group/edit','',0,0,NULL,1000,1,1706973230,1706973230,0),(53,13,'删除','far fa-circle','user/user_group/del','',0,0,NULL,1000,1,1706973230,1706973230,0),(54,14,'查看','far fa-circle','user/user_rule/index','',0,0,NULL,1000,1,1706973234,1706973234,0),(55,14,'添加','far fa-circle','user/user_rule/add','',0,0,NULL,1000,1,1706973234,1706973234,0),(56,14,'编辑','far fa-circle','user/user_rule/edit','',0,0,NULL,1000,1,1706973234,1706973234,0),(57,14,'删除','far fa-circle','user/user_rule/del','',0,0,NULL,1000,1,1706973234,1706973234,0),(58,2,'系统配置','fas fa-tasks','setting/config/config','',1,1,NULL,999,1,1706973484,1708586928,0),(59,12,'测试','far fa-circle','index/text','',0,0,NULL,1000,1,1707123771,1707143606,0),(60,16,'查看','far fa-circle','setting/config/index','',0,0,NULL,1000,1,1708420000,1708420000,0),(61,16,'添加','far fa-circle','setting/config/add','',0,0,NULL,1000,1,1708420000,1708420000,0),(62,16,'编辑','far fa-circle','setting/config/edit','',0,0,NULL,1000,1,1708420000,1708420000,0),(63,16,'删除','far fa-circle','setting/config/del','',0,0,NULL,1000,1,1708420000,1708420000,0),(64,17,'查看','far fa-circle','setting/config_group/index','',0,0,NULL,1000,1,1708420004,1708420004,0),(65,17,'添加','far fa-circle','setting/config_group/add','',0,0,NULL,1000,1,1708420004,1708420004,0),(66,17,'编辑','far fa-circle','setting/config_group/edit','',0,0,NULL,1000,1,1708420004,1708420004,0),(67,17,'删除','far fa-circle','setting/config_group/del','',0,0,NULL,1000,1,1708420004,1711079918,0),(68,9,'菜单','far fa-circle','auth/auth_rule/menus','',0,0,NULL,1000,1,1708422069,1708422971,0),(69,8,'授权','far fa-circle','auth/auth_group/access','',0,0,NULL,1000,1,1708422154,1708434412,0),(70,8,'权限','far fa-circle','auth/auth_group/accessrules','',0,0,NULL,1000,1,1708437846,1708495005,0),(71,9,'规则列表','far fa-circle','auth/auth_rule/rules','',0,0,NULL,1000,1,1708438005,1708438005,0),(72,8,'分组','far fa-circle','auth/auth_group/groups','',0,0,NULL,1000,1,1708495031,1708495031,0),(73,2,'前台配置','far fa-circle','setting/config/indexconfig','',1,1,NULL,1000,1,1708596195,1708596195,0),(74,0,'文章管理','far fa-newspaper','article','',1,1,NULL,1000,1,1748354117,1748356340,0),(75,74,'文章管理','fas fa-newspaper','article/article','',1,1,NULL,1000,1,1748356373,1748356373,0),(76,74,'栏目管理','fas fa-indent','article/category','',1,1,NULL,1000,1,1748356454,1748414805,0),(77,75,'查看','far fa-circle','article/article/index','',0,0,NULL,1000,1,1748431677,1748431677,0),(78,75,'添加','far fa-circle','article/article/add','',0,0,NULL,1000,1,1748431677,1748431677,0),(79,75,'编辑','far fa-circle','article/article/edit','',0,0,NULL,1000,1,1748431677,1748431677,0),(80,75,'删除','far fa-circle','article/article/del','',0,0,NULL,1000,1,1748431677,1748431677,0),(81,76,'查看','far fa-circle','article/category/index','',0,0,NULL,1000,1,1748431682,1748431682,0),(82,76,'添加','far fa-circle','article/category/add','',0,0,NULL,1000,1,1748431682,1748431682,0),(83,76,'编辑','far fa-circle','article/category/edit','',0,0,NULL,1000,1,1748431682,1748431682,0),(84,76,'删除','far fa-circle','article/category/del','',0,0,NULL,1000,1,1748431682,1748431682,0);
/*!40000 ALTER TABLE `ha_auth_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ha_category`
--

DROP TABLE IF EXISTS `ha_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父栏目id',
  `name` varchar(255) DEFAULT '' COMMENT '名称',
  `code` varchar(64) DEFAULT '' COMMENT '缩写',
  `img` varchar(255) DEFAULT '' COMMENT '图片',
  `sort_number` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_menu` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否菜单 1是 0否',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示 1是 0否',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `create_time` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `update_time` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `delete_time` bigint(16) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COMMENT='栏目表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_category`
--

LOCK TABLES `ha_category` WRITE;
/*!40000 ALTER TABLE `ha_category` DISABLE KEYS */;
INSERT INTO `ha_category` VALUES (1,0,'国际','guoji','/storage/uploads/20250528/f8161e70a192cd363b964fbcb9b937cc.jpg',1000,1,1,1,1748416580,1748418327,0),(2,0,'国内','guonei','/storage/uploads/20250528/ee0e3d81109d817fb56c250db43b7eda.jpg',1000,1,1,1,1748416920,1748416920,0),(3,1,'亚洲','asian','',1000,1,1,1,1748416953,1748416953,0),(4,1,'欧洲','europe','',1000,1,1,1,1748417115,1748417115,0),(5,1,'北美','na','',1000,1,1,1,1748417147,1748417147,0),(6,3,'中国','china','',1000,1,1,1,1748417601,1748417601,0),(7,5,'美国','american','',1000,1,1,1,1748417648,1748417648,0),(8,4,'英国','english_delete_2e102b165bd7d8dc47dbc97f762cce8e','',1000,1,1,0,1748418423,1748421223,1748421223);
/*!40000 ALTER TABLE `ha_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ha_config`
--

DROP TABLE IF EXISTS `ha_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) DEFAULT '0' COMMENT '所属分组',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名称',
  `description` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '描述',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '代码',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '设置配置及内容',
  `sort_number` int(10) DEFAULT '1000' COMMENT '排序',
  `create_time` int(10) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_group_id` (`group_id`,`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设置';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_config`
--

LOCK TABLES `ha_config` WRITE;
/*!40000 ALTER TABLE `ha_config` DISABLE KEYS */;
INSERT INTO `ha_config` VALUES (1,1,'基础配置','请勿修改本配置代码hot_base，系统保留用','base','[{\"name\":\"\\u7f51\\u7ad9\",\"type\":\"text\",\"field\":\"name\",\"value\":\"HotAdmin\",\"options\":\"\",\"desc\":\"\\u7f51\\u7ad9\\u540d\\u79f0\"},{\"name\":\"\\u7248\\u672c\",\"type\":\"text\",\"field\":\"version\",\"value\":\"1.0.0\",\"options\":\"\",\"desc\":\"\"},{\"name\":\"\\u7248\\u6743\",\"type\":\"text\",\"field\":\"copy_right\",\"value\":\"Copyright \\u00a9 2023-2026 hotadmin.cn. All rights reserved.\",\"options\":\"\",\"desc\":\"\"}]',1000,1708589635,1748431822,0),(2,1,'登录设置','','login','[{\"name\":\"\\u767b\\u5f55\\u9a8c\\u8bc1\\u7801\",\"type\":\"select\",\"field\":\"captcha\",\"value\":\"image\",\"options\":\"off|\\u4e0d\\u5f00\\u542f\\r\\nimage|\\u56fe\\u5f62\\u9a8c\\u8bc1\\u7801\\r\\nslide|\\u6ed1\\u5757\\u9a8c\\u8bc1\\u7801\",\"desc\":\"\"},{\"name\":\"\\u5355\\u8bbe\\u5907\\u767b\\u5f55\",\"type\":\"switch\",\"field\":\"single_device\",\"value\":\"0\",\"options\":\"\",\"desc\":\"\"}]',1000,1708595557,1711110776,0),(3,1,'安全设置','','security5','[]',1000,1710680047,1710776580,0),(4,2,'网站设置','','base','[{\"name\":\"\\u7f51\\u7ad9\\u540d\\u79f0\",\"type\":\"text\",\"field\":\"sitename\",\"value\":\"HotAdmin\\u540e\\u53f0\\u7ba1\\u7406\\u7cfb\\u7edf\",\"options\":\"\",\"desc\":\"\"}]',1000,1710680059,1748428221,0),(6,2,'注册设置','','register','{\"0\":{\"name\":\"\\u6ce8\\u518c\\u9a8c\\u8bc1\\u7801\",\"type\":\"select\",\"field\":\"captcha\",\"value\":\"image\",\"options\":\"off|\\u4e0d\\u5f00\\u542f\\r\\nimage|\\u56fe\\u5f62\\u9a8c\\u8bc1\\u7801\\r\\nslide|\\u6ed1\\u5757\\u9a8c\\u8bc1\\u7801\",\"desc\":\"\"},\"1\":{\"name\":\"\\u90ae\\u7bb1\\u9a8c\\u8bc1\",\"type\":\"switch\",\"field\":\"mail_verify\",\"value\":\"1\",\"options\":\"\",\"desc\":\"\"},\"2\":{\"name\":\"\\u77ed\\u4fe1\\u9a8c\\u8bc1\",\"type\":\"switch\",\"field\":\"sms_verify\",\"value\":\"0\",\"options\":\"\",\"desc\":\"\"}}',1000,1710776509,1711096142,0),(7,2,'登录设置','','login','[{\"name\":\"\\u9a8c\\u8bc1\\u7801\",\"type\":\"select\",\"field\":\"captcha\",\"value\":\"off\",\"options\":\"off|\\u4e0d\\u5f00\\u542f\\r\\nimage|\\u56fe\\u5f62\\u9a8c\\u8bc1\\u7801\\r\\nslide|\\u6ed1\\u5757\\u9a8c\\u8bc1\\u7801\",\"desc\":\"\"}]',1000,1710779919,1748428216,0);
/*!40000 ALTER TABLE `ha_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ha_config_group`
--

DROP TABLE IF EXISTS `ha_config_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_config_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '作用模块',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名称',
  `description` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '描述',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '代码',
  `sort_number` int(10) DEFAULT '1000' COMMENT '排序',
  `auto_create_config` tinyint(1) DEFAULT '0' COMMENT '自动生成配置文件',
  `create_time` int(10) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='设置分组';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_config_group`
--

LOCK TABLES `ha_config_group` WRITE;
/*!40000 ALTER TABLE `ha_config_group` DISABLE KEYS */;
INSERT INTO `ha_config_group` VALUES (1,'admin','后台管理','请勿修改本配置代码hot_admin，系统保留用','hot_admin',1000,1,1708589483,1708595158,0),(2,'index','前台管理','','hot_index',1000,1,1708595368,1708595368,0);
/*!40000 ALTER TABLE `ha_config_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ha_menu`
--

DROP TABLE IF EXISTS `ha_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT '0' COMMENT '父级菜单',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名称',
  `url` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'url',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'fas fa-list' COMMENT '图标',
  `is_show` tinyint(1) unsigned DEFAULT '1' COMMENT '是否显示',
  `is_top` tinyint(1) unsigned DEFAULT '0' COMMENT '是否为顶部菜单',
  `is_header` tinyint(1) unsigned DEFAULT '0' COMMENT '是否为菜单头，只做显示用',
  `sort_number` int(10) unsigned DEFAULT '1000' COMMENT '排序号',
  `log_method` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT '不记录' COMMENT '记录日志方法',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `index_url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='后台菜单';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_menu`
--

LOCK TABLES `ha_menu` WRITE;
/*!40000 ALTER TABLE `ha_menu` DISABLE KEYS */;
INSERT INTO `ha_menu` VALUES (1,0,'控制台','index/index','fas fa-home',1,1,0,99,'不记录',1705478773,1706863702,0),(2,0,'常规管理','general','fas fa-desktop',1,1,0,1099,'不记录',1705478773,1706882398,0),(3,2,'用户管理2','admin/admin_user/index','fas fa-user',1,0,0,1000,'不记录',1705478773,1706865151,1706865151),(4,3,'添加用户','admin/admin_user/add','fas fa-plus',0,0,0,1000,'POST',1705478773,1705478773,0),(5,3,'修改用户','admin/admin_user/edit','fas fa-edit',0,0,0,1000,'POST',1705478773,1705478773,0),(6,3,'删除用户','admin/admin_user/del','fas fa-close',0,0,0,1000,'POST',1705478773,1705478773,0),(7,2,'角色管理','admin/admin_role/index','fas fa-user-friends',1,0,0,1000,'不记录',1705478773,1706865159,1706865159),(8,7,'添加角色','admin/admin_role/add','fas fa-plus',0,0,0,1000,'POST',1705478773,1705478773,0),(9,7,'修改角色','admin/admin_role/edit','fas fa-edit',0,0,0,1000,'POST',1705478773,1705478773,0),(10,7,'删除角色','admin/admin_role/del','fas fa-close',0,0,0,1000,'POST',1705478773,1705478773,0),(11,7,'角色授权','admin/admin_role/access','fas fa-key',0,0,0,1000,'POST',1705478773,1705478773,0),(12,53,'系统配置','setting/config/show','fas fa-wrench',1,0,0,1000,'不记录',1705478773,1706869610,0),(13,12,'添加菜单','admin/ha_menu/add','fas fa-plus',0,0,0,1000,'POST',1705478773,1706865190,1706865190),(14,12,'修改菜单','admin/ha_menu/edit','fas fa-edit',0,0,0,1000,'POST',1705478773,1706865186,1706865186),(15,12,'删除菜单','admin/ha_menu/del','fas fa-close',0,0,0,1000,'POST',1705478773,1706865182,1706865182),(16,2,'操作日志','admin/admin_log/index','fas fa-keyboard',1,0,0,1000,'不记录',1705478773,1706865149,1706865149),(17,16,'查看操作日志详情','admin/admin_log/view','fas fa-search-plus',0,0,0,1000,'不记录',1705478773,1705478773,0),(18,2,'个人资料','admin/admin_user/profile','fas fa-smile',1,0,0,1000,'POST',1705478773,1706865145,1706865145),(19,0,'权限管理','#','fab fa-autoprefixer',1,0,0,1000,'不记录',1705478773,1706866333,0),(20,19,'用户管理','user/index','fas fa-user',1,0,0,1000,'不记录',1705478773,1705502969,0),(21,12,'配置分组','setting/config_group','fas fa-plus',1,0,0,1000,'POST',1705478773,1706884838,0),(22,20,'修改用户','admin/user/edit','fas fa-pencil',0,0,0,1000,'POST',1705478773,1705478773,0),(23,20,'删除用户','admin/user/del','fas fa-trash',0,0,0,1000,'POST',1705478773,1705478773,0),(24,20,'启用用户','admin/user/enable','fas fa-circle',0,0,0,1000,'POST',1705478773,1705478773,0),(25,20,'禁用用户','admin/user/disable','fas fa-circle',0,0,0,1000,'POST',1705478773,1705478773,0),(26,19,'用户等级管理','admin/user_level/index','fas fa-th-list',1,0,0,1000,'不记录',1705478773,1705478773,0),(27,26,'添加用户等级','admin/user_level/add','fas fa-plus',0,0,0,1000,'POST',1705478773,1705478773,0),(28,26,'修改用户等级','admin/user_level/edit','fas fa-pencil',0,0,0,1000,'POST',1705478773,1705478773,0),(29,26,'删除用户等级','admin/user_level/del','fas fa-trash',0,0,0,1000,'POST',1705478773,1705478773,0),(30,26,'启用用户等级','admin/user_level/enable','fas fa-circle',0,0,0,1000,'POST',1705478773,1705478773,0),(31,26,'禁用用户等级','admin/user_level/disable','fas fa-circle',0,0,0,1000,'POST',1705478773,1705478773,0),(32,2,'附件管理','#','fas fa-code',1,0,0,1005,'不记录',1705478773,1706865250,0),(33,32,'代码生成','admin/generate/index','fas fa-file-code',1,0,0,1000,'不记录',1705478773,1706865237,1706865237),(34,32,'设置配置','admin/develop/setting','fas fa-cogs',1,0,0,995,'不记录',1705478773,1706865234,1706865234),(35,34,'设置管理','admin/setting/index','fas fa-cog',1,0,0,1000,'不记录',1705478773,1705478773,0),(36,35,'添加设置','admin/setting/add','fas fa-plus',0,0,0,1000,'POST',1705478773,1705478773,0),(37,35,'修改设置','admin/setting/edit','fas fa-pencil',0,0,0,1000,'POST',1705478773,1705478773,0),(38,35,'删除设置','admin/setting/del','fas fa-trash',0,0,0,1000,'POST',1705478773,1705478773,0),(39,34,'设置分组管理','admin/setting_group/index','fas fa-list',1,0,0,1000,'不记录',1705478773,1705478773,0),(40,39,'添加设置分组','admin/setting_group/add','fas fa-plus',0,0,0,1000,'POST',1705478773,1705478773,0),(41,39,'修改设置分组','admin/setting_group/edit','fas fa-pencil',0,0,0,1000,'POST',1705478773,1705478773,0),(42,39,'删除设置分组','admin/setting_group/del','fas fa-trash',0,0,0,1000,'POST',1705478773,1705478773,0),(43,0,'设置中心','admin/setting/center','fas fa-cogs',1,0,0,1000,'不记录',1705478773,1705478773,0),(44,21,'账户管理','auth/admin/index','fas fa-list',1,0,0,1000,'不记录',1705478773,1706886381,0),(47,22,'后台设置','admin/setting/admin','fas fa-adjust',1,0,0,1000,'不记录',1705478773,1706937133,0),(48,2,'个人资料','general/profile/index','fas fa-user-circle',1,0,0,1000,'POST',1705478773,1706887928,0),(49,32,'数据维护','admin/database/table','fas fa-database',1,0,0,1000,'不记录',1705478773,1706865231,1706865231),(50,49,'查看表详情','admin/database/view','fas fa-eye',0,0,0,1000,'不记录',1705478773,1705478773,0),(51,49,'优化表','admin/database/optimize','fas fa-refresh',0,0,0,1000,'POST',1705478773,1705478773,0),(52,49,'修复表','admin/database/repair','fas fa-circle-o-notch',0,0,0,1000,'POST',1705478773,1705478773,0),(53,19,'设置管理','setting','fas fa-cogs',1,0,0,1000,'不记录',1705478773,1706944501,0),(54,19,'菜单管理','setting/menu','fas fa-bars',1,0,0,1000,'不记录',1705478773,1706944522,0),(55,53,'编辑器上传文件','admin/file/editor','fas fa-upload',0,0,0,1000,'不记录',1705478773,1706869594,1706869594),(56,0,'测试1','text','fas fa-search',0,0,0,1000,'不记录',1706364327,1706865171,1706865171),(57,0,'测试1','text','fas fa-search',0,0,0,1000,'不记录',1706364346,1706865167,1706865167),(58,0,'测试1','text','fas fa-search',0,0,0,1000,'不记录',1706364367,1706865164,1706865164),(59,0,'测试1','text','fas fa-search',0,0,0,1000,'不记录',1706364380,1706859205,1706859205);
/*!40000 ALTER TABLE `ha_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ha_user`
--

DROP TABLE IF EXISTS `ha_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(32) DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) DEFAULT '' COMMENT '昵称',
  `password` varchar(32) DEFAULT '' COMMENT '密码',
  `salt` varchar(30) DEFAULT '' COMMENT '密码盐',
  `email` varchar(100) DEFAULT '' COMMENT '电子邮箱',
  `mobile` varchar(11) DEFAULT '' COMMENT '手机号',
  `avatar` varchar(255) DEFAULT '' COMMENT '头像',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '等级',
  `gender` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `score` int(10) NOT NULL DEFAULT '0' COMMENT '积分',
  `description` varchar(255) NOT NULL DEFAULT '',
  `login_time` bigint(16) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) DEFAULT '' COMMENT '登录IP',
  `loginfailure` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '失败次数',
  `joinip` varchar(50) DEFAULT '' COMMENT '加入IP',
  `token` varchar(50) DEFAULT '' COMMENT 'Token',
  `email_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '邮箱验证状态',
  `mobile_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '手机验证状态',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `create_time` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `update_time` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `delete_time` bigint(16) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COMMENT='会员表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_user`
--

LOCK TABLES `ha_user` WRITE;
/*!40000 ALTER TABLE `ha_user` DISABLE KEYS */;
INSERT INTO `ha_user` VALUES (1,'admin','Admin','ea3fa79d46060c1ccaef0c182a4fbe56','uzZYGE','admin@163.com','13888888888','/static/img/avatar.png',0,0,'2017-04-08',0.00,0,'',1710311555,'127.0.0.1',0,'127.0.0.1','',0,0,1,0,1711076354,0),(2,'test1','测试','eaf17997c9b978c1d889d244fc5d3267','SIQBNO','123123@qq.com','13612341234','/static/img/avatar.png',0,0,NULL,0.00,0,'<p>我是一条来自<b><font color=\"#ff0000\">北方</font></b>的狼</p><p><img style=\"width: 160px;\" src=\"/storage/uploads/20250527/bbe9f7f44cd52a8725183e0d577665fe.jpg\"><br></p>',1711030225,'',0,'','',1,1,1,1708787847,1748431743,0),(3,'test2','测试233','1ff7b578f06703cc14ec5a1fe30f425c','So9Cne','123123@qq.com','17588999988','/static/img/avatar.png',0,0,NULL,0.00,0,'',NULL,'',0,'','',0,0,1,1708788909,1711076354,0),(4,'test3','测试3','32102af5b12acff61885db4d750f8a8c','uTVGEC','123123@qq.com','','/static/img/avatar.png',0,0,NULL,0.00,0,'',NULL,'',0,'','',0,0,1,1708788943,1711076354,0),(5,'test4','测试4','432ea9f272ceb21c901ec1cae27d285c','SmR87r','','','/static/img/avatar.png',0,0,NULL,0.00,0,'',NULL,'',0,'','',0,0,1,1708789035,1711076354,0),(6,'adminxx','Admin2','543c22ba726636a85b01e8339ccbe039','hiPdBr','','','/static/img/avatar.png',0,0,NULL,0.00,0,'',NULL,'',0,'','',0,0,1,1709996453,1711076354,0),(7,'atest','TESTXX','c023c7825c61e559d7ebc095070a5d85','KgLPJY','12312333@qq.com','13541624167','/static/img/avatar.png',0,0,NULL,0.00,0,'',NULL,'',0,'','',0,0,1,1710604125,1711076354,0),(8,'btest','TEST','89afdd13ec64ff264a64f1a510ac8dcf','0cdMjo','123316333@qq.com','13641624166','/static/img/avatar.png',0,0,NULL,0.00,0,'',1710605153,'',0,'','',0,0,1,1710605132,1711076354,0),(9,'testx','TECC','810909d02a196d1e1403a04af2b5e144','po6Srl','644600723@qq.com','13641624165','/static/img/avatar.png',0,0,NULL,0.00,0,'',1710835431,'',0,'','',0,0,0,1710835417,1711076354,0),(10,'testb','TTXX','377b024f338311efb936a80dca44bc2d','nWAcfw','644601727@qq.com','13641624160','/static/img/avatar.png',0,0,NULL,0.00,0,'',1710836410,'',0,'','',0,0,1,1710836394,1711076354,0),(11,'test20','TEST20','641fd11cd2f493400582160eccf07f53','zJoVND','644600737@qq.com','13322556651','/static/img/avatar.png',0,0,NULL,0.00,0,'',NULL,'',0,'','',0,0,1,1710837254,1711076829,1711076829),(12,'test21','TEST201','424b35cc8e3dad1cc8162e499b265aa0','BU7wOM','644600731@qq.com','13322556653','/static/img/avatar.png',0,0,NULL,0.00,0,'我是一条鱼',1710918689,'',0,'','',0,0,1,1710837456,1711076829,1711076829),(13,'test5','测试用户5','7870453235916c05652c11dab478a516','IvCS71','123123@qq.com','13612341234','/static/img/avatar.png',0,0,NULL,0.00,0,'<p>溜溜球</p>',NULL,'',0,'','',0,0,1,1748431792,1748431792,0);
/*!40000 ALTER TABLE `ha_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ha_user_group`
--

DROP TABLE IF EXISTS `ha_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父id',
  `name` varchar(100) DEFAULT '' COMMENT '组名',
  `rules` text COMMENT '规则ID',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='分组表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_user_group`
--

LOCK TABLES `ha_user_group` WRITE;
/*!40000 ALTER TABLE `ha_user_group` DISABLE KEYS */;
INSERT INTO `ha_user_group` VALUES (1,0,'默认组','*',1,1,1708765677,1711029958,0);
/*!40000 ALTER TABLE `ha_user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ha_user_group_access`
--

DROP TABLE IF EXISTS `ha_user_group_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_user_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '级别ID',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限分组表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_user_group_access`
--

LOCK TABLES `ha_user_group_access` WRITE;
/*!40000 ALTER TABLE `ha_user_group_access` DISABLE KEYS */;
INSERT INTO `ha_user_group_access` VALUES (1,1,0,0,0),(2,1,0,0,0),(3,1,0,0,0),(4,1,0,0,0),(5,1,0,0,0),(6,1,0,0,0),(7,1,0,0,0),(8,1,0,0,0),(9,1,0,0,0),(10,1,0,0,0),(12,1,1710837457,1710837457,0),(13,1,0,0,0);
/*!40000 ALTER TABLE `ha_user_group_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ha_user_rule`
--

DROP TABLE IF EXISTS `ha_user_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ha_user_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) DEFAULT '' COMMENT '图标',
  `rule` varchar(100) DEFAULT NULL COMMENT '规则URL',
  `condition` varchar(255) DEFAULT '' COMMENT '条件',
  `is_menu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `is_show` tinyint(1) NOT NULL DEFAULT '0',
  `menu_type` enum('addtabs','blank','dialog','ajax') DEFAULT NULL COMMENT '菜单类型',
  `sort_number` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`rule`),
  KEY `pid` (`parent_id`),
  KEY `weigh` (`sort_number`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COMMENT='节点表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ha_user_rule`
--

LOCK TABLES `ha_user_rule` WRITE;
/*!40000 ALTER TABLE `ha_user_rule` DISABLE KEYS */;
INSERT INTO `ha_user_rule` VALUES (1,0,'前台','far fa-circle','index','',0,0,NULL,1000,1,1708753275,1709975936,0),(2,0,'api接口','far fa-circle','api','',0,0,NULL,1000,1,1708753452,1710993660,0),(3,1,'会员','far fa-circle','index/user','',0,0,NULL,1000,1,1708758556,1708758556,0),(4,3,'会员中心','far fa-circle','index/user/index','',0,0,NULL,1000,1,1708758591,1711028866,0),(5,3,'注册','far fa-circle','index/user/register','',0,0,NULL,1000,1,1708758619,1708758619,0),(6,3,'登录','far fa-circle','index/user/login','',0,0,NULL,1000,1,1708758636,1708758636,0),(7,3,'个人资料','far fa-circle','index/user/profile','',0,0,NULL,1000,1,1708758667,1708758667,0),(8,1,'首页','far fa-circle','index/index','',1,1,NULL,1000,1,1710947193,1711011921,0),(9,1,'多级菜单','far fa-circle','index/index#1','',1,1,NULL,1000,1,1710991314,1711008848,0),(10,9,'二级菜单1','far fa-circle','index/index#2','',1,1,NULL,1000,1,1710991337,1710991337,0),(12,9,'二级菜单2','far fa-circle','index/index#3','',1,1,NULL,1000,1,1710991625,1710991625,0),(13,12,'三级菜单1','far fa-circle','index/index#4','',1,1,NULL,1000,1,1710991644,1710991644,0),(14,12,'三级菜单2','far fa-circle','index/index#5','',1,1,NULL,1000,1,1710991674,1710991674,0),(15,13,'四级菜单','far fa-circle','index/index#6','',1,1,NULL,1000,1,1710991697,1710991697,0),(16,1,'联系我们','far fa-circle','index/contact','',1,1,NULL,1000,1,1710994627,1711090642,0),(17,15,'五级菜单','far fa-circle','index/index#7','',1,1,NULL,1000,1,1711013270,1711032398,0),(18,15,'五级菜单','far fa-circle','index/index#8','',1,1,NULL,1000,1,1711013294,1711032416,0),(19,15,'五级菜单','far fa-circle','index/index#9','',1,1,NULL,1000,1,1711013627,1711032431,0);
/*!40000 ALTER TABLE `ha_user_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'admin_tp8_com'
--

--
-- Dumping routines for database 'admin_tp8_com'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-28 19:37:31
