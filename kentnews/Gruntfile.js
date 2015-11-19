'use strict';
module.exports = function(grunt) {
  // Load all tasks
  require('load-grunt-tasks')(grunt);
  // Show elapsed time
  require('time-grunt')(grunt);

  grunt.initConfig({
    less: {
      build: {
        files: {
          'metaboxes/css/post_metaboxes.css': [
            'metaboxes/css/post_metaboxes.less'
          ]
        },
        options: {
          compress: true
        }
      }
    }
  });

  // Register tasks
  grunt.registerTask('default', [
    'build'
  ]);
  grunt.registerTask('build', [
    'less'
  ]);
};
