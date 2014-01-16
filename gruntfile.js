module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			my_target: {
				files: {
					'js/jsj-code-highlight.js': [
						'js/libs/highlight.min.js', 
						'js/app/app.js',
					],
				}
			}
		},
		compass: {
			dist: {
				options: {
					sassDir: 'scss',
					cssDir: 'css',
				}
			}
		},
		watch: {
			css: {
				files: ['**/*.scss', '**/*.js'],
				tasks: ['compass', 'uglify'],
			}
		},	
		wp_readme_to_markdown: {
			your_target: {
				files: {
					'README.md': 'readme.txt'
				},
			},
		},
		checkwpversion: {
			options: {
				readme: 'readme.txt',
				plugin: 'jsj-code-highlight.php',
			},
			check: { //Check plug-in version and stable tag match
				version1: 'plugin',
				version2: 'readme',
				compare: '==',
			},
			check2: { //Check plug-in version and package.json match
				version1: 'plugin',
				version2: '<%= pkg.version %>',
				compare: '==',
			},
        },
        pot: {
			options: {
				text_domain: 'jsj-code-highlight', //Your text domain. Produces my-text-domain.pot
				dest: 'languages/', //directory to place the pot file
				keywords: [
					'__',
					'_e:',
					// '__:1,2d',
					// '_e:1,2d',
					// '_x:1,2c,3d',
					// 'esc_html__:1,2d',
					// 'esc_html_e:1,2d',
					// 'esc_html_x:1,2c,3d',
					// 'esc_attr__:1,2d', 
					// 'esc_attr_e:1,2d', 
					// 'esc_attr_x:1,2c,3d', 
					// '_ex:1,2c,3d',
					// '_n:1,2,4d', 
					// '_nx:1,2,4c,5d',
					// '_n_noop:1,2,3d',
					// '_nx_noop:1,2,3c,4d'
				]
			},
			files:{
				src: [ 
					'**/*.php',
					'*.php',
					'jsj-code-highlight.php',
					'jsj-code-highlight-settings.php',
					'!node_modules/**',
					'!build/**'
                ], //Parse all php files
				expand: true,
			}
		},
		po2mo: {
			files: {
				src: 'languages/*.po',
				expand: true,
			},
		},
		clean: {
            //Clean up build folder
            main: ['build/jsj-code-highlight']
        },
        copy: {
            // Copy the plugin to a versioned release directory
            main: {
                src:  [
                    '**',
                    '!node_modules/**',
                    '!build/**',
                    '!.git/**',
                    '!gruntfile.js',
                    '!package.json',
                    '!.gitignore',
                    '!.gitmodules',
                    '!*~',
                    '!README.md',
                    '!config.rb',
                ],
                dest: 'build/jsj-code-highlight/',
            }                
        },
		wp_deploy: {
			deploy: { 
				options: {
					plugin_slug: 'jsj-code-highlight',
					svn_user: 'jorge.silva',    
					build_dir: 'build/jsj-code-highlight/' //relative path to your build directory
				},
			}
		},
	});

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-copy');

	grunt.loadNpmTasks('grunt-wp-readme-to-markdown');
	grunt.loadNpmTasks('grunt-checkwpversion');
	grunt.loadNpmTasks('grunt-pot');
	grunt.loadNpmTasks('grunt-wp-deploy');
	grunt.loadNpmTasks('grunt-po2mo');

	// Default task(s).
	grunt.registerTask('default', ['compass' ,'uglify']);
	grunt.registerTask('build', ['compass' ,'uglify', 'checkwpversion', 'pot', 'po2mo','wp_readme_to_markdown', 'clean', 'copy']);
	grunt.registerTask( 'deploy', [ 'wp_readme_to_markdown', 'clean', 'copy', 'wp_deploy' ] );

};