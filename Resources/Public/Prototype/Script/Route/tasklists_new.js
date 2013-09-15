App.TasklistsNewRoute = Ember.Route.extend({
  model: function() {
    return this.session.create(App.Tasklist);
  }
});
