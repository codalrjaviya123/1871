(function() {
  'use strict';
  angular.module('app.page.ctrls', []).controller('invoiceCtrl', [
    '$scope', '$window', function($scope, $window) {
      return $scope.printInvoice = function() {
        var originalContents, popupWin, printContents;
        printContents = document.getElementById('invoice').innerHTML;
        originalContents = document.body.innerHTML;
        popupWin = window.open();
        popupWin.document.open();
        popupWin.document.write('<html><head><link rel="stylesheet" type="text/css" href="styles/main.css" /></head><body onload="window.print()">' + printContents + '</html>');
        return popupWin.document.close();
      };
    }
  ])
  
  .controller('signinCtrl', [
    '$scope', 'loginService', function($scope, loginService) {
      
      //Validations
      var original;
      $scope.signin = {
        email: '',
        password: ''
      };
      original = angular.copy($scope.signin);
      $scope.canSubmit = function() {
        return $scope.signinForm.$valid && !angular.equals($scope.signin, original);
      };
      
      //Form submit action
      $scope.dosignin = function () {
          var user_data = $scope.signin;
                    
          loginService.login(user_data);      
      }      
    }
  ])

  .controller('signupCtrl', [
    '$scope', '$http', 'logger', '$location', 'notifyService', function($scope, $http, logger, $location, notifyService) {
     
        //Validations
        var original;
          $scope.signup = {
              fullName: '',
              email: '',
              password: '',
              phone: '',
              companyName: '',
              billNo: '',
              userType: ''
          };
          original = angular.copy($scope.signup);
          
        $scope.canSubmit = function() {
          return $scope.signupForm.$valid && !angular.equals($scope.signup, original);
        };

        //Form submit action
        $scope.dosignup = function () {

          var user_data = $scope.signup;          
          var request = $http({
              method: "post",
              url: webScriptServerUrl + "signup",
              data: {
                  full_name: user_data.fullName,
                  email: user_data.email,
                  password: user_data.password,
                  phone_number: user_data.phone,
                  company_name: user_data.companyName,
                  bill_account_no: user_data.billNo,
                  user_type: user_data.userType
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
                $location.url('/pages/signin?msg:account_created'); 
              }
          });
        }
    }
  ])

}).call(this);
