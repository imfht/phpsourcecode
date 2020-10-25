var Carousel=new Class({'Implements':[Control],'initialize':function(el,options){this.setOptions({},options);this.element=$(el);this.current=null;this.__buildComponents();this.going=false;this.__bindEvents();},'seek':function(el){var pages=0;el=$(el);if(el){var found=false;var item=0;for(var i=0;i<this.components.items.childNodes.length;i++){var curr=this.components.items.childNodes[i];if($type(curr)=='element'){if(!this.current){this.current=el;}
if(this.current==el){if(found){pages-=(item-found);}else{pages=0;}}
if(curr==el){found=item;}
pages++;item++;}}
if(found){pages=found;}else{pages=0;}}
this.go(found*-1);},'go':function(pages){if(this.going==false&&pages!=0){this.going=true;var el=$(this.components.items);this.pages=pages;var move=0;if(pages>0){var c=0;var left=0;while(c<pages){var last=el.childNodes[el.childNodes.length-1];el.removeChild(last);if($type(last)=='element'){el.insertBefore(last,el.firstChild);this.current=last;c++;}else{last=null;}}
el.setStyle('left',-1*this.pages*this.itemSize.width+'px');}else{move=this.pages*this.itemSize.width;}
el.store('completed',false);var fx=new Fx.Morph(el,{'duration':160,'transition':Fx.Transitions.Quart.easeOut,'onComplete':function(el){if(!el.retrieve('completed')){el=$(el);if(this.pages<0){var c=0;while(c>this.pages){var first=el.firstChild;el.removeChild(first);if($type(first)=='element'){el.appendChild(first);this.current=first;c--;}else{first=null;}}}
el.store('completed',true);}
el.setStyle('left','0px');this.going=false;}.bind(this,el)});fx.start({'left':move,'opacity':0.8}).chain(function(){this.start({'opacity':1});});}},'forward':function(pages){this.go(pages?pages:1);},'back':function(pages){this.go(-1*(pages?pages:1));},'__buildComponents':function(){this.element.className=this.element.className+' m-carousel';this.components={'items':$(this.element.getElementsByTagName('ul')[0]),'frame':$(this.element)};this.options.items=0;var size=false;for(var i=0;i<this.components.items.childNodes.length;i++){var node=$(this.components.items.childNodes[i]);if($type(node)=='element'){if(size==false){this.itemSize=node.getDimensions();size=true;}
this.options.items++;}}
if(size){this.components.items.setStyle('width',(this.itemSize.width*this.options.items)+'px');}
this.__cursor=0;},'__bindEvents':function(){}});