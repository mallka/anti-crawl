<?php

	namespace mallka\anticrawl;

	use Yii;
	use yii\base\InvalidConfigException;


	class Anti extends \yii\base\Widget
	{





		public function run()
		{

			$this->uploadFingerprint();
		}

		/**
		 * reject dev tools
		 */
		public function stopDebugger()
		{

			$js =<<<EOF
function checkDebugger(){const d=new window["\x44\x61\x74\x65"]();debugger;const dur=window["\x44\x61\x74\x65"]['\x6e\x6f\x77']()-d;if(dur<5){return false}else{return true}}function breakDebugger(){if(checkDebugger()){breakDebugger()}}window["\x64\x6f\x63\x75\x6d\x65\x6e\x74"]['\x62\x6f\x64\x79']['\x6f\x6e\x63\x6c\x69\x63\x6b']=function(){breakDebugger();window["\x61\x6c\x65\x72\x74"]("\u\x35\x39\x32\x37\u\x34\x66\x36\x63\u\x37\x65\x64\x39\u\x36\x37\x36\x31\u\x36\x64\x33\x62\u\x38\x64\x65\x66\u\x35\x34\x32\x37\u\x66\x66\x30\x63\u\x35\x32\x32\x62\u\x37\x32\x32\x63\u\x34\x65\x38\x36\u\x33\x30\x30\x32\u\x30\x30\x35\x30\u\x30\x30\x36\x63\u\x30\x30\x36\x35\u\x30\x30\x36\x31\u\x30\x30\x37\x33\u\x30\x30\x36\x35\u\x30\x30\x32\x30\u\x30\x30\x36\x34\u\x30\x30\x36\x66\u\x30\x30\x32\x30\u\x30\x30\x36\x65\u\x30\x30\x36\x66\u\x30\x30\x37\x34\u\x30\x30\x32\x30\u\x30\x30\x32\x30\u\x30\x30\x36\x33\u\x30\x30\x37\x32\u\x30\x30\x36\x31\u\x30\x30\x37\x37\u\x30\x30\x36\x63\u\x30\x30\x32\x31")};
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
		 * post方式提交，会有fingerPrint、executeTime、detail 三个参数提交。
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
          
          details += line + "\n"
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
