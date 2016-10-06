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
	$rootScope.Global = {};
	$rootScope.adminServices = [];	
	$rootScope.adminUsers = [];
	$rootScope.checkAdminDR = [];
	$rootScope.checkAdminUsers = [];
	$rootScope.checkAdminServices = [];

	
	
	admin.adminGlobal();
	admin.adminDR();
	admin.adminServices();
	admin.adminUsers();
	
	$scope.getAdminGlobal = function(){admin.adminGlobal();};
	$scope.getAdminDR = function(){admin.adminDR();};
	$scope.getAdminServices = function(){admin.adminServices();};
	$scope.getAdminUsers = function(){admin.adminUsers();};
	
	$scope.installedServices = function(s){	
		var out = '';
		
		angular.forEach(s.split(';'), function(value, key){
			var r = value.split('=');
			if(r[1] != 'notfound' && r[0]) {out += r[0] + ', '}
		});
		
		return out.slice(0, -2);
	}
	
	$scope.isNumeric = function(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	}
	
	$scope.deleteMsg = function()
	{
		$rootScope.showMsg = {};
	}
	
	// USERS
	
	$scope.adminAddUser = function(){
		var userName = prompt("Please enter User name", "");
			
		if(!userName || !userName.length) {			
			admin.showMsg('ADMINBADUSER');
			
			return false;
		}

		admin.adminAddUser(userName);
	};
	
	$scope.adminDeleteUsers = function(){
		var u = $rootScope.checkAdminUsers;
		
		if(!u.length){
			admin.showMsg('ADMINNOSELECTED');
			return false;
		}
		
		if(!confirm('Do you really want to delete ' + u.length + ' selected users?')){
			return false;
		}
		
		admin.adminDeleteUsers(u);
	};
	
	$scope.adminChangeAccess = function(a){
		var u = $rootScope.checkAdminUsers;
		
		if(!u.length){
			admin.showMsg('ADMINNOSELECTED');
			return false;
		}
		
		if(!confirm('Do you really want to change access for ' + u.length + ' selected users?')){
			return false;
		}
		
		admin.adminChangeAccess(u, a);
	};
		
	$scope.adminChangePassword = function(a) {
		var u = $rootScope.checkAdminUsers;
		var pwd = '';
				
		if(a != -1) {pwd = prompt("Please enter Password (min. 6 characters)", "")};
			
		if((!pwd || !pwd.length) && a != -1) {			
			admin.showMsg('ADMINBADPASSWORD');
			
			return false;
		}
				
		admin.adminChangePassword(u, pwd);
	};
	
	// SERVICES
	
	$scope.adminServiceAdd = function(){
		serviceName = prompt("Please enter Service Name", "");
		
			
		if(!serviceName || !serviceName.length) {			
			admin.showMsg('ADMINBADSERVICENAME');
			
			return false;
		}
		
		admin.adminServiceAdd(serviceName);
	}
	
	$scope.adminServiceDelete = function(){
		var s = $rootScope.checkAdminServices;
		
		if(!s.length){
			admin.showMsg('ADMINNOSELECTED');
			return false;
		}
		
		if(!confirm('Do you really want to delete ' + s.length + ' selected services?')){
			return false;
		}
		
		admin.adminServiceDelete(s);
	};
	
	$scope.adminServiceDisable = function(d){
		var s = $rootScope.checkAdminServices;
		
		if(!s.length){
			admin.showMsg('ADMINNOSELECTED');
			return false;
		}
		
		if(!confirm('Do you really want to change status for ' + s.length + ' selected services?')){
			return false;
		}
		
		admin.adminServiceDisable(s, d);
	};
	
	// GLOBAL 
	
	$scope.adminGlobalChangeParam = function(n, v) {
		admin.adminGlobalChangeParam(n, v);
	};
	
	$scope.adminGlobalChangeMessage = function() {
		var m = prompt("Please enter Offline Message", "");
			
		if(!m || !m.length) {			
			admin.showMsg('ADMINGLOBALBADINPUT');
			
			return false;
		}
		
		admin.adminGlobalChangeParam('message', m);
	};
	
	$scope.adminGlobalChangeIdle = function() {
		var m = prompt("Please enter Idle Time", "");
			
		if(!m || !m.length || !$scope.isNumeric(m)) {			
			admin.showMsg('ADMINGLOBALBADINPUT');
			
			return false;
		}
		
		admin.adminGlobalChangeParam('idle', m);
	};
	
	// NODE
	
	
	$scope.adminNodesDisable = function(d){
		var s = $rootScope.checkAdminDR;
		
		if(!s.length){
			admin.showMsg('ADMINNOSELECTED');
			return false;
		}
		
		if(!confirm('Do you really want to change status for ' + s.length + ' selected nodes?')){
			return false;
		}
		
		admin.adminNodesDisable(s, d);
	}
	
	$scope.adminNodeDelete = function(){
		var s = $rootScope.checkAdminDR;
		
		if(!s.length){
			admin.showMsg('ADMINNOSELECTED');
			return false;
		}
		
		if(!confirm('Do you really want to delete ' + s.length + ' selected nodes?')){
			return false;
		}
		
		admin.adminNodeDelete(s);
	};
	
	$scope.sendCmd = function(cmd){
		var s = $rootScope.checkAdminDR;
		
		if(!s.length){
			admin.showMsg('ADMINNOSELECTED');
			return false;
		}
		
		admin.sendCmd(s, cmd);
	}
	
	
	$scope.adminRunService = function(name){
		
		if(!confirm('Do you really want to run ' + name + ' on selected nodes?')){
			return false;
		}
		
		admin.showMsg('ADMINSTARTSERVICE', name);
		
		$scope.sendCmd('STARTSERVICE:' + name);
	}
	
	$scope.adminStopService = function(){
		
		if(!confirm('Do you really want to stop all services on selected nodes?')){
			return false;
		}
		
		admin.showMsg('ADMINSTOPSERVICE');
		
		$scope.sendCmd('STOPSERVICES');
	}
	
	$scope.adminRebootNodes = function(){
		
		if(!confirm('Do you really want to reboot selected nodes?')){
			return false;
		}
		
		admin.showMsg('ADMINREBOOT');
		
		$scope.sendCmd('REBOOT');
	}
	
	// CHECKS
	$rootScope.check1 = {};
	$scope.$watchCollection('check1', function () {
		$rootScope.checkAdminUsers = [];
		
		angular.forEach($rootScope.check1, function (value, key) {
		  if (value) {
			$rootScope.checkAdminUsers.push(key);			
		  }
		});
	  });
	$rootScope.check2 = {};
	$scope.$watchCollection('check2', function () {
		$rootScope.checkAdminServices = [];
		
		angular.forEach($rootScope.check2, function (value, key) {
		  if (value) {
			$rootScope.checkAdminServices.push(key);			
		  }
		});
	  });
	  
	$rootScope.check3 = {};
	$scope.$watchCollection('check3', function () {
		$rootScope.checkAdminDR = [];
		
		angular.forEach($rootScope.check3, function (value, key) {
		  if (value) {
			$rootScope.checkAdminDR.push(key);			
		  }
		});
	  });
});
	// HOME
app.controller("homeCtrl", function ($scope, vault, admin, $timeout, $interval, $rootScope) {
	$rootScope.Global = {};
	admin.adminGlobal();
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
	};
	
	$scope.startService = function(name){
		vault.startService(name);
	};
	
	$scope.getNodes = function()
	{
		vault.getNodes();
	};
		
	$scope.deleteMsg = function()
	{
		$rootScope.showMsg = {};
	};
	
	$scope.dropNodes = function()
	{		
		vault.dropNodes();	
	};
	
	$scope.isReserved = function(user)
	{
		return user != null && $rootScope.userInfo && user != $rootScope.userInfo.user;
	};
	
	$scope.orderNodes = 'name';
	$scope.reverse = false;
	
	$scope.orderByParam = function(x) {
		$scope.reverse = !$scope.reverse;
		$scope.orderNodes = x;
	};
		
	$rootScope.startingSpawners = false;	
	$rootScope.currentService = '';		
	
	$scope.runService = function(x){		
						
		if(x != null && x != '')
		{
			$rootScope.startingSpawners = true;
			$rootScope.currentService = x;
			vault.startService(x);
		}
	};
	$scope.runService();
	
	$scope.rebootNodes = function(){
		if(confirm('Do you really want to reboot nodes?'))
		{
			vault.rebootNodes();
		}
	};
	
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
		$rootScope.checkResultsNames = [];
		angular.forEach($rootScope.checkModel, function (value, key) {
		  if (value) {
			$rootScope.checkResults.push(key);
			//$rootScope.checkResults.push($rootScope.dr[]);
			
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
	
	var showMsg = function(r, p)
	{
		$rootScope.showMsg = {};
		
		m = r.message ? r.message : r;
		switch(m)
		{			
			case 'ADMINBADUSER':  $rootScope.showMsg.warn = 'Please enter correct User name!';
			break;
			case 'ADMINUSERADDED':  $rootScope.showMsg.success = 'Success! User "' + p + '" added!';
			break;
			case 'ADMINUSERNOTADDED':  $rootScope.showMsg.error = 'Error! User "' + p + '" not added!';
			break;
			case 'ADMINUSERNOTDELETED':  $rootScope.showMsg.error = 'Error! Users not deleted!';
			break;
			case 'ADMINUSERDELETED':  $rootScope.showMsg.success = 'Success! Users deleted!';
			break;
			case 'ADMINNOSELECTED':  $rootScope.showMsg.warn = 'Please select at leaset one item!';
			break;
			case 'ADMINACCESSCHANGED':  $rootScope.showMsg.success = 'Success! Access changed to "' + (p == '#admin' ? 'Administrator' : 'User') + '"!';
			break;
			case 'ADMINACCESSNOTCHANGED':  $rootScope.showMsg.error = 'Error! Access not changed!';
			break;
			case 'ADMINCHANGEPASSWORD':  $rootScope.showMsg.success = 'Success! Password changed success for ' + p + ' selected users!';
			break;
			case 'ADMINNOCHANGEPASSWORD':  $rootScope.showMsg.error = 'Error! Password change failed!';
			break;
			case 'ADMINBADPASSWORD':  $rootScope.showMsg.warn = 'Please enter correct Password!';
			break;
			case 'ADMINBADSERVICENAME':  $rootScope.showMsg.warn = 'Please enter correct Service Name!';
			break;
			case 'ADMINSERVICEADDED':  $rootScope.showMsg.success = 'Success! Service "' + p + '" added!';
			break;
			case 'ADMINSERVICENOTADDED':  $rootScope.showMsg.error = 'Error! Service "' + p + '" not added!';
			break;
			case 'ADMINDELETEBAD':  $rootScope.showMsg.error = 'Error! Items not deleted!';
			break;
			case 'ADMINDELETEOK':  $rootScope.showMsg.success = 'Success! Items deleted!';
			break;			
			case 'ADMINGLOBALOK':  $rootScope.showMsg.success = 'Success! Parameter changed!';
			break;
			case 'ADMINGLOBALBAD':  $rootScope.showMsg.error = 'Error! Parameter not chaged!';
			break;
			case 'ADMINGLOBALBADINPUT':  $rootScope.showMsg.warn = 'Please enter correct value!';
			break;
			case 'ADMINSTATUSBAD':  $rootScope.showMsg.error = 'Error! Status not chaged!';
			break;	
			case 'ADMINSTATUSOK':  $rootScope.showMsg.success = 'Success! Status changed to "' + (p ? 'Disable' : 'Enable') + '"!';
			break;
			case 'ADMINSTARTSERVICE':  $rootScope.showMsg.warn = 'Try to force start "' + p + '" on selected nodes!';
			break;	
			case 'ADMINSTOPSERVICE':  $rootScope.showMsg.warn = 'Try to force stop all services on selected nodes!';
			break;	
			case 'ADMINREBOOT':  $rootScope.showMsg.warn = 'Try to force reboot selected nodes!';
			break;			
			default: $rootScope.showMsg.error = 'Error! Can`t receive responce from server!';
			break;
		}
	}
	
	// SIMPLIFY POST PROCEDURE
	var HttpPost = function(query, json)
	{		
		return $http({
			url: 'admin/admin.php?query=' + query + '&time=' + new Date().getTime(),
			method: "POST",
			data: json
		});
	}
	
	var httpGet = function(query)
	{		
		return $http.get('admin/admin.php?query=' + query + '&time=' + new Date().getTime());
	}
	
	// GET GLOBAL
	
	var adminGlobal = function() {
		httpGet('getGlobal').success(function(r){
			$rootScope.Global = {};
			if(!r.message) {
				angular.forEach(r, function(value, key){					
					$rootScope.Global[value.name] = value.value;	
				});				
			}							
		});				
	}
	
	// GET DR
	var adminDR = function()
	{	
		httpGet('getDR').success(function(r){
			$rootScope.adminDR = [];
			$rootScope.check3 = {};
			
			if(!r.message) {$rootScope.adminDR = r;}							
		});		 			
	}
	// GET SERVICES
	var adminServices = function()
	{	
		httpGet('getServices').success(function(r){
			$rootScope.adminServices = [];
			$rootScope.check2 = {};
			
			if(!r.message) {$rootScope.adminServices = r;}							
		});		 			
	}
	// GET USERS
	var adminUsers = function()
	{	
		httpGet('getUsers').success(function(r){
			$rootScope.adminUsers = [];
			$rootScope.check1 = {};
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
	
	// USERS
	var adminAddUser = function(u){
		
		var json = {'user': u};
		HttpPost('addUser', json).then(function(r){			
			showMsg(r.data, u);
			adminUsers();
		}, 
		function(r){
			showMsg('ERROR', u);
			adminUsers();
		});	
	};
	
	var adminDeleteUsers = function(u){
		
		var json = {'users': u};
		HttpPost('deleteUsers', json).then(function(r){			
			showMsg(r.data, u);
			adminUsers();
		}, 
		function(r){
			showMsg('ERROR', u);
			adminUsers();
		});	
	};
	
	var adminChangeAccess = function(u, a){
		
		var json = {'users': u, 'access': a};
		HttpPost('changeAccess', json).then(function(r){			
			showMsg(r.data, a);
			adminUsers();			
		}, 
		function(r){
			showMsg('ERROR', u);
			adminUsers();
		});	
	};
	
	var adminChangePassword = function(u, pwd){
		
		var json = {'users': u, 'pwd': pwd};
		HttpPost('changePassword', json).then(function(r){			
			showMsg(r.data, u.length);
			adminUsers();			
		}, 
		function(r){
			showMsg('ERROR', u);
			adminUsers();
		});	
	};
	
	// SERVICES
	
	var adminServiceAdd = function(s){
		var json = {'name': s};
		HttpPost('serviceAdd', json).then(function(r){			
			showMsg(r.data, s);
			adminServices();			
		}, 
		function(r){
			showMsg('ERROR', u);
			adminServices();
		});
	};
	
	var adminServiceDelete = function(s){
		var json = {'names': s};
		HttpPost('serviceDelete', json).then(function(r){			
			showMsg(r.data, s.length);
			
			adminServices();			
		}, 
		function(r){
			showMsg('ERROR');
			adminServices();
		});
	};
	
	var adminServiceDisable = function(s, d) {
		var json = {'names': s, 'status': d};
		HttpPost('serviceDisable', json).then(function(r){			
			showMsg(r.data, d);
			
			adminServices();			
		}, 
		function(r){
			showMsg('ERROR');
			adminServices();
		});
	}
	
	// GLOBAL
	
	var adminGlobalChangeParam = function(n, v){
		var json = {'name': n, 'value': v};
		HttpPost('globalChangeParam', json).then(function(r){			
			showMsg(r.data);			
			adminGlobal();			
		}, 
		function(r){
			showMsg('ERROR');
			adminGlobal();
		});		
	};
	
	var adminSendEmail = function(c, s, n){
		var json = {'content': c, 'subject': s, 'notify': n};
		HttpPost('sendEmail', json).then(function(r){
			adminGlobal();			
		}, 
		function(r){
			showMsg('ERROR');
			adminGlobal();
		});		
	};
	
	// NODE
	
	var adminNodesDisable = function(s, d) {
		var json = {'ip': s, 'status': d};
		HttpPost('nodesDisable', json).then(function(r){			
			showMsg(r.data, d);
			
			adminDR();			
		}, 
		function(r){
			showMsg('ERROR');
			adminDR();
		});
	}
	
	var adminNodeDelete = function(s){
		var json = {'ip': s};
		HttpPost('nodeDelete', json).then(function(r){			
			showMsg(r.data, s.length);
						
			adminDR();			
		}, 
		function(r){
			showMsg('ERROR');
			adminDR();
		});
	};
	
	// WORK WITH SOCKET
	var socket = function(ip, cmd)
	{
		var json = {'ip': ip, 'cmd': cmd};
		
		HttpPost('sendCmd', json).then(function(r){							
			
		}, 
		function(r){			
		});		
	}
	// SEND COMMAND TO ALL SERVERS	
	var sendCmd = function(s, cmd)
	{			
		for(var i = 0; i < s.length; i++)  
		{			
			var ip = s[i];
			
			socket(ip, cmd);				
		}	
	}
		
	return {
		adminGlobal: adminGlobal,
		sendCmd: sendCmd,
		adminDR: adminDR,
		adminServices: adminServices,
		adminUsers: adminUsers,
		adminAddUser: adminAddUser,
		showMsg: showMsg,
		adminDeleteUsers: adminDeleteUsers,
		adminChangeAccess: adminChangeAccess,
		adminChangePassword: adminChangePassword,
		adminServiceAdd: adminServiceAdd,
		adminServiceDelete: adminServiceDelete,
		adminServiceDisable: adminServiceDisable,
		adminGlobalChangeParam: adminGlobalChangeParam,
		adminSendEmail: adminSendEmail,
		adminNodesDisable: adminNodesDisable,
		adminNodeDelete: adminNodeDelete,
		sendCmd: sendCmd
	};
});


app.service('vault', function($http, $rootScope, $timeout, $interval, admin) {
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
		
	var sendMail = function(r){
		var n = $rootScope.Global.notify;
		var isSend = $rootScope.Global.email;
		var c = '';		
		var s = 'Render Farm Manager';
		var u = $rootScope.userInfo.user;
		var nodes = $rootScope.reservedDrNames.join('\r\n');
		var free = $rootScope.freeNodes.length;
		var old = $rootScope.oldNodes;
				
		var m = r.message ? r.message : r;
		
		switch(m){
			case 'NODESDROPPED': 
				c = 'User "' + u + '" drop nodes:\r\n\r\n';
				c += old;
				c += '\r\n\r\nNow free ' + free + ' nodes.';
				s += ': Nodes Dropped';
			break;
			case 'NODESRESERVED':
				c = 'User "' + u + '" reserved nodes:\r\n\r\n';
				c += 'Job Name: ' + $rootScope.jobName + '\r\n'
				c += nodes;
				c += '\r\n\r\nNow free ' + free + ' nodes.';
				s += ': Nodes Dropped';
			break;
		}
			
		if(isSend == 1 && c != ''){			
			admin.adminSendEmail(c, s, n);
		}
	}
	
	// GET DR
	var getDR = function(mailType)
	{	
		$rootScope.oldNodes = $rootScope.reservedDrNames;
	
		httpGet('getDR').success(function(r){
			$rootScope.dr = r;
			$rootScope.reservedDr = [];
			$rootScope.reservedDrNames = [];
			$rootScope.otherUsers = [];
			$rootScope.freeNodes = [];
			
			var runninSrv = [];
			
			angular.forEach(r, function(value, key){
				if(value.user != null) {$rootScope.checkModel[value.ip] = false;}	
				
				if(value.user != '' && value.user != null && $rootScope.otherUsers.indexOf(value.user) == -1) {$rootScope.otherUsers.push(value.user);}
				if((value.user == '' || value.user == null) && value.status == 0) {$rootScope.freeNodes.push(value.name);}
				
				if($rootScope.userInfo && value.user === $rootScope.userInfo.user)
				{
					$rootScope.checkModel[value.ip] = true;
					$rootScope.reservedDr.push(value);
					$rootScope.reservedDrNames.push(value.name);
					
					if(value.services == $rootScope.currentService) {runninSrv.push(true)}			
				}
			});			
			$rootScope.firstLoad++;
			$rootScope.startingSpawners = $rootScope.reservedDr.length != runninSrv.length && $rootScope.currentService.length;	
			
			// SEND EMAIL
			
			if(mailType) {sendMail(mailType);}
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
		
		$rootScope.jobName = jobName;
			
		var json = {};
		json.nodes = nodes;
		json.job = jobName;
		
		HttpPost('getNodes', json).then(function(r){
			showMsg(r.data);										
			getDR(r.data);			
		}, 
		function(r){			
			console.log(r);
		});	
	}
		
	
	var dropNodes = function()
	{		
		var isReserved = $rootScope.reservedDrNames.length > 0;
		httpGet('dropNodes').success(function(r){
			
			$rootScope.currentService = '';
			$rootScope.checkModel = {};	
			
			if(isReserved){
				showMsg(r);
				
				getDR(r);
			}					
		});	
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


