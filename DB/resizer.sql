/*
 Navicat Premium Data Transfer

 Source Server         : Local
 Source Server Type    : MySQL
 Source Server Version : 50518
 Source Host           : localhost
 Source Database       : resizer

 Target Server Type    : MySQL
 Target Server Version : 50518
 File Encoding         : utf-8

 Date: 05/22/2012 14:15:47 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `batch`
-- ----------------------------
DROP TABLE IF EXISTS `batch`;
CREATE TABLE `batch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `preset`
-- ----------------------------
DROP TABLE IF EXISTS `preset`;
CREATE TABLE `preset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `image_prefix` varchar(10) NOT NULL,
  `image_w` int(11) NOT NULL,
  `image_h` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `preset`
-- ----------------------------
BEGIN;
INSERT INTO `preset` VALUES ('1', 'iPhone', 'iphone', '480', '480', '2012-05-22 11:15:31'), ('2', 'iPhone Retina', 'iphone2x', '960', '960', '2012-05-22 11:15:48'), ('3', 'iPad', 'ipad', '1024', '1024', '2012-05-22 11:15:55'), ('4', 'iPad Retina', 'ipad2x', '2048', '2048', '2012-05-22 11:16:43'), ('5', 'Screen Small', 'sSmall', '800', '600', '2012-05-22 11:17:23'), ('6', 'Screen Med', 'sMed', '1024', '768', '2012-05-22 11:17:39'), ('7', 'Screen Large', 'sLarge', '1680', '1050', '2012-05-22 11:18:27'), ('8', 'Screen HD', 'sHD', '1920', '1080', '2012-05-22 11:19:20'), ('9', 'Laptop Normal', 'lNorm', '1280', '1024', '2012-05-22 11:19:39'), ('10', 'Laptop Large', 'lLarge', '1440', '900', '2012-05-22 11:20:08'), ('11', 'Screen X-Large', 'sXLarge', '2560', '1440', '2012-05-22 11:20:56');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
