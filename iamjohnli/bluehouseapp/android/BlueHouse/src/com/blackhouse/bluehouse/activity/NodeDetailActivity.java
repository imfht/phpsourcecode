package com.blackhouse.bluehouse.activity;

import org.json.JSONObject;

import com.blackhouse.bluehouse.R;
import com.blackhouse.bluehouse.util.Constants;
import com.blackhouse.bluehouse.util.NetUtil;
import com.handmark.pulltorefresh.library.PullToRefreshBase;
import com.handmark.pulltorefresh.library.PullToRefreshListView;

import android.app.Activity;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.AdapterView.OnItemClickListener;

/**
 * 主题详情
 * @author leo
 *
 */
public class NodeDetailActivity extends Activity {

	
	private static final String TAG = NodeDetailActivity.class.getSimpleName();
	
	private PullToRefreshListView refreshListView;
	private ListView listView;
	
	private String url;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		setContentView(R.layout.activity_topic);
		findViews();
		setListener();
		super.onCreate(savedInstanceState);
		url = this.getIntent().getStringExtra("url");
		new getAllPostTask(url).execute();
	}
	
	
	private void findViews() {
		refreshListView = (PullToRefreshListView) findViewById(R.id.listview);
		//adapter = new NodeAdapter(getApplicationContext(), nodes);
	}
	private void setListener() {
		refreshListView
				.setOnRefreshListener(new PullToRefreshBase.OnRefreshListener2<ListView>() {

					@Override
					public void onPullDownToRefresh(
							PullToRefreshBase<ListView> refreshView) {
						new getAllPostTask(Constants.NODE).execute();
					}

					@Override
					public void onPullUpToRefresh(
							PullToRefreshBase<ListView> refreshView) {
						/*if (!nodeList.getPage().equals(nodeList.getPages())
								&& (nodeList.getLinks().getNext() != null)) {
							new getAllNodeTask(nodeList.getLinks().getNext())
									.execute();
						}*/
					}
				});
		listView = refreshListView.getRefreshableView();
	//	listView.setAdapter(adapter);
		listView.setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> parent, View view,
					int position, long id) {
			}
		});
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
			refreshListView.onRefreshComplete();
			/*if (result != null) {
				try {
					nodeList = new NodeList(result);
					if (nodeList.getPage().equals("1")) {
						nodes.clear();
						nodes.addAll(nodeList.getNodes());
					}
					nodes.addAll(nodeList.getNodes());
					Log.d(TAG, "nodeList.getnext"
							+ (nodeList.getLinks().getNext() == null));
					adapter.notifyDataSetChanged();

				} catch (JSONException e) {
					Toast.makeText(getApplicationContext(), "数据格式错误",
							Toast.LENGTH_SHORT).show();
					e.printStackTrace();
				}
			}*/
			super.onPostExecute(result);
		}

	}
      
}
