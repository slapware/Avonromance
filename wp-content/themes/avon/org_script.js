$(document).ready(function() {

var cover_images = function() {
	$("img.cover").each(function() {
		var $this = $(this);
		var $parent = $this.parent();
		
		cover_image($this, $parent);
	});
};
var cover_image = function($this, $parent) {
	$this.css({
		"width":"auto",
		"max-width":"none",
		"height":"auto",
		"max-height":"none",
		"position": "relative"
	});
	
	var parent_ar = $parent.width() / $parent.height();
	var ar = $this.width() / $this.height();
	
	if (parent_ar > ar)
	{
		$this.css({
			"width":"100%",
		});
		var offset_top = Math.round( ($this.height() - $parent.height()) / 2 );
		$this.css("top", -1 * offset_top);
	}
	else
	{
		$this.css({
			"height":"100%"
		});
		var offset_left = Math.round( ($this.width() - $parent.width()) / 2 );
		$this.css("left", -1 * offset_left);
	}
	
};
var slide_images_right = function(select) {
	   var active = $('.featured-content .image.active');
	   var previous = $('.featured-content .image.previous');
	   var next = $('.featured-content .image.next');

	   if ( next.hasClass('last') ) {
		$('.featured-next').fadeOut('fast').addClass('hidden');
	   }

	   if ($('.featured-previous').hasClass('hidden')) {
		$('.featured-previous').removeClass('hidden').fadeIn('fast');
	   }

	   next = next.next('.image');
	   next.addClass('next').removeClass('hidden');
	   active.removeClass('active').addClass('previous');
	   select.removeClass('next').addClass('active');
	   previous.addClass('hidden').removeClass('previous');
	
		if ( ! $('.featured-content .image.next').length > 0 ) {
			$('.featured-next').fadeOut('fast').addClass('hidden');
		}

	   show_slider_content(select);
}

var slide_images_left = function(select) {
	var active = $('.featured-content .image.active');
	   var previous = $('.featured-content .image.previous');
	   var next = $('.featured-content .image.next');

	   if (previous.hasClass('first')) {
		$('.featured-previous').fadeOut('fast').addClass('hidden');
	   }

	   if ($('.featured-next').hasClass('hidden')) {
		$('.featured-next').removeClass('hidden').fadeIn('fast');
	   }

	   previous = previous.prev();
	   previous.addClass('previous').removeClass('hidden');
	   active.removeClass('active').addClass('next');
	   select.removeClass('previous').addClass('active');
	   next.addClass('hidden').removeClass('next');

	   show_slider_content(select);
	   
}

var show_slider_content = function(select) {
	var href = select.find('a').attr('href');
	   $('.featured-navigation a.active').removeClass('active');
	   $('.featured-navigation a#' + href).addClass('active');

	   $('.featured-content .info .active').addClass('hidden').removeClass('active');
	   $('.featured-content .info #' + href).addClass('active').removeClass('hidden');
}


$('.featured-navigation a').click(function() {
			return false;
		});
		
$('.featured-next').click(function() {
	var select = $('.featured-content .image.next');
	
	if (! select) {
		return false;
	}
	
	console.log(select);
	
	slide_images_right(select);
});

$('.featured-previous').click(function() {
	var select = $('.featured-content .image.previous');
	
	if (! select) {
		return false;
	}
	
	slide_images_left(select);
});

$('.featured-content .image a').click(function() {
	var select = $(this).closest('.image');
	
	if ( select.hasClass('active') ) {
		window.location = $(this).attr('data-href');
		return false;
	}

	if ( select.hasClass('next') ) {
		slide_images_right(select); 
	}
	
	if ( select.hasClass('previous') ) {
		slide_images_left(select);
	}
	
	return false;
});


if ( ! $('.featured-content .image.next').length > 0 ) {
	$('.featured-next').addClass('hidden');
}
	
	
$(window).load(function() {
	cover_images();
});



$('.signup-form form').submit(function() {
		
	var email = $('.signup-form form input').val();
	
	var month = parseInt($('.signup-form form select.month').val());
	var day = parseInt($('.signup-form form select.day').val());
	var year = parseInt($('.signup-form form select.year').val());
	
	var today = new Date();
	
	var birthday = new Date(year, month, day);
	var min_age = new Date(today.getFullYear() - 13, today.getMonth(), today.getDate());

	if (birthday > min_age && ! $('.parent-email input').val()) {
		$('.parent-email').removeClass('hidden');
		$('.parent-email input').attr('required', 'required');
		return false;
	}
	else 
	{
		$('.signup-form').append('<p style="text-align:right;clear:both;">Thank you. Your email was submitted.</p>');
		
		$.post('http://services.harpercollins.com/widgets/subscription/subscribe.aspx', $('.signup-form form').serialize(), function(resp) {
				console.log(resp);
		});
		
		return false;
	}

});
		
$('.vote-link').click(function() {
	var $this = $(this);
	var slug = $this.attr('data-slug');
	var votes = $('.vote-count[data-slug=' + slug + ']');
	votes.fadeOut();
	$.getJSON("/vote.php?q=" + slug, function(resp) {
			if (resp.ok)
			{
					votes.text( parseInt(votes.text()) + 1 );
			}
			votes.fadeIn();
	});

	return false;
});
		
$('.book-vote-link').click(function() {
	var $this = $(this);
	var slug = $this.attr('data-slug');
	var votes = $this.find('.book-vote-count[data-slug=' + slug + ']');
	votes.fadeOut();
	$.getJSON("/vote.php?q=" + slug, function(resp) {
		if (resp.ok)
		{
				votes.text( parseInt(votes.text()) + 1 );
		}
		votes.fadeIn();
	});

	return false;
});


$('.slide .slide-next').click(function() {
				var current = $('.slide .book').not('.hidden');
				var next = $('.slide .book.hidden');
				$(current).animate({opacity:0}, 100, function() {
					$(next).removeClass('hidden').css({"opacity":"0"});
					$(current).addClass('hidden');
					$(next).animate({opacity:1}, 300);
					$('.slide .slide-prev').removeClass('hidden');
					$('.slide .slide-next').addClass('hidden');
				});
				return false;
		});
		
		$('.slide .slide-prev').click(function() {
				var current = $('.slide .book').not('.hidden');
				var next = $('.slide .book.hidden');
				$(current).animate({opacity:0}, 100, function() {
					$(next).removeClass('hidden').css({"opacity":"0"});
					$(current).addClass('hidden');
					$(next).animate({opacity:1}, 300);
					$('.slide .slide-next').removeClass('hidden');
					$('.slide .slide-prev').addClass('hidden');
				});
				return false;
		});
		
		$('.slide-vert .slide-next').click(function() {
				var start = $('.slide-vert .book.active');
				var next0 = $(start).last().next();
				var next1 = $(next0).next();
				var next2 = $(next1).next();
				var next = $.merge(next0, next1);
				next = $.merge(next, next2);
				$(start).animate({opacity:0}, 300, function() {
					$(next).removeClass('hidden').addClass('active').css({"opacity":"0"});
					$(this).addClass('hidden').removeClass('active');
					$(next).animate({opacity:1}, 300);
				});
				if ($('.slide-vert .slide-prev').hasClass('hidden')) {
					$('.slide-vert .slide-prev').removeClass('hidden');
				}
				if ( ! $(next).last().next().hasClass('book')) {
					$('.slide-vert .slide-next').addClass('hidden');
				}
				return false;
		});
		
		$('.slide-vert .slide-prev').click(function() {
				var start = $('.slide-vert .book.active');
				var next0 = $(start).first().prev();
				var next1 = $(next0).prev();
				var next2 = $(next1).prev();
				var next = $.merge(next0, next1);
				next = $.merge(next, next2);
				$(start).animate({opacity:0}, 300, function() {
					$(next).removeClass('hidden').addClass('active').css({"opacity":"0"});
					$(this).addClass('hidden').removeClass('active');
					$(next).animate({opacity:1}, 300);
				});
				if ( ! $(next).last().prev().hasClass('book')) {
					$('.slide-vert .slide-prev').addClass('hidden');
				}
				if ($(next).length > 2) {
					$('.slide-vert .slide-next').removeClass('hidden');
				}
				return false;
		});
});