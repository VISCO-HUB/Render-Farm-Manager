<?php

?>


<div ng-show="Global.status==0" class="alert alert-warning" role="alert">
	{{Global.message}}
</div>

<div ng-show="Global.status==1 || userInfo.admin" class="home">

<div style="height: 20px">
	<div float="100">
		<button type="button" class="btn btn-primary" ng-click="sendChallange()" ng-show="false"><span class="glyphicon glyphicon-refresh" aria-hidden="true" ></span> Update</button>
		<label class="pull-right hidden-xs" ng-show="!isIE"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> <input ng-model="search.$"></label> 
		<button type="button" class="btn btn-primary" ng-click="uncheckAll()">Uncheck All</button>
		<button type="button" class="btn btn-primary" ng-click="checkFree()">Check Free</button>
		<button type="button" class="btn btn-primary" ng-click="checkReserved()" ng-show="reservedDr.length">Check Reserved</button>
		<button type="button" class="btn btn-primary" ng-click="getLastNodes()">Last Used</button>		
	</div>
</div>
<br><br>

<div class="table-responsive">
<table class="table table-hover">
	<tr>
		<th width="30px">#</th><th width="20%" ng-click="orderByParam('name')"><span>Name</span> <span class="glyphicon" ng-show="orderNodes == 'name'" ng-class="reverse ? 'glyphicon-triangle-bottom' : 'glyphicon-triangle-top'" aria-hidden="true"></span></th>
		<th ng-click="orderByParam('user')"><span>User</span> <span class="glyphicon" ng-show="orderNodes == 'user'" ng-class="reverse ? 'glyphicon-triangle-bottom' : 'glyphicon-triangle-top'" aria-hidden="true"></span></th>
		<th ng-click="orderByParam('job')"><span>Job Name</span> <span class="glyphicon" ng-show="orderNodes == 'job'" ng-class="reverse ? 'glyphicon-triangle-bottom' : 'glyphicon-triangle-top'" aria-hidden="true"></span></th>
		<th ng-click="orderByParam('services')"><span>Service</span> <span class="glyphicon" ng-show="orderNodes == 'services'" ng-class="reverse ? 'glyphicon-triangle-bottom' : 'glyphicon-triangle-top'" aria-hidden="true"></span></th>
		<th width="25%" ng-click="orderByParam('cpu')" class="hidden-xs"><span>CPU</span> <span class="glyphicon" ng-show="orderNodes == 'cpu'" ng-class="reverse ? 'glyphicon-triangle-bottom' : 'glyphicon-triangle-top'" aria-hidden="true"></span></th>
	</tr>
	<tr ng-repeat="node in dr | orderBy:orderNodes:reverse | filter:search" ip="{{node.ip}}" ng-if="node.status==0" ng-show="socketResponse[node.ip] != 'DISCONNECTED'" ng-disabled="(isReserved(node.user) || (node.cpu > 60 && node.user != userInfo.user)) && !userInfo.admin"  
	ng-model="checkModel[node.ip]" uib-btn-checkbox ng-class="{disabled: isReserved(node.user) || (node.cpu > 60 && node.user != userInfo.user), reserved: node.user == userInfo.user && userInfo.user.length}" ng-mouseleave="chekSwipe($event, node.ip, isReserved(node.user) || node.cpu > 60)">
		<td ><span class="glyphicon" ng-class="checkModel[node.ip] ? 'glyphicon-check': 'glyphicon-unchecked'" aria-hidden="true"></span></td>
		<td ><a href="" uib-tooltip="IP: {{node.ip}}">{{node.name}}</a></td>
		<td >
			{{node.user}}
		</td>
		<td >
			{{node.job}}
		</td>
		<td >
			<span ng-show="node.services">{{node.services}}</span>
			<span ng-show="!node.services">None</span>
		</td>
		<td style="min-width: 100px;">
		<div class="progress" style="margin-bottom: 0;">		
		  <div class="progress-bar progress-bar-striped active" ng-style="{'width': node.cpu + '%'}" ng-class="{'progress-bar-success': node.cpu > 0, 'progress-bar-warning': node.cpu > 33, 'progress-bar-danger': node.cpu > 66}">{{node.cpu}}%</div>		 		
		</div>
		</td>
	</tr>
</table>
</div>

<div class="popup" ng-show="showMsg.warn || showMsg.error || showMsg.success"  ng-class="{move: hideMsg}">
<div class="alert alert-dismissible fade in" ng-click="deleteMsg()" role="alert" ng-class="{'alert-warning': showMsg.warn, 'alert-success': showMsg.success, 'alert-danger': showMsg.error}">
			<button type="button" class="close"  aria-label="Close" ng-click="deleteMsg()"><span aria-hidden="true">&times;</span></button>
			
			{{showMsg.warn}}{{showMsg.error}}{{showMsg.success}}
</div>
</div>
<div class="main-buttons">
	<div class="container">

		<button type="button" class="btn btn-success" ng-click="getNodes()"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> {{reservedDr.length ? 'Add Selected' : 'Get Selected'}}</button>	
				
		<div class="btn-group dropup">
		  <button type="button" class="btn btn-danger" data-toggle="dropdown"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>  Drop Nodes</button>
		  <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		  </button>
		<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">			
			<li><a href="" ng-click="dropSelectedNodes()" tooltip-placement="left" uib-tooltip="This action will drop only checked nodes."><span class="glyphicon glyphicon-check" aria-hidden="true"></span> Drop Selected</a></li> 
			<li><a href="" ng-click="dropNodes()" tooltip-placement="left" uib-tooltip="This action will drop all reserved nodes."><span class="glyphicon glyphicon-tasks" aria-hidden="true"></span> Drop All</a></li> 
		  </ul>
		</div>
		
		<div class="btn-group dropup" ng-show="reservedDr.length">
		  <button type="button" class="btn btn-primary" data-toggle="dropdown">Services</button>
		  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		  </button>
		<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
			<li ng-repeat="service in services" ng-if="service.status==0"><a href="" ng-click="runService(service.name)" tooltip-placement="left" uib-tooltip="This action will stop all services and run '{{service.name}}' on selected nodes."><span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span> {{service.name}}</a></li>        			
		  </ul>
		</div>

		<div class="btn-group dropup" ng-show="userInfo.admin">
		  <button type="button" class="btn btn-warning" data-toggle="dropdown">Admin Tools</button>
		  <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		  </button>
		<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
			<li ng-repeat="otherUser in otherUsers"><a href="" ng-click="adminDropNodes(otherUser)" tooltip-placement="left" uib-tooltip="This action will drop nodes for {{otherUser}}."><span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> {{otherUser}}</a></li>        		
			<li ng-show="!otherUsers.length"><a>No actions</a></li>
			<li ng-show="otherUsers.length" class="divider"></li>
			<li ng-show="otherUsers.length"><a href="" ng-click="kickSelectedNodes()" tooltip-placement="left" uib-tooltip="This action will kick user for selected node and stop all services."><span class="glyphicon glyphicon-share" aria-hidden="true"></span> Kick Selected User Nodes</a></li>        		
		  </ul>
		</div>
	
	</div>
</div>
</div>
</div>



