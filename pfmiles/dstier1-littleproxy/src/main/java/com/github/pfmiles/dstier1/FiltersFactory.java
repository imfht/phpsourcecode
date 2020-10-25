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
package com.github.pfmiles.dstier1;

import java.util.Collection;

/**
 * Builds filters(T1Filter) to be used to process reqs/rsps. Filters will be
 * created in a per-request manner. So they are thread-safe by natural.
 * 
 * @author pf-miles
 *
 */
public interface FiltersFactory {
	/**
	 * Builds new filter instances. This method is invoked at every time a brand new
	 * request is arrived. Note that an arrival of a chunked content does not cause
	 * the invocation of this method, only initial requests does. So generally
	 * speaking, I may create new filter instances at the arrival of a initial
	 * request and continue using it statefully during the following progress of a
	 * chunked transference.
	 * 
	 * @param reqInfo
	 *            read-only request info
	 * @return new filter instances, will be used in a thread-safe-per-request env,
	 *         so they can keep states. <b>(* Note that this method MUST return
	 *         brand-new filter instances when every time it is invoked *)</b>
	 */
	Collection<T1Filter> buildFilters(RequestInfo reqInfo);
}
