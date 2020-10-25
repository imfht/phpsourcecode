package com.blackhouse.bluehouse.adapter;

import com.blackhouse.bluehouse.R;
import com.blackhouse.bluehouse.util.BaseUtil;

import android.content.Context;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

/**
 * 无评论
 * 
 */
public class NoReplyAdapter extends BaseAdapter {

	private Context context;

	public NoReplyAdapter(Context context) {
		this.context = context;
	}

	@Override
	public int getCount() {
		return 1;
	}

	@Override
	public Object getItem(int position) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub
		return 0;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		TextView textView = new TextView(context);
		int padding = BaseUtil.dp(context, 10);
		textView.setPadding(padding, padding, padding, padding);
		textView.setGravity(Gravity.CENTER);
		textView.setText(R.string.no_reply);
		return textView;
	}

}
