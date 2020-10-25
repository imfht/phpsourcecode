package com.blackhouse.bluehouse.activity;

import java.util.ArrayList;

import me.maxwin.view.listview.XListView;
import me.maxwin.view.listview.XListView.IXListViewListener;

import org.json.JSONException;
import org.json.JSONObject;

import com.blackhouse.bluehouse.R;
import com.blackhouse.bluehouse.adapter.PostAdapter;
import com.blackhouse.bluehouse.entity.Post;
import com.blackhouse.bluehouse.entity.PostList;
import com.blackhouse.bluehouse.util.Constants;
import com.blackhouse.bluehouse.util.NetUtil;

import android.app.Activity;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.Toast;
import android.widget.AdapterView.OnItemClickListener;

/**
 * 节点下所有帖子列表
 * 
 * @author leo
 * 
 */
public class PostActivity extends Activity {

	private static final String TAG = PostActivity.class.getSimpleName();
	private ArrayList<Post> posts = new ArrayList<Post>();

	private PostAdapter adapter;

	private XListView listView;

	private PostList postList;

	private String url;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		setContentView(R.layout.activity_post);
		super.onCreate(savedInstanceState);
		findViews();
		setListener();
		url = this.getIntent().getStringExtra("url");
		new getAllPostTask(url).execute();
	}

	private void setListener() {
		listView.setPullLoadEnable(false);
		listView.setPullRefreshEnable(true);
		listView.setXListViewListener(new IXListViewListener(){

			@Override
			public void onRefresh() {
				new getAllPostTask(url).execute();
			}

			@Override
			public void onLoadMore() {
				if (!postList.getPage().equals(postList.getPages())
						|| (postList.getLinks().getNext() != null)) {
					new getAllPostTask(postList.getLinks().getNext())
							.execute();
				} else {
					Toast.makeText(getApplicationContext(), "您已经到最后了",
							Toast.LENGTH_SHORT).show();
				}
			}});
		listView.setAdapter(adapter);
		listView.setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> parent, View view,
					int position, long id) {

			}
		});
	}

	private void findViews() {
		listView = (XListView) findViewById(R.id.listview);
		adapter = new PostAdapter(getApplicationContext(), posts);
	}

	private class getAllPostTask extends AsyncTask<Void, Void, JSONObject> {

		private String url;

		public getAllPostTask(String url) {
			this.url = url;
		}

		@Override
		protected JSONObject doInBackground(Void... params) {
			JSONObject jsonObject = null;
			try {
				jsonObject = NetUtil.sendGETRequest(Constants.BASEURL + url,
						null, "UTF-8");
			} catch (Exception e) {
				e.printStackTrace();
			}
			return jsonObject;
		}

		@Override
		protected void onPostExecute(JSONObject result) {
			Log.d(TAG, "result" + result);
			listView.stopLoadMore();
			listView.stopRefresh();
			if (result != null) {
				try {
					postList = new PostList(result);
					if (postList.getPage().equals("1")) {
					
						posts.clear();
					}
					if (Integer.valueOf(postList.getPage()) < Integer
							.valueOf(postList.getPages())) {
						listView.setPullLoadEnable(true);
					}
					posts.addAll(postList.getPosts());
					adapter.notifyDataSetChanged();
				} catch (JSONException e) {
					Toast.makeText(getApplicationContext(), "数据格式错误",
							Toast.LENGTH_SHORT).show();
					e.printStackTrace();
				}
			}
			super.onPostExecute(result);
		}

	}

}
