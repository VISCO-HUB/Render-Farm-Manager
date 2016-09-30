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
	Batch

*/

/*
	TODO:
	
	Админка:
	Добавление, удаление сервисов
	Управление пользователями в системе
	Создание глобального статуса - вкл./выкл. сервис
	Перезагрузска всех нод
	Выключение сервисов на всех нодах
	Mail notifications
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

document.addEventListener("contextmenu", function(e){
    e.preventDefault();
}, false);

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
        templateUrl : "templates/admin.php",
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
app.controller("adminCtrl", function($scope, $rootScope, admin){
	$scope.adminSection = 'global';
	$rootScope.adminDR = [];
	$rootScope.adminServices = [];
	$rootScope.checkAdminDR = [];
	$rootScope.adminUsers = [];
	$rootScope.checkAdminUsers = [];
	
	admin.adminDR();
	admin.adminServices();
	admin.adminUsers()
	
	$scope.installedServices = function(s){	
		var out = '';
		
		angular.forEach(s.split(';'), function(value, key){
			var r = value.split('=');
			if(r[1] != 'notfound' && r[0]) {out += r[0] + ', '}
		});
		
		return out.slice(0, -2);
	}
	
});
	// HOME
app.controller("homeCtrl", function ($scope, vault, $timeout, $interval, $rootScope) {
	// sendChallange 
	$rootScope.firstLoad = 0;
	vault.getServices();
	
	$rootScope.otherUsers = [];
	
	$rootScope.sendChallange = function(){
		//vault.sendChallenge();
		$rootScope.firstLoad = 10;
		
		vault.getDR();
		$scope.search = '';
		$rootScope.showMsg = {}
	}
	
	$scope.uncheckAll = function() {
		$rootScope.checkModel = {};
	}
	
	$scope.checkFree = function() {
		$rootScope.checkModel = {};
		angular.forEach($rootScope.dr, function(value, key){
			if(value.user == '' || value.user == null){
				$rootScope.checkModel[value.ip] = true;
			}
		});
	}
	
	$scope.getLastNodes = function(){
		$rootScope.checkModel = {};
		vault.getLastNodes();
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
	
	$scope.orderNodes = 'name';
	$scope.reverse = false;
	
	$scope.orderByParam = function(x) {
		$scope.reverse = !$scope.reverse;
		$scope.orderNodes = x;
	}
		
	$rootScope.startingSpawners = false;	
	$rootScope.currentService = '';		
	
	$scope.runService = function(x){		
						
		if(x != null && x != '')
		{
			$rootScope.startingSpawners = true;
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
	
		var timer = $interval( function(){					
			vault.getDR();
		}, 1000);	
	

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
			//$rootScope.checkModel[ip] = !$rootScope.checkModel[ip];
			$rootScope.checkModel[ip] = true;
		}
		if(e.buttons == 2){
			$rootScope.checkModel[ip] = false;
		}
	  }		 	 
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

app.service('admin', function($http, $rootScope, $timeout, $interval) {	
	
	// SIMPLIFY POST PROCEDURE
	var HttpPost = function(file, json)
	{		
		return $http({
			url: 'admin/' + file + '.php?time=' + new Date().getTime(),
			method: "POST",
			data: json
		});
	}
	
	var httpGet = function(query)
	{		
		return $http.get('admin/admin.php?query=' + query + '&time=' + new Date().getTime());
	}
	
	// GET DR
	var adminDR = function()
	{	
		httpGet('getDR').success(function(r){
			$rootScope.adminDR = [];
			
			if(!r.message) {$rootScope.adminDR = r;}							
		});		 			
	}
	
	var adminServices = function()
	{	
		httpGet('getServices').success(function(r){
			$rootScope.adminServices = [];
			
			if(!r.message) {$rootScope.adminServices = r;}							
		});		 			
	}
	
	var adminUsers = function()
	{	
		httpGet('getUsers').success(function(r){
			$rootScope.adminUsers = [];
			
			if(!r.message) {$rootScope.adminUsers = r;}							
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
	
	return {
		sendCmd: sendCmd,
		adminDR: adminDR,
		adminServices: adminServices,
		adminUsers: adminUsers
	};
});


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
					$rootScope.showMsg.success = 'Success. All nodes are dropped! Automatically starting BackBurner service...'; 
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
			$rootScope.otherUsers = [];
			
			var runninSrv = [];
			
			angular.forEach(r, function(value, key){
				if(value.user != null) {$rootScope.checkModel[value.ip] = false;}	
				
				if(value.user != '' && value.user != null && $rootScope.otherUsers.indexOf(value.user) == -1) {$rootScope.otherUsers.push(value.user);}
				
				if($rootScope.userInfo && value.user === $rootScope.userInfo.user)
				{
					$rootScope.checkModel[value.ip] = true;
					$rootScope.reservedDr.push(value);
					
					if(value.services == $rootScope.currentService) {runninSrv.push(true)}			
				}
			});			
			$rootScope.firstLoad++;
			$rootScope.startingSpawners = $rootScope.reservedDr.length != runninSrv.length && $rootScope.currentService.length;	
		});		 			
	}
	
	// WORK WITH SOCKET
	var socket = function(ip, cmd)
	{
		var json = {'ip': ip, 'cmd': cmd};
		HttpPost('socket', json).then(function(r){
			$rootScope.socketResponse[ip] =  r.data;
			
			//getDR();
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
		httpGet('dropNodes').success(function(r){
			showMsg(r);
			$rootScope.currentService = '';
			$rootScope.checkModel = {};	
			//getDR();
		});	
		//sendCmd('DROP');
	}
	
	var getLastNodes = function()
	{
		httpGet('getLastNodes').success(function(r){			
			if(r == 'RESTRICTED') {
				showMsg(r);
				return false;
			}
			
			lastNodes = r.split('|');
			angular.forEach($rootScope.dr, function(value, key){
				if(value.user == '' || value.user == null){
					if(lastNodes.indexOf(value.ip) != -1){
						$rootScope.checkModel[value.ip] = true;
					}
				}
			});
			
			//getDR();
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
	adminDropNodes: adminDropNodes,
	getServices: getServices,
	getLastNodes: getLastNodes
  };

});

