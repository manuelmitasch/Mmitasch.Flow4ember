App.TasklistController = Ember.ObjectController.extend({
	needs: ['tasklistsIndex'],

	actions: {
		remove: function (tasklist) {
			var controller = this;

	  		if (window.confirm('Do you really want to delete this record?')) {
		  		this.session.deleteModel(tasklist);
		  		this.session.flush().then(function(models) {
			    	controller.get('controllers.tasklistsIndex').content.removeObject(tasklist);
			    	controller.transitionToRoute("tasklists");
			    }, function(models){
			     	alert("Could not remove record."); // TODO: add proper error handling
			    });
	  		}
	  	},
	  	startEditing: function() {
	  		this.set('isEditing', true);
	  	},
	  	stopEditing: function() {
	  		this.set('isEditing', false);
	  	},
		save: function (tasklist) {
			var controller = this;
			this.session.flush().then(function(models) {
		     	newRecord = controller.get('content');
		    	controller.send("stopEditing");
		    }, function(models){
		     	alert("Could not save record."); // TODO: add proper error handling
		    });
	  	},
	  	cancel: function() {
	    	this.transitionToRoute("tasklists");
	  	}
	},

	isEditing: false,

});
