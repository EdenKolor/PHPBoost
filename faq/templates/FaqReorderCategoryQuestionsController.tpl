# IF C_QUESTIONS #
<script>
<!--
	var FaqQuestions = function(id){
		this.id = id;
		this.questions_number = {QUESTIONS_NUMBER};
	};

	FaqQuestions.prototype = {
		init_sortable : function() {
			jQuery("ul#questions-list").sortable({
				handle: '.sortable-selector',
				placeholder: '<div class="dropzone">' + ${escapejs(LangLoader::get_message('position.drop_here', 'common'))} + '</div>'
			});
		},
		serialize_sortable : function() {
			jQuery('#tree').val(JSON.stringify(this.get_sortable_sequence()));
		},
		get_sortable_sequence : function() {
			var sequence = jQuery("ul#questions-list").sortable("serialize").get();
			return sequence[0];
		},
		change_reposition_pictures : function() {
			sequence = this.get_sortable_sequence();
			var length = sequence.length;
			for(var i = 0; i < length; i++)
			{
				if (jQuery('#list-' + sequence[i].id).is(':first-child'))
					jQuery("#move-up-" + sequence[i].id).hide();
				else
					jQuery("#move-up-" + sequence[i].id).show();

				if (jQuery('#list-' + sequence[i].id).is(':last-child'))
					jQuery("#move-down-" + sequence[i].id).hide();
				else
					jQuery("#move-down-" + sequence[i].id).show();
			}
		}
	};

	var FaqQuestion = function(id, faq_questions){
		this.id = id;
		this.FaqQuestions = faq_questions;

		if (FaqQuestions.questions_number > 1)
			FaqQuestions.change_reposition_pictures();
	};

	FaqQuestion.prototype = {
		delete : function() {
			if (confirm(${escapejs(LangLoader::get_message('confirm.delete', 'status-messages-common'))}))
			{
				jQuery.ajax({
					url: '${relative_url(FaqUrlBuilder::ajax_delete())}',
					type: "post",
					dataType: "json",
					data: {'id' : this.id, 'token' : '{TOKEN}'},
					success: function(returnData) {
						if(returnData.code > 0) {
							jQuery("#list-" + returnData.code).remove();
							# IF NOT C_DISPLAY_TYPE_ANSWERS_HIDDEN #
							jQuery("#title-question-" + returnData.code).remove();
							# ENDIF #

							FaqQuestions.init_sortable();
							FaqQuestions.questions_number--;

							FaqQuestions.change_reposition_pictures();
							if (FaqQuestions.questions_number == 1) {
								jQuery("#position-update-button").hide();
							} else if (FaqQuestions.questions_number == 0) {
								jQuery("#position-update-form").hide();
								# IF NOT C_DISPLAY_TYPE_ANSWERS_HIDDEN #
								jQuery("#questions-titles-list").hide();
								# ENDIF #
								jQuery("#no-item-message").show();
							}
						}
					}
				});
			}
		}
	};

	var FaqQuestions = new FaqQuestions('questions-list');
	jQuery(document).ready(function() {
		FaqQuestions.init_sortable();
		jQuery('li.sortable-element').on('mouseout',function(){
			FaqQuestions.change_reposition_pictures();
		});
	});
-->
</script>
# ENDIF #
# INCLUDE MSG #
<section id="module-faq">
	<header>
		<h1>
			<a href="${relative_url(SyndicationUrlBuilder::rss('faq', ID_CAT))}" title="${LangLoader::get_message('syndication', 'common')}"><i class="fa fa-syndication"></i></a>
			{@module_title}# IF NOT C_ROOT_CATEGORY # - {CATEGORY_NAME}# ENDIF # # IF IS_ADMIN #<a href="{U_EDIT_CATEGORY}" title="${LangLoader::get_message('edit', 'common')}"><i class="fa fa-edit small"></i></a># ENDIF #
		</h1>
		# IF C_CATEGORY_DESCRIPTION #
			<div class="cat-description">
				{CATEGORY_DESCRIPTION}
			</div>
		# ENDIF #
	</header>

	# IF C_QUESTIONS #
		# IF NOT C_DISPLAY_TYPE_ANSWERS_HIDDEN #
		<div id="questions-titles-list">
				<ol>
				# START questions #
					<li id="title-question-{questions.ID}"# IF questions.C_NEW_CONTENT # class="new-content"# ENDIF #>
						<a href="#question{questions.ID}">{questions.QUESTION}</a>
					</li>
				# END questions #
				</ol>
				<hr />
		</div>
		# ENDIF #

		<div class="content elements-container">
			<form action="{REWRITED_SCRIPT}" method="post" id="position-update-form" onsubmit="FaqQuestions.serialize_sortable();" class="faq-reorder-form">
				<fieldset id="questions-management">
					<ul id="questions-list" class="sortable-block">
						# START questions #
						<li class="sortable-element# IF questions.C_NEW_CONTENT # new-content# ENDIF #" id="list-{questions.ID}" data-id="{questions.ID}">
							<div class="sortable-selector" title="${LangLoader::get_message('position.move', 'common')}"></div>
							<div class="sortable-title">
								<h3 class="question-title"><span>{questions.QUESTION}</span></h3>
							</div>
							<div class="sortable-actions">
								# IF C_MORE_THAN_ONE_QUESTION #
								<a href="" title="${LangLoader::get_message('position.move_up', 'common')}" id="move-up-{questions.ID}" onclick="return false;"><i class="fa fa-arrow-up fa-fw"></i></a>
								<a href="" title="${LangLoader::get_message('position.move_down', 'common')}" id="move-down-{questions.ID}" onclick="return false;"><i class="fa fa-arrow-down fa-fw"></i></a>
								# ENDIF #
								<a href="{questions.U_EDIT}" title="${LangLoader::get_message('edit', 'common')}"><i class="fa fa-edit fa-fw"></i></a>
								<a href="" onclick="return false;" title="${LangLoader::get_message('delete', 'common')}" id="delete-{questions.ID}"><i class="fa fa-delete fa-fw"></i></a>
							</div>
							<div class="spacer"></div>
							<script>
							<!--
							jQuery(document).ready(function() {
								var faq_question = new FaqQuestion({questions.ID}, FaqQuestions);

								jQuery('#delete-{questions.ID}').on('click',function(){
									faq_question.delete();
								});

								if (FaqQuestions.questions_number > 1) {
									jQuery('#move-up-{questions.ID}').on('click',function(){
										var li = jQuery(this).closest('li');
										li.insertBefore( li.prev() );
										FaqQuestions.change_reposition_pictures();
									});
									jQuery('#move-down-{questions.ID}').on('click',function(){
										var li = jQuery(this).closest('li');
										li.insertAfter( li.next() );
										FaqQuestions.change_reposition_pictures();
									});
								}
							});
							-->
							</script>
						</li>
						# END questions #
					</ul>
				</fieldset>
				# IF C_MORE_THAN_ONE_QUESTION #
				<fieldset class="fieldset-submit" id="position-update-button">
					<button type="submit" name="submit" value="true" class="submit">${LangLoader::get_message('position.update', 'common')}</button>
					<input type="hidden" name="token" value="{TOKEN}">
					<input type="hidden" name="tree" id="tree" value="">
				</fieldset>
				# ENDIF #
			</form>
		</div>
	# ENDIF #
	# IF NOT C_HIDE_NO_ITEM_MESSAGE #
		<div id="no-item-message"# IF C_QUESTIONS # style="display: none;"# ENDIF #>
			<div class="center">
				${LangLoader::get_message('no_item_now', 'common')}
			</div>
		</div>
	# ENDIF #

	<footer></footer>
</section>
