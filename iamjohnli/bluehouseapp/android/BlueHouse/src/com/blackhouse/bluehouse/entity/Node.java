package com.blackhouse.bluehouse.entity;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * 节点信息
 * 
 * @author leo
 * 
 */
public class Node {

	private String id; // 节点id
	private String name; // 节点名称
	private String description; // 节点描述
	private String href; // 链接

	public Node(JSONObject jsonObject) throws JSONException {
		id = jsonObject.getString("id");
		name = jsonObject.getString("name");
		description = jsonObject.getString("description");

		JSONObject links = jsonObject.getJSONObject("_links");
		href = links.getJSONObject("posts").getString("href");
	}

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getDescription() {
		return description;
	}

	public void setDescription(String description) {
		this.description = description;
	}

	public String getHref() {
		return href;
	}

	public void setHref(String href) {
		this.href = href;
	}

}
