package com.blackhouse.bluehouse.entity;

import java.io.Serializable;

/**
 * ÆÀÂÛ
 * 
 * @author leo
 * 
 */
public class Comment implements Serializable {

	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;

	private String id;

	private String content;
	private String created;
	private String modified;

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getContent() {
		return content;
	}

	public void setContent(String content) {
		this.content = content;
	}

	public String getCreated() {
		return created;
	}

	public void setCreated(String created) {
		this.created = created;
	}

	public String getModified() {
		return modified;
	}

	public void setModified(String modified) {
		this.modified = modified;
	}

	public static long getSerialversionuid() {
		return serialVersionUID;
	}

}
