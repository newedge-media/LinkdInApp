(function($) {
$.entwine('ss', function($) {

	$('.cms #Form_ItemEditForm .Actions button.gridfield-better-buttons-done').entwine({
		
		Toggled: false,

		onadd: function() {
			var text = this.data('confirmtext');
			this.before("&nbsp; <a class='gridfield-better-buttons-undone ss-ui-button' href='javascript:void(0)'>"+text+"</a>");
			this._super();
		},

		onclick: function(e) {
			e.preventDefault();
			
			if(this.getToggled()) {
				return this._super(e);
			}
			this.toggleText();			
			$('.gridfield-better-buttons-undone').show();
		},


		toggleText: function() {
			var text = this.find(".ui-button-text").text();
			this.find(".ui-button-text").text(this.data('toggletext'));
			this.data('toggletext', text);
			this.setToggled(!this.getToggled());
		}
	});


	$('.gridfield-better-buttons-undone').entwine({

		onclick: function(e) {			
			e.preventDefault();
			$('.gridfield-better-buttons-done').toggleText();
			this.hide();
		}
	})


});
})(jQuery);