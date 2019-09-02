<?php

?>


<h1>Current State</h1>
<hr>
<div class="col-md-6 col-sm-6 col-lg-6">
	<div class="dashboard-card blue">
		<div class="statistic" ng-class="{'show': totalcnt > 0}">
			<div class="big-font counter" value="totalcnt" to="statistic.totalcnt" from="0" duration="counterDuration">{{totalcnt | number:0}}</div>
			<div class="icon"><span class="glyphicon glyphicon-tasks" aria-hidden="true"></span></div>
			<div>Working Nodes</div>
			<div class="bottom"></div>
		</div>
	</div>
</div>

 
<div class="col-md-6 col-sm-6 col-lg-6" >
	<div class="dashboard-card red">
		<div class="statistic" ng-class="{'show': totalcnt > 0}">
			<div class="big-font">
				<span class="counter" value="farmrender" to="statistic.farmrender" from="0" duration="counterDuration">{{farmrender | number:0}}</span> 
				<span class="counter" value="farmusage" to="statistic.farmusage" from="0" duration="counterDuration">({{farmusage | number:0}}%)</span></div>
			<div class="icon"><span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span></div>
			<div>All Nodes Current Load</div>
			<div class="bottom"></div>
		</div>
	</div>
</div>

<div class="col-md-6 col-sm-6 col-lg-6" >
	<div class="dashboard-card green">
		<div class="statistic" ng-class="{'show': totalcnt > 0}">
			<div class="big-font counter" value="youused" to="statistic.youused" from="0" duration="counterDuration">{{youused | number:0}}</div>
			<div class="icon"><span class="glyphicon glyphicon-saved" aria-hidden="true"></span></div>
			<div>My Nodes</div>
			<div class="bottom"></div>
		</div>
	</div>
</div>

<div class="col-md-6 col-sm-6 col-lg-6" >
	<div class="dashboard-card yellow">
		<div class="statistic" ng-class="{'show': totalcnt > 0}">
			<div class="big-font"><span class="counter" value="efficiencyPercent" to="statistic.efficiency.percent" from="0" duration="counterDuration">{{efficiencyPercent | number:0}}%</span> (<span class="counter" value="efficiencyUnused" to="statistic.efficiency.used" from="0" duration="counterDuration">{{efficiencyUsed | number:0}}</span>/<span class="counter" value="efficiencyUnused" to="statistic.efficiency.unused" from="0" duration="counterDuration">{{efficiencyUnused | number:0}}</span>)</div>
			<div class="icon"><span class="glyphicon glyphicon-equalizer" aria-hidden="true"></span></div>
			<div>My Nodes Load</div>
			<div class="bottom"></div>
		</div>
	</div>
</div>

<div class="col-md-6 col-sm-6 col-lg-6" >
	<div class="dashboard-card color2">
		<div class="statistic" ng-class="{'show': totalcnt > 0}">
			<div class="big-font counter" value="usersrend" to="statistic.usersrend" from="0" duration="counterDuration">{{usersrend | number:0}}</div>
			<div class="icon"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></div>
			<div>{{usersrend == 1 ? 'User Is' : 'Users Are'}} Rendering</div>
			<div class="bottom"></div>
		</div>
	</div>
</div>

<div class="col-md-6 col-sm-6 col-lg-6" >
	<div class="dashboard-card color3">
		<div class="statistic" ng-class="{'show': totalcnt > 0}">
			<div class="big-font">{{statistic.topuser}}</div>
			<div class="icon"><span class="glyphicon glyphicon-star" aria-hidden="true" ></span></div>
			<div>Most Active User<br>
			<span ng-show="statistic.topusernodes">{{statistic.topusernodes}} Nodes In Use</span>
			</div>
			<div class="bottom"></div>
		</div>
	</div>
</div>
<br style="clear: both"><br>

<div class="col-md-6 col-sm-6 col-lg-6" >
	<h3 style="height:60px">Nodes Reserved By User</h3>
	<canvas id="bar" class="chart-doughnut" chart-data="dataUserEmpl" chart-labels="labelsUserEmpl" chart-dataset-override="datasetOverrideUserEmpl" chart-options="optionsUserEmpl" chart-colors="labelsColorsUserEmpl"> </canvas>
	<ul class="legend">
		<li ng-repeat="l in labelsUserEmpl"><span class="color" ng-style="{'background-color': labelsColorsUserEmpl[$index]}"></span>{{l}}: <b>{{dataUserEmpl[$index]}}</b></li>
	</ul>
</div>

<div class="col-md-6 col-sm-6 col-lg-6" >
	<h3 style="height:60px">Rendering Nodes By Offices/Groups</h3>
	<canvas id="bar" class="chart-doughnut" chart-data="dataRenderOffice" chart-labels="labelsRenderOffice" chart-dataset-override="datasetOverrideRenderOffice" chart-options="optionsRenderOffice" chart-colors="labelsColorsRenderOffice"> </canvas>
	<ul class="legend">
		<li ng-repeat="l in labelsRenderOffice"><span class="color" ng-style="{'background-color': labelsColorsRenderOffice[$index]}"></span>{{l}}: <b>{{dataRenderOffice[$index]}}</b></li>
	</ul>
</div>

<br style="clear: both"><br><br><br>
<h1>Statistics</h1>
<hr>
<div class="col-md-12 col-sm-12 col-lg-12" >
<div style="min-height: 55px">
<div class="row" autofloat="true"> 
	<div class="btn-group" uib-dropdown>
		<button id="btn-append-to-single-button" type="button" class="btn btn-lg btn-primary" uib-dropdown-toggle>
		{{office_filter}} <span class="caret"></span>
		</button>
		<ul class="dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="btn-append-to-single-button">
		  <li role="menuitem"><a href="" ng-click="setOfficeFilter('All')">All</a></li>
		  <li class="divider"></li>
		  <li role="menuitem" ng-repeat="o in statistic.offices"><a href="" ng-click="setOfficeFilter(o)">{{o}}</a></li>
		  <li class="divider"></li>
		  <li role="menuitem"><a href="" ng-click="setOfficeFilter('Unsorted')">Unsorted</a></li>
		</ul>
	  </div>

	 <div class="btn-group pull-right">
        <label class="btn btn-lg btn-success" ng-click="changePeriod('Week')" ng-model="period" uib-btn-radio="'Week'" uib-uncheckable="uncheckable">Week</label>
        <label class="btn btn-lg btn-success" ng-click="changePeriod('Month')" ng-model="period" uib-btn-radio="'Month'" uib-uncheckable="uncheckable">Month</label>
        <label class="btn btn-lg btn-success" ng-click="changePeriod('Year')" ng-model="period" uib-btn-radio="'Year'" uib-uncheckable="uncheckable">Year</label>
		<label class="btn btn-lg btn-success" ng-click="changePeriod('Custom')" ng-model="period" uib-btn-radio="'Custom'" uib-uncheckable="uncheckable">Custom</label> 
    </div>
	<div ng-show="period=='Custom'" class="row">
<br>
 <div class="col-md-6">	
		From:
        <p class="input-group">
			<span class="input-group-addon" id="basic-addon1"><i class="glyphicon glyphicon-calendar"></i></span>			
			<input type="date" id="party" name="party" class="form-control" ng-model="dt.from" ng-change="changePeriod('Custom')" required>          		  
        </p>
      </div>

      <div class="col-md-6">
	  To:
        <p class="input-group">
			<span class="input-group-addon" id="basic-addon1"><i class="glyphicon glyphicon-calendar"></i></span>
			<input type="date" id="party" name="party" class="form-control" ng-model="dt.to" ng-change="changePeriod('Custom')" required>                    
        </p>
      </div>
</div>		
</div>	
</div>
<br>
<div style="clear: both">
	<h3>Render Time <i>(Hours per {{period}} - {{office_filter}} Nodes)</i></h3>
	<br>
	<canvas id="line" class="chart chart-line" chart-data="dataMonthRenderTime" chart-labels="labelsMonthRenderTime" chart-dataset-override="datasetOverrideRenderTime" chart-options="optionsRenderTime" chart-colors="colorsRenderTime"> </canvas>
</div>	
</div>
<br style="clear: both">

<div class="col-md-6 col-sm-6 col-lg-6" >
	<h3 style="height:60px">Render Farm Usage <i>(Hours per {{period}} - {{office_filter}} Nodes)</i></h3>
	<canvas id="bar" class="chart-doughnut" chart-data="dataFarmEmpl" chart-labels="labelsFarmEmpl" chart-dataset-override="datasetOverrideFarmEmpl" chart-options="optionsFarmEmpl" chart-colors="labelsColorsFarmEmpl"> </canvas>
	<ul class="legend">
		<li ng-repeat="l in labelsFarmEmpl"><span class="color" ng-style="{'background-color': labelsColorsFarmEmpl[$index]}"></span>{{l}}: <b>{{dataFarmEmpl[$index]}}</b> (Hrs)</li>
	</ul>
</div>

<div class="col-md-6 col-sm-6 col-lg-6" >
	<h3 style="height:60px">Power Consumption <i>(kW per {{period}} - {{office_filter}} Nodes)</i></h3>
	<canvas id="bar" class="chart-doughnut" chart-data="dataFarmPowerEmpl" chart-labels="labelsFarmPowerEmpl" chart-dataset-override="datasetOverrideFarmPowerEmpl" chart-options="optionsFarmPowerEmpl" chart-colors="labelsColorsFarmPowerEmpl"> </canvas>
	<ul class="legend">
		<li ng-repeat="l in labelsFarmPowerEmpl"><span class="color" ng-style="{'background-color': labelsColorsFarmPowerEmpl[$index]}"></span>{{l}}: <b>{{dataFarmPowerEmpl[$index]}}</b> <i>(kW)</i></li>
	</ul>
	<br><b>Total:</b> {{dataFarmPowerEmpl[0] + dataFarmPowerEmpl[1] | number: 1}} <i>(kW)</i> 
	
</div>

<br style="clear: both"><br><br>
<h3>Rendering Time By Node <i>(Hours per {{period}})</i></h3>
<div class="table-responsive">
<table class="table table-hover stat-by-node">
	<tr>
		<th width="30px">#</th>
		<th width="20%">Name</th>
		<th>Rendering (Hrs)</th>
		<th>Idle (Hrs)</th>
		<th width="40%"> Rendering / Idle (%)</th>
	</tr>
	<tr ng-repeat="n in dataFarmByNodeEmpl" ng-if="n.name"> 
		<td>{{$index + 1}}</td>
		<td class="text-uppercase">{{n.name}}</td>
		<td>{{n.rend}}</td>
		<td>{{n.idle}}</td>
		<td>			
			<div ng-style="{'width': (n.rend_p + '%')}" class="load-percent green">{{n.rend_p > 6 ? n.rend_p + '%' : '&nbsp'}}</div> 
			<div ng-style="{'width': (n.idle_p + '%')}" class="load-percent red">{{n.rend_p > 92 ? '&nbsp' : n.idle_p + '%'}}</div>
		</td>
	</tr>
</table>