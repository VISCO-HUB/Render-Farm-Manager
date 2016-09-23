/*
	In Project Used:

	JavaScript
	JQuery
	Angular
	Angular UI
	BootStrap
	HTML
	CSS
	MySQL
	Sockets
	PHP
	Vb Net
	Dot Net
	MaxScript

*/

/*
	TODO:
	
	Админка:
	Добавление, удаление сервисов
	Управление пользователями в системе
	Создание глобального статуса - вкл./выкл. сервис
	Перезагрузска всех нод
	Выключение сервисов на всех нодах
*/

/* GLOBAL FUNCTIONS */

Array.prototype.makeUnique = function(){
   var u = {}, a = [];
   for(var i = 0, l = this.length; i < l; ++i){
      if(u.hasOwnProperty(this[i])) {
         continue;
      }
      a.push(this[i]);
      u[this[i]] = 1;
   }
   return a;
}

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
	.when("/about", {
        templateUrl : "templates/about.html",
		controller: 'aboutCtrl'
    }) 	
	.otherwise({redirectTo:'/'});
});

// CONTROLLERS
	// ABOUT
app.controller("aboutCtrl", function($scope){
	
});
	// ADMIN
app.controller("adminCtrl", function($scope){
	
});
	// HOME
app.controller("homeCtrl", function ($scope, vault, $timeout, $rootScope) {
	// sendChallange 
	$rootScope.firstLoad = 0;
	
	$rootScope.sendChallange = function(){
		vault.sendChallenge();
		$scope.search = '';
		$rootScope.showMsg = {}
	}
	
	$scope.startService = function(name){
		vault.startService(name);
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
		return user != null && $rootScope.userInfo && user != $rootScope.userInfo.user;
	}
	
	//$scope.orderNodes = 'name';
	$scope.reverse = true;
	
	$scope.orderByParam = function(x) {
		$scope.reverse = !$scope.reverse;
		$scope.orderNodes = x;
	}
	
	
	$scope.runService = function(x){		
		$rootScope.currentService = 'Spawners';
		
		if(x != null && x != '')
		{
			$rootScope.currentService = x;
			vault.startService(x);
		}
	}
	$scope.runService();
	
	$scope.rebootNodes = function(){
		if(confirm('Do you really want to reboot nodes?'))
		{
			vault.rebootNodes();
		}
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

	  $rootScope.chekSwipe = function(e, ip, disabled) {
		if(disabled) {return false}
				
		if(e.buttons == 1){			
			$rootScope.checkModel[ip] = !$rootScope.checkModel[ip];
		}
		/*if(e.buttons == 2){
			$rootScope.checkModel[ip] = false;
		}*/
	  }	

	$scope.adminMenu = {
		content: {},
		templateUrl: 'adminMenu.html',
		title: 'Title'
	};  	  
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
		$rootScope.logOut = function(m){vault.logOut(m);}
		$rootScope.isIE = vault.isIE();
		
		vault.logIn();
		
		$rootScope.adminDropNodes = function(u){
			if(confirm('Do you really want to drop ' + u + '?'))
			{
				vault.adminDropNodes(u);
			}			
		}
    });
});
// SERVICES
app.service('vault', function($http, $rootScope, $timeout, $interval) {
	// MESSAGES
	var showMsg = function(r, p)
	{
		$rootScope.showMsg = {};
		
		m = r.message ? r.message : r;
		switch(m)
		{
			case 'ADMINNODESDROPPED': $rootScope.showMsg.success = 'Success! User ' + r.user + ' dropped!';
			break;
			case 'JOBNAME': $rootScope.showMsg.error = 'Please enter Job Name!';
			break;
			case 'JOBNAMEOVERFLOW': $rootScope.showMsg.error = 'Name can\'t be more than 40 characters!';
			break;
			case 'ERROR': $rootScope.showMsg.error = 'MySQL connection error :( ...';
			break;
			case 'RESTRICTED':
			{
				$rootScope.showMsg.warn = 'This user restricted!';
				$rootScope.logOut('Your session has been expired!');
			}
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
			case 'REBOOT':  $rootScope.showMsg.warn = 'Nodes will reboot! Update the status in few minutes...';
			break;			
			case 'STARTSERVICE':  $rootScope.showMsg.warn = 'Start ' + p + ' on all reserved nodes! Update the status in few minutes...';
			break;
		}
	}
	
	var isIE = function()
	{
		return navigator.userAgent.indexOf('MSIE') > 0;
	}
	
	// SIMPLIFY POST PROCEDURE
	var HttpPost = function(file, json)
	{		
		return $http({
			url: 'vault/' + file + '.php?time=' + new Date().getTime(),
			method: "POST",
			data: json
		});
	}
	
	var httpGet = function(file)
	{		
		return $http.get('vault/' + file + '.php?time=' + new Date().getTime());
	}
	
	//GET SERVICES
	var getServices = function(){				
		httpGet('getServices').success(function(r){			
			$rootScope.services = r;
		})
		.error(function(r){
			$rootScope.services = {};
		});	
	}
	// GET DR
	var getDR = function()
	{
		httpGet('getDR').success(function(r){
			$rootScope.dr = r;
			$rootScope.reservedDr = [];
			
			angular.forEach(r, function(value, key){
				$rootScope.checkModel[value.ip] = false;				
				
				if($rootScope.userInfo && value.user === $rootScope.userInfo.user)
				{
					$rootScope.checkModel[value.ip] = true;
					$rootScope.reservedDr.push(value);
				}
			});			
			if($rootScope.firstLoad == 0){$rootScope.sendChallange()}			
			getServices();
		});		 			
	}
	
	// WORK WITH SOCKET
	var socket = function(ip, cmd)
	{
		var json = {'ip': ip, 'cmd': cmd};
		HttpPost('socket', json).then(function(r){
			$rootScope.socketResponse[ip] =  r.data;
			
			getDR();
			$rootScope.firstLoad++;
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
			var user = a[i].user;
			if($rootScope.userInfo && user === $rootScope.userInfo.user)
			{
				socket(ip, cmd);	
			}			
		}	
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
	// START/STOP SERVICE
	var stopService = function(name)
	{
		sendCmd('STOPSERVICE:' + name);		
	}
	var startService = function(name)
	{
		sendCmd('STARTSERVICE:' + name);
		showMsg('STARTSERVICE', name);
	}
	// REBOOT NODE
	var rebootNodes = function()
	{
		showMsg('REBOOT');
		sendCmd('REBOOT');
		
		var challangeCnt = 0;
		var timer = $interval( function(){
			sendChallenge(); 
			challangeCnt++;
			
			if(challangeCnt > 20) $interval.cancel(timer);
		}, 3000);
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
	
	var logOut = function(m)
	{
		httpGet('logout').success(function(r){
			r.msg = 'You are logout!';
			if(m) {r.msg = m}
			$rootScope.userInfo = r;			
		});
		
	}
	
	var getNodes = function()
	{	
		var nodes = $rootScope.checkResults;
		
		if(!nodes.length) {
			showMsg('NONODES');
			return false;
		}
		
		var jobName = prompt("Please enter job name", "");			
			
		if(!jobName || !jobName.length) {			
			showMsg('JOBNAME');
			
			return false;
		}
		
		if(jobName.length > 40){
			showMsg('JOBNAMEOVERFLOW');
			
			return false;
		}
			
		var json = {};
		json.nodes = nodes;
		json.job = jobName;
		
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
		sendCmd('DROP');	
		httpGet('dropNodes').success(function(r){
			showMsg(r);
			getDR();
		});	
	}
	
	// ADMIN COMMANDS
	var adminDropNodes = function(u){
		
		var json = {'user': u};
		HttpPost('adminDropNodes', json).then(function(r){
			r.data.message = 'ADMIN' + r.data.message;
			r.data.user = u;
			showMsg(r.data);				
			
			getDR();			
		}, 
		function(r){			
			console.log(r);
		});		
	}
		
  return {
    socket: socket,
	sendChallenge: sendChallenge,
	getDR: getDR,
	stopService: stopService,
	startService: startService,
	logIn: logIn,
	logOut: logOut,
	getNodes: getNodes,
	dropNodes: dropNodes,
	rebootNodes: rebootNodes,
	showMsg: showMsg,
	isIE: isIE,
	adminDropNodes, adminDropNodes
  };

});

