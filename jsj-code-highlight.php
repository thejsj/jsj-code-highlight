<?php
/**
 * @package JSJ_Code_Highlight
 * @version 0.1
 */
/*
Plugin Name: JSJ Code Hightlight
Plugin URI: http://thejsj.com
Description: This plugin Blabh blah blah
Author: Jorge Silva Jetter
Version: 0.1
Author URI: http://thejsj.com
*/

/* 

If Line Number are not being included, add correct classes to DOM. 

Add CSS for no line numbers



Add Credits ... anything else? 

*/

$jsj_code_highlight = new JSJCodeHighlight();

// Init Set All Plugin Variables
add_action('init', array($jsj_code_highlight, 'init') );

// Call register settings function
add_action( 'admin_init', array($jsj_code_highlight, 'register_settings') );

// Add JS scripts
add_action( 'wp_enqueue_scripts', array($jsj_code_highlight, 'queque_scripts') );

// Add CSS For Admin
add_action( 'admin_enqueue_scripts', array($jsj_code_highlight, 'admin_queque_scripts') );

// Hook for adding admin menus
add_action('admin_menu',  array($jsj_code_highlight, 'add_menu_page'));

// Add JS code to the Footer   
add_action('wp_footer', array($jsj_code_highlight, 'footer_actions'), 30); //Enqueued scripts are executed at priority level 20.\

// Add Body Class For Several User Settings
add_filter('body_class',array($jsj_code_highlight, 'body_class'));

class JSJCodeHighlight {

	private $name_space = 'jsj_code_highlight';
	private $directory = 'jsj-code-jsj_code_highlight';

	// Text Fields
	private $settings_page_title = 'JSJ Code Highlight Settings';

	// Settings
	private $settings = Array();
	public $all_styles = array(
		'monokai_sublime'  => array(
			'name' => 'monokai_sublime', 
			'title' => 'Monakai Sublime'
		),
		'solarized_dark' => array(
			'name' => 'solarized_dark', 
			'title' => 'Solarized Dark'
		),
		'github'  => array(
			'name' => 'github', 
			'title' => 'Github'
		),
		'tomorrow'  => array(
			'name' => 'tomorrow', 
			'title' => 'Tommorow'
		),		
	);
	public $all_fonts = array(
		'inconsolta' => array(
			'name'  => 'inconsolta',
			'title' => 'Inconsolata',
			'url'   => 'http://fonts.googleapis.com/css?family=Inconsolata:400,700'
		),
		'droid_sans_mono' => array(
			'name'  => 'droid_sans_mono',
			'title' => 'Droid Sans Mono',
			'url'   => 'http://fonts.googleapis.com/css?family=Inconsolata:400,700'
		),
		'source_code_pro' => array(
			'name'  => 'source_code_pro',
			'title' => 'Source Code Pro',
			'url'   => 'http://fonts.googleapis.com/css?family=Source+Code+Pro:400,700'
		),
		'deja_vu_sans_mono' => array(
			'name'  => 'deja_vu_sans_mono',
			'title' => 'Deja Vu Sans Mono',
			'url'   => null // To be populated later
		),
		'web_fonts' => array(
			'name'  => 'web_fonts',
			'title' => 'Web Fonts (No External Fonts)',
			'url'   => false
		)
	);

	/**
	 * Populate a couple of variables in the 
	 * 
	 * @return void
	 */
	public function __construct(){
		$this->all_fonts['deja_vu_sans_mono']['url'] = plugins_url( 'css/dejavu-sans-mono.css' , __FILE__ );
		$this->settings_name_space = $this->name_space . '-settings';
	}

	/**
	 * Init Plugin and get all settings
	 * 
	 * @return void
	 */
	public function init(){
		global $jsj_code_highlight_options;

		// Include Settings Files
		require( plugin_dir_path( __FILE__ ) . '/jsj-code-highlight-settings.php');

		// Populate All Options
		$this->settings = $jsj_code_highlight_options;
		foreach($this->settings as $key => $setting){
			// Set Setting Name Space
			$this->settings[$key]->name_space = $this->name_space . '-' . $setting->name;

			// Get Value
			$this->settings[$key]->value = get_option($setting->name_space , $setting->default);
			if($this->settings[$key]->value == ''){
				$this->settings[$key]->value = 0;
			}
		}

		// Set Options According to Settings
		$this->theme = $this->all_styles[$this->settings['style']->value];
		$this->font = $this->all_fonts[$this->settings['font']->value];
	}

	/**
	 * Add default settings to the WordPress database
	 * 
	 * @return void
	 */
	public function register_settings(){	
		// Register our settings`
		foreach($this->settings as $key => $setting){
			register_setting( $this->settings_name_space , $setting->name_space );
		}
	}
	
	/**
	 * This will create a menu item under the option menu
	 * @see http://codex.wordpress.org/Function_Reference/add_options_page
	 */
	public function add_menu_page(){
		add_options_page(__( 'JSJ Code Highlight Options', 'jsj_code_highlight' ), 'JSJ Code Highlight', 'manage_options', $this->name_space, array($this, 'options_page'));
	}

	/*
	 * Queue scripts
	 * 
	 * @return void
	 */
	public function queque_scripts(){
		if(!wp_script_is('jquery')){
			wp_enqueue_script( 'jquery' );
		}
		// Enqueue Highlight.js
		wp_enqueue_script(
			'highlightPack',
			// plugins_url( 'js/highlight.pack.js' , __FILE__ ),
			plugins_url( 'js/highlight.min.js' , __FILE__ ),
			array('jquery'), // Deps
			"", // Version
			true //
		);
		// Only enqueue the style file we need
		wp_enqueue_style(
			$this->name_space . '-' . $this->theme['name'] . "_theme", 
			plugins_url( 'styles/' . $this->theme['name'] . '.css' , __FILE__ )
		);
		// Enqueue Additional Styles
		if($this->settings['include_additional_styles']){
			wp_enqueue_style(
				$this->name_space . "_client_style", 
				plugins_url( 'css/client-style.css' , __FILE__ )
			);
		}
		// Enqueue Fonts
		if($this->font['url']){ // Only include CSS file if necessary
			wp_enqueue_style(
				$this->name_space . '-' . $this->font['name'] . "_font_style", 
				$this->font['url']
			);
		}
	}

	/*
	 * Queue styles for admin page
	 * 
	 * @return void
	 */
	public function admin_queque_scripts(){
		wp_enqueue_style(
			$this->name_space . "_admin_style", 
			plugins_url( 'css/admin-style.css' , __FILE__ )
		);
	}

	/*
	 * Add a body class to be used by our CSS file) which determines several CSS options such as font-family
	 * 
	 * @return void
	 */
	public function body_class($class){
		$class[count($class)] = $this->name_space;
		$class[count($class)] = $this->name_space . '-' . $this->theme['name'];
		$class[count($class)] = $this->name_space . '-' . $this->font['name'];
		$class[count($class)] = $this->strinfy_boolen($this->settings['include_additional_styles'], $this->name_space . '-');
		$class[count($class)] = $this->strinfy_boolen($this->settings['add_line_numbers'], $this->name_space . '-');
		$class[count($class)] = $this->strinfy_boolen($this->settings['tab_replacement'], $this->name_space . '-');
		return  $class;
	}

	/*
	 * Retruns a string with wether this property is true or not. Prepends a string
	 * 
	 * @return str
	 */
	private function strinfy_boolen($property, $prepend = ''){
		if($property->value){
			return $prepend . $property->name;
		}
		else {
			return $prepend . 'no-' . $property->name;
		}
	}

	/*
	 * Markup for the Options Page
	 * 
	 * @return void
	 */
	public function options_page(){
		// Reset Settings to default if set
		if($_POST && isset($_POST[$this->name_space . '_switch_default']) && $_POST[$this->name_space . '_switch_default']) { 
			foreach($this->settings as $key => $setting){
				// Update Value in the database
				update_option($setting->name_space , $setting->default);
				// Update Value in our current array of settings (in order for them to be displayed correctly)
				$this->settings[$key]->value =  $setting->default;
			}
			echo('<div class="updated settings-error"><p>' . __( 'Your settings have been reset.', 'jsj_code_highlight' ) . '</p></div>');
		}
		?>

		<style>
			.<?php echo $this->name_space; ?> .<?php echo $this->name_space; ?>_styles_container input[type=radio]:checked + img{
				border-color: <?php echo $this->get_admin_color(); ?>; /* Get WordPress colors set by user */
			}
		</style>

		<div id='<?php echo $this->name_space;?>_container' class="<?php echo $this->name_space;?>">

			<!-- Main Title -->
			<h2 class="<?php echo $this->name_space;?>_title"><?php echo $this->settings_page_title; ?></h2>

			<!-- Start Form -->
			<form method="post" action="options.php" id="<?php echo $this->name_space;?>_styles_container" class="<?php echo $this->name_space;?> <?php echo $this->name_space;?>_form">
				<?php settings_fields( $this->settings_name_space ); ?>

				<!-- Visual Styles-->
				<h3><?php _e('Visual Theme', 'jsj_code_highlight'); ?></h3>
				<div class="<?php echo $this->name_space;?> <?php echo $this->name_space;?>_styles_container">
					<?php $style_setting = $this->settings['style']; ?>
					<?php foreach($this->all_styles as $style): ?>
						<label 
							class="<?php echo $this->name_space;?>_style_image_container <?php echo $this->name_space;?>_theme_label" 
							for="<?php echo $style['name']; ?>"
						>
							<input 
								id="<?php echo $style['name']; ?>" 
								type="radio" 
								name="<?php echo $style_setting->name_space; ?>" 
								value="<?php echo $style['name']; ?>" 
								<?php if ( $style['name'] == $style_setting->value ) echo 'checked="checked"'; ?>
							/>
							<img 
								src="<?php echo plugins_url( 'images/' . $style['name'] . '.png' , __FILE__ ); ?>" 
								alt="<?php echo $style['title']; ?>" 
							/>
							<p><?php echo $style['title']; ?></p>
						</label>
					<?php endforeach; ?>
				</div>

				<!-- More Options -->
				<h3><?php _e('Other Options', 'jsj_code_highlight'); ?></h3>
				<div class='<?php echo $this->name_space;?> <?php echo $this->name_space;?>_other_options'>
					<table>
					<?php $this->parse_setting_field($this->settings['font'], $this->all_fonts); ?>
					<?php $this->parse_setting_field($this->settings['include_additional_styles']); ?>
					<?php $this->parse_setting_field($this->settings['add_line_numbers']); ?>
					<?php $this->parse_setting_field($this->settings['tab_replacement']); ?>
					<?php $this->parse_setting_field($this->settings['tab_number_ratio']); ?>
					</table>
				</div>

				<!-- Submit -->
				<?php submit_button(); ?>

			</form>

			<!-- Revert to Defaults -->
			<form name="<?php echo $this->name_space; ?>_default" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                <input type="hidden" name="<?php echo $this->name_space; ?>_switch_default" value="1">  
                <input type="submit" name="Submit" value="<?php _e( 'Reset Settings to Default', 'jsj_code_highlight' ); ?>" />
            </form>

			<!-- Instructions on Formatting -->
			<h3><?php _e('Instructions on Formatting', 'jsj_code_highlight'); ?></h3>
			<div class='<?php echo $this->name_space;?> <?php echo $this->name_space;?>_instructions'>
				<p><?php _e('Make sure you add all your code in the following format:', 'jsj_code_highlight'); ?></p>
				<p><?php _e('1. Put a <strong>&lt;pre&gt;</strong> tag before and after all your code snippets.', 'jsj_code_highlight'); ?><p>
				<p><?php _e('2. Put a <strong>&lt;code&gt;</strong> tag inside of those <strong>&lt;pre&gt;</strong> tags.', 'jsj_code_highlight'); ?><p>
				<p><?php _e('3. Add a class with the desired language to your <strong>&lt;code&gt;</strong> tag.', 'jsj_code_highlight'); ?><p>
<pre><code class='html'>&lt;pre&gt;
    &lt;code class='javascript'&gt;
        console.log('<?php _e('Hello World', 'jsj_code_highlight'); ?>');
    &lt;/code&gt;
&lt;/pre&gt;
</code></pre><br/>
			</div>

			<!-- Credit and Links -->
			<h3><?php _e('Credit and Links', 'jsj_code_highlight'); ?></h3>
			<p><?php echo sprintf( __('Plugin by %sJorge Silva-Jetter%s', 'jsj_code_highlight' ), '<a href="http://thejsj.com">', '</a>'); ?></p>
			<p><?php echo sprintf( __('Built with %sHighlight.js%s', 'jsj_code_highlight' ), '<a href="http://highlightjs.org/">', '</a>'); ?></p>
			<p><?php echo sprintf( __('Unashamedly inspired by  %sOctopress%s', 'jsj_code_highlight' ), '<a href="http://octopress.org/">', '</a>'); ?></p>
		</div>
		<?php
	}

	/*
	 * Get a specific admin color user user preferences and the WP array of colors
	 *
	 * @return string
	 */
	private function get_admin_color($key = 3){
		if(!isset($this->colors)){
			global $_wp_admin_css_colors;
			$current_color = get_user_option( 'admin_color' );
			if($current_color && $_wp_admin_css_colors[$current_color]){
				$this->colors = $_wp_admin_css_colors[$current_color];
			}
		}
		if(isset($this->colors) && isset($this->colors->colors[$key])){
			return $this->colors->colors[$key];
		}
		return '#000'; 
	}

	/*
	 * Parse a setting into HTML
	 *
	 * @return string
	 */
	private function parse_setting_field($setting, $options = false){ ?>
		<tr>
			<td>
				<strong><?php echo $setting->title; ?></strong>
			</td>
			<td>
				<label>
				<?php if($setting->type == 'boolean'): // Boolean ?>
					<input type='checkbox' name="<?php echo $setting->name_space; ?>" value='1' <?php if ( 1 == $setting->value ) echo 'checked="checked"'; ?> />
				<?php elseif($setting->type == 'number'): // Number ?>
					<input type="number" name="<?php echo $setting->name_space; ?>" value="<?php echo $setting->value; ?>">
				<?php elseif($setting->type == 'select'): // Select ?>
					<?php //echo json_encode($options); ?>
					<select name="<?php echo $setting->name_space; ?>">
						<!-- Display Current Value - If Found -->
						<?php if(isset($options[$setting->value])): ?>
							<option value="<?php echo $setting->value; ?>"><?php echo $options[$setting->value]['title']; ?></option>
						<?php endif; ?>
						<!-- Display All Options -->
						<?php foreach($options as $option): ?>
							<?php if(!$setting->value ||  ($setting->value && ($option['name'] != $setting->value))) : ?>
								<option value="<?php echo $option['name']; ?>"><?php echo $option['title']; ?></option>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
					<?php echo $setting->descp; ?>
				<label>
			</td>
		</tr>
		<?php
	}

	public function footer_actions(){  ?>
		<?php if($this->settings['add_line_numbers']->value): ?>
		<!-- Add Line Numbers -->
		<script>
			(function($){
				var $pre = $('pre')
				// Add class to all <pre>
				$pre.addClass('<?php echo $this->name_space; ?>');
				$pre.each(function(i){
					$this = $(this);
					// Wrap all lines around <span>
					var new_html = '', line_numbers_html = '', template;
					var old_html = $this.children('code').html(); 
					var code_class = $this.children('code').attr('class');
					var lines = old_html.match(/[^\n\r]+/g);
					for(var i = 0; i < lines.length; i++){
						var index = i + 1;
						// Add line number
						line_numbers_html += '<span class="line-number">' + index + '</span>\n';
						// Add Html
						new_html += '<span class="line">' + lines[i] + '</span>\n';
					}
					$this.children('code').html(new_html);
					// Add Containers (Ugly template, right? ...not worth including a library just for this, though)
					template = '<div  class="<?php echo $this->name_space; ?> <?php echo $this->name_space; ?>-container <?php echo $this->name_space; ?>-table_container">\
<table>\
		<tbody>\
			<tr>\
				<td class="gutter"><pre class="line-numbers">' + line_numbers_html + '</pre></td>\
				<td class="code"><pre><code class="' + code_class + '">' + new_html + '</code></pre></td>\
			</tr>\
		</tbody>\
	</table>\
</div>';
					// Re-Append
					$this.replaceWith(template);
				});
			})(jQuery);
		</script>
		<?php endif; ?>

		<!-- Call Highlight JS -->
		<script>
		<?php if($this->settings['tab_replacement']->value): ?>
		console.log('Replacement --<?php echo str_repeat(" ", $this->settings['tab_number_ratio']->value); ?>--');
		hljs.configure({tabReplace: '<?php echo str_repeat(" ", $this->settings['tab_number_ratio']->value); ?>'});
		<?php endif; ?>
		hljs.initHighlightingOnLoad();
		</script>
	<?php }
}
?>
