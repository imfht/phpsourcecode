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
public class NodeList extends BaseResult {

	private ArrayList<Node> nodes = new ArrayList<Node>();

	public NodeList(JSONObject jsonObject) throws JSONException {
		super(jsonObject);
		JSONArray array = jsonObject.getJSONObject("_embedded").getJSONArray(
				"items");
		for (int i = 0; i < array.length(); i++) {
			Node node = new Node(array.getJSONObject(i));
			nodes.add(node);
		}
	}

	public ArrayList<Node> getNodes() {
		return nodes;
	}

	public void setNodes(ArrayList<Node> nodes) {
		this.nodes = nodes;
	}
	
	

}
