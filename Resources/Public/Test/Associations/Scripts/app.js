window.App = Ember.Application.create();

App.Adapter = DS.RESTAdapter.extend({
  bulkCommit: false,
  namespace: 'rest'
});

App.Store = DS.Store.extend({
  revision: 12,
  adapter:  App.Adapter.create()
});


App.Router.map(function() {
  this.resource('tasklists', { path: '/' });
});

App.TasklistsRoute = Ember.Route.extend({
  model: function() {
    return App.Tasklist.find();
  }
});


App.TasklistsController = Ember.ArrayController.extend({
  recent: null,

  createTasklist: function () {
  	var transaction = this.get('store').transaction();
  	var list1 = transaction.createRecord(App.Tasklist, { name: "Peter" });
    this.set('recent', list1);
  	// var task1 = this.transaction.createRecord(App.Task, { name: 'task A' });
  	// list1.set('tasks', [task1]);
  	transaction.commit();
  },

  createTask: function() {
    if (this.get('recent.id')) {
      // var transaction = this.get('store').transaction();
      var tasklist = this.get('recent');
      var task1 = App.Task.createRecord({ name: 'task A', list: tasklist });
      tasklist.get('tasks').pushObject(task1);
      // transaction.add(tasklist);
      debugger;
      // transaction.commit();
      this.get('store').commit();
    }
  }.observes('recent.id')
});


