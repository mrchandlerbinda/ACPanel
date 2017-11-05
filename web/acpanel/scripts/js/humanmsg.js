var humanMsg = {
	setup: function(appendTo, logName, msgOpacity) {
		humanMsg.msgID = 'humanMsg';
		humanMsg.logID = 'humanMsgLog';
		humanMsg.curClass = '';

		// appendTo is the element the msg is appended to
		if (appendTo == undefined)
			appendTo = 'body';

		// The text on the Log tab
		if (logName == undefined)
			logName = 'Message Log';

		// Opacity of the message
		humanMsg.msgOpacity = .8;

		if (msgOpacity != undefined) 
			humanMsg.msgOpacity = parseFloat(msgOpacity);

		// Inject the message structure
		jQuery(appendTo)
			.append(jQuery('<div>').attr('id',humanMsg.msgID).addClass('humanMsg')
				.append('<p></p>')
			)
			.append(jQuery('<div>').attr('id',humanMsg.logID)
				.append(jQuery('<p>').html(logName))
				.append('<ul></ul>')
			);
		
		jQuery('#'+humanMsg.logID+' p').click(
			function() { jQuery(this).siblings('ul').slideToggle() }
		)
	},

	displayMsg: function(msg,cl) {
		if (msg == '')
			return;

		clearTimeout(humanMsg.t2);

		humanMsg.curClass = cl;

		// Inject message
		jQuery('#'+humanMsg.msgID).addClass(cl);
		jQuery('#'+humanMsg.msgID+' p').html(msg);
	
		// Show message
		jQuery('#'+humanMsg.msgID+'').show().animate({ opacity: humanMsg.msgOpacity}, 200, function() {
			humanMsg.addLogs(msg);
		});

		// Watch for mouse & keyboard in .5s
		humanMsg.t1 = setTimeout('humanMsg.bindEvents()', 700);
		// Remove message after 5s
		humanMsg.t2 = setTimeout('humanMsg.removeMsg()', 5000);
	},

	addLogs: function(msg) {
		jQuery('#'+humanMsg.logID)
			.show().children('ul').prepend('<li>'+msg+'</li>')	// Prepend message to log
			.children('li:first').slideDown(200)				// Slide it down
	
		if ( jQuery('#'+humanMsg.logID+' ul').css('display') == 'none') {
			jQuery('#'+humanMsg.logID+' p').animate({ bottom: 40 }, 200, 'linear', function() {
				jQuery(this).animate({ bottom: 0 }, 300, 'linear', function() { jQuery(this).css({ bottom: 0 }) })
			})
		}
	},

	bindEvents: function() {

	// Remove message if mouse is moved or key is pressed
		jQuery(window)
			.mousemove(humanMsg.removeMsg)
			.click(humanMsg.removeMsg)
			.keypress(humanMsg.removeMsg)
	},

	removeMsg: function() {
		// Unbind mouse & keyboard
		jQuery(window)
			.unbind('mousemove', humanMsg.removeMsg)
			.unbind('click', humanMsg.removeMsg)
			.unbind('keypress', humanMsg.removeMsg)

		// If message is fully transparent, fade it out
		if (jQuery('#'+humanMsg.msgID).css('opacity') >= (humanMsg.msgOpacity-0.05))
		{
			jQuery('#'+humanMsg.msgID).animate({ opacity: 0 }, 500, function() {
				jQuery(this).hide();
				jQuery(this).removeClass(humanMsg.curClass);
			 })
		}
	}
};

jQuery(document).ready(function($){
	humanMsg.setup();
})