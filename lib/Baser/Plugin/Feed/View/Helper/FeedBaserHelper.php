<?php

/**
 * フィードBaserヘルパー
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * フィードBaserヘルパー
 *
 * @package Feed.View.Helper
 *
 */
class FeedBaserHelper extends AppHelper {

/**
 * フィード出力
 * 
 * @param int $id
 * @param mixid $mobile '' / boolean
 * @return void
 * @access public
 */
	public function feed($id, $mobile = '') {
		$url = array('mobile' => true, 'plugin' => 'feed', 'controller' => 'feed', 'action' => 'index');
		if ($mobile === '') {
			$mobile = (Configure::read('BcRequest.agent') == 'mobile');
		}
		if ($mobile) {
			$url = array_merge($url, array(Configure::read('BcAgent.mobile.prefix') => true));
		}
		echo $this->requestAction($url, array('pass' => array($id)));
	}

}
