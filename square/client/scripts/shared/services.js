(function() {
  angular.module('app.services', [])
  
  .factory('notifyService', [
    'logger', function(logger) {
      return {
        notify: function(type, message) {
          switch (type) {
            case 'info':
              return logger.log(message);
            case 'success':
              return logger.logSuccess(message);
            case 'error':
              return logger.logError(message);
          }
        }
      };
    }
  ])
  
  .factory('loginService', [
    '$http', '$location', 'notifyService', 'sessionService', function($http, $location, notifyService, sessionService) {
      return {
        login: function(user_data) {
           var request = $http({
              method: "post",
              url: webScriptServerUrl + "login",
              data: {
                  email: user_data.email,
                  password: user_data.password
              },
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
          });
           
          // Check whether the HTTP Request is successful or not. 
          request.success(function (data) {
              notifyService.notify(data.status, data.message);                
              if (data.status === 'error') {
                return false;
              }
              else {
                sessionService.set('uid', data.uid);
                $location.url('/dashboard?msg:logged_in'); 
              }
          });
        },
        logout: function() {
            sessionService.destroy('uid');
            $location.url('/pages/signin?msg:logged_out'); 
        },
        islogged: function() {
           var $checkSessionServer = $http.get(webScriptServerUrl + "session");
           return $checkSessionServer;
        }
      };
    }
  ])

.factory('sessionService', [
    '$http', function($http) {
      return {
        set: function(key, value) {
          return sessionStorage.setItem(key, value);
        },
        get: function(key) {
          return sessionStorage.getItem(key);
        },
        destroy: function(key) {
          $http.get(webScriptServerUrl + "logout");
          return sessionStorage.removeItem(key);
        }
      };
    }
  ]);

}).call(this);
