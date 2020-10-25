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

import java.util.Map;

import io.netty.handler.codec.http.HttpObject;
import io.netty.handler.codec.http.HttpResponse;

/**
 * The filter components interface for request & response filtering. T1 filters
 * could examine or modify request and response data pass through. Not all
 * filter implementations are activated by default, it depends on the result of
 * method 'active'.
 * <p />
 * T1 filter instances are created in a per-connection manner, so it's
 * thread-safe by default and could keep internal states during its activation.
 * 
 * @author pf-miles
 *
 */
public interface T1Filter {
	/**
	 * Tell if this filter should be activated upon a particular incoming request.
	 * 
	 * @param req
	 *            information about the incoming request
	 * @return true if active on this request, false otherwise.
	 */
	boolean active(RequestInfo req);

	/**
	 * Filtering requests from client to server. Return null if you want the
	 * requesting progress to continue, or a hand-crafted HttpResponse object to
	 * short-circuit the process. The HttpObjectAggregator.decode method could be a
	 * reference to this method's implementation.
	 * 
	 * @param httpObj
	 *            the HttpRequest object to be filtered, or HttpContent object
	 *            during a chunked encoding request transmission.
	 * @param reqContext
	 *            a context shared among all the onRequesting and onResponding
	 *            methods during this progress of request
	 * @return null to continue processing as usual, or a short-circuit response
	 */
	HttpResponse onRequesting(HttpObject httpObj, Map<String, Object> reqContext);

	/**
	 * Filtering responses from server to client, returning null will force a
	 * disconnect. The HttpObjectAggregator.decode method could be a reference to
	 * this method's implementation.
	 * 
	 * @param httpObj
	 *            the response(HttpResponse) or chunk(HttpContent) object to be
	 *            filtered.
	 * @param reqContext
	 *            a context shared among all the onRequesting and onResponding
	 *            methods during this progress of request
	 * @return a modified or unchanged response object(could either be a
	 *         HttpResponse or a HttpContent), null will force a disconnect at once.
	 */
	HttpObject onResponding(HttpObject httpObj, Map<String, Object> reqContext);
}
