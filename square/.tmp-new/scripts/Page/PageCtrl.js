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
  ]).controller('signupCtrl', [
    '$scope', function($scope) {
      $scope.fullName = "";
      $scope.email = "";
      $scope.password = "";
      $scope.phone = "";
      $scope.companyName = "";
      $scope.billNo = "";
      return $scope.userType = "";
    }
  ]);

}).call(this);

//# sourceMappingURL=PageCtrl.js.map
