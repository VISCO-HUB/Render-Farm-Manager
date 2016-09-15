/* APP */
var app = angular.module('app', ['ngRoute', 'ngSanitize', 'ui.bootstrap', 'ngAnimate']);


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

app.controller("homeCtrl", function ($scope, vault, $timeout, $rootScope) {
	// sendChallange 
	$scope.sendChallange = function(){
		vault.sendChallenge();
		$scope.search = '';
	}
	
	$scope.stopService = function(name){
		vault.stopService(name);
	}
	
	$scope.getNodes = function()
	{
		vault.getNodes();
	}
	$scope.deleteMsg = function()
	{
		$rootScope.showMsg = {};
	}
	
	$scope.dropNodes = function()
	{		
		vault.dropNodes();
	}
	
	$scope.isReserved = function(user)
	{
		return user != $rootScope.userInfo.user && user != null;
	}
	
	//$scope.orderNodes = 'name';
	$scope.reverse = true;
	
	$scope.orderByParam = function(x) {
		$scope.reverse = !$scope.reverse;
		$scope.orderNodes = x;
	}
	
	//	GET RUNNINI SERVICES
	/*$scope.getUsedServices = function(services){					
		var serviceInfo = "";
		angular.forEach(services.split(';'), function(value, key){					
			var d = value.split('=');			
			if(d[1] == 4) {
				serviceInfo += d[0] + ' ';
			}			
			return serviceInfo;
		});		
		return serviceInfo;
	}*/
		
	vault.getDR();

	$rootScope.checkModel = {};
	$scope.$watchCollection('checkModel', function () {
		$rootScope.checkResults = [];
		angular.forEach($rootScope.checkModel, function (value, key) {
		  if (value) {
			$rootScope.checkResults.push(key);
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
		$rootScope.showMsg = {};
		$rootScope.logIn = function(){vault.logIn();}
		$rootScope.logOut = function(){vault.logOut();}
		
		vault.logIn();
    });
});
// SERVICES
app.service('vault', function($http, $rootScope, $timeout) {
	// MESSAGES
	var showMsg = function(r)
	{
		$rootScope.showMsg = {};
		
		switch(r.message)
		{
			case 'ERROR': $rootScope.showMsg.error = 'MySQL connection error :( ...';
			break;
			case 'RESTRICTED': $rootScope.showMsg.warn = 'This user restricted!';
			break;
			case 'NODESDROPPED':   
			{
				if($rootScope.checkResults.length)
				{	
					$rootScope.showMsg.success = 'Success. All nodes are dropped!'; 
				}
				else
				{
					$rootScope.showMsg.error = 'You have no reserved nodes for drop!'; 					
				}					
			}
			break;
			case 'NODESRESERVED':  
				if(r.cnt > 0)
				{
					$rootScope.showMsg.success = 'Success! ' + r.cnt + ' nodes reserved!';
				}
				else
				{
					$rootScope.showMsg.error = 'No nodes reserved!';
				}
			break;
			case 'NONODES':  $rootScope.showMsg.warn = 'Please select at leaset one node!';
			break;
		}
	}
	
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
		httpGet('getDR').success(function(r){
			$rootScope.dr = r;
			angular.forEach(r, function(value, key){					
				$rootScope.checkModel[value.ip] = value.user == $rootScope.userInfo.user;								
			});			
		});		 			
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
	
	var getNodes = function()
	{		
		var json = $rootScope.checkResults;
		HttpPost('getNodes', json).then(function(r){
			showMsg(r.data);				
			getDR();			
		}, 
		function(r){			
			console.log(r);
		});	
	}
	
	var dropNodes = function()
	{	
		httpGet('dropNodes').success(function(r){
			showMsg(r);
			getDR();			
		});	
	}
		
  return {
    socket: socket,
	sendChallenge: sendChallenge,
	getDR: getDR,
	stopService: stopService,
	logIn: logIn,
	logOut: logOut,
	getNodes: getNodes,
	dropNodes: dropNodes
  };


});

