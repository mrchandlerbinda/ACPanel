(function ($) {
	$(function() {
		// Preload images
		$.preloadCssImages();
	
		// CSS tweaks
		if( $('#header #nav li.menu').length )
		{
			$('#header #nav li.menu:last').addClass('nobg');
		}
	
		$('.block_head ul').each(function() { $('li:first', this).addClass('nobg'); });
		$('.block table tr:odd').not('.monitoring tr').css('background-color', '#fbfbfb');
		$('.block form input[type=file]').addClass('file');
	
		// Check / uncheck all checkboxes
		$('.check_all').click(function() {
			$(this).parents('form').find('input:checkbox').attr('checked', $(this).is(':checked'));
		});
	
		// Messages
		$('.block .message').hide().append('<span class="close" title="Dismiss"></span>').fadeIn('slow');
		$('.block .message .close').hover(
			function() { $(this).addClass('hover'); },
			function() { $(this).removeClass('hover'); }
		);
	
		$('.block .message .close').click(function() {
			$(this).parent().fadeOut('slow', function() { $(this).remove(); });
		});
	
		// Form select styling
		$("form select.styled").select_skin();
	
		// Tabs
		$(".tab_content").hide();
		$("ul.tabs li:first-child").addClass("active").show();
		$(".block").find(".tab_content:first").show();
	
		$("ul.tabs li").click(function() {
			$(this).parent().find('li').removeClass("active");
			$(this).addClass("active");
			$(this).parents('.block').find(".tab_content").hide();
	
			var activeTab = $(this).find("a").attr("href");
			$(activeTab).show();
			return false;
		});
	
		// Sidebar Tabs
		$(".sidebar_content").hide();
		$("ul.sidemenu li:first-child").addClass("active").show();
		$(".block").find(".sidebar_content:first").show();
	
		$("ul.sidemenu li").click(function() {
			$(this).parent().find('li').removeClass("active");
			$(this).addClass("active");
			$(this).parents('.block').find(".sidebar_content").hide();
	
			var activeTab = $(this).find("a").attr("href");
			$(activeTab).show();
			return false;
		});
	
		// Block search
		$('.block .block_head form .text').bind('click', function() { $(this).attr('value', ''); });
	
		// Navigation dropdown fix for IE6
		if($.browser.version.substr(0,1) < 7) {
			$('#header #nav li, #header #h-wrap li').hover(
				function() { $(this).addClass('iehover'); },
				function() { $(this).removeClass('iehover'); }
			);
		}
	
		// IE6 PNG fix
		$(document).pngFix();
	
		// Switch category
		$('#h-wrap').hover(function(){
				$(this).toggleClass('active');
				$("#h-wrap ul").css('display', 'block');
			}, function(){
				$(this).toggleClass('active');
				$("#h-wrap ul").css('display', 'none');
		});
	
		// Select lang
		$('#forma-lang select').change(function () {
			var lng = $('option:selected', this).val();
	
			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_main',
				data:'go=1&lang=' + lng,
				success:function(result) {
					window.location.reload();
				}
			});
		});
	});
})(jQuery);