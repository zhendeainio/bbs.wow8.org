(function(a, b, c) {
    "use strict";
    var d = b.event,
    e;
    d.special.smartresize = {
        setup: function() {
            b(this).bind("resize", d.special.smartresize.handler)
        },
        teardown: function() {
            b(this).unbind("resize", d.special.smartresize.handler)
        },
        handler: function(a, c) {
            var d = this,
            f = arguments;
            a.type = "smartresize",
            e && clearTimeout(e),
            e = setTimeout(function() {
                b.event.handle.apply(d, f)
            },
            c === "execAsap" ? 0 : 100)
        }
    },
    b.fn.smartresize = function(a) {
        return a ? this.bind("smartresize", a) : this.trigger("smartresize", ["execAsap"])
    },
    b.Mason = function(a, c) {
        this.element = b(c),
        this._create(a),
        this._init()
    },
    b.Mason.settings = {
        isResizable: !0,
        isAnimated: !1,
        animationOptions: {
            queue: !1,
            duration: 500
        },
        gutterWidth: 0,
        isRTL: !1,
        isFitWidth: !1,
        containerStyle: {
            position: "relative"
        }
    },
    b.Mason.prototype = {
        _filterFindBricks: function(a) {
            var b = this.options.itemSelector;
            return b ? a.filter(b).add(a.find(b)) : a
        },
        _getBricks: function(a) {
            var b = this._filterFindBricks(a).css({
                position: "absolute"
            }).addClass("masonry-brick");
            return b
        },
        _create: function(c) {
            this.options = b.extend(!0, {},
            b.Mason.settings, c),
            this.styleQueue = [];
            var d = this.element[0].style;
            this.originalStyle = {
                height: d.height || ""
            };
            var e = this.options.containerStyle;
            for (var f in e) this.originalStyle[f] = d[f] || "";
            this.element.css(e),
            this.horizontalDirection = this.options.isRTL ? "right": "left",
            this.offset = {
                x: parseInt(this.element.css("padding-" + this.horizontalDirection), 10),
                y: parseInt(this.element.css("padding-top"), 10)
            },
            this.isFluid = this.options.columnWidth && typeof this.options.columnWidth == "function";
            var g = this;
            setTimeout(function() {
                g.element.addClass("masonry")
            },
            0),
            this.options.isResizable && b(a).bind("smartresize.masonry",
            function() {
                g.resize()
            }),
            this.reloadItems()
        },
        _init: function(a) {
            this._getColumns(),
            this._reLayout(a)
        },
        option: function(a, c) {
            b.isPlainObject(a) && (this.options = b.extend(!0, this.options, a))
        },
        layout: function(a, b) {
            for (var c = 0,
            d = a.length; c < d; c++) this._placeBrick(a[c]);
            var e = {};
            e.height = Math.max.apply(Math, this.colYs);
            if (this.options.isFitWidth) {
                var f = 0;
                c = this.cols;
                while (--c) {
                    if (this.colYs[c] !== 0) break;
                    f++
                }
                e.width = (this.cols - f) * this.columnWidth - this.options.gutterWidth
            }
            this.styleQueue.push({
                $el: this.element,
                style: e
            });
            var g = this.isLaidOut ? this.options.isAnimated ? "animate": "css": "css",
            h = this.options.animationOptions,
            i;
            for (c = 0, d = this.styleQueue.length; c < d; c++) i = this.styleQueue[c],
            i.$el[g](i.style, h);
            this.styleQueue = [],
            b && b.call(a),
            this.isLaidOut = !0
        },
        _getColumns: function() {
            var a = this.options.isFitWidth ? this.element.parent() : this.element,
            b = a.width();
            this.columnWidth = this.isFluid ? this.options.columnWidth(b) : this.options.columnWidth || this.$bricks.outerWidth(!0) || b,
            this.columnWidth += this.options.gutterWidth,
            this.cols = Math.floor((b + this.options.gutterWidth) / this.columnWidth),
            this.cols = Math.max(this.cols, 1)
        },
        _placeBrick: function(a) {
            var c = b(a),
            d,
            e,
            f,
            g,
            h;
            d = Math.ceil(c.outerWidth(!0) / this.columnWidth),
            d = Math.min(d, this.cols);
            if (d === 1) f = this.colYs;
            else {
                e = this.cols + 1 - d,
                f = [];
                for (h = 0; h < e; h++) g = this.colYs.slice(h, h + d),
                f[h] = Math.max.apply(Math, g)
            }
            var i = Math.min.apply(Math, f),
            j = 0;
            for (var k = 0,
            l = f.length; k < l; k++) if (f[k] === i) {
                j = k;
                break
            }
            var m = {
                top: i + this.offset.y
            };
            m[this.horizontalDirection] = this.columnWidth * j + this.offset.x,
            this.styleQueue.push({
                $el: c,
                style: m
            });
            var n = i + c.outerHeight(!0),
            o = this.cols + 1 - l;
            for (k = 0; k < o; k++) this.colYs[j + k] = n
        },
        resize: function() {
            var a = this.cols;
            this._getColumns(),
            (this.isFluid || this.cols !== a) && this._reLayout()
        },
        _reLayout: function(a) {
            var b = this.cols;
            this.colYs = [];
            while (b--) this.colYs.push(0);
            this.layout(this.$bricks, a)
        },
        reloadItems: function() {
            this.$bricks = this._getBricks(this.element.children())
        },
        reload: function(a) {
            this.reloadItems(),
            this._init(a)
        },
        appended: function(a, b, c) {
            if (b) {
                this._filterFindBricks(a).css({
                    top: this.element.height()
                });
                var d = this;
                setTimeout(function() {
                    d._appended(a, c)
                },
                1)
            } else this._appended(a, c)
        },
        _appended: function(a, b) {
            var c = this._getBricks(a);
            this.$bricks = this.$bricks.add(c),
            this.layout(c, b)
        },
        remove: function(a) {
            this.$bricks = this.$bricks.not(a),
            a.remove()
        },
        destroy: function() {
            this.$bricks.removeClass("masonry-brick").each(function() {
                this.style.position = "",
                this.style.top = "",
                this.style.left = ""
            });
            var c = this.element[0].style;
            for (var d in this.originalStyle) c[d] = this.originalStyle[d];
            this.element.unbind(".masonry").removeClass("masonry").removeData("masonry"),
            b(a).unbind(".masonry")
        }
    },
    b.fn.imagesLoaded = function(a) {
        function h() {
            a.call(c, d)
        }
        function i(a) {
            var c = a.target;
            c.src !== f && b.inArray(c, g) === -1 && (g.push(c), --e <= 0 && (setTimeout(h), d.unbind(".imagesLoaded", i)))
        }
        var c = this,
        d = c.find("img").add(c.filter("img")),
        e = d.length,
        f = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==",
        g = [];
        return e || h(),
        d.bind("load.imagesLoaded error.imagesLoaded", i).each(function() {
            var a = this.src;
            this.src = f,
            this.src = a
        }),
        c
    };
    var f = function(b) {
        a.console && a.console.error(b)
    };
    b.fn.masonry = function(a) {
        if (typeof a == "string") {
            var c = Array.prototype.slice.call(arguments, 1);
            this.each(function() {
                var d = b.data(this, "masonry");
                if (!d) {
                    f("cannot call methods on masonry prior to initialization; attempted to call method '" + a + "'");
                    return
                }
                if (!b.isFunction(d[a]) || a.charAt(0) === "_") {
                    f("no such method '" + a + "' for masonry instance");
                    return
                }
                d[a].apply(d, c)
            })
        } else this.each(function() {
            var c = b.data(this, "masonry");
            c ? (c.option(a || {}), c._init()) : b.data(this, "masonry", new b.Mason(a, this))
        });
        return this
    }
})(window, jQuery);


/*
 * jQuery imagesLoaded plugin v1.2.1
 * http://github.com/desandro/imagesloaded
 *
 * MIT License. by Paul Irish et al.
 */
(function($, undefined) {
    $.fn.imagesLoaded = function(callback) {
        var $this = this,
        deferred = $.Deferred(),
        $images = $this.find("img").add($this.filter("img")),
        len = $images.length,
        blank = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==",
        loaded = [],
        proper = [],
        broken = [];
        function doneLoading() {
            var $proper = $(proper),
            $broken = $(broken);
            if (broken.length) {
                deferred.reject($images, $proper, $broken)
            } else {
                deferred.resolve($images)
            }
            callback.call($this, $images, $proper, $broken)
        }
        function imgLoaded(event) {
            if (event.target.src === blank || $.inArray(this, loaded) !== -1) {
                return
            }
            loaded.push(this);
            if (event.type === "error") {
                broken.push(this)
            } else {
                proper.push(this)
            }
            deferred.notify($images.length, loaded.length, proper.length, broken.length);
            if (--len <= 0) {
                setTimeout(doneLoading);
                $images.unbind(".imagesLoaded", imgLoaded)
            }
        }
        if (!len) {
            doneLoading()
        }
        $images.bind("load.imagesLoaded error.imagesLoaded", imgLoaded).each(function() {
            var src = this.src;
            this.src = blank;
            this.src = src
        });
        return deferred.promise($this)
    }
})(jQuery);


function autoLoad(url, wrapId) {
    var $wraper = $("#" + wrapId);
    var tops;
	//alert("a");
    if ($wraper.offset()) {
        tops = $wraper.offset().top;
		//alert(tops);
    } else {
        tops = 0
    }
    $(window).scroll(onScroll);
	
	
	var page = 2;
	var loading = false;
    function onScroll() {
		if (loading || page>40) return false;
		
        var scrollTop = $(window).scrollTop();
		var wrapHeight = parseInt($wraper.height());
		var wH = parseInt($(window).height());
		var loadBoud = wrapHeight + tops - wH - 50;
        if (scrollTop < loadBoud ) return false;
		//alert("a");
		loading = true;
		$(".loading").show();
		
		loadStart();
		// $(".loading").hide();
    }
	
	
	function loadStart() {
		//alert(url);
		$.ajax({
			type: "GET",
			url: url,
			dataType: "json",
			data: {page:page},
			contentType: "application/json",
   			async:false,
			success: function(ret) {
				showImg(ret.data||[]);
				page++;
				loading = false;
			
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$(".loading").hide();
				loading = false;
			},
			timeout: 5000
		})
	}
	
	function createImg(item) {
		var imgType = item.catid==8?'mobile-portrait':'pc';
		var htm = [
			"<div class='item animations ", imgType, " ",  "' type='", imgType, "' id='", item.id, "'>",
				"<a class='pic' picrel=", item.id, " target='_blank' href='", item.url, "'>",
					"<img src='", item.thumb, "' width='230' height='{height}' title='", eHtm(item.title), "' type='", imgType, "' />",
					"<span title='", eHtm(item.title), "'>", 
						"<em>", eHtm(item.title), "</em>",
					"</span>",
				"</a>",
				'<div class="sns-share">',
					'<div class="share">',
						'<span class="share-btn">分享</span>',
						'<div class="share-item" picid="', item.id, '" sid="1" sname="', eHtm(item.title), '">',
							'<a class="qqzone" onclick="return false;" href="javascript:void(0);"><i></i>QQ空间</a>',
							'<a class="sina" onclick="return false;" href="javascript:void(0);"><i></i>新浪微博</a>',
							'<a class="qqweibo" onclick="return false;" href="javascript:void(0);"><i></i>腾讯微博</a>',
							'<a class="renren" onclick="return false;" href="javascript:void(0);"><i></i>人人网</a>',
						"</div>",
					"</div>",
				"</div>",
				"<div class='shadow'></div>",
			"</div>"
		].join('');
		var img = new Image();
		img.onload = function() {
			var w = this.width;
			var h = this.height;
			htm = htm.split("{height}").join(imgType == 'pc' ? 130:312);
			var $box = $(htm);
			$box.css({ "opacity": "0" });
			$wraper.append($box).masonry("appended", $box);
			$box.animate({
				"opacity": "1"
			}, 500);
			img = null;
		};
		img.src = item.thumb;
	}
	function showImg(list) {
        for (var i = 0; i<list.length; i++) {
			createImg(list[i]);
            // loadBoud = parseInt($wraper.css("height")) + tops - wH - 50
        }
	}
	var eDiv = $('<div></div>');
	function eHtm(x) {
		return eDiv.text(x||'').html();
	}
}



var share = function(shareConfig) {
    var allApi = {
        "qqzone": "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url={URL}&title={DESC}&desc={TITLE}&summary=&site={SITE}&pics={PICS}",
        // "sina": "http://service.weibo.com/share/share.php?url={URL}&title={TITLE}&pic={PIC}&appkey=",
        "sina": "http://service.weibo.com/share/share.php?url={URL}&title={TITLE}&pic=&appkey=",
        "qqweibo": "http://share.v.t.qq.com/index.php?c=share&a=index&url={URL}&title={TITLE}&pic={PICS}&appkey=",
        "renren": "http://widget.renren.com/dialog/share?resourceUrl={URL}&srcUrl={SRCURL}&title={TITLE}&description={DESC}&pic={PIC}"
    };
    var options = {
        "type": "NOTYPE",
        "url": "",
        "title": "",
        "desc": "",
        "pics": []
    };
    options = $.extend(options, shareConfig);
    var apiUrl = allApi[options.type];
    if (apiUrl == undefined) {
        return
    }
    var pics = options.pics.join("||") || "";
    var pic = options.pics.pop() || "";
    apiUrl = apiUrl.replace("{URL}", encodeURIComponent(options.url));
    apiUrl = apiUrl.replace("{SRCURL}", encodeURIComponent(options.url));
    apiUrl = apiUrl.replace("{TITLE}", encodeURIComponent(options.title));
    apiUrl = apiUrl.replace("{DESC}", encodeURIComponent(options.desc));
    apiUrl = apiUrl.replace("{SUMMARY}", encodeURIComponent(options.desc));
    apiUrl = apiUrl.replace("{PICS}", encodeURIComponent(pics));
    apiUrl = apiUrl.replace("{PIC}", encodeURIComponent(pic));
    apiUrl = apiUrl.replace("{SITE}", encodeURIComponent('绿茶壁纸库'));
    window.open(apiUrl)
};
$(".share-item a").live("click", 
function() {
    var type = $(this).attr("class");
    var picId = $(this).parent().attr("picId");
    var aNode = $("a[picrel=" + picId + "]");
    var url = aNode.attr("href");
    var ownerPNode = aNode.parents(".masonry-brick").find(".owner").find("p");
    var author = ownerPNode.find("a").text();
    var creatDate = ownerPNode.find("label").attr("data");
    var pics = [];
    var nowUrl = $("a[picRel=" + picId + "] img").attr("src");
    var sid = $(this).parent().attr("sid");
    var sname = $(this).parent().attr("sname");
    if (sid == 5) {
        var rep = "320x510";
        var shareTxt = "#绿茶壁纸库分享# 我在绿茶壁纸库发现了一张非常精美的壁纸哦，真的很好看。我把她分享给大家，一起来看看吧。『" + sname + "』查看高清大图请点击这里："
    } else {
        if (sid == 6) {
            var rep = "750x530";
            var shareTxt = "#绿茶壁纸库分享# 我在绿茶壁纸库发现了一张非常精美的壁纸哦，真的很好看。我把她分享给大家，一起来看看吧。『" + sname + "』查看高清大图请点击这里："
        } else {
            var rep = "960x600";
            var shareTxt = "#绿茶壁纸库分享# 我在绿茶壁纸库发现了一张非常精美的壁纸哦，真的很好看。我把她分享给大家，一起来看看吧。『" + sname + "』查看高清大图请点击这里："
        }
    }
    var title = shareTxt;
    var desc = shareTxt;
    var rUrl = nowUrl.replace(/[0-9]+x[0-9]+/g, rep);
    pics.push( nowUrl );
    var shareConfig = {
        type: type,
        url: url,
        title: title,
        desc: desc,
        pics: pics
    };
    share(shareConfig);
    var shareObj = $("#sns_" + picId).find(".share-nums").find("span");
    var shareNum = parseInt(shareObj.html());
    shareNum = isNaN(shareNum) ? 1: shareNum + 1;
    shareObj.html(shareNum)
});

    
    function add_mine() /*收藏的*/
{
	var sURL = window.location.href;
	var sTitle = document.title;
	try
	{
		window.external.addFavorite(sURL, sTitle);
	}
	catch (e)
	{
		try
		{
			window.sidebar.addPanel(sTitle, sURL, "");
		}
		catch (e)
		{
			alert("加入收藏失败，请使用Ctrl+D进行添加");
		}
	}
}
<!--//调用js--> 
<!--//瀑布流--> 
<!--鼠标滑过增加样式--> 
var type = '';

$(".item").live("mouseover", function() {
    type = $(this).attr('type');
    $(this).addClass(type+"-hover");
    $(this).find('.sns-share').show();
});

$(".item").live("mouseout", function() {
    $(this).removeClass(type+"-hover");
    $(this).find('.sns-share').hide();
});


$(".share").live("mouseover", function() {

    $(this).find('.share-item').show();
});

$(".share").live("mouseout", function() {

    $(this).find('.share-item').hide();
});