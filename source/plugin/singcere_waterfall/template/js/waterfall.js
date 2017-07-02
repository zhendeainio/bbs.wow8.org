function addreply(id, uid, username, message) {
	jQuery('#reply' + id).prepend(
			"<div class=\"hp_b cl hp_b_now\">" + "<dl class=\"hp_share cl\">" + "<dt class=\"avatar32\"><a class=\"trans07 left\" href=\"home.php?mod=space&amp;uid=" + uid
					+ "\" c=\"1\" target=\"_blank\"><img class=\"namecard js_processed\" src=\"uc_server/avatar.php?uid=" + uid + "&amp;size=small\"></a></dt>" + "<dd><span class=\"fb\"><a href=\"home.php?mod=space&amp;uid=" + uid + "\">"
					+ username + "</a></span> <span class=\"gray\">" + message + "</span></dd>" + "</dl>" + "</div>");
	jQuery("#wall").resize();
}

function updateCollection(id, collections) {
	jQuery('.block_' + id + " .collectionnum").html(collections);
}



function setImageHeight(id, height) {
	jQuery('.block_' + id + ' .sh').attr('height', height);
}

function changefollowstatus(id, op) {
	jQuery('#followbtn_' + id).attr("href", "plugin.php?id=singcere_mls_ext&mod=misc&op=" + op + "&ctid=" + id);
	jQuery('#followbtn_' + id + " span").attr("class", "pink_" + op);
}

function updatetotalnum(tid, collectionnum, relaynum, likenum, replynum) {
	if (collectionnum != 0) {
		var id = jQuery('.block_' + tid + " .collectionnum");
		value = parseInt(id.text()) + collectionnum;
		id.html(value);
	}
	if (relaynum != 0) {
		var id = jQuery('.block_' + tid + " .relaynum");
		value = parseInt(id.text()) + relaynum;
		id.html(value);
	}
	if (likenum != 0) {
		var id = jQuery('.block_' + tid + " .likenum");
		value = parseInt(id.text()) + likenum;
		id.html(value);
	}
	if (replynum != 0) {
		var id = jQuery('.block_' + tid + " .replynum");
		value = parseInt(id.text()) + replynum;
		id.html(value);
	}
}

function relayupdate() {
}
function update_collection() {
}
function checkBlind() {
}

// 閻庢垵绔峰ù渚�帳缂冿拷Sing_Did 2012.08.07
jQuery(document).ready(function() {
	var jQuerycontainer = jQuery('#wall');
	jQuerycontainer.imagesLoaded(function() {
		jQuerycontainer.masonry({
			itemSelector : '.h_poster'
		});
	});
	
//	var blocks = jQuerycontainer.find('.h_poster');
//	var count = blocks.length;
//	for(var i = 0; i < count; i++) {
//		jQuerycontainer.masonry('appended', blocks[i], true);
//	}
//	jQuerycontainer.show();
	var nav_more = jQuery('#nav_bar_more');
	nav_more.click(function() {
		if (jQuery("#drop_down").is(":hidden")) {
			jQuery("#drop_down").show(1000);
		} else {
			jQuery("#drop_down").hide(1000);
		}
	});
});

var filternav = jQuery(".fixed-nav");
if (filternav.length > 0) {
	var ftop = filternav.offset().top;
}

var spart = 1;
var islock = false;
var show_subnav = true;
jQuery(window).bind('scroll', function() {
	if (filternav.length > 0) {
		if (jQuery(document).scrollTop() <= ftop && jQuery('.fixed-nav').hasClass('enable')) {
			jQuery('.fixed-nav').removeClass('enable').css('margin-left', '0');
			jQuery('.fixed-nav .subnav').fadeIn(500);
			if (!show_subnav) {
				jQuery('.fixed-nav .subnav').show();
				jQuery('.dailydown').text('隐藏').removeClass('daily_hide').addClass('daily_show');
				show_subnav = true;
			}
		}

		if (jQuery(document).scrollTop() > ftop) {
			if (jQuery('html').hasClass('widthauto')) {
				jQuery('.fixed-nav').addClass('enable').css('width', jQuery('#wall').width() - 18 + 'px').css('margin-left', (jQuery('#w-container').width() - 17) / 2 * (-1) + "px");
			} else {
				jQuery('.fixed-nav').addClass('enable').css('width', '960px').css('margin-left', "-480px");
			}

			if (show_subnav) {
				jQuery('.fixed-nav .subnav').hide();
				jQuery('.dailydown').text('显示').removeClass('daily_show').addClass('daily_hide');
				show_subnav = false;
			}

		}
	}
	if (spart) {
		if (jQuery(document).scrollTop() + jQuery(window).height() > jQuery(document).height() - 1000) {
			if (!islock) {

				jQuery('#infscr-loading').fadeIn(500);
				islock = true;
				var link = jQuery('#page-nav a').attr('href') + "&spart=" + (spart + 1);
				jQuery.ajax({
					url : link,
					success : function(data) {
						spart = spart + 1;
						var items = jQuery('.h_poster', data);
						islock = items.length == 0 ? true : false;
						jQuery('#wall').append(items).masonry('appended', items, false).resize();
						jQuery('#infscr-loading').fadeOut(200);
						if (items.length == 0) {
							jQuery('#mpages').show();
						}
					},
					statusCode : {
						404 : function() {
							islock = true;
							jQuery('#wall').resize();
							// setTimeout(function(){container.masonry('reload')},500);
							jQuery('#infscr-loading').fadeOut(200);
							jQuery('#mpages').show();
						}
					}
				});
			}
		}
	}

});

function subNav(e) {

	if (show_subnav) {
		jQuery('.dailydown').text('显示').removeClass('daily_show').addClass('daily_hide');
		show_subnav = false;
	} else {
		jQuery('.dailydown').text('隐藏').removeClass('daily_hide').addClass('daily_show');
		show_subnav = true;
	}
	var nav = jQuery('.fixed-nav');
	var subnav = jQuery('.subnav');
	if (subnav.is(":hidden")) {
		if (nav.hasClass('enable')) {
			subnav.fadeIn(500);
		} else {
			subnav.show(500);
		}
	} else {
		if (nav.hasClass('enable')) {
			subnav.fadeOut(500);
		} else {
			subnav.hide(500);
		}
	}
}

function sethc(value) {
	setcookie('singcere_waterfall_hc', value, 86400*7);
	window.location.reload();
}
