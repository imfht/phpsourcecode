$(document).ready(function() {
});

/**
 * Posts
 * @author songhuan <trotri@yeah.net>
 * @version $Id: posts.js 1 2013-10-16 18:38:00Z $
 */
Posts = {
  /**
   * Comments 文档评论
   */
  Comments: {
	/**
	 * 加载评论列表
	 * @param string listId
	 * @param string url
	 * @param integer postId
	 * @param integer paged
	 * @param json langs {response: "回复", prev: "上一页", next: "下一页"}
	 * @return void
	 */
    load: function(listId, url, postId, paged, langs) {
      Trotri.log({"listId": listId, "url": url, "postId": postId, "paged": paged, "langs": langs});
      url += "&" + new Date().getTime();
      $.getJSON(url, {"postid": postId, "paged": paged}, function(ret) {
        var obj = $("#" + listId); obj.empty();
        if (ret.err_no == 0) {
          var data = ret.data;
          var rows = data.rows;
          for (var i in rows) {
  	        obj.append(Posts.Comments.getBlock(rows[i], true, langs.response));
  	        var _obj = $("#comm_view_" + rows[i].comment_id);
  	        if (typeof(rows[i].data) == "object") {
  	          for (var j in rows[i].data) {
  	        	_obj.append(Posts.Comments.getBlock(rows[i].data[j], true, langs.response));
  	            var __obj = $("#comm_view_" + rows[i].data[j].comment_id);
  	            for (var z in rows[i].data[j].data) {
                  __obj.append(Posts.Comments.getBlock(rows[i].data[j].data[z], false, langs.response)); 
  	            }
  	          }
  	        }
          }

          var paginator = Core.getPaginator("loadComments", data.total, data.limit, data.offset, langs.prev, langs.next);
          obj.append(paginator);
        }
      });
    },

    /**
     * 获取单个评论HTML
     * @param json row
     * @param boolean hasResp
     * @param string respStr
     * @return string
     */
    getBlock: function(row, hasResp, respStr) {
      if (typeof(row) != "object") { return ; }

      if (typeof(row.comment_id) == "undefined" || typeof(row.content) == "undefined" || typeof(row.dt_last_modified) == "undefined" || typeof(row.author_name) == "undefined") {
        Trotri.log("Posts.Comments.getBlock args is wrong");
        return ;
      }

      var blockId = "comm_view_" + row.comment_id;
      var replyId = "comm_reply_" + row.comment_id;

      var string = '<blockquote id="' + blockId + '">';
      string += '<p>' + row.content + '</p>';
      string += '<p class="blog-post-meta">' + row.dt_last_modified + ' by ' + row.author_name + '</p>';
      if (hasResp) {
        string += '<p id="' + replyId + '"><a href="javascript: Posts.Comments.move(\'' + row.comment_id + '\');">' + respStr + '</a></p>';	
      }

      string += '</blockquote>';
      return string;
    },

    /**
     * 提交评论表单
     * @param string formId
     * @param boolean isPublish
     * @param json langs {auditing: "审核中...", just_now: "刚刚", response: "回复"}
     * @return void
     */
    save: function(formId, isPublish, langs) {
      Trotri.log({"formId": formId, "isPublish": isPublish, "langs": langs});
      var getObj = function(E, n) {
        return $("#" + formId + " " + E + "[name='" + n + "']");
      };

      var getUrl = function() {
        return $("#" + formId).attr("action"); 
      };

      var hasErr = function(obj) {
        return obj.parent().parent().hasClass("has-error");
      };

      var addErr = function(obj) {
        obj.parent().parent().addClass("has-error");
      };

      var removeErr = function(obj) {
        obj.parent().parent().removeClass("has-error");
      };

      var oAuthorName = getObj("input",    "author_name");
      var oAuthorMail = getObj("input",    "author_mail");
      var oContent    = getObj("textarea", "content");
      var oCommentPid = getObj("input",    "comment_pid");

      var objs = [oAuthorName, oAuthorMail, oContent];
      for (var i in objs) {
        objs[i].val() == '' ? addErr(objs[i]) : removeErr(objs[i]);
      }

      Trotri.isMail(oAuthorMail.val()) ? removeErr(oAuthorMail) : addErr(oAuthorMail);

      var isSubmit = function() {
        for (var i in objs) {
          if (hasErr(objs[i])) { return false; }
        }
        return true;
      };

      if (!isSubmit()) {
        return ;
      }

      var url = getUrl() + "&" + new Date().getTime();
      var params = {
        "author_name": oAuthorName.val(),
        "author_mail": oAuthorMail.val(),
        "content"    : oContent.val(),
        "post_id"    : getObj("input", "post_id").val(),
        "comment_pid": oCommentPid.val(),
      };

      $.getJSON(url, params, function(ret) {
        if (ret.err_no == 0) {
          var row = {
            "author_name": params.author_name,
            "content": isPublish ? oContent.val() : langs.auditing,
            "dt_last_modified": langs.just_now,
            "comment_id": ret.data.id,
          };

          if (params.comment_pid > 0) {
            $("#comm_reply_" + params.comment_pid).after(Posts.Comments.getBlock(row, false, langs.response));
          }
          else {
            $("#post_comments_box").prepend(Posts.Comments.getBlock(row, false, langs.response));
          }

          oAuthorName.val("");
          oAuthorMail.val("");
          oContent.val("");
        }
      });
    },

    /**
     * 移动评论表单
     * @param integer commId
     * @return void
     */
    move: function(commId) {
      var response = $("#comm_response");
      var remove = $(".comm-response-remove-reply");
      var commentPid = $("#comm_response :hidden[name='comment_pid']");

      commentPid.val(commId);
      response.insertAfter("#comm_reply_" + commId);
      remove.show();

      remove.click(function() {
        $(this).hide();
        commentPid.val(0);
        response.insertAfter("#comm_response_reference");
      });
    }
  }
}
