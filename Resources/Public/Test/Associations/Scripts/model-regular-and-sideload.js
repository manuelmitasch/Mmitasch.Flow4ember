App.Tasklist = DS.Model.extend({
	name: DS.attr('string'),
	tasks: DS.hasMany('App.Task')
});

App.Task = DS.Model.extend({
	name: DS.attr('string'),
	list: DS.belongsTo('App.Tasklist')
});


Ember.run.once(this, function() {
	// var list = App.Tasklist.find('10a315ed-9875-f6cb-448f-af3575bea5e7');
	// Ember.run.once(list, function() {
	// 	debugger;
	// });

	// debugger;
	// list.set('name', 'other name');
	// list.save();


	var list = App.Tasklist.createRecord({ name: "Peter", tasks: []});
	list.save();
});

