package com.blackhouse.bluehouse.activity;

import java.util.ArrayList;

import me.maxwin.view.listview.XListView;
import me.maxwin.view.listview.XListView.IXListViewListener;

import org.json.JSONException;
import org.json.JSONObject;

import com.blackhouse.bluehouse.R;
import com.blackhouse.bluehouse.adapter.NodeAdapter;
import com.blackhouse.bluehouse.entity.Node;
import com.blackhouse.bluehouse.entity.NodeList;
import com.blackhouse.bluehouse.util.Constants;
import com.blackhouse.bluehouse.util.NetUtil;
import com.handmark.pulltorefresh.library.PullToRefreshBase;
import com.handmark.pulltorefresh.library.PullToRefreshListView;

import android.app.Activity;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.Toast;
import android.widget.AdapterView.OnItemClickListener;

/**
 * 所有节点
 * 
 * @author leo
 * 
 */
public class NodeActivity extends Activity {

	private static final String TAG = NodeActivity.class.getSimpleName();
	private ArrayList<Node> nodes = new ArrayList<Node>();

	private NodeAdapter adapter;

	private XListView listView;

	private NodeList nodeList;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		setContentView(R.layout.activity_node);
		super.onCreate(savedInstanceState);
		findViews();
		setListener();
		new getAllNodeTask(Constants.NODE).execute();
	}

	private void setListener() {
		listView.setPullLoadEnable(false);
		listView.setPullRefreshEnable(true);
		listView.setXListViewListener(new IXListViewListener(){

			@Override
			public void onRefresh() {
				new getAllNodeTask(Constants.NODE).execute();
			}

			@Override
			public void onLoadMore() {
				if (Integer.valueOf(nodeList.getPage()) < Integer
						.valueOf(nodeList.getPages())) {
					new getAllNodeTask(nodeList.getLinks().getNext())
							.execute();
				}else{
					Toast.makeText(getApplicationContext(),"已经到最后了",Toast.LENGTH_SHORT).show();
				}
				
			}
			
		});
		listView.setAdapter(adapter);
		listView.setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> parent, View view,
					int position, long id) {

				if (nodes.size() > 0) {
					Intent intent = new Intent(getApplicationContext(),
							PostActivity.class);
					intent.putExtra("url", nodes.get(position - 1).getHref());
					startActivity(intent);
				}
			}
		});
	}

	private void findViews() {
		listView = (XListView) findViewById(R.id.listview);
		adapter = new NodeAdapter(getApplicationContext(), nodes);
	}

	private class getAllNodeTask extends AsyncTask<Void, Void, JSONObject> {

		private String url;

		public getAllNodeTask(String url) {
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
			listView.stopRefresh();
			listView.stopRefresh();
			if (result != null) {
				try {
					nodeList = new NodeList(result);
					if (nodeList.getPage().equals("1")) {
						nodes.clear();
					}
					if (Integer.valueOf(nodeList.getPage()) < Integer
							.valueOf(nodeList.getPages())) {
						listView.setPullLoadEnable(true);
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
			}
			super.onPostExecute(result);
		}

	}

}
