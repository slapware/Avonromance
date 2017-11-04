$(document).ready(function(){

	$('#impulse_form_id').submit(function(){

		var validated = validate_impulse_form();

		if(validated){

			return true;

		}

		return false;

	});

	$('.faq_question').bind({

		click: function() {

			if($(this).attr('class') == 'faq_question active'){

				$(this).removeClass('active');

				$(this).next().hide('slow');


			}else{

				$(this).addClass('active');

				$(this).next().show('slow');

			}

		}

	});

});

function validate_impulse_form(){

	hide_form_errors();

	errors = [];

	if(jQuery.trim($("#impulse_form_id input[name=man_title]").val()) == ''){

		errors.push('man_title');

	}

	if(jQuery.trim($("#impulse_form_id input[name=man_name]").val()) == '' ){

		errors.push('man_name');

	}

	if($("#impulse_form_id select[name=length] option:selected").val() == '-1'){

		errors.push('length');

	}

	if($("#impulse_form_id select[name=genre] option:selected").val() == '-1'){

		errors.push('genre');

	}

	if($("#impulse_form_id select[name=subcategory] option:selected").val() == '-1'){

		errors.push('subcategory');

	}

	if($("#impulse_form_id select[name=period] option:selected").val() == '-1'){

		errors.push('period');

	}

	if(jQuery.trim($("#impulse_form_id textarea[name=synopsis]").val()) == ''){

		$('#synopsis-error').html('Please enter a synopsis');

		errors.push('synopsis');

	}else if(word_count(jQuery.trim($("#impulse_form_id textarea[name=synopsis]").val())) > 200){

		$('#synopsis-error').html('The synopsis word count must be less than 200');

		errors.push('synopsis');

	}

	if(jQuery.trim($("#impulse_form_id textarea[name=best_scene]").val()) == ''){

		$('#best_scene-error').html('Please post the best scene or the first 1000 words of your manuscript');

		errors.push('best_scene');

	}else if(word_count(jQuery.trim($("#impulse_form_id textarea[name=best_scene]").val())) > 1000){

		$('#best_scene-error').html('The best scene word count must be less than 1000');

		errors.push('best_scene');

	}

	if(jQuery.trim($("#impulse_form_id textarea[name=query_letter]").val()) == ''){

		$('#query_letter-error').html('Please post your query letter');

		errors.push('query_letter');

	}else if(word_count(jQuery.trim($("#impulse_form_id textarea[name=query_letter]").val())) > 750){

		$('#query_letter-error').html('The query letter field word count must be less than 750');

		errors.push('query_letter');

	}

	if(jQuery.trim($("#impulse_form_id input[name=manuscript_file]").val()) == ''){

		errors.push('manuscript_file');

	}

	if(errors.length > 0){

		show_form_errors(errors);

		return false;

	}

	return true;

}

function word_count(text_val){

    var number = 0;

    var matches = text_val.match(/\s+/g);

    if(matches) {

        number = matches.length + 1;

    }

  	return number;

}


function validate_field(field_name){

	if($("#impulse_form_id input[name="+field_name+"]")){

		if($("#impulse_form_id input[name="+field_name+"]")){

			if(jQuery.trim($("#impulse_form_id input[name="+field_name+"]").val()).length > 0){

				return true;

			}

		}

		if($("#impulse_form_id select[name="+field_name+"]")){

			alert(field_name);

			if($("#impulse_form_id select[name="+field_name+"] option:selected").val() != '-1'){

				return true;

			}

		}



	}

	return false;

}

function hide_form_errors(req_fields){

	var req_fields = ["man_title","man_name","length","genre","subcategory","period","synopsis","best_scene","manuscript_file","query_letter"];

	jQuery.each(req_fields, function(i, l) {

		if($('#'+l+'-error')){

			hide($('#'+l+'-error'));

		}

	});

}

function show_form_errors(req_fields){

	jQuery.each(req_fields, function(i, l){

		if($('#'+l+'-error')){

			show($('#'+l+'-error'));

		}

	});

}

function show(object){

	object.fadeIn('fast');

}

function hide(object){

	object.fadeOut('fast');

}