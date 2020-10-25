package com.blackhouse.bluehouse.adapter;

import java.util.ArrayList;

import com.blackhouse.bluehouse.R;
import com.blackhouse.bluehouse.entity.Post;
import com.blackhouse.bluehouse.util.BaseUtil;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

/**
 * 帖子列表
 * 
 * @author leo
 * 
 */
public class PostAdapter extends BaseAdapter {

	private ArrayList<Post> posts;
	private Context context;
	private Post post;

	public PostAdapter(Context context, ArrayList<Post> posts) {
		this.context = context;
		this.posts = posts;
	}

	@Override
	public int getCount() {
		return posts.size();
	}

	@Override
	public Post getItem(int position) {
		return posts.get(position);
	}

	@Override
	public long getItemId(int position) {
		return 0;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		Holder holder;
		post = posts.get(position);
		if (convertView == null) {
			LayoutInflater inflater = (LayoutInflater) context
					.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
			convertView = inflater.inflate(R.layout.view_topic_detail, null);
			holder = new Holder();
			holder.view_topic_title = (TextView) convertView
					.findViewById(R.id.view_topic_title);
			holder.view_topic_content = (TextView) convertView
					.findViewById(R.id.view_topic_content);
			holder.img_view_topic_head = (ImageView) convertView
					.findViewById(R.id.img_view_topic_head);
			holder.view_topic_name = (TextView) convertView
					.findViewById(R.id.view_topic_name);
			holder.view_topic_time = (TextView) convertView
					.findViewById(R.id.view_topic_time);
			holder.view_topic_replies = (TextView) convertView
					.findViewById(R.id.view_topic_replies);
			holder.view_topic_node = (TextView) convertView
					.findViewById(R.id.view_topic_node);
			convertView.setTag(holder);
		} else {
			holder = (Holder) convertView.getTag();
		}
		holder.view_topic_title.setText(post.getTitle());
		holder.view_topic_content.setText(post.getContent());
		holder.view_topic_name.setText(post.getMemberName());
		holder.view_topic_time.setText(BaseUtil.convertTime(
				post.getLast_comment_time(), "MM月dd日"));
		holder.view_topic_replies.setText(post.getComment_count() + "个回复");
		holder.view_topic_node.setText(post.getNodeName());
		return convertView;
	}

	class Holder {
		private TextView view_topic_title; // 标题
		private TextView view_topic_content; // 内容
		private ImageView img_view_topic_head; // 头像
		private TextView view_topic_name; // 昵称
		private TextView view_topic_time; // 日期
		private TextView view_topic_replies; // 评论数
		private TextView view_topic_node; // 节点名称

	}

}
