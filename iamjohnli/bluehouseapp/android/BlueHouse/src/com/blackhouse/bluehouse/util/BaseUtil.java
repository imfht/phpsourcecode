package com.blackhouse.bluehouse.util;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;

import android.content.Context;
import android.content.res.Resources;
import android.util.TypedValue;

public class BaseUtil {

	public static String convertTime(String time, String format) {
		String newTime = time.replace("T", " ").replace("+0800", "");
		SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
		try {
			Date date = sdf.parse(newTime);
			SimpleDateFormat dateFormat = new SimpleDateFormat(format);// "yyyyÄêMMÔÂdd HH:mm"
			return dateFormat.format(date);
		} catch (ParseException e) {
			e.printStackTrace();
			return null;
		}
	}

	public static int dp(Context context, float dp) {
		Resources resources = context.getResources();
		int px = (int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP,
				dp, resources.getDisplayMetrics());
		return px;
	}

	public static int sp(Context context, float sp) {
		Resources resources = context.getResources();
		int px = (int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_SP,
				sp, resources.getDisplayMetrics());
		return px;
	}
}
