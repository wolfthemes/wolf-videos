module.exports = {

	build: {

		options : {
			banner : '/*! <%= app.name %> Wordpress Plugin v<%= app.version %> */ \n'
			// preserveComments : 'some'
		},

		files: {

			'<%= app.jsPath %>/videos.min.js': ['<%= app.jsPath %>/videos.js']
		}
	}
};