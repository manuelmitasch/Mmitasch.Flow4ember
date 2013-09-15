App.TasklistsNewController = Em.ObjectController.extend({
	needs: ['tasklistsIndex'],

	actions: {
		save: function (tasklist) {
			var controller = this;
			this.session.flush().then(function(models) {
		     	newRecord = controller.get('content');
		    	controller.get('controllers.tasklistsIndex').content.pushObject(newRecord);
		    	controller.transitionToRoute("tasklists");
		    }, function(models){
		     	alert("Could not save record."); // TODO: add proper error handling
		    });
	  	},
	  	cancel: function () {
	  		this.transitionToRoute('tasklists');
	  	}
	}
});