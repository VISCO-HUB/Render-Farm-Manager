/* APP */
var app = angular.module('app', ['ngRoute', 'ngSanitize', 'ui.bootstrap']);


// CONFIG 
app.config(function($routeProvider) {
    $routeProvider
    .when("/", {
        templateUrl : 'templates/home.html',
		controller: 'homeCtrl'
    })
    .when("/admin", {
        templateUrl : "templates/admin.html",
		controller: 'adminCtrl'
    })   
	.otherwise({redirectTo:'/'});
});

// CONTROLLERS

app.controller("homeCtrl", function ($scope, vault, $timeout) {
	// sendChallange 
	$scope.sendChallange = function(){
		vault.sendChallenge();
	}
	
	$scope.stopService = function(name){
		vault.stopService(name);
	}
	
	//	GET RUNNINI SERVICES
	$scope.getUsedServices = function(services){					
		var serviceInfo = "";
		angular.forEach(services.split(';'), function(value, key){					
			var d = value.split('=');			
			if(d[1] == 4) {
				serviceInfo += d[0] + ' ';
			}			
			return serviceInfo;
		});		
		return serviceInfo;
	}
		
	vault.getDR();

	 $scope.checkModel = {};
	$scope.$watchCollection('checkModel', function () {
		$scope.checkResults = [];
		angular.forEach($scope.checkModel, function (value, key) {
		  if (value) {
			$scope.checkResults.push(key);
		  }
		});
	  });	
});
// AUTO RUN
app.run( function($rootScope, $location, $routeParams, vault) {
    $rootScope.$watch(function() { 
        return $location.path(); 
    },
     function(a){
		// INIT
		$rootScope.socketResponse = {};
		$rootScope.logIn = function(){vault.logIn();}
		$rootScope.logOut = function(){vault.logOut();}
		
		vault.logIn();
    });
});
// SERVICES
app.service('vault', function($http, $rootScope, $timeout) {
	// SIMPLIFY POST PROCEDURE
	var HttpPost = function(file, json)
	{		
		return $http({
			url: 'vault/' + file + '.php',
			method: "POST",
			data: json
		});
	}
	
	var httpGet = function(file)
	{		
		return $http.get('vault/' + file + '.php');
	}
	
	// GET DR
	var getDR = function()
	{
		httpGet('getDR').success(function(r){$rootScope.dr = r});		 			
	}
	
	// WORK WITH SOCKET
	var socket = function(ip, cmd)
	{
		var json = {'ip': ip, 'cmd': cmd};
		HttpPost('socket', json).then(function(r){
			$rootScope.socketResponse[ip] =  r.data;
			
			getDR();										
		}, 
		function(r){
			$rootScope.socketResponse[ip] =  'DISCONNECTED';
		});		
	}
	// SEND COMMAND TO ALL SERVERS	
	var sendCmd = function(cmd)
	{
		var a = $rootScope.dr;
		
		for(var i = 0; i < a.length; i++)  
		{			
			var ip = a[i].ip;
			socket(ip, cmd);
		}	
	}
	// STOP SERVICE
	var stopService = function(name)
	{
		sendCmd('STOPSERVICE:' + name)	
	}
	// SEND CHALLANGE	
	var sendChallenge = function()
	{			
		var a = $rootScope.dr;
		
		for(var i = 0; i < a.length; i++)  
		{			
			var ip = a[i].ip
			var cmd = "CHALLANGE"
			socket(ip, cmd);
		}	
	}
	// GET USER INFO
	var logIn = function()
	{			
		httpGet('login').success(function(r){			
			console.log($rootScope.userInfo);
			if(!r.logged) 
			{
				if($rootScope.userInfo)
				{
					r.error='Login or password is invalid!';				
				}
				else
				{					
					r.msg = 'Please login with your e-mail and password!';									
				}
			}
			
			if(!r.logged) {logIn();}
			$rootScope.userInfo = r;
		})
		.error(function(r){
			$rootScope.userInfo = {'error': 'Please enter correct e-mail and password!'};
		});		 				
	}
	
	var logOut = function()
	{
		httpGet('logout').success(function(r){
			r.msg = 'You are logout!';
			$rootScope.userInfo = r;			
		});
		
	}
	
	
  return {
    socket: socket,
	sendChallenge: sendChallenge,
	getDR: getDR,
	stopService: stopService,
	logIn: logIn,
	logOut: logOut
  };


});

