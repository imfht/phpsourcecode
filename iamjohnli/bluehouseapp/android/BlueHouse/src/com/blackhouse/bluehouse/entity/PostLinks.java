package com.blackhouse.bluehouse.entity;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * 帖子链接
 * 
 * @author leo
 * 
 */
public class PostLinks {
	private String node; // 节点链接
	private String detail; // 详情链接
	private String member; // 发帖人详情
	private String postComments; // 评论列表

	public PostLinks(JSONObject jsonObject) throws JSONException {
		node = jsonObject.getString("node");
		detail = jsonObject.getString("detail");
		member = jsonObject.getString("member");
		postComments = jsonObject.getString("postComments");
	}

	public String getNode() {
		return node;
	}

	public void setNode(String node) {
		this.node = node;
	}

	public String getDetail() {
		return detail;
	}

	public void setDetail(String detail) {
		this.detail = detail;
	}

	public String getMember() {
		return member;
	}

	public void setMember(String member) {
		this.member = member;
	}

	public String getPostComments() {
		return postComments;
	}

	public void setPostComments(String postComments) {
		this.postComments = postComments;
	}

}
