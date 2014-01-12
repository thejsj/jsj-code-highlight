module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		// uglify: {
		// 	my_target: {
		// 		files: {
		// 			'js/header.js': ['js/libs/Modernizr-2.7.1.js'],
		// 			'js/footer.js': ['js/libs/jquery-1.10.2.js', 'js/libs/bootstrap/bootstrap.js','js/app/app.js'],
		// 		}
		// 	}
		// },
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
				files: ['**/*.scss'],
				tasks: ['compass'],
			}
		},	
	});

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-watch');

	// Default task(s).
	grunt.registerTask('default', ['compass']);

};