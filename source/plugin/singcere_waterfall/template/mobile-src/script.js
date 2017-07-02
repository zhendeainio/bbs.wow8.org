// JavaScript Document



/**瀑布流**/
$(function() {
	//url data function dataType

	function loadMeinv(page,durl) {
			$.ajax({
			url:durl+"&page="+page,
			success:function(html){	
				//darray=new Array();
			    darray=html.split("|");
				darray.pop();
		
			    for(var i=0;i<darray.length;i++){
					$minUl = getMinUl();
					$minUl.append(darray[i]);
					//setTimeout("i++",300);
				}
				delete darray;
			},
			error:function(){
				alert("服务器没响应！");
			}
			}
		);
		
		
		
		
	}
	url=$("#loadMeinvMOre").attr("data-surl")+"";
	page=parseInt($("#loadMeinvMOre").attr("page"));
	loadMeinv(page,url);
	//无限加载
	/*$(window).on("scroll", function() {
		$minUl = getMinUl();
		if ($minUl.height() <= $(window).scrollTop() + $(window).height()) {
			//当最短的ul的高度比窗口滚出去的高度+浏览器高度大时加载新图片
			loadMeinv();//加载新图片
		}
	})*/
	
	function getMinUl() {//每次获取最短的ul,将图片放到其后
		var $arrUl = $("#container .col");
		var $minUl = $arrUl.eq(0);
		$arrUl.each(function(index, elem) {
			if (parseInt($(elem).height())< parseInt($minUl.height())) {
				$minUl = $(elem);
			}
		});
		return $minUl;
	}
	//点击更多加载
	$("#loadMeinvMOre").click(function() {
		page=page+1;
		loadMeinv(page,url);
	});
	
})