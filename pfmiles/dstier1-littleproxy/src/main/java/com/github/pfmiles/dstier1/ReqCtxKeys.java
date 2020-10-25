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

/**
 * Pre-defined system default keys of the request context values.
 * 
 * @author pf-miles
 *
 */
public interface ReqCtxKeys {
	/**
	 * The site from which this request be mapped, according to the "SiteMapping"
	 * context of SiteMappingManager
	 */
	String FROM_SITE = "__from_site__";
	/**
	 * The site to which this request be mapped, according to the "SiteMapping"
	 * context of SiteMappingManager
	 */
	String TO_SITE = "__to_site__";

}
