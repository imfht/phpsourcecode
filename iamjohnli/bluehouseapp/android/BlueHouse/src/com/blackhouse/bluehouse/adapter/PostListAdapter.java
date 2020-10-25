package com.blackhouse.bluehouse.adapter;

import java.util.List;



import com.blackhouse.bluehouse.R;
import com.blackhouse.bluehouse.entity.Post;
import com.blackhouse.bluehouse.util.BaseUtil;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

public class PostListAdapter extends BaseAdapter {
	
	private Context context;
	private List<Post> posts;
	private Post post;
	
	public PostListAdapter(Context context, List<Post> posts){
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
		// TODO Auto-generated method stub
		return position;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
        ViewHolder holder ;
        post = posts.get(position);
        if(convertView == null){
        	LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        	convertView = inflater.inflate(R.layout.post_item,null);
        	holder = new ViewHolder();
        	
        	holder.tv_post_title = (TextView) convertView.findViewById(R.id.tv_post_title);
        	holder.tv_post_comment = (TextView) convertView.findViewById(R.id.tv_title_comment);
        	holder.tv_post_time = (TextView) convertView.findViewById(R.id.tv_post_time);
        	convertView.setTag(holder);
        }else{
        	holder = (ViewHolder) convertView.getTag();
        }
        
        holder.tv_post_title.setText(post.getTitle());
        holder.tv_post_comment.setText(post.getContent());
        holder.tv_post_time.setText(BaseUtil.convertTime(post.getCreated(),"yyyy-MM-dd"));
        
		return convertView;
	}
	
	
	static class ViewHolder{
		TextView tv_post_title;
		TextView tv_post_comment;
		TextView tv_post_time;
	}
	

}
