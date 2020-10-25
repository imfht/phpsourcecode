(function(a){
    a.fn.touchwipe=function(c){
        var b={
            clearTouchWipe:false,
            drag:false,
            min_move_x:20,
            min_move_y:20,
            wipeLeft:function(){/*向左滑动*/},
            wipeRight:function(){/*向右滑动*/},
            wipeUp:function(){/*向上滑动*/},
            wipeDown:function(){/*向下滑动*/},
            wipe:function(){/*点击*/},
            wipehold:function(){/*触摸保持*/},
            wipeDrag:function(x,y){/*拖动*/},
            preventDefaultEvents:true
        };
        if(c){a.extend(b,c)};
        this.each(function(){
            var h,g,j=false,i=false,e;
            var supportTouch = "ontouchstart" in document.documentElement;
            var moveEvent = supportTouch ? "touchmove" : "mousemove",
            startEvent = supportTouch ? "touchstart" : "mousedown",
            endEvent = supportTouch ? "touchend" : "mouseup"
               
               
            /* 移除 touchmove 监听 */
            function m(){
                this.removeEventListener(moveEvent,d);
                h=null;
                j=false;
                clearTimeout(e)
            };
               
            /* 事件处理方法 */
            function d(q){
                if(b.preventDefaultEvents){
                    q.preventDefault()
                };
                if(j){
                    var n = supportTouch ? q.touches[0].pageX : q.pageX;
                    var r = supportTouch ? q.touches[0].pageY : q.pageY;
                    var p = h-n;
                    var o = g-r;
                    if(b.drag){
                        h = n;
                        g = r;
                        clearTimeout(e);
                        b.wipeDrag(p,o);
                    }
                    else{
                        if(Math.abs(p)>=b.min_move_x){
                            m();
                            if(p>0){b.wipeLeft()}
                            else{b.wipeRight()}
                        }
                        else{
                            if(Math.abs(o)>=b.min_move_y){
                                m();
                                if(o>0){b.wipeUp()}
                                else{b.wipeDown()}
                            }
                        }
                    }
                }
            };
               
            /*wipe 处理方法*/
            function k(){clearTimeout(e);if(!i&&j){b.wipe()};i=false;j=false;};
            /*wipehold 处理方法*/
            function l(){i=true;b.wipehold()};
               
            function f(n){
                //if(n.touches.length==1){
                    h = supportTouch ? n.touches[0].pageX : n.pageX;
                    g = supportTouch ? n.touches[0].pageY : n.pageY;
                    j=true;
                    this.addEventListener(moveEvent,d,false);
                    e=setTimeout(l,750)
                //}
            };
               
            //if("ontouchstart"in document.documentElement){
                if(b.clearTouchWipe){
                    this.removeEventListener(startEvent,f,false);
                    this.removeEventListener(endEvent,k,false);
                }else{
                    this.addEventListener(startEvent,f,false);
                    this.addEventListener(endEvent,k,false)
                }
            //}
        });
        return this
    };
})(jQuery);