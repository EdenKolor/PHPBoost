# INCLUDE MSG #
# IF C_MORE_THAN_ONE_SOCIAL_NETWORK #
<script>
<!--
var SocialNetworks = function(id){
	this.id = id;
};

SocialNetworks.prototype = {
	init_sortable : function() {
		jQuery("ul#social_networks_list").sortable({
			handle: '.sortable-selector',
			placeholder: '<div class="dropzone">' + ${escapejs(LangLoader::get_message('position.drop_here', 'common'))} + '</div>'
		});
	},
	serialize_sortable : function() {
		jQuery('#tree').val(JSON.stringify(this.get_sortable_sequence()));
	},
	get_sortable_sequence : function() {
		var sequence = jQuery("ul#social_networks_list").sortable("serialize").get();
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

var SocialNetwork = function(id, social_networks){
	this.id = id;
	this.SocialNetworks = social_networks;
	
	this.SocialNetworks.change_reposition_pictures();
};

SocialNetwork.prototype = {
	change_display : function() {
		jQuery("#change-display-" + this.id).html('<i class="fa fa-spin fa-spinner"></i>');
		jQuery.ajax({
			url: '/SocialNetworks/index.php?url=/config/change_display',
			type: "post",
			dataType: "json",
			data: {'id' : this.id, 'token' : '{TOKEN}'},
			success: function(returnData){
				if (returnData.id != '') {
					if (returnData.display) {
						jQuery("#change-display-" + returnData.id).html('<i class="fa fa-eye" title="{@admin.display_share_link}"></i>');
					} else {
						jQuery("#change-display-" + returnData.id).html('<i class="fa fa-eye-slash" title="{@admin.hide_share_link}"></i>');
					}
				}
			}
		});
	}
};

var SocialNetworks = new SocialNetworks('social_networks_list');
jQuery(document).ready(function() {
	SocialNetworks.init_sortable();
	jQuery('li.sortable-element').on('mouseout',function(){
		SocialNetworks.change_reposition_pictures();
	});
});
-->
</script>
<form action="{REWRITED_SCRIPT}" method="post" onsubmit="SocialNetworks.serialize_sortable();">
	<fieldset id="social_networks_management">
	<legend>{@admin.order.manage}</legend>
		<ul id="social_networks_list" class="sortable-block">
			# START social_networks_list #
				<li class="sortable-element" id="list-{social_networks_list.ID}" data-id="{social_networks_list.ID}">
					<div class="sortable-selector" title="${LangLoader::get_message('position.move', 'common')}"></div>
					<div class="sortable-title">
						<span class="social-connect {social_networks_list.CSS_CLASS}"><i class="fab fa-fw fa-{social_networks_list.ICON_NAME}"></i></span>
						{social_networks_list.NAME}
						<div class="sortable-actions">
							<a href="" title="${LangLoader::get_message('position.move_up', 'common')}" id="move-up-{social_networks_list.ID}" onclick="return false;"><i class="fa fa-arrow-up"></i></a>
							<a href="" title="${LangLoader::get_message('position.move_down', 'common')}" id="move-down-{social_networks_list.ID}" onclick="return false;"><i class="fa fa-arrow-down"></i></a>
							# IF social_networks_list.C_SHARING_CONTENT #<a href="" onclick="return false;" id="change-display-{social_networks_list.ID}"><i # IF social_networks_list.C_DISPLAY #class="fa fa-eye" title="{@admin.display_share_link}"# ELSE #class="fa fa-eye-slash" title="{@admin.hide_share_link}"# ENDIF #></i></a># ELSE #<i class="fa fa-ban" title="{@admin.no_sharing_content_url}"></i># ENDIF #
						</div>
					</div>
					<div class="spacer"></div>
					<script>
					<!--
					jQuery(document).ready(function() {
						var social_network = new SocialNetwork('{social_networks_list.ID}', SocialNetworks);
						
						jQuery("#change-display-{social_networks_list.ID}").on('click',function(){
							social_network.change_display();
						});
						
						jQuery("#move-up-{social_networks_list.ID}").on('click',function(){
							var li = jQuery(this).closest('li');
							li.insertBefore( li.prev() );
							SocialNetworks.change_reposition_pictures();
						});
						
						jQuery("#move-down-{social_networks_list.ID}").on('click',function(){
							var li = jQuery(this).closest('li');
							li.insertAfter( li.next() );
							SocialNetworks.change_reposition_pictures();
						});
					});
					-->
					</script>
				</li>
			# END social_networks_list #
		</ul>
	</fieldset>
	<fieldset class="fieldset-submit">
		<button type="submit" name="order_manage_submit" value="true" class="submit">${LangLoader::get_message('position.update', 'common')}</button>
		<input type="hidden" name="token" value="{TOKEN}">
		<input type="hidden" name="tree" id="tree" value="">
	</fieldset>
</form>
# ENDIF #
# INCLUDE FORM #