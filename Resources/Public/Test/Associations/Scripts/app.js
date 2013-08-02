App = Ember.Application.create();

App.Store = DS.Store.extend({});

DS.RESTAdapter.reopen({
  namespace: 'rest'
});

App.Router.map(function() {
  // put your routes here
});

App.IndexRoute = Ember.Route.extend({
  model: function() {
    return App.Tasklist.find();
  }
});



