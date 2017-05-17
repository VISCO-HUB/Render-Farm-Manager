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

<div ng-show="showMsg.warn || showMsg.error || showMsg.success" class="alert alert-dismissible fade in" role="alert" ng-class="{'alert-warning': showMsg.warn, 'alert-success': showMsg.success, 'alert-danger': showMsg.error}">
	<button type="button" class="close"  aria-label="Close" ng-click="deleteMsg()"><span aria-hidden="true">&times;</span></button>
	
	{{showMsg.warn}}{{showMsg.error}}{{showMsg.success}}
</div>


<div class="btn-group admin-menu" uib-dropdown>
<button id="btn-append-to-single-button" type="button" class="btn btn-primary admin-menu" uib-dropdown-toggle>
 <span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
</button>
<ul class="dropdown-menu dropdown-menu-right" uib-dropdown-menu role="menu" aria-labelledby="btn-append-to-single-button">
	<li role="menuitem"> <a class="list-group-item" ng-class="{active: adminSection=='global'}" href="#/admin/global">Global</a> </li>
	<li role="menuitem"><a class="list-group-item" ng-class="{active: adminSection=='nodes'}" href="#/admin/nodes">Nodes <span class="badge" style="border-radius: 10px !important"> {{adminDR.length}}</span></a> </li>
	<li role="menuitem"><a  class="list-group-item" ng-class="{active: adminSection=='users'}" href="#/admin/users">Users <span class="badge" style="border-radius: 10px !important">{{adminUsers.length}}</span></a> </li>
	<li role="menuitem"><a class="list-group-item" ng-class="{active: adminSection=='services'}" href="#/admin/services">Services <span class="badge" style="border-radius: 10px !important">{{adminServices.length}}</span></a> 		</li>
	<li role="menuitem"><a class="list-group-item" ng-class="{active: adminSection=='groups'}" href="#/admin/groups">Groups <span class="badge" style="border-radius: 10px !important"> {{adminGroups.length}}</span></a> </li>
	<li role="menuitem"><a class="list-group-item" ng-class="{active: adminSection=='offices'}" href="#/admin/offices">Offices <span class="badge" style="border-radius: 10px !important"> {{adminOffices.length}}</span></a> </li>
</ul>
</div>

<div class="col-sm-12 col-md-3 col-lg-3"><br>
	<div class="list-group" topfix="101"> 
		
	</div>
</div>
<br style="clear: both">
<div class="pin-container"> 
	<!-- GLOBAL -->
	<div ng-show="adminSection=='global'">
		<h1>Global</h1>
		<h2><small>Global status:</small></h2>
		<div class="btn-group" data-toggle="buttons">
			<button type="button" class="btn" ng-class="Global.status == 1 ? 'btn-success' : 'btn-default'" ng-click="adminGlobalChangeParam('status', '1')">&nbsp;ON&nbsp;</button>
			<button type="button" class="btn" ng-class="Global.status == 0 ? 'btn-danger' : 'btn-default'" ng-click="adminGlobalChangeParam('status', '0')">OFF</button>
		</div>
		<hr>
		<h2><small>Offline Message:</small></h2>
		<div class="form-group">
			<input type="text" class="form-control" disabled placeholder="{{Global.message}}">
		</div>
		<button type="submit" class="btn btn-primary" ng-click="adminGlobalChangeMessage()">Change</button>
		<hr>
		<h2><small>Idle Time:</small></h2>
		<div>
			Time in minutes for drop nodes if user not render (default: 120):
			<br><br>
		</div>
		<div class="form-group">
			<input type="text" class="form-control" disabled placeholder="{{Global.idle}}" style="width: 100px;">
		</div>
		<button type="submit" class="btn btn-primary" ng-click="adminGlobalChangeIdle()">Change</button>
		<hr>
		<h2><small>E-Mail Notifications:</small></h2>
		<div class="btn-group" data-toggle="buttons">
			<button type="button" class="btn" ng-class="Global.email == 1 ? 'btn-success' : 'btn-default'" ng-click="adminGlobalChangeParam('email', '1')">&nbsp;ON&nbsp;</button>
			<button type="button" class="btn" ng-class="Global.email == 0 ? 'btn-danger' : 'btn-default'" ng-click="adminGlobalChangeParam('email', '0')">OFF</button>
		</div>
		<h2><small>Who To Notify:</small></h2>
		<div class="btn-group">
			<button type="button" class="btn btn-info">{{Global.notify == 0 ? 'All' : (Global.notify == 2 ? 'Administrators' : 'Users')}}</button>
			<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
			<ul class="dropdown-menu">
				<li><a href="" ng-click="adminGlobalChangeParam('notify', '0')">All</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="" ng-click="adminGlobalChangeParam('notify', '1')">Users Only</a></li>
				<li><a href="" ng-click="adminGlobalChangeParam('notify', '2')">Administrators Only</a></li>
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
					<th ng-click="userSort('user')" class="pointer text-primary">Name
						<span class="sortorder" ng-show="userSortParam === 'user'" ng-class="{reverse: userSortReverce}"></span>
					</th>
					<th ng-click="userSort('ip')" class="pointer text-primary">IP
						<span class="sortorder" ng-show="userSortParam === 'ip'" ng-class="{reverse: userSortReverce}"></span>
					</th>
					<th ng-click="userSort('rights')" class="pointer text-primary">Access
						<span class="sortorder" ng-show="userSortParam === 'rights'" ng-class="{reverse: userSortReverce}"></span>
					</th>
					<th ng-click="userSort('group')" class="pointer text-primary">Group
						<span class="sortorder" ng-show="userSortParam === 'group'" ng-class="{reverse: userSortReverce}"></span>
					</th>
				</tr>
				<tr ng-repeat="user in adminUsers | orderBy:userSortParam:userSortReverce" ng-show="user.user != 'v.lukyanenko'"  ng-model="check1[user.user]" uib-btn-checkbox  ng-disabled="user.user == userInfo.user" ng-class="{disabled: user.user == userInfo.user}"> 
					<td><span class="glyphicon" ng-class="check1[user.user] ? 'glyphicon-check': 'glyphicon-unchecked'" aria-hidden="true"></span></td>
					<td> {{user.user}} </td>
					<td> {{user.ip}} </td>
					<td style="font-size: 17px;"><span ng-show="user.rights==1" class="label label-danger">Administrator</span> <span ng-show="user.rights==0" class="label label-success">User</span></td>
					<td style="font-size: 17px;"> <span class="label" class="label-default" ng-style="{'background-color': stringToColour(user.group)}">{{user.group ? user.group : 'None'}}</span> </td>
				</tr>
			</table>
		</div>
		<div class="pin container">
			<button type="button" class="btn btn-success" ng-click="adminAddUser()">Add</button>
			<button type="button" class="btn btn-danger" ng-click="adminDeleteUsers()">Delete</button>
			<div class="btn-group dropup">
				<button type="button" class="btn btn-warning" data-toggle="dropdown">Access</button>
				<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li><a href="" ng-click="adminChangeAccess('#user')"><span class="glyphicon glyphicon-user text-success" aria-hidden="true"></span> User</a></li>
					<li><a href="" ng-click="adminChangeAccess('#admin')"><span class="glyphicon glyphicon-king text-danger" aria-hidden="true"></span> Administrator</a></li>
					<li class="divider"></li>
					<li><a href="" ng-click="adminChangePassword(1)"><span class="glyphicon glyphicon-pencil text-warning" aria-hidden="true"></span> Change Password</a></li>
					<li><a href="" ng-click="adminChangePassword(-1)"><span class="glyphicon glyphicon-refresh text-warning" aria-hidden="true"></span> Reset Password</a></li>
				</ul>
			</div>
			<div class="btn-group dropup">
				<button type="button" class="btn btn-primary" data-toggle="dropdown">Groups</button>
				<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li ng-repeat="grp in adminGroups" ng-if="grp.name"><a href="" ng-click="adminAssignGroup(grp.name)"><span class="glyphicon glyphicon-user text-success" aria-hidden="true"></span> Assign to "{{grp.name}}"</a></li>
					<li ng-if="!adminGroups.length"><a href="#/admin/groups"><span class="glyphicon glyphicon-plus-sign text-success" aria-hidden="true"></span> Add Groups</a></li>
					<li class="divider"></li>									
					<li><a href="" ng-click="adminAssignGroup(-1)"><span class="glyphicon glyphicon-minus-sign text-danger" aria-hidden="true"></span> Clear User Group</a></li>
				</ul>
			</div>
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
				<tr ng-repeat="service in adminServices"  ng-model="checkItem[service.name]" uib-btn-checkbox>
					<td><span class="glyphicon" ng-class="checkItem[service.name] ? 'glyphicon-check': 'glyphicon-unchecked'" aria-hidden="true"></span></td>
					<td > {{service.name}} </td>
					<td style="font-size: 17px;"><span ng-show="service.status==1" class="label label-danger">Disabled</span> <span ng-show="service.status==0" class="label label-success">Enabled</span></td>
				</tr>
			</table>
		</div>
		<div class="pin container">
			<button type="button" class="btn btn-success" ng-click="adminItemAdd('services')">Add</button>
			<button type="button" class="btn btn-danger" ng-click="adminItemDelete('services')">Delete</button>
			<div class="btn-group dropup">
				<button type="button" class="btn btn-warning" data-toggle="dropdown">Status</button>
				<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li><a href="" ng-click="adminItemDisable(false, 'services')"><span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span> Enable Services</a></li>
					<li class="divider"></li>
					<li><a href="" ng-click="adminItemDisable(true, 'services')"><span class="glyphicon glyphicon-ban-circle text-danger" aria-hidden="true"></span> Disable Services</a></li>
				</ul>
			</div>
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
			<table class="table table-hover" id="repTb">
				<thead>
				<tr>
					<th width="30px">#</th>
					<th ng-click="nodeSort('name')" class="pointer text-primary">Name
						 <span class="sortorder" ng-show="nodeSortParam === 'name'" ng-class="{reverse: nodeSortReverce}"></span>
					</th>
					<th>Installed Services</th>
					<th ng-click="nodeSort('status')" class="pointer text-primary">Status
						<span class="sortorder" ng-show="nodeSortParam === 'status'" ng-class="{reverse: nodeSortReverce}"></span>
					</th>
					<th ng-click="nodeSort('group')" class="pointer text-primary">Visibility
						<span class="sortorder" ng-show="nodeSortParam === 'group'" ng-class="{reverse: nodeSortReverce}"></span>
					</th>
					<th ng-click="nodeSort('office')" class="pointer text-primary">Office
						<span class="sortorder" ng-show="nodeSortParam === 'office'" ng-class="{reverse: nodeSortReverce}"></span>
					</th>
					<th ng-click="nodeSort('desc')" class="pointer text-primary">Description
						<span class="sortorder" ng-show="nodeSortParam === 'desc'" ng-class="{reverse: nodeSortReverce}"></span>
					</th>
					<th ng-click="nodeSort('srvautostart')" class="pointer text-primary">Autostart BBurner
						<span class="sortorder" ng-show="nodeSortParam === 'srvautostart'" ng-class="{reverse: nodeSortReverce}"></span>
					</th>
					<th ng-click="nodeSort('rendkw')" class="pointer text-primary">
						Rendering Power (W)
					</th>
					<th ng-click="nodeSort('idlekw')" class="pointer text-primary">
						Idle Power (W)
					</th>						
				</tr>
				</thead>
				<tbody>
					<tr ng-repeat="node in adminDR | orderBy:nodeSortParam:nodeSortReverce"  ng-model="check3[node.ip]" uib-btn-checkbox>
						<td><span class="glyphicon" ng-class="check3[node.ip] ? 'glyphicon-check': 'glyphicon-unchecked'" aria-hidden="true"></span></td>
						<td><a href="" uib-tooltip="IP: {{node.ip}}">{{node.name}}</a></td>
						<td style="font-size: 10px;" ng-bind-html="installedServices(node.services)"></td>
						<td style="font-size: 17px;"><span ng-show="node.status==1" class="label label-danger">Disabled</span> <span ng-show="node.status==0" class="label label-success">Enabled</span></td>
						<td style="font-size: 17px;"><span class="label" ng-class="{'label-info': node.group, 'label-default': !node.group}">{{node.group ? node.group : 'All'}}</span></td>
						<td style="font-size: 17px;"><span class="label" class="label-default" ng-style="{'background-color': stringToColour(node.office)}">{{node.office ? node.office : 'None'}}</span></td>
						<td>{{node.desc}}</td> 
						<td style="font-size: 17px;"><span ng-show="node.srvautostart==0" class="label label-danger">No</span> <span ng-show="node.srvautostart==1" class="label label-success">Yes</span></td>
						<td>{{node.rendkw}}</td>
						<td>{{node.idlekw}}</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="pin container">
			<div class="btn-group dropup">
				<button type="button" class="btn btn-primary" data-toggle="dropdown">Services</button>
				<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li ng-repeat="service in adminServices | orderBy:'name'" ng-if="service.status==0"><a href="" ng-click="adminRunService(service.name)"><span class="glyphicon glyphicon-play text-success" aria-hidden="true"></span> {{service.name}}</a></li>
					<li class="divider"></li>
					<li><a href="" ng-click="adminStopService()"><span class="glyphicon glyphicon-stop text-danger" aria-hidden="true"></span> Stop All Services</a></li>
					<li class="divider"></li>
					<li><a href="" ng-click="adminNodesAutoStartSrv(true)">Set BBurner Auto Start: <b>Yes</b></a></li>
					<li><a href="" ng-click="adminNodesAutoStartSrv(false)">Set BBurner Auto Start: <b>No</b></a></li>
				</ul>
			</div>
			<div class="btn-group dropup">
				<button type="button" class="btn btn-danger" data-toggle="dropdown">Maintenance</button>
				<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li><a href="" ng-click="adminRebootNodes()"><span class="glyphicon glyphicon-repeat text-danger" aria-hidden="true"></span> Reboot Nodes</a></li>
					<li class="divider"></li>
					<li><a href="" ng-click="adminNodeDelete()"><span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span> Remove Nodes</a></li>
				</ul>
			</div>
			<div class="btn-group dropup">
				<button type="button" class="btn btn-warning" data-toggle="dropdown">Status</button>
				<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li><a href="" ng-click="adminNodesDisable(false)"><span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span> Enable Nodes</a></li>
					<li class="divider"></li>
					<li><a href="" ng-click="adminNodesDisable(true)"><span class="glyphicon glyphicon-ban-circle text-danger" aria-hidden="true"></span> Disable Nodes</a></li>
				</ul>
			</div>
			<div class="btn-group dropup">
				<button type="button" class="btn btn-primary" data-toggle="dropdown">Set Visibility</button>
				<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li ng-repeat="grp in adminGroups" ng-if="grp.name"><a href="" ng-click="adminAssignNodeGroup(grp.name)">Visible to "<strong>{{grp.name}}</strong>"</a></li>
					<li ng-show="!adminGroups.length"><a href="#/admin/groups"><span class="glyphicon glyphicon-plus-sign text-success" aria-hidden="true"></span> Add Groups</a></li>
					<li class="divider"></li>
					<li><a href="" ng-click="adminAssignNodeGroup(-1)"><span class="glyphicon glyphicon-ban-circle text-danger" aria-hidden="true"></span> Visible to All</a></li>
				</ul>
			</div>
			<div class="btn-group dropup">
				<button type="button" class="btn btn-default" data-toggle="dropdown">Link Office</button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li ng-repeat="office in adminOffices" ng-if="office.status == 0"><a href="" ng-click="adminAssignNodeOffice(office.name)">Link to "<strong>{{office.name}}</strong>"</a></li>
					<li class="divider" ng-show="adminOffices.length"></li>
					<li><a href="" ng-click="adminAssignNodeOffice(-1)"><span class="glyphicon glyphicon-minus-sign text-danger" aria-hidden="true"></span> Unlink Office</a></li>
				</ul>
			</div>
			<div class="btn-group dropup">
				<button type="button" class="btn btn-default" data-toggle="dropdown">Description</button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li><a href="" ng-click="adminNodesDescription(true)"><span class="glyphicon glyphicon glyphicon-plus text-success" aria-hidden="true"></span> Add Description</a></li>
					<li class="divider"></li>
					<li><a href="" ng-click="adminNodesDescription(false)"><span class="glyphicon glyphicon-ban-circle text-danger" aria-hidden="true"></span> Clear Description</a></li>
				</ul>
			</div>
			<div class="btn-group dropup">
				<button type="button" class="btn btn-default" data-toggle="dropdown">Set Power</button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li><a href="" ng-click="adminNodesPower('rendkw')">Set Rendering Power (Watt)</a></li>
					<li class="divider"></li>
					<li><a href="" ng-click="adminNodesPower('idlekw')">Set Idle Power (Watt)</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!-- OFFICES -->
	<div ng-show="adminSection=='offices'">
		<h1>Offices</h1>
		<br>
		<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Set office for node to create a filter.</div><br>
		<div class="table-responsive">
			<table class="table table-hover">
				<tr>
					<th width="30px">#</th>
					<th>Office</th>
					<th>Status</th>
				</tr>
				<tr ng-repeat="o in adminOffices"  ng-model="checkItem[o.name]" uib-btn-checkbox>
					<td><span class="glyphicon" ng-class="checkItem[o.name] ? 'glyphicon-check': 'glyphicon-unchecked'" aria-hidden="true"></span></td>
					<td > {{o.name}} </td>
					<td style="font-size: 17px;"><span ng-show="o.status==1" class="label label-danger">Disabled</span> <span ng-show="o.status==0" class="label label-success">Enabled</span></td>
				</tr>
			</table>
		</div>
		<div class="pin container">
			<button type="button" class="btn btn-success" ng-click="adminItemAdd('offices')">Add</button>
			<button type="button" class="btn btn-danger" ng-click="adminItemDelete('offices')">Delete</button>
			<div class="btn-group dropup">
				<button type="button" class="btn btn-warning" data-toggle="dropdown">Status</button>
				<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li><a href="" ng-click="adminItemDisable(false, 'offices')"><span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span> Enable Item</a></li>
					<li class="divider"></li>
					<li><a href="" ng-click="adminItemDisable(true, 'offices')"><span class="glyphicon glyphicon-ban-circle text-danger" aria-hidden="true"></span> Disable Item</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!-- GROUPS -->
	<div ng-show="adminSection=='groups'">
		<h1>Groups</h1>
		<br>
		<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> With groups you can show/hide nodes for specific users.</div><br>
		<div class="table-responsive">
			<table class="table table-hover">
				<tr>
					<th width="30px">#</th>
					<th>Groups</th>
					<!--<th>Status</th>-->
				</tr>
				<tr ng-repeat="o in adminGroups"  ng-model="checkItem[o.name]" uib-btn-checkbox>
					<td><span class="glyphicon" ng-class="checkItem[o.name] ? 'glyphicon-check': 'glyphicon-unchecked'" aria-hidden="true"></span></td>
					<td > {{o.name}} </td>
					<!--<td style="font-size: 17px;"><span ng-show="o.status==1" class="label label-danger">Disabled</span> <span ng-show="o.status==0" class="label label-success">Enabled</span></td>-->
				</tr>
			</table>
		</div>
		<div class="pin container">
			<button type="button" class="btn btn-success" ng-click="adminItemAdd('groups')">Add</button>
			<button type="button" class="btn btn-danger" ng-click="adminItemDelete('groups')">Delete</button>
			<!--<div class="btn-group dropup">
				<button type="button" class="btn btn-warning" data-toggle="dropdown">Status</button>
				<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span> <span class="sr-only">Toggle Dropdown</span> </button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li><a href="" ng-click="adminItemDisable(false, 'groups')"><span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span> Enable Item</a></li>
					<li class="divider"></li>
					<li><a href="" ng-click="adminItemDisable(true, 'groups')"><span class="glyphicon glyphicon-ban-circle text-danger" aria-hidden="true"></span> Disable Item</a></li>
				</ul>
			</div>-->
		</div>
	</div>
	
</div>

