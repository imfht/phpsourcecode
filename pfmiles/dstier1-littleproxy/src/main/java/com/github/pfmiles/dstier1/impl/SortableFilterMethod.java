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

import java.lang.reflect.Method;

import com.github.pfmiles.dstier1.T1Filter;

/**
 * Filter method encapsulation which can be sorted by execution order.
 * 
 * @author pf-miles
 *
 */
public class SortableFilterMethod implements Comparable<SortableFilterMethod> {
	private T1Filter filter;
	private Method method;
	private int priority;

	public SortableFilterMethod(T1Filter filter, Method method, int priority) {
		this.filter = filter;
		this.method = method;
		this.priority = priority;

	}

	@Override
	public int compareTo(SortableFilterMethod o) {
		return this.priority - o.priority;
	}

	public T1Filter getFilter() {
		return filter;
	}

	public void setFilter(T1Filter filter) {
		this.filter = filter;
	}

	public Method getMethod() {
		return method;
	}

	public void setMethod(Method method) {
		this.method = method;
	}

	public int getPriority() {
		return priority;
	}

	public void setPriority(int priority) {
		this.priority = priority;
	}

}
