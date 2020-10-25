(function(){
var all=$.event.props,
len=all.length,
res=[];
while(len--){
var el=all[len];
if(el!='layerX'&&el!='layerY')res.push(el);}
$.event.props=res;}());
tiggerScrollEvent=function(objectId,loadingId,errorId,retryBtnId,nomoreId,newOpts,pauseId){
var opts={
'contentPage':'pageListTest',
'contentData':{'pcount':10},
'scrollTarget':$(window),
'heightOffset':10,
'childSum':$(objectId).children().size(),
'waiting':0,
'maxWaitTime':1000,
'isRunning':false,
'totalPage':-1,
'beforeLoad':function(){
if(this.isRunning){
this.waiting++;
return false;}
this.isRunning=true;
this.contentData.childSize=this.childSum;
if(!$(loadingId).is(":visible"))
$(loadingId).show();
return true;},
'afterLoad':function(elementsLoaded,isFisLoad){
$(loadingId).hide();
$(elementsLoaded).fadeInWithDelay();
this.isRunning=false;
if($(elementsLoaded).length<this.contentData.pcount||this.totalPage==this.childSum){
$(loadingId).remove();
$(objectId).stopScrollPagination();
if(isFisLoad==undefined){
$(nomoreId).fadeIn();
$(nomoreId).delay(1100).fadeOut();}
return true;}
return false;},
'errorHandler':function(status){
$(loadingId).hide();
this.isRunning=false;
$(errorId).fadeIn();
$(objectId).stopScrollPagination();},
'funforarest':function(){
$(pauseId).fadeIn();
$(objectId).stopScrollPagination();
return true;}};
if(newOpts.contentData!=null){
opts.contentData=$.extend(opts.contentData,newOpts.contentData);
newOpts.contentData=undefined;}
opts=$.extend(opts,newOpts);
$(objectId).scrollPagination(opts);
LoadRetry=function(event){
$(objectId).restartScrollPagination();
$(errorId).hide();};
ContinueLoad=function(event){
$(objectId).restartScrollPagination();
$(pauseId).hide();};
$(retryBtnId).bind("click",LoadRetry);
$(pauseId).bind("click",ContinueLoad);
$.fn.fadeInWithDelay=function(){
var delay=0;
return this.each(function(){
$(this).delay(delay).animate({opacity:1},200);
delay+=100;});};}
