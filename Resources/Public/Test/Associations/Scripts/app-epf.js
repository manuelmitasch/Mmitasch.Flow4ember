window.App = Ember.Application.create({
  LOG_TRANSITIONS: true,
  LOG_BINDINGS: true,
  LOG_ACTIVE_GENERATION: true,
  LOG_VIEW_LOOKUPS: true,
});

App.Adapter = Ep.RestAdapter.extend({
  namespace: 'rest'
});


App.Router.map(function() {
  this.resource('tasklists', { path: '/' });
});

App.TasklistsRoute = Ember.Route.extend({
  model: function() {
    return this.session.query('tasklist');
  }
});


App.TasklistsController = Ember.ArrayController.extend({
  createTasklist: function () {
    var session = this.session;
    var list = session.create(App.Tasklist, { name: "Gordon" });
    var task1 = session.create(App.Task, { name: 'task A' });
    var task2 = session.create(App.Task, { name: 'task B' });


    list.get('tasks').pushObject(task1);
    task1.set('list', list);
    list.get('tasks').pushObject(task2);
    task2.set('list', list);

    // debugger;

    session.flush().then(function() {
      // alert("bravo");
      // all changes will be persisted
    });

  },

  createTasklistExistingTasks: function () {
    var session = this.session;

    var task1 = session.load(App.Task, "beb02568-f7f2-8900-8a62-2eedc7bc0a8e").then(function(task1) {
      debugger;
      var list = session.create(App.Tasklist, { name: "Peter" });
      list.get('tasks').pushObject(task1);
      task1.set('list', list);
      session.add(task1);

      session.flush().then(function() {
        // alert("bravo");
        // all changes will be persisted
      });
    });
  },

  refreshModels: function () {
    this.session.refresh();
  }



});


