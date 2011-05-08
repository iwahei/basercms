<?php
/* SVN FILE: $Id$ */
/**
 * コンテンツ管理ビヘイビア
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.models.behaviors
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * コンテンツ管理ビヘイビア
 *
 * @subpackage		baser.models.behaviors
 */
class ContentsManagerBehavior extends ModelBehavior {
/**
 * Content Model
 * @var Content
 * @access public
 */
	var $Content = null;
/**
 * コンテンツデータを登録する
 *
 * コンテンツデータを次のように作成して引き渡す
 * array('Content' =>
 *			array(	'model_id'	=> 'モデルでのID'
 *					'category'	=> 'カテゴリ名',
 *					'title'		=> 'コンテンツタイトル',		// 検索対象
 *					'detail'	=> 'コンテンツ内容',		// 検索対象
 *					'url'		=> 'URL',
 *					'status' => '公開ステータス'
 * ))
 *
 * @param Model $model
 * @param array $data
 * @return boolean
 * @access public
 */
	function saveContent(&$model, $data) {

		if(!$data) {
			return;
		}

		$data['Content']['model'] = $model->alias;
		// タグ、空白を除外
		$data['Content']['detail'] = preg_replace("/[\n\t\s]+/is", ' ', trim(strip_tags($data['Content']['detail'])));

		// 検索用データとして保存
		$id = '';
		$this->Content = ClassRegistry::init('Content');
		if(!empty($data['Content']['model_id'])) {
			$before = $this->Content->find('first', array(
				'fields' => array('Content.id', 'Content.category'),
				'conditions' => array(
					'Content.model' => $data['Content']['model'],
					'Content.model_id' => $data['Content']['model_id']
			)));
		}
		if($before) {
			$data['Content']['id'] = $before['Content']['id'];
			$this->Content->set($data);
		} else {
			$this->Content->create($data);
		}
		$result = $this->Content->save();

		// カテゴリを site_configsに保存
		if($result && $data['Content']['category'] != $before['Content']['category']) {
			return $this->updateContentCategory($model, $data['Content']['category']);
		}

		return $result;

	}
/**
 * コンテンツデータを削除する
 * 
 * URLをキーとして削除する
 * 
 * @param Model $model
 * @param string $url 
 */
	function deleteContent(&$model, $id) {

		$this->Content = ClassRegistry::init('Content');
		if($this->Content->deleteAll(array('Content.model' => $model->alias, 'Content.model_id' => $id))) {
			return $this->updateContentCategory($model);
		}

	}
/**
 * コンテンツカテゴリを更新する
 *
 * @param string $contentCategory
 * @return boolean
 * @access public
 */
	function updateContentCategory(&$model, $contentCategory = null) {
		
		$db = ConnectionManager::getDataSource('baser');
		$contentCategories = array();
		if($db->config['driver']=='csv') {
			// CSVの場合GROUP BYが利用できない（BaserCMS 1.6.11）
			$contents = $this->Content->find('all', array('conditions' => array('Content.status' => true)));
			foreach($contents as $content) {
				if($content['Content']['category'] && !in_array($content['Content']['category'], $contentCategories)) {
					$contentCategories[$content['Content']['category']] = $content['Content']['category'];
				}
			}
		} else {
			$contents = $this->Content->find('all', array('fields' => array('Content.category'), 'group' => array('Content.category')));
			foreach($contents as $content) {
				if($content['Content']['category']) {
					$contentCategories[$content['Content']['category']] = $content['Content']['category'];
				}
			}
		}

		if($contentCategory && !in_array($contentCategory, $contentCategories)) {
			$contentCategories[$contentCategory] = $contentCategory;
		}

		$siteConfigs['SiteConfig']['content_categories'] = serialize($contentCategories);
		$SiteConfig = ClassRegistry::init('SiteConfig');
		return $SiteConfig->saveKeyValue($siteConfigs);

	}

}