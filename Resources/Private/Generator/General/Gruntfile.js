module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    emberTemplates: {
      options: {
        templateBasePath: /Script\/Template\//
      },
      compile: {
        files: {
          'Resources/Public/Script/Build/templates.js': ['Resources/Public/Script/Template/*.hbs','Resources/Public/Script/Template/**/*.hbs']
        }
      }
    },

    watch: {
      emberTemplates: {
        files: ['Resources/Public/Script/Template/*.hbs','Resources/Public/Script/Template/**/*.hbs'],
        tasks: ['emberTemplates'],
        options: {
          livereload: true
        }
      },
      js: {
        files: 'Resources/Public/Script/*',
        options: {
          livereload: true
        }
      }
    }
  });

  // Load the plugin that provides the "ember-templates" task.
  grunt.loadNpmTasks('grunt-ember-templates');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['emberTemplates']);

};