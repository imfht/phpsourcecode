package com.blackhouse.bluehouse.util;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.util.Map;

import org.apache.http.HttpStatus;
import org.json.JSONObject;

import android.util.Log;

/**
 * 网络访问
 * 
 */
public class NetUtil {

	private static final String TAG = NetUtil.class.getSimpleName();

	public static JSONObject sendPOSTRequest(String path,
			Map<String, String> params, String encoding) throws Exception {
		StringBuilder data = new StringBuilder();
		if (params != null && !params.isEmpty()) {
			for (Map.Entry<String, String> entry : params.entrySet()) {
				data.append(entry.getKey()).append("=");
				data.append(URLEncoder.encode(entry.getValue(), encoding));
				data.append('&');
			}
			data.deleteCharAt(data.length() - 1);
		}
		byte[] entity = data.toString().getBytes();
		HttpURLConnection connection = (HttpURLConnection) new URL(path)
				.openConnection();
		connection.setConnectTimeout(5000);
		connection.setRequestMethod("POST");
		connection.setRequestProperty("Content-Type",
				"application/x-www-form-urlencoded");
		connection.setRequestProperty("Content-Length",
				String.valueOf(entity.length));
		connection.addRequestProperty("WH-Context", "HTTP_USER_AGENT Android");
		connection.setDoOutput(true); // 
		OutputStream outputStream = connection.getOutputStream();
		outputStream.write(entity);
		InputStream is = null;
		try {
			if (connection.getResponseCode() == HttpStatus.SC_OK) {
				is = connection.getInputStream();
				Log.w(TAG, "is content" + inStream2String(is));
				return inStream2JSON(is);
			} else {
				Log.w(TAG, "executePost error :" + connection.getResponseCode()
						+ "; url :" + path);
			}

		} catch (Exception e) {
			Log.w(TAG,
					"executePost: " + path + " convert error :"
							+ e.getMessage());
		} finally {
			is.close();
			outputStream.close();
			connection.disconnect();
		}
		return null;

	}

	public static JSONObject sendGETRequest(String path,
			Map<String, String> params, String encoding) throws Exception {
		StringBuilder url = new StringBuilder(path);
		if (params != null && !params.isEmpty()) {
			for (Map.Entry<String, String> entry : params.entrySet()) {
				url.append(URLEncoder.encode(entry.getValue(), encoding)); // ����
				url.append("&");
			}
			url.deleteCharAt(url.length() - 1);
		}
		Log.d(TAG, "get url:"+ url);
		HttpURLConnection connection = (HttpURLConnection) new URL(
				url.toString()).openConnection();
		connection.setConnectTimeout(5000);
		connection.setRequestMethod("GET");
		connection.addRequestProperty("WH-Context", "HTTP_USER_AGENT Android");
		InputStream is = null;
		try {
			if (connection.getResponseCode() == HttpStatus.SC_OK) {
				is = connection.getInputStream();
				return inStream2JSON(is);
			} else {
				Log.w(TAG, "executeGet error :" + connection.getResponseCode()
						+ "; url :" + path);
			}

		} catch (Exception e) {
			Log.w(TAG,
					"executeGet: " + path + " convert error :" + e.getMessage());
		} finally {
			is.close();
			connection.disconnect();
		}
		return null;

	}
	
	private static String inStream2String(InputStream is){
		ByteArrayOutputStream baos = new ByteArrayOutputStream();
		byte[] buffer = new byte[1024];
		int len = -1;
		try {
			while ((len = is.read(buffer)) != -1) {
				baos.write(buffer, 0, len);
			}
		} catch (IOException e) {
			e.printStackTrace();
		}

		String jsonObject = new String(baos.toByteArray());
		return jsonObject;
		
	}

	private static JSONObject inStream2JSON(InputStream is) throws Exception {

		ByteArrayOutputStream baos = new ByteArrayOutputStream();
		byte[] buffer = new byte[1024];
		int len = -1;
		while ((len = is.read(buffer)) != -1) {
			baos.write(buffer, 0, len);
		}

		String jsonObject = new String(baos.toByteArray());

		return new JSONObject(jsonObject);
	}

}
