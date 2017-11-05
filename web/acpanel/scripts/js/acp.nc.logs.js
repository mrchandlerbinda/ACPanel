(function ($) {
	$(function () {
		// Modal boxes - to all links with rel="facebox"
		$('a[rel*=facebox]').facebox()
	});
})(jQuery);

jQuery(document).ready(function($) {
	$('.tablesorter').tablesorter({
		widgets: ['zebra'],
		headers: {4:{sorter: false}}
	});
});