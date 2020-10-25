/*******************************************************************************
 * Copyright 2019 pf-miles
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License.  You may obtain a copy
 * of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the
 * License for the specific language governing permissions and limitations under
 * the License.
 ******************************************************************************/
package com.github.pfmiles.dstier1.impl;

import java.util.Date;

/**
 * A utility helper class to cooperate with google guava cache to implement
 * initiative expire control.
 * 
 * @author pf-miles
 *
 */
public class TimedCacheItem<T> {

	// the cached item
	private T item;
	// time to expire
	private Date expireOn;

	public TimedCacheItem(T item, Date expireOn) {
		this.item = item;
		this.expireOn = expireOn;
	}

	public TimedCacheItem(T item) {
		this.item = item;
		this.expireOn = new Date(Long.MAX_VALUE);// never expire on it's own initiative
	}

	public T getItem() {
		return item;
	}

	public void setItem(T item) {
		this.item = item;
	}

	public Date getExpireOn() {
		return expireOn;
	}

	public void setExpireOn(Date expireOn) {
		this.expireOn = expireOn;
	}

}
