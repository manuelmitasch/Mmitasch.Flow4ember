App.TasklistsIndexController = Ember.ArrayController.extend({
	
	actions: {
		remove: function (tasklist) {
	  		if (window.confirm('Do you really want to delete this record?')) {
		  		this.session.deleteModel(tasklist);
		  		this.session.flush().then(null, function(models){
			     	alert("Could not remove record."); // TODO: add proper error handling
			    });
			}
	  	},
	}

});
