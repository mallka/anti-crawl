<?php

	namespace mallka\anticrawl;

	use yii\web\AssetBundle;

	class FingerprintAsset extends AssetBundle
	{
		public $js
			= [
				'assets/fingerprintjs2-2.1.0/fingerprint2.js',
			];
		public $depends
			= [
				'yii\web\JqueryAsset',
			];
		public function init()
		{
			$this->sourcePath = __DIR__ . '/assets';
			parent::init();
		}
	}
