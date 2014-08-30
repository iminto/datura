{include header}
<div><!-- $site --></div>
演示验证码b：
<br>
新建一个表
<pre>
CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `createDateTime` datetime NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `url` varchar(200) DEFAULT NULL,
  `viewCount` int(11) NOT NULL DEFAULT '0',
  `replyCount` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `categorySubId` int(11) DEFAULT NULL,
  `tags` varchar(300) NOT NULL,
  `projectId` int(11) DEFAULT NULL,
  `finish` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8
</pre>
查询结果(演示模板的循环语法)：
<table border='1'>
    <tr><td>id</td><td>标题</td><td>内容</td><td>标签</td></tr>
    <!-- loop from=article key=$key item=$a -->
    <tr><td><!-- $a.id --></td><td><!-- $a.title --></td><td><!-- $a.content --></td><td><!-- $a.tags --></td></tr>
    <!-- /loop -->
</table>
版本：<!-- $version -->
<!-- if $version=='0.02' -->
第一个正式版
<!-- else -->
这不是正式版
<!-- /if -->
{include footer}
