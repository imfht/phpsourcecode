package com.blackhouse.bluehouse.entity;

import java.io.Serializable;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * 帖子
 * 
 * 
 */
public class Post implements Serializable {

	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;

	private String memberName; // 用户名
	private String id; // 用户id
	private String title; // 标题
	private String content; // 内容
	private String created; // 创建日期
	private String modified; // 修改日期
	private String comment_count; // 评论数量
	private String last_comment_time; // 最后评论时间
	private String nodeName; // 节点名称
	private PostLinks postlink;

	public Post(JSONObject jsonObject) throws JSONException {
		memberName = jsonObject.getString("memberName");
		id = jsonObject.getString("id");
		title = jsonObject.getString("title");
		content = jsonObject.getString("content");
		created = jsonObject.getString("created");
		modified = jsonObject.getString("modified");
		comment_count = jsonObject.getString("comment_count");
		last_comment_time = jsonObject.getString("last_comment_time");
		nodeName = jsonObject.getString("nodeName");
		postlink = new PostLinks(jsonObject.getJSONObject("_links"));

	}

	public String getMemberName() {
		return memberName;
	}

	public void setMemberName(String memberName) {
		this.memberName = memberName;
	}

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getTitle() {
		return title;
	}

	public void setTitle(String title) {
		this.title = title;
	}

	public String getContent() {
		return content;
	}

	public void setContent(String content) {
		this.content = content;
	}

	public String getCreated() {
		return created;
	}

	public void setCreated(String created) {
		this.created = created;
	}

	public String getModified() {
		return modified;
	}

	public void setModified(String modified) {
		this.modified = modified;
	}

	public String getComment_count() {
		return comment_count;
	}

	public void setComment_count(String comment_count) {
		this.comment_count = comment_count;
	}

	public String getLast_comment_time() {
		return last_comment_time;
	}

	public void setLast_comment_time(String last_comment_time) {
		this.last_comment_time = last_comment_time;
	}

	public PostLinks getPostlink() {
		return postlink;
	}

	public void setPostlink(PostLinks postlink) {
		this.postlink = postlink;
	}

	public String getNodeName() {
		return nodeName;
	}

	public void setNodeName(String nodeName) {
		this.nodeName = nodeName;
	}

}
