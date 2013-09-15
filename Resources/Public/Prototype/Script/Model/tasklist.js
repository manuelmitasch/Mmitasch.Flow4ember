App.Tasklist.reopen({
	name: Ep.attr('string'),
	tasks: Ep.hasMany(App.Task, { inverse: 'list' })
});