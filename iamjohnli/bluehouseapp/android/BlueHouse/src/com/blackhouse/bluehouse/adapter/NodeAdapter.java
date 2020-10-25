package com.blackhouse.bluehouse.adapter;

import java.util.ArrayList;

import com.blackhouse.bluehouse.R;
import com.blackhouse.bluehouse.entity.Node;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

/**
 * 节点列表
 * 
 * @author leo
 * 
 */
public class NodeAdapter extends BaseAdapter {

	private ArrayList<Node> nodes;
	private Context context;
	private Node node;

	public NodeAdapter(Context context, ArrayList<Node> nodes) {
		this.context = context;
		this.nodes = nodes;
	}

	@Override
	public int getCount() {
		return nodes.size();
	}

	@Override
	public Node getItem(int position) {
		return nodes.get(position);
	}

	@Override
	public long getItemId(int position) {
		return 0;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		Holder holder;
		node = nodes.get(position);
		if (convertView == null) {
			LayoutInflater inflater = (LayoutInflater) context
					.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
			convertView = inflater.inflate(R.layout.node_item, null);
			holder = new Holder();
			holder.tv_title = (TextView) convertView
					.findViewById(R.id.tv_title);
			holder.tv_descripton = (TextView) convertView
					.findViewById(R.id.tv_description);
			holder.tv_topics = (TextView) convertView
					.findViewById(R.id.tv_topics);
			convertView.setTag(holder);
		} else {
			holder = (Holder) convertView.getTag();
		}

		holder.tv_title.setText(node.getName());
		holder.tv_descripton.setText(node.getDescription());
		return convertView;
	}

	class Holder {
		private TextView tv_title; // 节点标题
		private TextView tv_descripton; // 节点描述
		private TextView tv_topics; // 节点标题数 咱不显示
	}

}
