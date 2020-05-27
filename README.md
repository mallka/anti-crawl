# Anti web crawl,

## Install


The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist mallka/anti-crawl "dev-master"
```

or add

```
"mallka/anti-crawl": "dev-master"
```

to the require section of your `composer.json` file.


## How to use


###1. In view file:

```angular2html
<?= \mallka\anticrawl\Anti::widget([

        //the url of upload fingerprint,it will not fetch fingerprint if not set
        'uploadFingerUrl'=>Url::to(['/anticrawl/anti-log/create']),   
         
                                   ]);?>
```

###2.Create some action for collect data

```
//sample action ,please create table first.
<?php

	use Yii;
	

	class AntiLogController extends \yii\web\Controller
	{
		public function actionCreate()
		{
			$model = new AntiLog();
			$model->loadDefaultValues();
			$model->ip = Yii::$app->request->getUserIP();
			$model->url =Yii::$app->request->getReferrer();
			$model->finger = Yii::$app->request->post('fingerPrint');
			$model->finger_time = Yii::$app->request->post('executeTime',0);
			$model->finger_detail = Yii::$app->request->post('detail',0);
			$model->create_at=time();
			$model->user_id = Yii::$app->user->getId();
			$model->save();
			return;
		}

	}


```
