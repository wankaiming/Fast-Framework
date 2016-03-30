/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50141
Source Host           : localhost:3306
Source Database       : demo

Target Server Type    : MYSQL
Target Server Version : 50141
File Encoding         : 65001

Date: 2016-03-30 23:28:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for news
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_cate_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `cont` text NOT NULL,
  `sort` int(11) NOT NULL,
  `update_time` datetime NOT NULL,
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of news
-- ----------------------------
INSERT INTO `news` VALUES ('1', '1', '标题1', '内容1', '1', '2016-03-30 21:37:17', '2016-03-30 21:37:06');
INSERT INTO `news` VALUES ('2', '1', '标题2', '内容2', '2', '2016-03-30 21:37:17', '2016-03-30 21:37:06');

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(30) NOT NULL,
  `allow_module` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('1', 'admin', 'all');
INSERT INTO `role` VALUES ('2', 'product', 'product,attribute,banner,categories,member,news');
INSERT INTO `role` VALUES ('3', 'order', 'order,attribute,banner,categories,member,news');
INSERT INTO `role` VALUES ('4', 'news', 'attribute,banner,categories,member,news');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `pwd` varchar(50) NOT NULL,
  `fk_role_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `tel` varchar(30) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', '1', '2016-03-30 22:12:56', 'Man', '88888888', '123@123.com');
