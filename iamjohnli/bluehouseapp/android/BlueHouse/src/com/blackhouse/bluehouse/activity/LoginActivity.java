package com.blackhouse.bluehouse.activity;

import java.util.HashMap;

import org.json.JSONObject;

import com.blackhouse.bluehouse.R;
import com.blackhouse.bluehouse.util.Constants;
import com.blackhouse.bluehouse.util.NetUtil;

import android.app.Activity;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;


/**
 * 登录
 *
 */
public class LoginActivity extends Activity {
	
	
	private static String TAG = LoginActivity.class.getSimpleName();
	private EditText et_name;
	private EditText et_password;
    private Button login;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		setContentView(R.layout.activity_login);
		findView();
		setListener();
		super.onCreate(savedInstanceState);
	}

	
	private void findView(){
		et_name = (EditText) findViewById(R.id.et_name);
		et_password = (EditText) findViewById(R.id.et_password);
	    login = (Button) findViewById(R.id.login);
	} 
	
	private void setListener(){
		login.setOnClickListener(new OnClickListener(){

			@Override
			public void onClick(View v) {

				   if(et_name.getText().toString().trim().length() < 1){
					   Toast.makeText(LoginActivity.this,"请输入用户名",Toast.LENGTH_SHORT).show();
					   return;
				   }else if(et_password.getText().toString().trim().length() <1){
					   Toast.makeText(LoginActivity.this,"请输入密码",Toast.LENGTH_SHORT).show();
					   return;
				   }else{
					   new LoginTask().execute();
				   }
			}});
	}
	
	
	
	private class LoginTask extends AsyncTask<Void,Void,JSONObject>{

		@Override
		protected JSONObject doInBackground(Void... params) {
			JSONObject jsonObject = null;
			HashMap<String,String> data = new HashMap<String,String>();
			data.put("Username", et_name.getText().toString().trim());
			data.put("Password",et_password.getText().toString().trim());
			try{
				jsonObject = NetUtil.sendPOSTRequest(Constants.BASEURL+Constants.LOGIN,data,"UTF-8");
				Log.d(TAG,"jsonObject"+jsonObject);
			}catch(Exception e){
				e.printStackTrace();
			}
			return jsonObject;
		}
		
	}
}
