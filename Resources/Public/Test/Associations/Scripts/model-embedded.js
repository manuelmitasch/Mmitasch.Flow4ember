App.Tasklist = DS.Model.extend({
	name: DS.attr('string'),
	tasks: DS.hasMany('App.Task')
});

App.Task = DS.Model.extend({
	name: DS.attr('string'),
	list: DS.belongsTo('App.Tasklist')
});

App.Adapter.map('App.Tasklist', {
  tasks: {embedded: 'always'}
});