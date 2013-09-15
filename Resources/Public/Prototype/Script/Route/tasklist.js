App.TasklistRoute = Ember.Route.extend({

	actions: {
		error: function(error, transition) {
			this.transitionTo('tasklists');
		}
	},

	setupController: function(controller, model) {
    	this._super.apply(this, arguments);

	    // reset editing state
	    if (controller.get('isEditing')) {
	      controller.send('stopEditing');
	    }
	}
});