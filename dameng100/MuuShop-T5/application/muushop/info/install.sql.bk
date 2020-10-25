-- -----------------------------
-- 表结构 `muucmf_muushop_cart`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `muucmf_muushop_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '顾客id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `sku_id` varchar(128) NOT NULL COMMENT '格式 pruduct_id;尺寸:X;颜色:红色',
  `quantity` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '件数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `us` (`user_id`,`sku_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='购物车';


-- -----------------------------
-- 表结构 `muucmf_muushop_coupon`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `muucmf_muushop_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '优惠券id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `duration` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期, 单位为秒, 0表示长期有效',
  `publish_cnt` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总发放数量',
  `used_cnt` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已发放数量',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '优惠券名称',
  `img` varchar(255) NOT NULL DEFAULT '' COMMENT '优惠券图片',
  `brief` varchar(256) NOT NULL DEFAULT '' COMMENT '优惠券说明',
  `valuation` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型, 0 现金券, 1 折扣券',
  `rule` text NOT NULL COMMENT '计费json {discount: 1000}',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='优惠券';


-- -----------------------------
-- 表结构 `muucmf_muushop_delivery`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `muucmf_muushop_delivery` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '运费模板id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '模板名称',
  `brief` varchar(256) NOT NULL DEFAULT '' COMMENT '模板说明',
  `valuation` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '计费方式, 0 固定邮费, 1 计件',
  `rule` text NOT NULL COMMENT '计费json {express: {normal:{start:2,start_fee:10,add:1, add_fee:12}, custom:{location:[{}],}}}',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `muucmf_muushop_messages`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `muucmf_muushop_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '留言id',
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `reply_cnt` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id, 0表示商户回复',
  `extra_info` varchar(255) NOT NULL DEFAULT '' COMMENT '其他信息',
  `brief` varchar(255) NOT NULL DEFAULT '' COMMENT '留言',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0 待审核, 1 审核成功,  2 审核失败',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `muucmf_muushop_nav`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `muucmf_muushop_nav` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '导航ID',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级导航ID',
  `title` char(30) NOT NULL COMMENT '导航标题',
  `url` char(100) NOT NULL COMMENT '导航链接[如果是分类即显示分类ID]',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '导航排序',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `target` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '新窗口打开',
  `color` varchar(30) NOT NULL,
  `band_color` varchar(30) NOT NULL,
  `band_text` varchar(30) NOT NULL,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `muucmf_muushop_order`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `muucmf_muushop_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pingid` varchar(128) NOT NULL COMMENT 'ping++ID',
  `order_no` varchar(22) NOT NULL COMMENT '商家订单号 唯一',
  `client_ip` varchar(255) NOT NULL COMMENT '用户下单ip',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '顾客id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下单时间',
  `paid_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `paid` int(1) NOT NULL DEFAULT '0' COMMENT '0未支付 1已支付',
  `send_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发货时间',
  `recv_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收货时间',
  `paid_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最终支付的总价, 单位为分',
  `discount_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已优惠的价格, 是会员折扣, 现金券,积分抵用 之和',
  `delivery_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '邮费',
  `use_point` varchar(255) NOT NULL DEFAULT '0' COMMENT '使用了积分情况{score1:100,score2:500}',
  `back_point` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '返了多少积分',
  `pay_type` varchar(36) NOT NULL COMMENT '0 未设置, ''onlinepay'':''在线支付'',''balance'':''余额'',''delivery'':''货到付款''',
  `pay_info` varchar(512) NOT NULL DEFAULT '' COMMENT '根据pay_type有不同的数据',
  `channel` varchar(64) NOT NULL COMMENT '在线支付的支付类型',
  `address` varchar(512) NOT NULL DEFAULT '' COMMENT '收货信息json {province:广东,city:深圳,town:南山区,address:工业六路,name:猴子,phone:15822222222, delivery:express}',
  `delivery_info` varchar(512) NOT NULL DEFAULT '' COMMENT '发货信息 {name:顺丰快递, order:12333333}',
  `info` text NOT NULL COMMENT '信息 {remark: 买家留言, fapiao: 发票抬头}',
  `products` text NOT NULL COMMENT '商品信息[{sku_id:"pruduct_id;尺寸:X;颜色:红色", paid_price:100, quantity:2, title:iphone,main_img:xxxxxx}]',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1 待付款, 2 待发货, 3 已发货, 4 已收货, 5 退货中, 6，退货完成 9 卖家取消 10 已取消 11 等待卖家确认 12 已评论',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=utf8 COMMENT='订单';


-- -----------------------------
-- 表结构 `muucmf_muushop_product`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `muucmf_muushop_product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品id',
  `cat_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '商品标题',
  `description` varchar(255) NOT NULL COMMENT '简短描述',
  `content` text NOT NULL COMMENT '商品详情',
  `main_img` int(11) NOT NULL DEFAULT '0' COMMENT '商品主图',
  `images` text NOT NULL COMMENT '商品图片,分号分开多张图片',
  `like_cnt` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `fav_cnt` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数',
  `comment_cnt` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `click_cnt` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点击数',
  `sell_cnt` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总销量',
  `score_cnt` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评分次数',
  `score_total` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总评分',
  `price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '价格,单位为分',
  `ori_price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '原价,单位为分',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '库存',
  `product_code` varchar(64) NOT NULL DEFAULT '' COMMENT '商家编码,可用于搜索',
  `info` varchar(32) NOT NULL DEFAULT '0' COMMENT '从低到高默认 0 不货到付款, 1不包邮 2不开发票 3不保修 4不退换货 5不是新品 6不是热销 7不是推荐',
  `position` varchar(32) NOT NULL COMMENT '展示位',
  `back_point` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买返还积分',
  `point_price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '积分换购所需分数',
  `buy_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '限购数,0不限购',
  `sku_table` text NOT NULL COMMENT 'sku表json字符串,空表示没有sku, 如{table:[{尺寸:[X,M,L]}], info: }',
  `location` varchar(255) NOT NULL DEFAULT '' COMMENT '货物所在地址json {country:中国,province:广东,city:深圳,town:南山区,address:工业六路}',
  `delivery_id` int(11) NOT NULL DEFAULT '0' COMMENT '运费模板id, 不设置将免运费',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modify_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '编辑时间',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序,从大到小',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1 正常, 0 下架',
  PRIMARY KEY (`id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `muucmf_muushop_product_cats`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `muucmf_muushop_product_cats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父分类id',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '分类名称',
  `title_en` varchar(128) NOT NULL DEFAULT '' COMMENT '分类名称英文',
  `image` int(11) NOT NULL DEFAULT '0' COMMENT '图片id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序,从大到小',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0 正常, 1 隐藏',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;


-- -----------------------------
-- 表结构 `muucmf_muushop_product_comment`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `muucmf_muushop_product_comment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '评论id',
  `product_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `order_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0 未审核, 1 审核成功, 20 审核失败',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `images` varchar(256) NOT NULL DEFAULT '' COMMENT '晒图,分号分开多张图片',
  `score` decimal(3,2) unsigned NOT NULL DEFAULT '5.00' COMMENT '用户打分, 1 ~ 5 星',
  `brief` varchar(256) NOT NULL DEFAULT '' COMMENT '回复内容',
  `sku_id` varchar(64) NOT NULL DEFAULT '' COMMENT '商品 sku_id',
  PRIMARY KEY (`id`),
  KEY `po` (`product_id`,`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='评论';


-- -----------------------------
-- 表结构 `muucmf_muushop_product_extra_info`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `muucmf_muushop_product_extra_info` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `product_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序,从大到小',
  `ukey` varchar(32) NOT NULL COMMENT '键',
  `data` varchar(512) NOT NULL COMMENT '值',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品更多信息表';


-- -----------------------------
-- 表结构 `muucmf_muushop_product_sell`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `muucmf_muushop_product_sell` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '交易id',
  `product_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `order_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `paid_price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下单价格',
  `quantity` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '下单数目',
  `detail` text NOT NULL COMMENT '商品信息{sku_id:"pruduct_id;尺寸:X;颜色:红色"}',
  PRIMARY KEY (`id`),
  KEY `po` (`product_id`,`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='交易记录';


-- -----------------------------
-- 表结构 `muucmf_muushop_user_address`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `muucmf_muushop_user_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '顾客id',
  `modify_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后使用时间',
  `name` varchar(64) NOT NULL COMMENT '收货人姓名',
  `phone` varchar(16) NOT NULL DEFAULT '' COMMENT '电话',
  `province` int(10) NOT NULL COMMENT '省',
  `city` int(10) NOT NULL COMMENT '市',
  `district` int(10) NOT NULL COMMENT '地区',
  `address` varchar(64) NOT NULL DEFAULT '' COMMENT '详细地址',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COMMENT='收货地址';


-- -----------------------------
-- 表结构 `muucmf_muushop_user_coupon`
-- -----------------------------
CREATE TABLE IF NOT EXISTS `muucmf_muushop_user_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户优惠券id',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '到期时间,0表示永不过期',
  `order_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '使用的订单id, 0表示未使用',
  `read_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '领取时间 或 阅读时间',
  `coupon_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券id',
  `info` text NOT NULL COMMENT '计费json {title: 10元, img: xxx, valuation: 0, rule{discount: 1000}}',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='优惠券';


-- -----------------------------
-- 表内记录 `muucmf_muushop_delivery`
-- -----------------------------
INSERT INTO `muucmf_muushop_delivery` VALUES ('2', '1483580004', '1517042609', '固定运费模板', '固定运费模板', '0', '{\"express\":{\"name\":\"\\u666e\\u901a\\u5feb\\u9012\",\"cost\":1000}}');
INSERT INTO `muucmf_muushop_delivery` VALUES ('3', '1483588779', '1483876559', '指定地区运费', '按照地区不同设置不同价位的运费', '1', '{\"express\":{\"name\":\"\\u666e\\u901a\\u5feb\\u9012\",\"normal\":{\"start\":\"1\",\"start_fee\":1000,\"add\":\"1\",\"add_fee\":300},\"custom\":[{\"area\":[{\"id\":\"110000\",\"name\":\"\\u5317\\u4eac\\u5e02\"},{\"id\":\"120000\",\"name\":\"\\u5929\\u6d25\\u5e02\"},{\"id\":\"130000\",\"name\":\"\\u6cb3\\u5317\\u7701\"},{\"id\":\"140000\",\"name\":\"\\u5c71\\u897f\\u7701\"}],\"cost\":{\"start\":\"1\",\"start_fee\":600,\"add\":\"1\",\"add_fee\":300}},{\"area\":[{\"id\":\"150000\",\"name\":\"\\u5185\\u8499\\u53e4\"},{\"id\":\"210000\",\"name\":\"\\u8fbd\\u5b81\\u7701\"},{\"id\":\"220000\",\"name\":\"\\u5409\\u6797\\u7701\"},{\"id\":\"230000\",\"name\":\"\\u9ed1\\u9f99\\u6c5f\"}],\"cost\":{\"start\":\"1\",\"start_fee\":1000,\"add\":\"1\",\"add_fee\":200}},{\"area\":[{\"id\":\"540000\",\"name\":\"\\u897f\\u3000\\u85cf\"},{\"id\":\"650000\",\"name\":\"\\u65b0\\u3000\\u7586\"},{\"id\":\"710000\",\"name\":\"\\u53f0\\u6e7e\\u7701\"},{\"id\":\"810000\",\"name\":\"\\u9999\\u3000\\u6e2f\"},{\"id\":\"820000\",\"name\":\"\\u6fb3\\u3000\\u95e8\"}],\"cost\":{\"start\":\"1\",\"start_fee\":2000,\"add\":\"1\",\"add_fee\":600}},{\"area\":[{\"id\":\"310000\",\"name\":\"\\u4e0a\\u6d77\\u5e02\"},{\"id\":\"320000\",\"name\":\"\\u6c5f\\u82cf\\u7701\"},{\"id\":\"330000\",\"name\":\"\\u6d59\\u6c5f\\u7701\"}],\"cost\":{\"start\":\"1\",\"start_fee\":800,\"add\":\"1\",\"add_fee\":300}}]}}');
-- -----------------------------
-- 表内记录 `muucmf_muushop_nav`
-- -----------------------------
INSERT INTO `muucmf_muushop_nav` VALUES ('1', '0', '首页', '/muushop', '0', '1', '0', '#000000', '#000000', '', '0', '0');
INSERT INTO `muucmf_muushop_nav` VALUES ('2', '0', '全部商品', 'index.php?s=/muushop/index/cats', '1', '1', '0', '#000000', '#000000', '', '0', '0');
INSERT INTO `muucmf_muushop_nav` VALUES ('3', '0', '手机', '4', '2', '1', '0', '#000000', '#000000', '', '0', '0');
INSERT INTO `muucmf_muushop_nav` VALUES ('4', '0', '美容&彩妆', '4', '3', '1', '0', '#000000', '#000000', '', '0', '0');
INSERT INTO `muucmf_muushop_nav` VALUES ('5', '0', '用户', 'index.php?s=/muushop/user', '4', '1', '0', '#000000', '#000000', '', '0', '0');

-- -----------------------------
-- 表内记录 `muucmf_muushop_product_cats`
-- -----------------------------
INSERT INTO `muucmf_muushop_product_cats` VALUES ('4', '0', '美容&彩妆', 'Moblie', '0', '1474959660', '2', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('5', '0', '小皮具', 'test2', '0', '1475324579', '3', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('6', '4', 'test11', 'test11', '26', '1475325849', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('7', '4', 'test12', 'test12', '27', '1475326163', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('8', '0', '首饰&配饰', 'pc', '0', '1475401673', '6', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('9', '0', '手袋', 'Communication', '0', '1475401776', '4', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('10', '0', '鞋履系列', 'HooMuu', '0', '1475401899', '5', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('11', '5', '皮质配饰', 'xiezi', '30', '1475453902', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('12', '8', '首饰', 'pc', '0', '1475453960', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('13', '8', '太阳镜', 'pad', '0', '1480938605', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('14', '0', '成衣', 'Women', '0', '1517471661', '1', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('15', '14', '毛呢大衣', '', '22', '1517471710', '4', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('16', '14', '羽绒服', '', '23', '1517471741', '5', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('17', '14', '针织毛衣', '', '24', '1517471798', '2', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('18', '14', '裙装裤装', '', '25', '1517471825', '3', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('19', '4', '彩妆', '', '28', '1517475159', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('20', '4', '美发护发', '', '29', '1517477998', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('21', '14', '夹克&外套', '', '31', '1517716418', '1', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('22', '5', '拉链钱包', '', '0', '1517716569', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('23', '10', '平底鞋', '', '0', '1517716755', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('24', '10', '高跟鞋', '', '0', '1517716772', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('25', '10', '凉鞋', '', '0', '1517716787', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('26', '10', '球鞋', '', '0', '1517716823', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('27', '10', '长靴&短靴', '', '0', '1517716860', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('28', '9', '肩背包', '', '0', '1517718119', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('29', '9', '手提包', '', '0', '1517718135', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('30', '9', '购物袋', '', '0', '1517718148', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('31', '9', '斜挎包&双肩包', '', '0', '1517720583', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('32', '9', '手拿包', '', '0', '1517720631', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('33', '9', '行李箱', '', '0', '1517720670', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('34', '5', '长款钱包', '', '0', '1517730850', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('35', '5', '迷你钱包', '', '0', '1517731859', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('36', '5', '卡片夹&零钱包', '', '0', '1517732031', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('37', '8', '织物配饰', '', '0', '1517734701', '0', '1');
INSERT INTO `muucmf_muushop_product_cats` VALUES ('38', '8', '皮带', '', '0', '1517734710', '0', '1');

-- -----------------------------
-- 表内记录 `muucmf_muushop_user_coupon`
-- -----------------------------
INSERT INTO `muucmf_muushop_user_coupon` VALUES ('6', '1', '1482982087', '1483586887', '0', '0', '2', '{\"title\":\"\\u6d4b\\u8bd5\\u4f18\\u60e0\\u5238\",\"img\":\"\",\"valuation\":\"0\",\"rule\":{\"max_cnt\":1,\"max_cnt_day\":1,\"min_price\":1000,\"discount\":100}}');
INSERT INTO `muucmf_muushop_user_coupon` VALUES ('7', '1', '1482984487', '0', '74', '0', '1', '{\"title\":\"\\u6d4b\\u8bd5\\u4f18\\u60e0\",\"img\":\"\",\"valuation\":\"0\",\"rule\":{\"discount\":100}}');
INSERT INTO `muucmf_muushop_user_coupon` VALUES ('8', '1', '1484462795', '0', '92', '0', '1', '{\"title\":\"\\u6d4b\\u8bd5\\u4f18\\u60e0\",\"img\":\"\",\"valuation\":\"0\",\"rule\":{\"discount\":100}}');
INSERT INTO `muucmf_muushop_user_coupon` VALUES ('9', '1', '1521503836', '0', '0', '0', '1', '{\"title\":\"\\u6d4b\\u8bd5\\u4f18\\u60e0\",\"img\":\"\",\"valuation\":\"0\",\"rule\":{\"discount\":100}}');
