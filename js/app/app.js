var createJSJCodeHighlight = (function($){

	var name_space = jsjCodeHighlightOptions.name_space; 
	var settings = jsjCodeHighlightOptions.settings;
	var initialized = false; 

	return function(){
		var $pre = $('pre');
		// Add class to all <pre>
		$pre.addClass(name_space);
		$pre.each(function(i){
			$this = $(this);
			// Wrap all lines around <span>
			var new_html = '', line_numbers_html = '', template;
			var old_html = $this.children('code').html(); 
			if ( old_html && $this.data('highlight') != 'true') {
				var code_class = $this.children('code').attr('class');
				// Split Content Into Lines
				var lines = old_html.match(/[^\n\r]+/g);
				for(var i = 0; i < lines.length; i++){
					// Add Html
					new_html += '<span class="line">' + lines[i] + '</span>\n';
				}		
				/*
				 * If Add Line Numbers
				 */
				if(settings.add_line_numbers){
					for(var i = 0; i < lines.length; i++){
						var index = i + 1;
						// Add line number
						line_numbers_html += '<span class="line-number">' + index + '</span>\n';
					}
					// Add Containers (Ugly template, right? ...not worth including a library just for this, though)
					template = '<div  class="' + name_space + ' ' + name_space + '-container ' + name_space + '-table_container">\
	<table>\
		<tbody>\
			<tr>\
				<td class="gutter"><pre class="line-numbers">' + line_numbers_html + '</pre></td>\
				<td class="code"><pre><code class="' + code_class + '">' + new_html + '</code></pre></td>\
			</tr>\
		</tbody>\
	</table>\
</div>';
				}
				/*
				 * If No New Line Numbers
				 */
				else {
					// Add Containers (Ugly template, right? ...not worth including a library just for this, though)
					template = '<div  class="' + name_space + ' ' + name_space + '-container ' + name_space + '-div_container">\
	<pre><code class="' + code_class + '">' + new_html + '</code></pre>\
</div>';
				}
				// Re-Append
				$this.children('code').html(new_html);
				$this.replaceWith(template);
			}
		});
		// Call Highlight JS 
		if(settings.tab_replacement){
			hljs.configure({tabReplace: settings.tab_number_ratio });
		}
		if(initialized){
			hljs.initHighlightingOnLoad();
			initialized = true;
		}
		else {
			$('pre code').each(function(i, e){
				if($(this).parent('pre').data('highlight') != 'true'){
					hljs.highlightBlock(e);
				}
			});
		}
		$('pre').each(function(i, e){
			$(this).data('highlight', 'true');
		});
	
	}
})(jQuery);

jQuery(window).resize(function(){
	createJSJCodeHighlight();
});
jQuery(document).ready(function(){
	createJSJCodeHighlight();
});


