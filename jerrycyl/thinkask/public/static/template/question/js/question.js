layui.use(['jquery','layer','flow','thinkask', 'laytpl', 'form', 'upload', 'util'], function(){

 	 var flow = layui.flow,thinkask = layui.thinkask;;
       flow.load({
        elem: '#question' //流加载容器
        ,scrollElem: '#question' //滚动条所在元素，一般不用填，此处只是演示需要。
        ,isAuto: false
        ,isLazyimg: true
        ,done: function(page, next){ //加载下一页
          var lis = [];
            thinkask.tajax('/question/api/lists?page='+page).done(function(res){
                if(page>1){
                    var html=""
                    //假设你的列表返回在data集合中
                    ////以jQuery的Ajax请求为例，请求下一页数据（注意：page是从2开始返回）
                    layui.each(res.data.data, function(index, v){
                            html += '<li class="fly-list-li">\
                                    <a href="/people/'+v.uid+'.html" class="fly-list-avatar">\
                                      <img src="'+v.avatar_file+'" alt="'+v.question_content+'">\
                                    </a>\
                                    <h2 class="fly-tip">\
                                      <a href="/question/detail/'+v.encry_id+'.html">'+v.question_content+'</a>\
                                    </h2>\
                                    <p>\
                                      <span><a href="/people/'+v.uid+'.html">'+v.user_name+'</a></span>\
                                      <span>time </span>\
                                      <span>{'+v.c_title+'</span>\
                                      <span class="fly-list-hint"> \
                                        <i class="iconfont" title="回答">&#xe60c;</i> '+v.comment_count+'\
                                        <i class="iconfont" title="人气">&#xe60b;</i> '+v.view_count+'\
                                      </span>\
                                    </p>\
                                  </li>';
                      lis.push(html);

                    });  
                }
                
                //执行下一页渲染，第二参数为：满足“加载更多”的条件，即后面仍有分页
                //pages为Ajax返回的总页数，只有当前页小于总页数的情况下，才会继续出现加载更多
                next(lis.join(''), page < res.data.per_page);   
            })
              

          
        }
      });

 

});