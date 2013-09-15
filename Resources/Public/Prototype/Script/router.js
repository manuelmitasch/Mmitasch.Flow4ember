App.Router.map(function () {
	this.resource('tasklists', function() {
		this.route('new');
    	this.resource('tasklist', {path: ':tasklist_id'});
 	});
});