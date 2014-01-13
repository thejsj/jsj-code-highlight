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
	});

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default task(s).
	grunt.registerTask('default', ['compass' ,'uglify']);

};