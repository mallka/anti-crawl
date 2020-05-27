<?php

	namespace mallka\risegrid;

	use yii\web\AssetBundle;

	class FingerprintAsset extends AssetBundle
	{
		public $js
			= [
				'ssets/fingerprintjs2-2.1.0/fingerprint2.js', #右键菜单
			];

		public $depends
			= [
				'yii\web\JqueryAsset',
			];

		public function init()
		{
			$this->sourcePath = __DIR__ . '/resources';
			parent::init();
		}
	}
