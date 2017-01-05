/* Copy and paste in footer for active Placeholder text in all older IE browsers */
			
				<script type="text/javascript">
					$(function() {
						var input = document.createElement("input");
						if(('placeholder' in input)==false) { 
							$('[placeholder]').focus(function() {
								var i = $(this);
								if(i.val() == i.attr('placeholder')) {
									i.val('').removeClass('placeholder');
									if(i.hasClass('password')) {
										i.removeClass('password');
										this.type='password';
									}			
								}
							}).blur(function() {
								var i = $(this);	
								if(i.val() == '' || i.val() == i.attr('placeholder')) {
									if(this.type=='password') {
										i.addClass('password');
										this.type='text';
									}
									i.addClass('placeholder').val(i.attr('placeholder'));
								}
							}).blur().parents('form').submit(function() {
								$(this).find('[placeholder]').each(function() {
									var i = $(this);
									if(i.val() == i.attr('placeholder'))
										i.val('');
								})
							});
						}
					}); 
				</script>