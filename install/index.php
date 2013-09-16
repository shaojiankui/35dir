<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>35分类目录网站安装向导</title>
<link href="images/skin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="main">
	<?php require('header.php'); ?>
	<div class="central">
    	<div id="left">
    		<ul>
				<li>
					<h1 class="install">1</h1>
					<div class="left_title">  
						<h2 class="install">准备安装</h2>
						<p class="install">欢迎您使用35分类目录网站内容管理系统！</p>
					</div>
				</li>
				<li>
					<h1>2</h1>
					<div class="left_title">  
						<h2>阅读协议</h2>
						<p>请认真阅读软件使用协议，以免您的利益受到损害！</p>
					</div>
				</li>
				<li>
					<h1>3</h1>
					<div class="left_title">  
						<h2>基本设置</h2>
						<p>请设置网站的基本信息进行网站安装！</p>
					</div>
				</li>
				<li>
					<h1>4</h1>
					<div class="left_title">  
						<h2>开始安装</h2>
						<p>开始愉快的网站安装之旅吧！</p>
					</div>
				</li>
			</ul>
		</div>
		<div class="right">
			<div class="right_title">软件使用说明</div>
			<div style="font-size: 14px; line-height: 25px; padding: 20px 0; text-align: left;">
				<p>35分类目录是一款简洁、开源、高效、免费的网站分类目录内容管理系统。<br />
1. 程序采用目前比较流行的PHP+MYSQL架构作为程序运行平台，程序运行高效稳定安全；<br />
2. 基于Smarty模板引擎，可自由定制网站风格，灵活多变；<br />
3. 全站伪静态（Rewrite）,更有利于SEO优化；<br />
4. 采用高效的页面缓存技术，可使网站数据可达到百万级的负载量；<br />
35分类目录是一款遵守LGPL协议的开源性软件。使用前请认真阅读LGPL协议。<br />
				</p>
             </div>
			<div class="agree"  align="center">
				<form action="agreement.php">
         		<input hidefocus="true" type="submit" class="button" value="马上进入下一步！" />
            	</form>
            </div>
		</div>
 	</div>
</div>
<?php require('footer.php'); ?>
</body>
</html>