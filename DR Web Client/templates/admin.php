<?php
	
	INCLUDE_ONCE '../vault/config.php';
	INCLUDE_ONCE '../vault/functions.php';
	
	SESSION_START();
		
	IF(!ISSET($_SESSION["user"]) ||
	isUserAllow($_SESSION["user"]) != 1 ||
	$_SESSION['logged'] == false)
	{
		DIE('<div class="alert alert-warning" role="alert">You have no rights for enter in this section!</div>');		
	}
?>

<div class="col-sm-3 col-md-3 col-lg-3"> <br>
	<div class="list-group"> <a href="" class="list-group-item" ng-class="{active: adminSection=='global'}" ng-click="adminSection='global'">Global</a> <a href="" class="list-group-item" ng-class="{active: adminSection=='users'}" ng-click="adminSection='users'">Users <span class="badge" style="border-radius: 10px !important">{{adminUsers.length}}</span></a> <a href="" class="list-group-item" ng-class="{active: adminSection=='services'}" ng-click="adminSection='services'">Services <span class="badge" style="border-radius: 10px !important">{{adminServices.length}}</span></a> <a href="" class="list-group-item" ng-class="{active: adminSection=='nodes'}" ng-click="adminSection='nodes'">Nodes <span class="badge" style="border-radius: 10px !important"> {{adminDR.length}}</span></a> </div>
</div>
<div class="col-sm-9 col-md-9 col-lg-9"> 
	<!-- GLOBAL -->
	<div ng-show="adminSection=='global'">
		<h1>Global</h1>
		<h2><small>Global status:</small></h2>
		<div class="btn-group" data-toggle="buttons">
			<button type="button" class="btn btn-success" ng-click="">&nbsp;ON&nbsp;</button>
			<button type="button" class="btn btn-default" ng-click="">OFF</button>
		</div>
		<hr>
		<h2><small>Offline Message:</small></h2>
		<div class="form-group">
			<input type="text" class="form-control" disabled placeholder="Render Farm Manager under maintenance!">
		</div>
		<button type="submit" class="btn btn-primary">Change</button>
		<hr>
		<h2><small>E-Mail Notifications:</small></h2>
		<div class="btn-group" data-toggle="buttons">
			<button type="button" class="btn btn-success" ng-click="">&nbsp;ON&nbsp;</button>
			<button type="button" class="btn btn-default" ng-click="">OFF</button>
		</div>
		<h2><small>Who To Notify:</small></h2>
		<div class="btn-group">
			<button type="button" class="btn btn-info">All</button>
			<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
			<ul class="dropdown-menu">
				<li><a href="">All</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="">Users Only</a></li>
				<li><a href="">Administrators Only</a></li>
			</ul>
		</div>
	</div>
	<!-- USERS -->
	<div ng-show="adminSection=='users'">
		<h1>Users</h1>
		<br>
		<div class="table-responsive">
			<table class="table table-hover">
				<tr>
					<th width="30px">#</th>
					<th>Name</th>
					<th>IP</th>
					<th>Access</th>
				</tr>
				<tr ng-repeat="user in adminUsers"  ng-model="checkAdminUsers[user.user]" uib-btn-checkbox>
					<td><span class="glyphicon" ng-class="checkAdminUsers[user.user] ? 'glyphicon-check': 'glyphicon-unchecked'" aria-hidden="true"></span></td>
					<td> {{user.user}} </td>
					<td> {{user.ip}} </td>
					<td style="font-size: 17px;"><span ng-show="user.rights==1" class="label label-danger">Administrator</span> <span ng-show="user.rights==0" class="label label-success">User</span></td>
				</tr>
			</table>
		</div>
		<button type="button" class="btn btn-success" ng-click="adminAddUser()">Add</button>
		<button type="button" class="btn btn-danger" ng-click="adminDeleteUser()">Delete</button>
		<div class="btn-group dropup">
			<button type="button" class="btn btn-warning" data-toggle="dropdown">Access</button>
			<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
				<li><a href=""><span class="glyphicon glyphicon-user text-success" aria-hidden="true"></span> User</a></li>
				<li><a href=""><span class="glyphicon glyphicon-king text-danger" aria-hidden="true"></span> Administrator</a></li>
				<li class="divider"></li>
				<li><a href=""><span class="glyphicon glyphicon-pencil text-warning" aria-hidden="true"></span> Change Password</a></li>
			</ul>
		</div>
	</div>
	<!-- SERVICES -->
	<div ng-show="adminSection=='services'">
		<h1>Services</h1>
		<br>
		<div class="table-responsive">
			<table class="table table-hover">
				<tr>
					<th width="30px">#</th>
					<th>Service</th>
					<th>Status</th>
				</tr>
				<tr ng-repeat="service in adminServices"  ng-model="checkAdminServices[service.name]" uib-btn-checkbox>
					<td><span class="glyphicon" ng-class="checkAdminServices[service.name] ? 'glyphicon-check': 'glyphicon-unchecked'" aria-hidden="true"></span></td>
					<td > {{service.name}} </td>
					<td style="font-size: 17px;"><span ng-show="service.status==1" class="label label-danger">Disabled</span> <span ng-show="service.status==0" class="label label-success">Enabled</span></td>
				</tr>
			</table>
		</div>
		<button type="button" class="btn btn-success" ng-click="adminAddService()">Add</button>
		<button type="button" class="btn btn-danger" ng-click="adminAddService()">Delete</button>
		<div class="btn-group dropup">
			<button type="button" class="btn btn-warning" data-toggle="dropdown">Status</button>
			<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
				<li><a href=""><span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span> Enable Services</a></li>
				<li class="divider"></li>
				<li><a href=""><span class="glyphicon glyphicon-ban-circle text-danger" aria-hidden="true"></span> Disable Services</a></li>
			</ul>
		</div>
	</div>
	<!-- NODES -->
	<div ng-show="adminSection=='nodes'">
		<h1>Nodes</h1>
		<br>
		<button type="button" class="btn btn-primary" ng-click="adminCheckAllNodes()">Check All</button>
		<button type="button" class="btn btn-primary" ng-click="adminUncheckAllNodes()">Uncheck All</button>
		<br>
		<br>
		<div class="table-responsive">
			<table class="table table-hover">
				<tr>
					<th width="30px">#</th>
					<th>Name</span></th>
					<th>Installed Services</th>
					<th>Status</th>
				</tr>
				<tr ng-repeat="node in adminDR"  ng-model="checkAdminDR[node.ip]" uib-btn-checkbox>
					<td><span class="glyphicon" ng-class="checkAdminDR[node.ip] ? 'glyphicon-check': 'glyphicon-unchecked'" aria-hidden="true"></span></td>
					<td><a href="" uib-tooltip="IP: {{node.ip}}">{{node.name}}</a></td>
					<td> {{installedServices(node.services)}} </td>
					<td style="font-size: 17px;"><span ng-show="node.status==1" class="label label-danger">Disabled</span> <span ng-show="node.status==0" class="label label-success">Enabled</span></td>
				</tr>
			</table>
		</div>
		<div class="btn-group dropup">
			<button type="button" class="btn btn-primary" data-toggle="dropdown">Services</button>
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
				<li ng-repeat="service in adminServices"><a href="" ng-click="adminRunService(service.name)"><span class="glyphicon glyphicon-play text-success" aria-hidden="true"></span> {{service.name}}</a></li>
				<li class="divider"></li>
				<li><a href=""><span class="glyphicon glyphicon-stop text-danger" aria-hidden="true"></span> Stop All Services</a></li>
			</ul>
		</div>
		<div class="btn-group dropup">
			<button type="button" class="btn btn-danger" data-toggle="dropdown">Maintenance</button>
			<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
				<li><a href=""><span class="glyphicon glyphicon-repeat text-danger" aria-hidden="true"></span> Reboot Nodes</a></li>
				<li class="divider"></li>
				<li><a href=""><span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span> Remove Nodes</a></li>
			</ul>
		</div>
		<div class="btn-group dropup">
			<button type="button" class="btn btn-warning" data-toggle="dropdown">Status</button>
			<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
				<li><a href=""><span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span> Enable Nodes</a></li>
				<li class="divider"></li>
				<li><a href=""><span class="glyphicon glyphicon-ban-circle text-danger" aria-hidden="true"></span> Disable Nodes</a></li>
			</ul>
		</div>
	</div>
</div>
