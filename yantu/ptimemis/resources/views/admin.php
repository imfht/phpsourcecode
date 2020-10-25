<!DOCTYPE html>
<html lang="en" ng-app="myApp" ng-controller="GlobalController">
<head>
    <title ng-bind="globalData.site_title"></title>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="favicon.png" />

	<link rel="stylesheet" href="./bower_components/lumx/dist/lumx.css">
    <link rel="stylesheet" href="./css/app.css">
</head>
<body style="display:none;">
<div  flex-container="row"  class="bgc-grey-50" style="height:100%;">
    <div id="menu" class="bgc-blue-grey-900 tc-white-1" ng-class="{'less':menuLess}" >
    	<div class="toolbar bgc-blue-grey-800">
    		<img id="logo" src="favicon.png" />
    		<span class="toolbar__label fs-title" ng-if="!menuLess" ng-bind="globalData.site_title"></span>
    	</div>
    	<!--1/2级导航-->
    	<div ng-repeat="menuOne in menuData" class="tc-white-2">
    	    <div class="divider divider--dark"></div>
	    	<span class="fs-subhead display-block tc-white-3 p pl+" ng-if="!menuLess"  ng-bind="menuOne.name"></span>
	    	<span class="fs-subhead display-block tc-white-3 p pl+" ng-if="menuLess">——</span>
	        <ul class="list">
	            <li class="list-row list-row--has-separator" lx-tooltip="{{menuTwo.description}}" tooltip-position="right" ng-class="{active:menuTwo.key == breadcrumbData[1].key}" ng-click="stateGo(menuTwo)" ng-repeat="menuTwo in menuOne.son">
	            	<div class="list-row__primary">
	                    <i class="icon icon--xs icon--white-2 icon--flat mdi mdi-{{menuTwo.ico}}"></i>
	                </div>
	                <div class="list-row__content" ng-if="!menuLess" >
	                    <span ng-bind="menuTwo.name"></span>
	                </div>
	            </li>
	        </ul>
	    </div>
    </div>
    <div id="container">
	    <div class="toolbar bgc-white" style="position:relative;">
		    <div class="toolbar__label">
		    	<button class="btn btn--m btn--black btn--icon" lx-ripple ng-click="menuLess = !menuLess" >
		    		<i class="mdi" ng-class="{'mdi-format-indent-decrease':!menuLess,'mdi-format-indent-increase':menuLess}"></i>
		    	</button>
		    

		  
			    <lx-dropdown  ng-repeat="favorMenuOne in favorMenuData">
				    <button class="btn btn--m btn--black btn--flat" lx-ripple lx-dropdown-toggle ng-bind="favorMenuOne.name"></button>

				    <lx-dropdown-menu>
				        <ul>
				            <li ng-repeat="favorMenuTwo in favorMenuOne.son" >
				            	<a class="dropdown-link" ng-click="go(favorMenuTwo.path)" ng-bind="favorMenuTwo.name"></a>
				            </li>
				        </ul>
				    </lx-dropdown-menu>
				</lx-dropdown>
			</div>

		    <div class="toolbar__right">
				<button class="btn btn--m btn--black btn--flat" lx-ripple ng-bind="name"></button>
		        <button class="btn btn--m btn--black btn--icon" lx-ripple ng-click="logout()" ><i class="mdi mdi-exit-to-app"></i></button>
		    </div>
		    <div id="progress"></div>
		</div>

		<div class="divider divider--dark"></div>
		<div class="p+">
			<!--面包屑-->
			<div class="mb breadcrumbs">
				<span><i class="mdi mdi-home tc-grey-500"></i></span>
				<span class="level" ng-class="{'hover':breadcrumb.state}"
					ng-repeat="breadcrumb in breadcrumbData" 
					ng-bind="breadcrumb.name"
					ng-click="breadcrumb.state && stateGo(breadcrumb)"
				></span>
			</div>
			<div class="data-table-container card">
				<!--描述-->
				<span class="p++ bgc-blue-grey-50 fs-caption display-block" ng-bind="breadcrumbData[2]['description'] || breadcrumbData[1]['description']"></span>
				<!--TABS-->
				<ul class="tabs-new pt+ pb+" ng-if="breadcrumbData[2]">
					<li class="tab mr+" ng-class="{'active':menuThree.key==breadcrumbData[2].key}" lx-ripple ng-click="stateGo(menuThree)" ng-repeat="menuThree in objectMenuData[objectName]" ng-bind="menuThree['name']"></li>
				</ul>
				<div ui-view ></div>
				
			</div>   	
		</div>
    </div>
</div>


<script src="./bower_components/jquery/dist/jquery.min.js"></script>
<script src="./bower_components/velocity/velocity.min.js"></script>
<script src="./bower_components/moment/min/moment-with-locales.min.js"></script>
<script src="./bower_components/angular/angular.min.js"></script>
<script src="./bower_components/lumx/dist/lumx.min.js"></script>

<script src="./bower_components/angular/angular-file-upload.js"></script>
<script src="./bower_components/angular/angular-file-upload-all.js"></script>
<script src="./bower_components/angular/angular-file-upload-shim.js"></script>

<script src="./scripts/ueditor/ueditor.config.js"></script>   
<script src="./scripts/ueditor/ueditor.all.js"></script> 
<script src="./scripts/ueditor/angular-ueditor.js "></script>  


<script src="./bower_components/angular-ui-router/release/angular-ui-router.min.js"></script>
<script src="./bower_components/angular-resource/angular-resource.min.js"></script>

<script src="./function.js"></script>

<script src="./views/object/ObjectController.js"></script>

<script src="./config.js"></script>
<script src="./app.js"></script>

</body>
</html>

