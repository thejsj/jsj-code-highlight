<?php 

	// Start Setting Options

	$jsj_code_highlight_options = Array();

	$jsj_code_highlight_options['style'] = (object) array(
		'name' => 'style', 
		'title' => __( 'Select Your Code Style', 'jsj_code_highlight' ),
		'descp' => '',
		'type' => 'image-chooser',
		'default' => 'monokai_sublime'
	);

	$jsj_code_highlight_options['font'] = (object) array(
		'name' => 'font', 
		'title' => __( 'Code Typeface', 'jsj_code_highlight' ),
		'descp' => __( 'Select in typeface for code.', 'jsj_code_highlight' ),
		'type' => 'select',
		'default' => 'source_code_pro'
	);

	$jsj_code_highlight_options['include_additional_styles'] = (object) array(
		'name' => 'include_additional_styles', 
		'title' => __( 'Additional Styles', 'jsj_code_highlight' ),
		'descp' => __( 'Include additional CSS styles developed for this plugin.', 'jsj_code_highlight' ),
		'type' => 'boolean',
		'default' => '1'
	);

	$jsj_code_highlight_options['add_line_numbers'] = (object) array(
		'name' => 'add_line_numbers', 
		'title' => __( 'Line Numbers', 'jsj_code_highlight' ),
		'descp' => __( 'Automatically add lines numbers to your code snippets.', 'jsj_code_highlight' ),
		'type' => 'boolean',
		'default' => '1'
	);

	$jsj_code_highlight_options['tab_replacement'] = (object) array(
		'name' => 'tab_replacement', 
		'title' => __( 'Tab Replacement', 'jsj_code_highlight' ),
		'descp' => __( 'Automatically replace tabs in your code with spaces.', 'jsj_code_highlight' ),
		'type' => 'boolean',
		'default' => '0'
	);

	$jsj_code_highlight_options['tab_number_ratio'] = (object) array(
		'name' => 'tab_number_ratio', 
		'title' => __( 'Tab/Number Ratio', 'jsj_code_highlight' ),
		'descp' => __( 'Number of spaces to be insert per tab.', 'jsj_code_highlight' ),
		'type' => 'number',
		'default' => '4'
	);

?>