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
    
]);?>
```

###2.Create some action for collect data

TBC.
