App.TasklistsIndexRoute = Ember.Route.extend({
  model: function() {
    return this.session.query('tasklist');
  }
});
