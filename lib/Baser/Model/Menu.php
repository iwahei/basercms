<?php

/**
 * メニューモデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */

/**
 * メニューモデル
 *
 * @package Baser.Model
 */
class Menu extends AppModel {

/**
 * データベース接続
 *
 * @var string
 * @access public
 */
	public $useDbConfig = 'baser';

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Menu';

/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	public $actsAs = array('Tree', 'BcCache');

/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'name' => array(
			array('rule' => array('notEmpty'),
				'message' => 'メニュー名を入力してください。'),
			array('rule' => array('maxLength', 20),
				'message' => 'メニュー名は20文字以内で入力してください。')
		),
		'link' => array(
			array('rule' => array('notEmpty'),
				'message' => 'リンクURLを入力してください。'),
			array('rule' => array('maxLength', 255),
				'message' => 'リンクURLは255文字以内で入力してください。')
		)
	);

/**
 * コントロールソースを取得する
 *
 * @param string フィールド名
 * @return array コントロールソース
 * @access public
 */
	public function getControlSource($field = null) {
		$controlSources = array(
			'menu_type' => array(
				1 => 'コンテンツ', 
				2 => 'フォルダ', 
				3 => 'リンク'
		));
		if($field && !empty($controlSources[$field])) {
			return $controlSources[$field];
		} else {
			return $controlSources;
		}
	}

}
