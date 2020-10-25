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
package com.github.pfmiles;

import io.netty.handler.codec.http.DefaultHttpHeaders;
import io.netty.handler.codec.http.HttpHeaders;

/**
 * @author pf-miles
 *
 */
public class Test {
	public static void main(String... args) throws Exception {
		HttpHeaders hs = new DefaultHttpHeaders();
		hs.add("test", "v1");
		hs.add("test", "v2");
		hs.add("Test", "v3");
		hs.add("TEST", "v4");
		System.out.println(hs.get("test"));
	}
}
