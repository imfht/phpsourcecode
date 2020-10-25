(function($){
var glo_obj,glo_opts;
$.fn.scrollPagination=function(options){
var opts=$.extend($.fn.scrollPagination.defaults,options);
var target=opts.scrollTarget;
if(target==null){
target=obj;}
opts.scrollTarget=target;
return this.each(function(){
$.fn.scrollPagination.init($(this),opts);});};
$.fn.stopScrollPagination=function(){
return this.each(function(){
$(this).attr('scrollPagination','disabled');});};
$.fn.restartScrollPagination=function(){
$.fn.scrollPagination.loadContent(glo_obj,glo_opts);
this.each(function(){
$(this).attr('scrollPagination','enabled');});};
$.fn.enableScrollPagination=function(){
this.each(function(){
$(this).attr('scrollPagination','enabled');});};
$.fn.scrollPagination.loadContent=function(obj,opts,isFisLoad){
var target=opts.scrollTarget;
var a=$(target).scrollTop();
var b=$(document).height();
var c=$(target).height();
var mayLoadContent=$(target).scrollTop()+opts.heightOffset>=$(document).height()-$(target).height();
if(mayLoadContent){
if(opts.beforeLoad!=null){
if(!opts.beforeLoad())
return;}
$(obj).children().attr('rel','loaded');
$.ajax({
type:'GET',
url:opts.contentPage,
data:opts.contentData,
success:function(data){
$(obj).append(data);
opts.childSum=$(obj).children().size()
var objectsRendered=$(obj).children('[rel!=loaded]');
if(opts.afterLoad!=null){
if(opts.afterLoad(objectsRendered,isFisLoad))
return;
if((opts.childSum/opts.contentData.pcount)%3==0&&opts.funforarest()){
return;}
if(0<opts.waiting||$(target).scrollTop()<=0){
opts.waiting-=(opts.waiting>0?1:0);
$.fn.scrollPagination.loadContent(obj,opts);}}},
error:function(httpRequest,textStatus,errorThrown){
if(opts.errorHandler!=null)
opts.errorHandler(textStatus);},
dataType:'html',
timeout:opts.maxWaitTime});}};
$.fn.scrollPagination.init=function(obj,opts){
glo_obj=obj;
glo_opts=opts;
var target=opts.scrollTarget;
$(obj).attr('scrollPagination','enabled');
$(target).unbind('scroll');
$(target).bind('scroll',(function(event){
if($(obj).attr('scrollPagination')=='enabled'){
$.fn.scrollPagination.loadContent(glo_obj,glo_opts);}
else{
event.stopPropagation();}}));
$.fn.scrollPagination.loadContent(glo_obj,glo_opts,true);};
$.fn.scrollPagination.defaults={
'contentPage':null,
'contentData':{},
'beforeLoad':null,
'afterLoad':null,
'errorHandler':null,
'scrollTarget':null,
'heightOffset':0,
'childSum':0,
'waiting':0,
'maxWaitTime':15000,
'isRunning':false};})(jQuery);
