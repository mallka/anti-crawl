<?php

	namespace mallka\anticrawl;

	use Yii;
	use yii\base\InvalidConfigException;
	use yii\web\View;

	class Anti extends \yii\base\Widget
	{

		public $uploadFingerUrl;
		public $homelessJsCmd;

		public function init()
		{
			parent::init();

		}

		public function run()
		{

			$this->stopDebugger();


			if($this->homelessJsCmd!=null)
				$this->stopChromeHeadless($this->homelessJsCmd);
			else
				$this->stopChromeHeadless();

			if($this->uploadFingerUrl!=null)
				$this->uploadFingerprint($this->uploadFingerUrl);
		}

		/**
		 * reject dev tools
		 */
		public function stopDebugger()
		{

			$js =<<<EOF
function checkDebugger(){ 
    const d=new Date(); 
    debugger; 
    const dur=Date.now()-d; 
    if(dur<5){ 
        return false; 
    }else{ 
        return true; 
    } 
} 
function breakDebugger(){ 
    if(checkDebugger()){ 
        breakDebugger(); 
    } 
} 
document.body.onclick=function(){ 
    breakDebugger(); 
};

EOF;
			/** @var View  $view */
			$view = $this->getView();
			$view->registerJs($js,View::POS_END);
		}


		/** 检测headless模式 */
		public function stopChromeHeadless($jsHandleHeadLessCmd="alert('Chrome headless detected');")
		{
			$js = <<<EOF
function checkHeadless()
{
	if(typeof MessageEvent === "function") { 
		if(typeof getBoxObjectFor === "function"){
			return true;
		}
	} 

	if (/HeadlessChrome/.test(window.navigator.userAgent)) {
		handleHeadLess();
	}
	if(navigator.plugins.length == 0) {
		handleHeadLess();
		
	}
	if(navigator.languages == "") {
		handleHeadLess();
	}
	
	var canvas = document.createElement('canvas');
	var gl = canvas.getContext('webgl');
	var debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
	var vendor = gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL);
	var renderer = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL);
	if(vendor == "Brian Paul" && renderer == "Mesa OffScreen") {
		handleHeadLess();
	}
	var body = document.getElementsByTagName("body")[0];
	var image = document.createElement("img");
	image.src = "http://iloveponeydotcom32188.jg";
	image.setAttribute("id", "fakeimage");
	body.appendChild(image);
	image.onerror = function(){
		if(image.width == 0 && image.height == 0) {
			handleHeadLess();
		}
	}
}
function handleHeadLess()
{
	$jsHandleHeadLessCmd
}

checkHeadless();
EOF;

			/** @var View  $view */
			$view = $this->getView();
			$view->registerJs($js,View::POS_READY);
		}

		/**
		 * 上报浏览器指纹，
		 * post方式提交，会有fingerPrint、executeTime、detail 三个参数提交。detail参数以\n换行
		 * 预计耗费时间200-500ms以内。如需加速，可以排除一些依据
		 *
		 * @param $url 上报指纹的网址
		 */
		public function uploadFingerprint($url='aaa.t')
		{
			$view = $this->getView();
			FingerprintAsset::register($view);
			$js =<<< EOF
    var fingerprintReport = function () {
      var d1 = new Date()
      Fingerprint2.get(function(components) {
      	//fingetprint
        var murmur = Fingerprint2.x64hash128(components.map(function (pair) { return pair.value }).join(), 31)
        
        //execute times(ms)
        var d2 = new Date()
        var time = d2 - d1

		//detail
        var details = ""
        for (var index in components) {
          var obj = components[index]
          var line = obj.key + " = " + String(obj.value).substr(0, 100)
          
          details += line + "\\n"
         
        }
        $.post('{$url}', {fingerPrint:murmur, executeTime:time,detail:details});
        
        
      })
    }

    if (window.requestIdleCallback) {
      requestIdleCallback(fingerprintReport)
    } else {
      setTimeout(fingerprintReport, 500)
    }

EOF;
			$view->registerJs($js,View::POS_READY);



		}
	}
