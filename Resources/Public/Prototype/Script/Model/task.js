App.Task.reopen({
	name: Ep.attr('string'),
	list: Ep.belongsTo(App.Tasklist),
	assignee: Ep.belongsTo(App.Person)
});