module.exports = function(grunt) {

	grunt.registerTask( 'prod', function() {
		grunt.task.run( [
			'ftpush:prod',
			'ftpush:aku',
			'ftpush:wolf',
			'notify:prod'
		] );
	} );
};