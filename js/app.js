jQuery(function(($){
	var root = this;
	var App = root.App = {};

	var request = App.request = function(action){
		var params, objid, process;

		for(var i = 1; i < arguments.length; i++){
			switch(typeof arguments[i]){
				case 'function':
					process = arguments[i];
					break;
				case 'object':
					params = arguments[i];
					break;
				default:
					objid = arguments[i];
					break;
			}
		}

		jQuery.ajax({
			url: 'do.php',
			type: 'POST',
			data:{
				action: action,
				params: params,
				objid: objid
			},
			dataType: 'json',
			success: function(json){
				console.log(json);

				if(!json) return;

				if(json.error){
					alert(json.error.message);
				}else if(typeof process == 'function'){
					process(json);
				}
			},
			error: function(jqXHR){
				console.log(jqXHR.responseText);
			}
		});
	}

	var Model = App.Model = Backbone.Model.extend({
		// The name of the object,
		// for request & idAttribute purposes
		object: 'Model',

		// Blank the idAttribute,
		// we'll define it during construction
		idAttribute: null,

		// Custom constructor, defines idAttribute
		constructor: function(){
			// Unless a non-standard idAttribute was defined,
			// auto define it based on object name
			if(!this.idAttribute){
				this.idAttribute = this.object.toLowerCase()+'id';
			}

			Backbone.Model.apply(this, arguments);
		}
	});

	var Collection = App.Collection = Backbone.Collection.extend({});

	var Audio = App.Audio = Model.extend({
		library: 'AudioLibrary'
	});

	var Video = App.Video = Model.extend({
		library: 'VideoLibrary'
	});

	var Album = App.Album = Audio.extend({
		object: 'Album'
	});

	var Artist = App.Artist = Audio.extend({
		object: 'Artist'
	});

	var Song = App.Song = Audio.extend({
		object: 'Song'
	});

	var Movie = App.Movie = Video.extend({
		object: 'Movie'
	});

	var MovieSet = App.MovieSet = Video.extend({
		object: 'MovieSet',

		// XBMC takes setid, not moviesetid
		idAttribute: 'setid',

		// Initialize by converting movies to a Movies collection
		initialize: function(){
			this.Movies = new Movies(this.get('movies'));
		}
	});

	var TVShow = App.TVShow = Video.extend({
		object: 'TVShow'
	});

	var Episode = App.Episode = Video.extend({
		object: 'Episode'
	});

	var MusicVideo = App.MusicVideo = Video.extend({
		object: 'MusicVideo'
	});

	var Albums = App.Albums = Collection.extend({model:Album});

	var Artists = App.Artists = Collection.extend({model:Artist});

	var Songs = App.Songs = Collection.extend({model:Song});

	var Movies = App.Movies = Collection.extend({model:Movie});

	var MovieSets = App.MovieSets = Collection.extend({model:MovieSet});

	var TVShows = App.TVShows = Collection.extend({model:TVShow});

	var Episodes = App.Episodes = Collection.extend({model:Episode});

	var MusicVideos = App.MusicVideos = Collection.extend({model:MusicVideo});
});