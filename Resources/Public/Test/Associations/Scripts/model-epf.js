App.Task = Ep.Model.extend();

App.Tasklist = Ep.Model.extend({
	name: Ep.attr('string'),
	tasks: Ep.hasMany(App.Task, { inverse: 'list' })
});

//// Reopen generated Ember Objects after this line, to enable simple file merging when regenerating.'

App.Task.reopen({
	name: Ep.attr('string'),
	list: Ep.belongsTo(App.Tasklist)
});

// App.Adapter.map(App.Task,
//     {list: { owner: false }}
// );
