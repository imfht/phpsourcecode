package com.blackhouse.bluehouse.entity;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * 返回的标准结果
 * 
 * @author leo
 * 
 */
public class BaseResult {
	private String page; // 当前页
	private String limit; // 每页大小
	private String pages; // 总页数
	private String total; // 总条目数
	private Links links; // 分页信息

	public BaseResult(JSONObject jsonObject) throws JSONException {
		page = jsonObject.getString("page");
		limit = jsonObject.getString("limit");
		pages = jsonObject.getString("pages");
		total = jsonObject.getString("total");
		links = new Links(jsonObject.getJSONObject("_links"));
	}

	public String getPage() {
		return page;
	}

	public void setPage(String page) {
		this.page = page;
	}

	public String getLimit() {
		return limit;
	}

	public void setLimit(String limit) {
		this.limit = limit;
	}

	public String getPages() {
		return pages;
	}

	public void setPages(String pages) {
		this.pages = pages;
	}

	public String getTotal() {
		return total;
	}

	public void setTotal(String total) {
		this.total = total;
	}

	public Links getLinks() {
		return links;
	}

	public void setLinks(Links links) {
		this.links = links;
	}

}
