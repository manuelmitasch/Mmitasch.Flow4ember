App.Tasklist = DS.Model.extend({
	name: DS.attr('string'),
	tasks: DS.hasMany('App.Task', { inverse: 'list' })
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

//	var transaction = App.Store.transaction();
//	var list = transaction.createRecord(App.Tasklist, { name: "Peter" });
//	var task1 = list.get('tasks').createRecord(App.Task, { name: 'task A' });
//	transaction.commit();
	
//	task1.set('list', list);
//	task2.set('list', list);
//	list.save();
//	task1.save();
	
});

