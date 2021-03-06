<?php
/**
 * フィード読込モデル
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Vendor', 'Feed.SimplePie_Autoloader', true, array(), 'simplepie' . DS . 'autoloader.php');

/**
 * フィード読込モデル
 *
 * @package Feed.Model
 */
class Feed extends FeedAppModel {
	
/**
 * useDbConfig
 * 
 * @var string
 * @access public
 */
	public $useDbConfig = false;
	
/**
 * キャッシュフォルダー
 * 
 * @var string
 * @access	public
 */
	public $cacheFolder = 'views';
	
/**
 * フィードを取得する
 *
 * @param 	string RSSのURL
 * @param 	int 取得する件数
 * @param 	string キャッシュ保持期間
 * @param string 抽出するカテゴリ
 * @return array RSSデータ
 * @access public
 */
	public function getFeed($url, $limit = 10, $cacheExpires = null, $category = null) {
		
		// simplepie でフィードを取得する
		$datas = $this->_getFeed($url, $cacheExpires);

		// 指定カテゴリで絞り込む
		$datas['Items'] = $this->_filteringCategory($datas['Items'], $category);

		if (isset($datas['Items']) && $limit && count($datas['Items'] > $limit)) {
			$datas['Items'] = @array_slice($datas['Items'], 0, $limit);
		}

		return $datas;
		
	}

/**
 * カテゴリで抽出する
 *
 * @param array $items
 * @param mixed $filterCategory
 * @return array $items
 * @access public
 */
	public function _filteringCategory($items, $filterCategory = null) {
		
		if (!$items || !$filterCategory) {
			return $items;
		}

		$_items = array();
		foreach ($items as $item) {

			if (empty($item['category']['value'])) {
				continue;
			}

			/* 属しているカテゴリを取得 */
			$category = '';
			switch (gettype($item['category']['value'])) {
				case 'object':
					if (get_class($item['category']['value']) == 'SimplePie_Category') {
						$category = $item['category']['value']->term;
					}
					break;
				case 'string':
					$category = $item['category']['value'];
					break;
			}

			// 該当するカテゴリのみを取得
			if (is_array($filterCategory)) {
				if (in_array($category, $filterCategory)) {
					$_items[] = $item;
				}
			} else {
				if ($category == $filterCategory) {
					$_items[] = $item;
				}
			}
		}

		return $_items;
	}

/**
 * SimplePieでフィードを取得する
 *
 * @param string RSSのURL
 * @param string キャッシュ保持期間
 * @return array RSSデータ
 */
	protected function _getFeed($url, $cacheExpires = null) {
		
		if (!$url) {
			return false;
		}
		if (Configure::read('Cache.check') == false || Configure::read('debug') > 0) {
			// キャッシュをクリア
			clearCache($this->_createCacheHash('', $url), 'views', '.rss');
		}

		// キャッシュを取得
		$cachePath = $this->cacheFolder . DS . $this->_createCacheHash('.rss', $url);
		$rssData = cache($cachePath, null, $cacheExpires);

		if (empty($rssData)) {
			$SimplePie = new SimplePie();
			$SimplePie->set_feed_url($url);
			$SimplePie->enable_cache(false);

			// 一旦デバッグモードをオフに
			$debug = Configure::read('debug');
			Configure::write('debug', 0);

			$ret = $SimplePie->init();

			Configure::write('debug', $debug);

			if (!$ret) {
				return false;
			}

			$rssData = $this->_convertSimplePie($SimplePie->get_items());

			// ログインしてなければキャッシュを作成
			if (!isset($_SESSION['Auth']['User'])) {
				cache($cachePath, BcUtil::serialize($rssData));
				chmod(CACHE . $cachePath, 0666);
			}

			if ($rssData) {
				return $rssData;
			} else {
				return false;
			}
		} else {
			return BcUtil::unserialize($rssData);
		}
	}
	
/**
 * SimplePieで取得したデータを表示用に整形する
 * 2009/09/09	ryuring
 * 				古いバージョンのSimplePieでは、WordPress2.8.4が出力するRSSを解析できない事が判明。
 * 				SimplePie1.2に載せ換えて対応した。
 * TODO			このままでは、itemがない場合、RSS自体の情報が取得できないので修正が必要
 *
 * @param string SimplePieで取得したデータ
 * @return array RSSデータ
 */
	protected function _convertSimplePie($datas) {
		
		if (!$datas) {
			return null;
		}

		$simplePie = $datas[0]->get_feed();
		$feed['Channel']['title']['value'] = $simplePie->get_title();
		$feed['Channel']['link']['value'] = $simplePie->get_link();
		$feed['Channel']['description']['value'] = $simplePie->get_description();
		$feed['Channel']['pubDate']['value'] = '';
		$feed['Channel']['language']['value'] = $simplePie->get_language();
		$feed['Channel']['generator']['value'] = 'baserCMS';
		$feed['Items'] = array();

		foreach ($datas as $data) {

			$tmp = array();
			$tmp['title']['value'] = $data->get_title();
			$tmp['link']['value'] = $data->get_link();
			$tmp['pubDate']['value'] = date("r", strtotime($data->get_date('Y-m-d H:i:s')));
			$tmp['dc:creator']['value'] = $data->get_author();
			$cat = $data->get_category();
			if ($cat) {
				$tmp['category']['value'] = $cat->get_term();
			} else {
				$tmp['category']['value'] = '';
			}
			$tmp['guid']['value'] = $data->get_id();
			$tmp['guid']['attributes']['isPermaLink'] = $data->get_permalink();
			$tmp['description']['value'] = $data->get_description();
			$tmp['wfw:commentRss']['value'] = $data->get_title();

			$tmp['encoded']['value'] = $data->get_content();
			if (preg_match("/(<img.*?src=\"(.*?)\".*?\/>)/s", $tmp['encoded']['value'], $matches)) {
				$tmp['img']['tag'] = $matches[1];
				$tmp['img']['url'] = $matches[2];
			} else {
				$tmp['img']['tag'] = '';
			}

			$feed['Items'][] = $tmp;
		}
		return $feed;
		
	}
	
/**
 * Creates a unique cache file path by combining all parameters given to a unique MD5 hash
 *
 * @param string $ext The extension for the cache file
 * @return string Returns a unique file path
 */
	protected function _createCacheHash($ext = '.txt') {
		$args = func_get_args();
		array_shift($args);

		$hashSource = null;

		foreach ($args as $arg) {
			$hashSource = $hashSource . serialize($arg);
		}

		return md5($hashSource) . $ext;
	}
	
}