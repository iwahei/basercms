<?php
/**
 * [ADMIN] ブログ記事管理メニュー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>ブログ管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('記事一覧', array('controller' => 'blog_posts', 'action' => 'index', $blogContent['BlogContent']['id'])) ?></li>
			<?php if (isset($newCatAddable) && $newCatAddable): ?>
				<li><?php $this->BcBaser->link('新規記事を登録', array('controller' => 'blog_posts', 'action' => 'add', $blogContent['BlogContent']['id'])) ?></li>
			<?php endif ?>			
			<li><?php $this->BcBaser->link('コメント一覧', array('controller' => 'blog_comments', 'action' => 'index', $blogContent['BlogContent']['id'])) ?></li>
			<li><?php $this->BcBaser->link('ブログ基本設定', array('controller' => 'blog_contents', 'action' => 'edit', $blogContent['BlogContent']['id'])) ?></li>
			<li><?php $this->BcBaser->link('公開ページ確認', '/' . $blogContent['BlogContent']['name'] . '/index') ?></li>
		</ul>
	</td>
</tr>