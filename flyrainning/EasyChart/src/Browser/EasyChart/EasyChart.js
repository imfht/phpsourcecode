"use strict";


class EasyChart{
  constructor(_opt){
    var user_opt={};
    if (typeof(EasyChart_config)=="object") user_opt=EasyChart_config;
    this.opt=Object.assign({
      id:'',
      echarts_style:'macarons',
      loading_text:'loading ...',
      uri:"/api/",
      post:false,
      width:"",
      height:"",
      debug:false,

      use_websocket:false

    },user_opt);


    this.echarts=false;

    this.DOM=false;

    this.init(_opt);
  }
  debug(isdebug){
    this.opt.debug=(isdebug)?true:false;
  }
  setOpt(_opt){
    _opt=_opt || {};
    this.opt=Object.assign(this.opt,_opt);
  }
  on(evt,cb){
    var func=false;
    if (typeof(cb)=="function"){
      func=cb;
    }else if (typeof(cb)=="string"){
      func=new Function("data",cb);
    }

    this.echarts.on(evt,func);
  }
  init(_opt){
    this.setOpt(_opt);
    this.log("init option:",_opt);
    if (this.echarts) return;
    if (this.opt.id){
      this.DOM=jQuery("#"+this.opt.id);
      this.DOM.html("");
      this.size();

      this.echarts=echarts.init(document.getElementById(this.opt.id),this.opt.echarts_style);
    	this.echarts.showLoading({
    		text : this.opt.loading_text,
    	});

      if (typeof(this.opt.onload)=="function") this.opt.onload(this);

      if (this.opt.on){
        for(var key in this.opt.on){
          this.on(key,this.opt.on[key]);
        }
      }
    }else{
      this.log("No id for DOM");
    }

  }
  send(data,callback){

    if (this.opt.use_websocket){

    }else{
      if (typeof(data)=="string") data= jQuery(data).serialize();
      this.ajax(this.opt.api,data,callback);
    }
  }
  ajax(api,PostData,callback){
    var that=this;
    if (typeof(PostData)!="object"){
  		PostData={data:PostData};
  	}
    PostData.EC_api=api;

    that.log("Send Ajax:",PostData);

  	jQuery.ajax({
  		type: "POST",
  		timeout : 600000,
  		url: this.opt.uri,
  		data: PostData,
  		success: callback,
  		error:function(XMLHttpRequest, textStatus, errorThrown){
  			that.msg('与服务器链接失败，请重试 : '+textStatus+'  '+errorThrown,true);
  		}

  	});
  }
  msg(msg){

    if (msg){
      this.echarts.showLoading({text : msg});
    }else{
      this.echarts.hideLoading();
    }
  }
  size(width,height){
    if (width) this.opt.width=width;
    if (height) this.opt.height=height;
    if (this.opt.width) this.DOM.css({width:this.opt.width});
    if (this.opt.height) this.DOM.css({height:this.opt.height});
    this.resize();
  }
  resize(){
    if (this.echarts) this.echarts.resize();
  }
  load(data,_opt){//加载数据

    if (this.echarts){
      if (_opt) this.init(_opt);
    }else{
      this.init(_opt);
    }
  	var that=this;

  	var postdata=data || this.opt.post || "";

    this.send(postdata,function(msg){
      that.log("Recive(raw):",msg);
      if (typeof(msg)=="string") msg=JSON.parse(msg);
      that.log("Recive(obj):",msg);
      if (msg.result){

  			var opt={};
  			if (msg.data){
  				//config=eval("("+msg.data+")");
          opt=(new Function("EasyChart",msg.data))(that);
  			}
        that.log("option by recive",opt);

  			if (that.opt.debug){
          window.EC_Debug_opt=opt;
          window.EC_Debug_chart=that.echarts;
  			}



  			that.echarts.setOption(opt);
        that.echarts.hideLoading();
  		}else{
        var a=msg.msg || msg.data;
        //console.log(msg);
        var msgs=msg.msg||msg.data||msg.message||"";
  			that.msg(msgs);
  		}

    });

  }
  log(name,msg){
    if (this.opt.debug) console.log("EasyChart "+name,msg);
  }

};
