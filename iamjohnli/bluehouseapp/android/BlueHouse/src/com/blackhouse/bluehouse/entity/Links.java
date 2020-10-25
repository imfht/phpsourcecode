package com.blackhouse.bluehouse.entity;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * 
 * 分页信息
 */
public class Links {
	private String self; // 当前页链接
	private String first; // 第一页
	private String last; // 最后一页
	private String next; // 下一页

	public Links(JSONObject jsonObject) throws JSONException {
		self = jsonObject.getJSONObject("self").getString("href");
		first = jsonObject.getJSONObject("first").getString("href");
		last = jsonObject.getJSONObject("last").getString("href");
		if(jsonObject.has("next")){
			next = jsonObject.getJSONObject("next").getString("href");
		}
	}

	public String getSelf() {
		return self;
	}

	public void setSelf(String self) {
		this.self = self;
	}

	public String getFirst() {
		return first;
	}

	public void setFirst(String first) {
		this.first = first;
	}

	public String getLast() {
		return last;
	}

	public void setLast(String last) {
		this.last = last;
	}

	public String getNext() {
		return next;
	}

	public void setNext(String next) {
		this.next = next;
	}

}
