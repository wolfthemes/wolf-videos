module.exports = {
	
	options: {
		mangle: true,
		banner : '/*! <%= app.name %> v<%= app.version %> */\n'
	},

	dist: {
		files: {
			'../assets/js/app.min.js': [ '../assets/js/app.js']
		}
	}
	
};