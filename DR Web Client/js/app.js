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

Array.prototype.uniqueArray = function()
{
	var n = []; 
	for(var i = 0; i < this.length; i++) 
	{
		if (n.indexOf(this[i]) == -1) n.push(this[i]);
	}
	return n;
}

document.addEventListener("contextmenu", function(e){
   e.preventDefault();
}, false);

/* APP */

var app = angular.module('app', ['ngRoute', 'ngSanitize', 'ui.bootstrap', 'ngAnimate', 'counter', 'chart.js']);


// CONFIG 
app.config(function($routeProvider) {    
	timeStamp = new Date().getTime();
	$routeProvider
    .when('/home/:office', {
        templateUrl : 'templates/home.php',
		controller: 'homeCtrl'
    })
    .when('/admin/:page', {
        templateUrl : "templates/admin.php",
		controller: 'adminCtrl'
    })
	.when('/statistic/:page', {
        templateUrl : "templates/statistic.php",
		controller: 'statisticCtrl'
    })
	.when('/about/', {
        templateUrl : "templates/about.html",
		controller: 'aboutCtrl'
    }) 	
	.otherwise({redirectTo:'/home/All'});
});
// DIRECTIVES

app.directive("float", function ($window) {
    return function(scope, element, attrs) {
      
        angular.element($window).bind("scroll", function() {
            if (this.pageYOffset >= attrs.float) {                 
                element.addClass('float container');
             } else {
                element.removeClass('float container'); 
             }
            scope.$apply();
        });
    };
});


app.directive("autofloat", function ($window) {
    return function(scope, element, attrs) {
      	
		offsetTop = 0;
		
        angular.element($window).bind("scroll", function() {
			tmpOffset = $(element[0]).offset().top - 10;
			if(!element[0].classList.contains('float')) {offsetTop = tmpOffset}
							
            if (this.pageYOffset >= offsetTop) {                 
                element.addClass('float container');
             } else {
                element.removeClass('float container'); 
             }
            scope.$apply();
        });
    };
});

app.directive("nodeInfo", function($rootScope) {	 
    return {
        templateUrl : 'templates/node-info.html'
    };
});

// CONTROLLERS
	// STATISTIC
	
app.controller("statisticCtrl", function ($scope, vault, admin, $timeout, $interval, $rootScope, $routeParams) {
	$rootScope.totalcnt = 0;
	$rootScope.farmrender = 0;
	$rootScope.farmusage = 0;
	$rootScope.usedoffices = 0;
	$rootScope.usedgroups = 0;
	$rootScope.usersrend = 0;
	$rootScope.youused	= 0;
	$rootScope.efficiencyPercent = 0;
	$rootScope.efficiencyUsed = 0;
	$rootScope.efficiencyUnused = 0;
	$rootScope.counterDuration = 2000;
	
	$rootScope.statistic = {
		'totalcnt': 0,
		'farmusage': 0,
		'farmrender': 0,
		'usedoffices': 0,
		'usedgroups': 0,
		'youused': 0,
		'efficiency': {
			'percent': 0,
			'used': 0,
			'unused': 0
		},
		'usersrend': 0,
		'topuser': 'N/A'
	};
	
	$scope.loadClass = function(t) {
		return 'load-' + t;
	}
	
	$scope.lastWeek = new Date();
	$scope.lastWeek.setDate($scope.lastWeek.getDate() - 7);
	
	// Date Picker
	$rootScope.dt = {
		'from': $scope.lastWeek,
		'to': new Date()
	}
	
	$scope.office_filter = 'Lviv';
	$scope.setOfficeFilter = function(o) {
		$scope.office_filter = o;
		$scope.getStatistic();
	}
				
	$rootScope.period = 'Week';
			
	$scope.changePeriod = function(p) {		
		$rootScope.period = p;
		$scope.getStatistic();
	};
		
	$scope.getStatistic = function() {
		vault.getStatistic($rootScope.period, $scope.dt, $scope.office_filter);
	}
	
	$scope.getStatistic();
		
	$scope.setDate = function(year, month, day) {
		$scope.dt = new Date(year, month, day);
	};
		
		
	$rootScope.dataFarmByNodeEmpl = [];
		
	// RenderTime Graph

	$rootScope.dataMonthRenderTime = [];	
	$rootScope.labelsMonthRenderTime = [];	
	
	$scope.datasetOverrideRenderTime = [
      {
        label: "Hours",
        borderWidth: 3,
        hoverBackgroundColor: "rgba(255,99,132,0.4)",
        hoverBorderColor: "rgba(255,99,132,1)",
		pointRadius: 6   
      },
	  {}
    ];
	
	$scope.colorsRenderTime = ['rgba(0,154,191,0.5)'];
	
	$scope.optionsRenderTime = {
		scales: {
			reverse: true,
			yAxes: [
			{
			 ticks:				
				{
					beginAtZero:true
				}
			}
		  ]
		}
	};
	
	// Render by user graph
	
	$rootScope.dataUserEmpl = [];
	$rootScope.labelsUserEmpl = [];
		
	$rootScope.labelsColorsUserEmpl = ['#35A9E1', '#FF5555', '#FABB3C', '#8064A2', '#4BACC6', '#F79646', '#2C4D75', '#C0504D'];
	$scope.datasetOverrideUserEmpl = [
      {
        label: "Nodes",
        borderWidth: 3
      },
	  {}
    ];
	
	$scope.optionsUserEmpl = {
		responsive: true,
		maintainAspectRatio: true,
		segmentShowStroke: false,
		animateRotate: true,
		animateScale: false,
		percentageInnerCutout: 50,
		legend: {
			display: false,
			position: 'bottom',
			fullWidth: false
		}
	};
	
	// Render by user graph
	
	$rootScope.dataFarmUsage = [];
	$rootScope.labelsFarmUsage = [];
		
	$rootScope.labelsColorsFarmUsage = ['#5CB85C', '#D9534F'];
	$scope.datasetOverrideFarmUsage = [
      {
        label: "Nodes",
        borderWidth: 3
      },
	  {}
    ];
	
	$scope.optionsFarmUsage = {
		responsive: true,
		maintainAspectRatio: true,
		segmentShowStroke: false,
		animateRotate: true,
		animateScale: false,
		percentageInnerCutout: 50,
		legend: {
			display: false,
			position: 'bottom',
			fullWidth: false
		}
	};
	
	// Render by office
	
	$rootScope.dataRenderOffice = [];
	$rootScope.labelsRenderOffice = [];
		
	$rootScope.labelsColorsRenderOffice = ['#8064A2', '#00C0EF', '#D66A34', '#1A8C46', '#DE4B39', '#2C4D75'];
	$scope.datasetOverrideRenderOffice = [
      {
        label: "Nodes",
        borderWidth: 3
      },
	  {}
    ];
	
	$scope.optionsRenderOffice = {
		responsive: true,
		maintainAspectRatio: true,
		segmentShowStroke: false,
		animateRotate: true,
		animateScale: false,
		percentageInnerCutout: 50,
		legend: {
			display: false,
			position: 'bottom',
			fullWidth: false
		}
	};
	
	
	// Farm Empl
	
	
	$rootScope.dataFarmEmpl = [];
	$rootScope.labelsFarmEmpl = [];
		
	$rootScope.labelsColorsFarmEmpl = ['#5CB85C', '#D9534F'];
	$scope.datasetOverrideFarmEmpl = [
      {
        label: "Nodes",
        borderWidth: 3
      },
	  {}
    ];
	
	$scope.optionsFarmEmpl = {
		responsive: true,
		maintainAspectRatio: true,
		segmentShowStroke: false,
		animateRotate: true,
		animateScale: false,
		percentageInnerCutout: 50,
		legend: {
			display: false,
			position: 'bottom',
			fullWidth: false
		}
	};
	
	// Farm Nodes Empl
	
	
	$rootScope.dataFarmPowerEmpl = [];
	$rootScope.labelsFarmPowerEmpl = [];
		
	$rootScope.labelsColorsFarmPowerEmpl = ['#0D95BC', '#063951', '#EBCB38', '#A2B969', '#0D95BC', '#802750', '#482B56', '#A21C56', '#F36F13'];
	$scope.datasetOverrideFarmPowerEmpl = [
      {
        label: "Nodes",
        borderWidth: 3
      },
	  {}
    ];
	
	$scope.optionsFarmPowerEmpl = {
		responsive: true,
		maintainAspectRatio: true,
		segmentShowStroke: false,
		animateRotate: true,
		animateScale: false,
		percentageInnerCutout: 50,
		legend: {
			display: false,
			position: 'bottom',
			fullWidth: false
		}
	};
	
});


	// ABOUT
app.controller("aboutCtrl", function($scope){
	
});

	// ADMIN
app.controller("adminCtrl", function($scope, $rootScope, admin, $routeParams){
	$scope.adminSection = $routeParams.page;
	$rootScope.adminDR = [];
	$rootScope.Global = {};
	$rootScope.adminServices = [];	
	$rootScope.adminGroups = [];	
	$rootScope.adminOffices = [];	
	$rootScope.adminUsers = [];
	$rootScope.checkAdminDR = [];
	$rootScope.checkAdminUsers = [];
	$rootScope.checkAdminItems = [];
	$rootScope.Groups = [];
	$rootScope.Offices = [];
	
	admin.adminGlobal();
	admin.adminDR();
	admin.adminServices();
	admin.adminUsers();
	admin.adminGroups();
	admin.adminOffices();
	
	$scope.getAdminGlobal = function(){admin.adminGlobal();};
	$scope.getAdminDR = function(){admin.adminDR();};
	$scope.getAdminServices = function(){admin.adminServices();};
	$scope.getAdminUsers = function(){admin.adminUsers();};
	
	$scope.adminCheckAllNodes = function(){
		angular.forEach($rootScope.adminDR, function(value, key){			
			$rootScope.check3[value.ip] = true;
		});
	}
	
	$scope.nodeSortParam = 'name';
	$scope.nodeSortReverce = true;
	
	$scope.nodeSort = function(param){	
		$scope.nodeSortReverce = ($scope.nodeSortParam === param) ? !$scope.nodeSortReverce : false;
		$scope.nodeSortParam = param;
	}
	
	$scope.userSortParam = 'user';
	$scope.userSortReverce = false;
	
	$scope.userSort = function(param){	
		$scope.userSortReverce = ($scope.userSortParam === param) ? !$scope.userSortReverce : false;
		$scope.userSortParam = param;
	}
	
	$scope.adminUncheckAllNodes = function(){$rootScope.check3 = [];}
	
	$scope.installedServices = function(s){	
		var out = '';
		
		angular.forEach(s.split(';'), function(value, key){
			var r = value.split('=');
			if(r[1] != 'notfound' && r[0]) {out += r[0] + '<br>'}
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
			alert('Please select nodes!');
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
			alert('Please select nodes!');
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
	
	$scope.adminAssignNodeGroup = function(grp) {
		var s = $rootScope.checkAdminDR;
	
		if((!grp || !grp.length) && grp != -1) {
			admin.showMsg('ADMINBADGROUP');
			return false;
		}
		
		if(!s.length) {
			alert('Please select nodes!');
			return false;
		}
		
		if(grp == -1) {grp = null;}
		
		admin.adminAssignNodeGroup(s, grp);
	}
	
	$scope.adminAssignGroup = function(a) {
		var u = $rootScope.checkAdminUsers;
		var grp = null;
			
		if(!u.length) {
			alert('Please select users!');
			return false;
		}
					
		switch(a) {
			case 1: {
				grp = prompt("Please enter group name!)", "");
				if(!grp || !grp.length) {
					admin.showMsg('ADMINBADGROUP');
					return false;
				}
			}
			break;
			case -1:
			{
				if(!confirm('Do you really want to clear groups for ' + u.length + ' selected users?')){
					return false;
				}
				
				grp = null;
			}
			break;
			default:
			{
				if(!a || !a.length) {
					admin.showMsg('ADMINBADGROUP');
					return false;
				}
				
				grp = a;
			}
			break;
		}
			
		admin.adminAssignGroup(u, grp);
	};
	
	$scope.adminNodesPower = function(type) {
		var s = $rootScope.checkAdminDR;
		var power = 0;
						
		if(!s.length) {
			alert('Please select nodes!');
			return false;
		}
			
		if(!confirm('Do you really want to change power for selected nodes?')){
			return false;
		}
			
		
		power = prompt("Please enter the power in Watt", "");
		
		if(power.match(/[^0-9]/)) {
			alert('Wrong format! Please enter just number ex.: 250');
			return false;
		}
					
		admin.adminNodesPower(s, type, power);
	};
	
	$scope.adminAssignNodeOffice = function(a) {
		var s = $rootScope.checkAdminDR;
		var office = null;
			
		if(!s.length) {
			alert('Please select nodes!');
			return false;
		}
					
		switch(a) {
			case 1: {
				office = prompt("Please enter office name!)", "");
				if(!office || !office.length) {
					admin.showMsg('ADMINBADGROUP');
					return false;
				}
			}
			break;
			case -1:
			{
				if(!confirm('Do you really want to clear office for ' + s.length + ' selected nodes?')){
					return false;
				}
				
				office = null;
			}
			break;
			default:
			{
				if(!a || !a.length) {
					admin.showMsg('ADMINBADGROUP');
					return false;
				}
				
				office = a;
			}
			break;
		}
			
		admin.adminAssignNodeOffice(s, office);
	};
	
	$scope.adminNodesDescription = function(a) {
		var s = $rootScope.checkAdminDR;
		var desc = null;
						
		if(!s.length) {
			alert('Please select nodes!');
			return false;
		}
			
		if(!confirm('Do you really want to change description for selected nodes?')){
			return false;
		}
			
		if(a == true) {
			desc = prompt("Please enter the description", "");
		}
					
		admin.adminNodesDescription(s, desc);
	};
	
	// SERVICES
	
	$scope.adminItemAdd = function(item){
		name = prompt("Please enter the name", "");
		
			
		if(!name || !name.length) {			
			admin.showMsg('ADMINBADITEMNAME');
			
			return false;
		}
		
		admin.adminItemAdd(item, name);
	}
	
	$scope.adminItemDelete = function(item){
		var s = $rootScope.checkAdminItems;
		
		if(!s.length){
			alert('Please select nodes!');
			return false;
		}
		
		if(!confirm('Do you really want to delete ' + s.length + ' selected items?')){
			return false;
		}
		
		admin.adminItemDelete(s, item);
	};
	
	$scope.adminItemDisable = function(d, item){
		var s = $rootScope.checkAdminItems;
		
		if(!s.length){
			alert('Please select nodes!');
			return false;
		}
		
		if(!confirm('Do you really want to change status for ' + s.length + ' selected items?')){
			return false;
		}
		
		admin.adminItemDisable(s, d, item);
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
			alert('Please select nodes!');
			return false;
		}
		
		if(!confirm('Do you really want to change status for ' + s.length + ' selected nodes?')){
			return false;
		}
		
		admin.adminNodesDisable(s, d);
	}
	
	$scope.adminNodesAutoStartSrv = function(d){
		var s = $rootScope.checkAdminDR;
		
		if(!s.length){
			alert('Please select nodes!');
			return false;
		}
		
		if(!confirm('Do you really want to change auto start BBurner for ' + s.length + ' selected nodes?')){
			return false;
		}
		
		admin.adminNodesAutoStartSrv(s, d);
	}
	
	$scope.adminNodeDelete = function(){
		var s = $rootScope.checkAdminDR;
		
		if(!s.length){
			alert('Please select nodes!');
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
			alert('Please select nodes!');
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
	
	$scope.adminUpdateNodes = function() {
			
		if(!confirm('Do you really want to update DR Server for selected nodes?')){
			return false;
		}
		
		admin.showMsg('ADMINUPDATE');
		
		$scope.sendCmd('UPDATE');
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
	$rootScope.checkItem = {};
	$scope.$watchCollection('checkItem', function () {
		$rootScope.checkAdminItems = [];
		
		angular.forEach($rootScope.checkItem, function (value, key) {
		  if (value) {
			$rootScope.checkAdminItems.push(key);			
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
app.controller("homeCtrl", function ($scope, vault, admin, $timeout, $interval, $rootScope, $routeParams, $sce) {
	$rootScope.Global = {};
	admin.adminGlobal();
	// sendChallange 
	vault.getServices();
	vault.getOffices();
	
	$rootScope.otherUsers = [];
		
	$scope.hideShowNodeInfo = function(ip) {
		$rootScope.showNodeInfo = ip;
		
		if(!ip){			
			return false;
		}
		
		vault.getNodeInfo(ip);
		$rootScope.checkModel[ip] = true;
	}
		
	$rootScope.sendChallange = function(){
		//vault.sendChallenge();
		
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
			if(value.user == '' || value.user == null && value.cpu < 60){
				$rootScope.checkModel[value.ip] = true;
			}
		});
	}
	
	$scope.checkReserved = function() {
		$rootScope.checkModel = {};
		angular.forEach($rootScope.dr, function(value, key){
			if(value.user == $rootScope.userInfo.user){
				$rootScope.checkModel[value.ip] = true;
			}
		});
	}
		
	$scope.getRam = function(r, a)	{
		f = parseFloat(r) - parseFloat(a);		
		f = Math.round(parseFloat(f) * 100.0) / 100.0;
		return  f + " GB / " + r + " GB";
	}
	
	$scope.getTooltip = function(ip, desc) { 
		t = '';		
		if(ip) {t += '<b>IP</b>: ' + ip + '<br>';}
		if(desc) {t += '<b>INFO</b>: ' + desc + '<br>';}
		return t;	
	}
	
	$scope.getFreeRam = function(r, a)	{	
		return Math.round((parseFloat(r) - parseFloat(a)) / parseFloat(r) * 100.0);
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
	
	$scope.dropSelectedNodes = function()
	{		
		vault.dropSelectedNodes();	
	};
		
	
	$scope.kickSelectedNodes = function() 
	{
		if(!confirm('Do you really want to kick users on selected nodes?'))
		{
			return false;
		}
		
		vault.kickSelectedNodes();
	}
	
	$scope.isReserved = function(user)
	{
		return user != null && $rootScope.userInfo && user != $rootScope.userInfo.user;
	};
	
	$scope.office = $routeParams.office;
		
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
			if(!confirm('Do you really want to start "' + x + '" on selected nodes?'))
			{
				return false;
			}	
			
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
	
	vault.getDR();
	
	if (angular.isDefined($rootScope.timer)) {
		$interval.cancel($rootScope.timer);
		$rootScope.timer = undefined;
	}
	
	$rootScope.timer = $interval( function(){					
		vault.getDR();
	}, 5000);	
	

	$rootScope.checkModel = {};
	$scope.$watchCollection('checkModel', function () {
		$rootScope.checkResults = [];
		$rootScope.checkResultsNames = [];
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
    
	$rootScope.showNodeInfo = false;
	
	$rootScope.stringToColour = function(str) {
		if(!str) {return 'gray';}
		
		var hash = 0;
		for (var i = 0; i < str.length; i++) {
			hash = str.charCodeAt(i) + ((hash << 4) - hash);
		}
		var color = '#';
		for (var i = 0; i < 3; i++) {
			var value = ((hash >> (i * 1)) & 0xFF) * 2;
			color += ('00' + value.toString(16)).substr(-2);
		}
		return color;
	}
	
	
	$rootScope.$watch(function() { 
        return $location.path(); 
    },
     function(a){
		// INIT
		$rootScope.socketResponse = {};
		$rootScope.showMsg = {};
		$rootScope.logIn = function(){vault.logIn();}
		$rootScope.logOut = function(m){
			if(confirm('Do you really want to logut?'))
			{
				vault.logOut(m);
			}					
		}
		$rootScope.isIE = vault.isIE();
		
		vault.logIn();
		
		$rootScope.loginShown = false;
		
		$rootScope.adminDropNodes = function(u){
			if(confirm('Do you really want to drop ' + u + '?'))
			{
				vault.adminDropNodes(u);
			}			
		}
    });
});
// SERVICES

app.service('admin', function($http, $rootScope, $timeout, $interval, $timeout) {	
	
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
			case 'ADMINBADITEMNAME':  $rootScope.showMsg.warn = 'Please enter correct name!';
			break;
			case 'ADMINITEMADDED':  $rootScope.showMsg.success = 'Success! Item "' + p + '" added!';
			break;
			case 'ADMINITEMNOTADDED':  $rootScope.showMsg.error = 'Error! Item "' + p + '" not added!';
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
			case 'ADMINCHAGEPOWERBAD':  $rootScope.showMsg.error = 'Error while update the value!';
			break;	
			case 'ADMINSTATUSOK':  $rootScope.showMsg.success = 'Success! Status changed to "' + (p ? 'Disable' : 'Enable') + '"!';
			break;
			case 'ADMINSTARTSERVICE':  $rootScope.showMsg.warn = 'Try to force start "' + p + '" on selected nodes!';
			break;	
			case 'ADMINSTOPSERVICE':  $rootScope.showMsg.warn = 'Try to force stop all services on selected nodes!';
			break;	
			case 'ADMINREBOOT':  $rootScope.showMsg.warn = 'Try to force reboot selected nodes!';
			break;
			case 'ADMINUPDATE':  $rootScope.showMsg.warn = 'Try to force update DR Server on selected nodes!';
			break;
			case 'NONODES':  $rootScope.showMsg.error = 'Please select at least one node!';
			break;			
			case 'ERROR': $rootScope.showMsg.error = 'Error! Can`t receive responce from server!';
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
			
			// GET GROUPS
			$rootScope.Offices = [];
			angular.forEach(r, function(value, key){
				
				if($rootScope.Offices.indexOf(value.office) == -1 && value.office) {
					$rootScope.Offices.push(value.office);	
				}
			});
			
			if(!r.message) {$rootScope.adminDR = r;}							
		});		 			
	}
	// GET SERVICES
	var adminServices = function()
	{	
		httpGet('getServices').success(function(r){
			$rootScope.adminServices = [];
			$rootScope.checkItem = {};
			
			if(!r.message) {$rootScope.adminServices = r;}							
		});		 			
	}
	
	var adminGroups = function()
	{	
		httpGet('getGroups').success(function(r){
			$rootScope.adminGroups = [];
			$rootScope.checkGroups = {};
			
			if(!r.message) {$rootScope.adminGroups = r;}							
		});		 			
	}
	
	var adminOffices = function()
	{	
		httpGet('getOffices').success(function(r){
			$rootScope.adminOffices = [];
			$rootScope.checkOffices = {};
			
			if(!r.message) {$rootScope.adminOffices = r;}							
		});		 			
	}
	
	
	// GET USERS
	var adminUsers = function()
	{	
		httpGet('getUsers').success(function(r){
			$rootScope.adminUsers = [];
			$rootScope.check1 = {};
			if(!r.message) {
				$rootScope.adminUsers = r;
				
				// GET GROUPS
				$rootScope.Groups = [];
				angular.forEach(r, function(value, key){
					
					if($rootScope.Groups.indexOf(value.group) == -1 && value.group) {
						$rootScope.Groups.push(value.group);	
					}
				});
			}							
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
		});	
	};
	
	var adminDeleteUsers = function(u){
		
		var json = {'users': u};
		HttpPost('deleteUsers', json).then(function(r){			
			showMsg(r.data, u);
			adminUsers();
		});	
	};
	
	var adminChangeAccess = function(u, a){
		
		var json = {'users': u, 'access': a};
		HttpPost('changeAccess', json).then(function(r){			
			showMsg(r.data, a);
			adminUsers();			
		});	
	};
	
	var adminChangePassword = function(u, pwd){
		
		var json = {'users': u, 'pwd': pwd};
		HttpPost('changePassword', json).then(function(r){			
			showMsg(r.data, u.length);
			adminUsers();			
		});	
	};
	
	var adminAssignGroup = function(u, grp){
		
		var json = {'users': u, 'grp': grp};
		HttpPost('assignNewGroup', json).then(function(r){			
			
			showMsg(r.data, u.length);
			adminUsers();			
		});	
	};
			
	
	// SERVICES
	
	var adminItemAdd = function(item, s){
		var json = {'name': s, 'item': item};
		HttpPost('itemAdd', json).then(function(r){			
			showMsg(r.data, s);
			
			if(item == 'services') {adminServices();}			
			if(item == 'offices') {adminOffices();}			
			if(item == 'groups') {adminGroups();}			
		});
	};
	
	var adminItemDelete = function(s, item){
		var json = {'names': s, 'item': item};
		HttpPost('itemDelete', json).then(function(r){			
			showMsg(r.data, s.length);
			
			if(item == 'services') {adminServices();}			
			if(item == 'offices') {adminOffices();}			
			if(item == 'groups') {adminGroups();}	
		});
	};
	
	var adminItemDisable = function(s, d, item) {
		var json = {'names': s, 'status': d, 'item': item};
		HttpPost('itemDisable', json).then(function(r){			
			showMsg(r.data, d);
			
			if(item == 'services') {adminServices();}			
			if(item == 'offices') {adminOffices();}			
			if(item == 'groups') {adminGroups();}			
		});
	}
	
	// GLOBAL
	
	var adminGlobalChangeParam = function(n, v){
		var json = {'name': n, 'value': v};
		HttpPost('globalChangeParam', json).then(function(r){			
			showMsg(r.data);			
			adminGlobal();			
		});		
	};
	
	var adminSendEmail = function(c, s, n, u){
		var json = {'content': c, 'subject': s, 'notify': n, 'users': u};
			console.log(json)	
		HttpPost('sendEmail', json).then(function(r){
			console.log(r.data)
			adminGlobal();			
		});		
	};
	
	// NODE
		
	var adminNodesDisable = function(s, d) {
		var json = {'ip': s, 'status': d};
		HttpPost('nodesDisable', json).then(function(r){			
			showMsg(r.data, d);
			
			adminDR();			
		});
	}
	
	var adminNodesAutoStartSrv = function(s, d) {
		var json = {'ip': s, 'status': d};
		HttpPost('nodesSrvAutoStart', json).then(function(r){			
			showMsg(r.data, d);
			
			adminDR();			
		});
	}
	
	var adminAssignNodeGroup = function(s, grp){
		
		var json = {'ip': s, 'grp': grp};
		HttpPost('adminAssignNodeGroup', json).then(function(r){			
			console.log(r.data)
			showMsg(r.data, s.length);
			adminDR();			
		});	
	};
	
	var adminAssignNodeOffice = function(s, office){
		
		var json = {'ip': s, 'office': office};
		HttpPost('adminAssignNodeOffice', json).then(function(r){			
			console.log(r.data)
			showMsg(r.data, s.length);
			adminDR();			
		});	
	};
	
	var adminNodesPower = function(s, type, val){
		
		var json = {'ip': s, 'type': type, 'val': val};
		HttpPost('adminNodesPower', json).then(function(r){			
			console.log(r.data)
			showMsg(r.data, s.length);
			adminDR();			
		});	
	};
	
	var adminNodesDescription = function(s, desc){
		
		var json = {'ip': s, 'desc': desc};
		HttpPost('adminNodesDescription', json).then(function(r){			
			console.log(r.data)
			showMsg(r.data, s.length);
			adminDR();			
		});	
	};
	
	var adminNodeDelete = function(s){
		var json = {'ip': s};
		HttpPost('nodeDelete', json).then(function(r){			
			showMsg(r.data, s.length);
						
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
		adminItemAdd: adminItemAdd,
		adminItemDelete: adminItemDelete,
		adminItemDisable: adminItemDisable,
		adminGlobalChangeParam: adminGlobalChangeParam,
		adminSendEmail: adminSendEmail,
		adminNodesDisable: adminNodesDisable,
		adminNodeDelete: adminNodeDelete,
		sendCmd: sendCmd,
		adminAssignGroup: adminAssignGroup,
		adminAssignNodeGroup: adminAssignNodeGroup,
		adminAssignNodeOffice: adminAssignNodeOffice,
		adminGroups: adminGroups,
		adminOffices: adminOffices,
		adminNodesDescription: adminNodesDescription,
		adminNodesAutoStartSrv: adminNodesAutoStartSrv,
		adminNodesPower: adminNodesPower
	};
});


app.service('vault', function($http, $rootScope, $timeout, $interval, admin, $routeParams) {
	// MESSAGES
	var showMsg = function(r, p)
	{
		$rootScope.showMsg = {};
		
		$rootScope.hideMsg = false;
		
		$timeout.cancel($rootScope.msgTimer);
		$rootScope.msgTimer = $timeout(function(){
			$rootScope.hideMsg = true;
		}, 5500);
		
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
				if($rootScope.reservedDr.length)
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
			case 'NONODES':  $rootScope.showMsg.error = 'Please select at leaset one node!';
			break;
			case 'NONODESFORDROP':  $rootScope.showMsg.warn = 'You have no reserved nodes!';
			break;
			case 'REBOOT':  $rootScope.showMsg.warn = 'Nodes will reboot! Update the status in few minutes...';
			break;			
			case 'STARTSERVICE':  $rootScope.showMsg.warn = 'Start ' + p + ' on all selected nodes! Update the status in few minutes...';			
			break;					
			case 'NODESSELDROPPED':  
				if(r.cnt > 0)
				{
					$rootScope.showMsg.success = 'Success. Selected ' + r.cnt + ' nodes dropped!';
				}
				else
				{
					$rootScope.showMsg.warn = 'No nodes dropped!';
				}
				
			break;
			case 'NODESELKICK':  
			if(r.cnt > 0)
			{
				$rootScope.showMsg.success = 'Success. Selected ' + r.cnt + ' user nodes canceled!';
			}
			else
			{
				$rootScope.showMsg.warn = 'No nodes canceled!';
			}
				
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
				c = 'User "' + u + '" has dropped nodes:\r\n\r\n';
				c += old;
				c += '\r\n\r\nNow ' + free + ' nodes are available.';
				s += ': Nodes Dropped!';
			break;
			case 'NODESRESERVED':
				c = 'User "' + u + '" reserved nodes:\r\n\r\n';
				c += 'Job Name: ' + $rootScope.jobName + '\r\n'
				c += nodes;
				c += '\r\n\r\nNow ' + free + ' nodes are available.';
				s += ': Nodes Reserved!';
			break;			
		}
			
		if(isSend == 1 && c != ''){			
			admin.adminSendEmail(c, s, n);
		}
	}
	
	var getStatistic = function(period, dt, office_filter)
	{
		var json = {'page': 'main', 'period': period, 'dt': dt, 'office': office_filter};
		HttpPost('getStatistic', json).then(function(r){			
			
		
			$rootScope.statistic = r.data;
			
			$rootScope.labelsMonthRenderTime = [];
			$rootScope.dataMonthRenderTime = [];
			if(r.data.rendertime) {
				$rootScope.dataMonthRenderTime = [r.data.rendertime.data];
				$rootScope.labelsMonthRenderTime = r.data.rendertime.label;
			}
			
			$rootScope.dataUserEmpl = [];
			$rootScope.labelsUserEmpl = [];
			if(r.data.renderbyuser) {
				$rootScope.dataUserEmpl = r.data.renderbyuser.data;
				$rootScope.labelsUserEmpl = r.data.renderbyuser.label;
			}
			
			$rootScope.dataFarmEmpl = [];
			$rootScope.labelsFarmEmpl = [];
			if(r.data.rendertime && r.data.rendertime.empl) {
				$rootScope.dataFarmEmpl = r.data.rendertime.empl.data;
				$rootScope.labelsFarmEmpl = r.data.rendertime.empl.label;
			}
			
			$rootScope.dataFarmPowerEmpl = [];
			$rootScope.labelsFarmPowerEmpl = [];
			if(r.data.rendertime && r.data.rendertime.power) {
				$rootScope.dataFarmPowerEmpl = r.data.rendertime.power.data;
				$rootScope.labelsFarmPowerEmpl = r.data.rendertime.power.label;
			}
			
			if(r.data.rendertime && r.data.rendertime.bynode) {
				$rootScope.dataFarmByNodeEmpl = r.data.rendertime.bynode;
			}
			
			$rootScope.dataRenderOffice = [];
			$rootScope.labelsRenderOffice = [];		
			//$rootScope.labelsColorsRenderOffice = [];
			if(r.data.rendbyoffice) {
				$rootScope.dataRenderOffice = r.data.rendbyoffice.data;
				$rootScope.labelsRenderOffice = r.data.rendbyoffice.label;
				/*angular.forEach($rootScope.labelsRenderOffice, function(item, key) {
					var c = $rootScope.stringToColour(item);
					$rootScope.labelsColorsRenderOffice.push(c);
				});*/
			}
			
			
			$rootScope.dataFarmUsage = [r.data.farmrender, r.data.farmidle];
			$rootScope.labelsFarmUsage = ['Rendering Nodes (' + r.data.farmusage + '%)', 'Stand Idle (' + r.data.farmunused + '%)'];
			console.log(r.data)
			
		});		
	}
	
	// GET DR
	var getDR = function(mailType)
	{	
		$rootScope.oldNodes = $rootScope.reservedDrNames;
		
		var json = {'office': $routeParams.office};	
		HttpPost('getDR', json).then(function(r){
			//console.log(r.data);
			$rootScope.dr = r.data;
			$rootScope.reservedDr = [];
			$rootScope.reservedDrNames = [];
			$rootScope.otherUsers = [];
			$rootScope.freeNodes = [];
			
			var runninSrv = [];
			
			angular.forEach(r.data, function(value, key){
				//if(value.user != null) {$rootScope.checkModel[value.ip] = false;}	
				
				if(value.user != '' && value.user != null && $rootScope.otherUsers.indexOf(value.user) == -1) {$rootScope.otherUsers.push(value.user);}
				if((value.user == '' || value.user == null) && value.status == 0) {$rootScope.freeNodes.push(value.name);}
				
				if($rootScope.userInfo && value.user === $rootScope.userInfo.user)
				{
					//$rootScope.checkModel[value.ip] = true;
					$rootScope.reservedDr.push(value);
					$rootScope.reservedDrNames.push(value.name);
					$rootScope.jobName = value.job;
					if(value.services == $rootScope.currentService) {runninSrv.push(true)}			
				}
			});			
			
			//$rootScope.startingSpawners = $rootScope.reservedDr.length != runninSrv.length && $rootScope.currentService.length;	
			$rootScope.startingSpawners = false;	
			
			// SEND EMAIL
			
			if(mailType) {sendMail(mailType);}
		});		 			
	}
	
	var getNodeInfo = function(ip) {
		var json = {'ip': ip};	
		$rootScope.showNodeInfo = false;
		$rootScope.nodeInfo = {};
		
		HttpPost('getNodeInfo', json).then(function(r){
			
			$rootScope.showNodeInfo = true;
			$rootScope.nodeInfo = r.data;
			showMsg(r.data);															
		}, 
		function(r){			
			console.log(r);
		});
	}
	
	var getOffices = function()
	{	
		httpGet('getOffices').success(function(r){
							
			if(!r.message) {$rootScope.Offices = r;	}							
		});		 			
	}
		
		
	// WORK WITH SOCKET
	var socket = function(ip, cmd)
	{
		var json = {'ip': ip, 'cmd': cmd};
		HttpPost('socket', json).then(function(r){
			$rootScope.socketResponse[ip] =  r.data;
			
			//getDR();			
		}, 
		function(r){
			$rootScope.socketResponse[ip] =  'DISCONNECTED';
		});		
	}
	// SEND COMMAND TO ALL SERVERS	
	var sendCmd = function(cmd)
	{
		var a = $rootScope.dr;
		var b = [];
		
		
		for(var i = 0; i < a.length; i++)  
		{			
			var ip = a[i].ip;
			var user = a[i].user;
			if($rootScope.userInfo && user === $rootScope.userInfo.user && $rootScope.checkModel[ip] == true)
			{
				b.push(ip);		
			}			
		}	
				
		if(b.length == 0) {			
			showMsg('NONODES');
			$rootScope.startingSpawners = false;
			return false;
		}
		
		angular.forEach(b, function(value, key){						
			socket(value, cmd);
		});
			
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
		showMsg('STARTSERVICE', name);
		sendCmd('STARTSERVICE:' + name);		
	}
	// REBOOT NODE
	var rebootNodes = function()
	{
		showMsg('REBOOT');
		sendCmd('REBOOT');
	
	}
	// GET USER INFO
	var logOut = function(m)
	{
		httpGet('logout').success(function(r){
			r = {};
			r.user = ''
			r.logged = false;
			r.msg = 'You are logout!';
			if(m) {r.msg = m}
			$rootScope.userInfo = r;	
			document.execCommand("ClearAuthenticationCache");
			$timeout(function(){location.reload();}, 1000);
		});		
	}
	
	var logIn = function()
	{			
		httpGet('login').success(function(r){			
						
			if(!r.logged && $rootScope.loginShown > 3) 
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
			
			
			$rootScope.loginShown++;
			$rootScope.userInfo = r;			
			if(!r.logged) {
				$rootScope.userInfo = {};
				logIn();
			}										
		})
		.error(function(r){
			$rootScope.userInfo = {'error': 'Please enter correct e-mail and password!'}; 
		});		 				
	}

	
	var getNodes = function()
	{	
		var nodes = $rootScope.checkResults;
		var jobName = '';
		
		if(!nodes.length) {
			showMsg('NONODES');
			return false;
		}
		
		if(!$rootScope.reservedDr.length) {
		
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
		}
		else
		{
			jobName = $rootScope.jobName;
		}
					
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
			else
			{
				showMsg('NONODESFORDROP');
			}
		});	
	}
	
	var dropSelectedNodes = function()
	{	
		var nodes = $rootScope.checkResults;
				
		if(!nodes.length) {
			showMsg('NONODES');
			return false;
		}
				
					
		var json = {};
		json.nodes = nodes;
		
		HttpPost('dropSelectedNodes', json).then(function(r){			
			showMsg(r.data);										
			getDR(r.data);			
		}, 
		function(r){			
			console.log(r);
		});	
	}
	
	var sendCmdForIps = function(cmd, ips)
	{
		for(var i = 0; i < ips.length; i++)  
		{			
			var ip = ips[i];
			
			if($rootScope.userInfo && $rootScope.checkModel[ip] == true)
			{
				socket(ip, cmd);	
			}						
		}	
	}
	
	var kickSelectedEmail = function(user, nodes) 
	{
		s = 'Render Nodes Canceled!';
		
		c = 'Your Render nodes were canceled by the ' + $rootScope.userInfo.user + '\r\n\r\n';
		c += 'Please contact this user or system administrator about this issue.\r\n'
		c += '\r\n\r\nList of canceled nodes:\n';
		c += nodes.join('\n');
				
		admin.adminSendEmail(c, s, 3, [user]);		
	}
		
	var kickSelectedNodes = function()
	{	
		var nodes = $rootScope.checkResults;
		var users = [];
		var nodesList = {};
				
		if(!nodes.length) {
			showMsg('NONODES');
			return false;
		}
								
		var json = {};
		json.nodes = nodes;
		
		
		angular.forEach($rootScope.dr, function(value, key){						
			if(nodes.indexOf(value.ip) != -1){
				users.push(value.user);
				if(nodesList[value.user] == undefined) {nodesList[value.user] = [];}
				if(value.user) {nodesList[value.user].push(value.name)};
			}
		});
		
	
		json.users = users;
		
		HttpPost('kickSelectedNodes', json).then(function(r){						
			sendCmdForIps('STOPSERVICES', nodes);
			
			angular.forEach(nodesList, function(value, key){						
				kickSelectedEmail(key, value);
			});
					
			showMsg(r.data);										
			getDR(r.data);			
		}, 
		function(r){			
			console.log(r);
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
	adminDropNodes: adminDropNodes,
	isIE: isIE,	
	getServices: getServices,
	getLastNodes: getLastNodes,
	dropSelectedNodes: dropSelectedNodes,
	kickSelectedNodes: kickSelectedNodes,
	sendCmdForIps: sendCmdForIps,
	getOffices: getOffices,
	getNodeInfo: getNodeInfo,
	getStatistic: getStatistic
  };

});


