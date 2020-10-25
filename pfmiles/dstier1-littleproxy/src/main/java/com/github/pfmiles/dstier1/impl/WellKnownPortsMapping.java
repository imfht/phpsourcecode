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

import java.util.Map;
import java.util.TreeMap;

/**
 * Manage port numbers of well-known application protocols.
 * 
 * @author pf-miles
 */
public abstract class WellKnownPortsMapping {
	private static final Map<String, Integer> mapping = new TreeMap<>(String.CASE_INSENSITIVE_ORDER);
	static {
		mapping.put("http", 80);
		mapping.put("https", 443);
	}

	/**
	 * Retrieve the well known port number for a specific protocol name, the
	 * protocol name is case-insensitive.
	 * 
	 * @param protoName
	 *            case-insensitive protocol name, 'http' for example
	 * @return the corresponding port number for the specified protocol, or null
	 *         when not found
	 */
	public static final Integer getPortByName(String protoName) {
		return mapping.get(protoName);
	}
}
