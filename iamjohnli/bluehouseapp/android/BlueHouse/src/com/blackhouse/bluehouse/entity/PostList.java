package com.blackhouse.bluehouse.entity;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

/**
 * 节点列表
 * 
 * @author leo
 * 
 */
public class PostList extends BaseResult {

	private ArrayList<Post> posts = new ArrayList<Post>();

	public PostList(JSONObject jsonObject) throws JSONException {
		super(jsonObject);
		JSONArray array = jsonObject.getJSONObject("_embedded").getJSONArray(
				"items");
		for (int i = 0; i < array.length(); i++) {
			Post post = new Post(array.getJSONObject(i));
			posts.add(post);
		}
	}

	public ArrayList<Post> getPosts() {
		return posts;
	}

	public void setPosts(ArrayList<Post> posts) {
		this.posts = posts;
	}

}
