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

import java.net.InetSocketAddress;
import java.util.List;
import java.util.Map;

import com.github.pfmiles.dstier1.ReqCtxKeys;
import io.netty.channel.ChannelHandlerContext;
import io.netty.handler.codec.http.FullHttpResponse;
import io.netty.handler.codec.http.HttpObject;
import io.netty.handler.codec.http.HttpRequest;
import io.netty.handler.codec.http.HttpResponse;
import io.netty.handler.codec.http.HttpResponseStatus;
import io.netty.handler.codec.http.HttpVersion;
import org.apache.commons.lang3.StringUtils;
import org.littleshoot.proxy.HttpFiltersAdapter;
import org.littleshoot.proxy.impl.ProxyUtils;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * The filter adapting to the LittleProxy filtering mechanism. It weaves T1
 * filters' methods to the right place.
 * <p />
 * It's created in a per-request manner, so it's thread-safe by default.
 * 
 * @author pf-miles
 *
 */
public class DsT1AdaptingHttpFilters extends HttpFiltersAdapter {

	private static final Logger logger = LoggerFactory.getLogger(DsT1AdaptingHttpFilters.class);

	private List<SortableFilterMethod> sortedReqFilteringMethods;
	private List<SortableFilterMethod> sortedRspFilteringMethods;
	/**
	 * if terminate processing(may write bad-gateway response) on filter methods
	 * execution error
	 */
	private boolean failFastOnFilterError = false;

	public DsT1AdaptingHttpFilters(ChannelHandlerContext ctx, HttpRequest originalRequest,
			List<SortableFilterMethod> reqMethods, List<SortableFilterMethod> rspMethods, boolean failFastOnFilterError,
			ValueHolder perReqVals) {
		super(originalRequest, ctx, perReqVals);
		this.sortedReqFilteringMethods = reqMethods;
		this.sortedRspFilteringMethods = rspMethods;
		this.failFastOnFilterError = failFastOnFilterError;
	}

	@Override
	public HttpResponse clientToProxyRequest(HttpObject httpObject) {
		if (logger.isDebugEnabled()) {
			logger.debug("1 -> clientToProxyRequest: " + httpObject.toString());
		}
		return super.clientToProxyRequest(httpObject);
	}

	/**
	 * onRequesting filters intercepts here
	 */
	@Override
	public HttpResponse proxyToServerRequest(HttpObject httpObject) {
		if (logger.isDebugEnabled()) {
			logger.debug("8 -> proxyToServerRequest(create request): " + httpObject.toString());
		}
		for (SortableFilterMethod m : this.sortedReqFilteringMethods) {
			try {
				// onRequesting filtering...
				HttpResponse shortRsp = (HttpResponse) m.getMethod().invoke(m.getFilter(), httpObject,
						prepareDftCtxVals(this.perReqVals.getContext()));
				if (shortRsp != null) {
					if (logger.isDebugEnabled()) {
						logger.debug("Filter method: '" + m.getMethod()
								+ "' returned short-circuit response, cancel the following processing and respond immediately.");
					}
					return shortRsp;
				}
			} catch (Throwable e) {
				if (failFastOnFilterError) {
					// respond bad gateway to fail fast
					String errMsg = "Filter method execution throws exception, please contact the sys-admin.";
					if (httpObject instanceof HttpRequest && ProxyUtils.isHEAD((HttpRequest) httpObject)) {
						// head method request cannot have rsp body
						errMsg = null;
					}
					logger.error("Filter method: '" + m.getMethod()
							+ "' throws exception, processing terminated, bad gateway response returned.", e);
					return createBadGatewayRsp(errMsg);
				} else {
					// ignore and continue...
					logger.error("Filter method: '" + m.getMethod()
							+ "' throws exception, ignored and continue executing following ones.", e);
				}
			}
		}
		return null;
	}

	// setting the default context values define in 'ReqCtxKeys.java'
	private Map<String, Object> prepareDftCtxVals(Map<String, Object> context) {
		context.put(ReqCtxKeys.FROM_SITE, this.perReqVals.getFromSite());
		context.put(ReqCtxKeys.TO_SITE, this.perReqVals.getToSite());
		return context;
	}

	private static HttpResponse createBadGatewayRsp(String errMsg) {
		if (StringUtils.isBlank(errMsg)) {
			errMsg = "";
		}
		FullHttpResponse rsp = ProxyUtils.createFullHttpResponse(HttpVersion.HTTP_1_1, HttpResponseStatus.BAD_GATEWAY,
				errMsg);
		if (StringUtils.isBlank(errMsg)) {
			rsp.content().clear();
		}
		return rsp;
	}

	@Override
	public void proxyToServerRequestSending() {
		if (logger.isDebugEnabled()) {
			logger.debug("9 -> proxyToServerRequestSending...");
		}
		super.proxyToServerRequestSending();
	}

	@Override
	public void proxyToServerRequestSent() {
		if (logger.isDebugEnabled()) {
			logger.debug("10 -> proxyToServerRequestSent.");
		}
		super.proxyToServerRequestSent();
	}

	@Override
	public HttpObject serverToProxyResponse(HttpObject httpObject) {
		if (logger.isDebugEnabled()) {
			logger.debug("12 -> serverToProxyResponse(recieved): " + httpObject.toString());
		}
		return super.serverToProxyResponse(httpObject);
	}

	@Override
	public void serverToProxyResponseTimedOut() {
		if (logger.isDebugEnabled()) {
			logger.debug("12 -> serverToProxyResponseTimedOut...");
		}
		super.serverToProxyResponseTimedOut();
	}

	@Override
	public void serverToProxyResponseReceiving() {
		if (logger.isDebugEnabled()) {
			logger.debug("11 -> serverToProxyResponseReceiving...");
		}
		super.serverToProxyResponseReceiving();
	}

	@Override
	public void serverToProxyResponseReceived() {
		if (logger.isDebugEnabled()) {
			logger.debug("14 -> serverToProxyResponseReceived.");
		}
		super.serverToProxyResponseReceived();
	}

	/**
	 * onResponding filters intercepts here
	 */
	@Override
	public HttpObject proxyToClientResponse(HttpObject httpObject) {
		if (logger.isDebugEnabled()) {
			logger.debug("13 -> proxyToClientResponse: " + httpObject.toString());
		}
		HttpObject rsp = httpObject;
		for (SortableFilterMethod m : this.sortedRspFilteringMethods) {
			try {
				rsp = (HttpObject) m.getMethod().invoke(m.getFilter(), rsp, this.perReqVals.getContext());
			} catch (Exception e) {
				if (this.failFastOnFilterError) {
					// terminate processing and disconnect directly
					logger.error("Filter method: '" + m.getMethod()
							+ "' execution throws exception, processing terminated, disconnecting.", e);
					return null;
				} else {
					// ignore and continue execution
					logger.error("Filter method: '" + m.getMethod()
							+ "' throws exception, ignored and continue executing following ones.", e);
				}
			}
			if (rsp == null) {
				// disconnection required by filter
				if (logger.isDebugEnabled()) {
					logger.debug("Forced disconnection required by filter method: '" + m.getMethod()
							+ "', filtering process terminated.");
				}
				return null;
			}
		}
		return rsp;
	}

	@Override
	public void proxyToServerConnectionQueued() {
		if (logger.isDebugEnabled()) {
			logger.debug("2 -> proxyToServerConnectionQueued...");
		}
		super.proxyToServerConnectionQueued();
	}

	@Override
	public InetSocketAddress proxyToServerResolutionStarted(String resolvingServerHostAndPort) {
		if (logger.isDebugEnabled()) {
			logger.debug("3 -> proxyToServerResolutionStarted: " + resolvingServerHostAndPort);
		}
		return super.proxyToServerResolutionStarted(resolvingServerHostAndPort);
	}

	@Override
	public void proxyToServerResolutionFailed(String hostAndPort) {
		if (logger.isDebugEnabled()) {
			logger.debug("4 -> proxyToServerResolutionFailed: " + hostAndPort);
		}
		super.proxyToServerResolutionFailed(hostAndPort);
	}

	@Override
	public void proxyToServerResolutionSucceeded(String serverHostAndPort, InetSocketAddress resolvedRemoteAddress) {
		if (logger.isDebugEnabled()) {
			logger.debug("4 -> proxyToServerResolutionSucceeded: " + serverHostAndPort + ", resolved: "
					+ resolvedRemoteAddress);
		}
		super.proxyToServerResolutionSucceeded(serverHostAndPort, resolvedRemoteAddress);
	}

	@Override
	public void proxyToServerConnectionStarted() {
		if (logger.isDebugEnabled()) {
			logger.debug("5 -> proxyToServerConnectionStarted...");
		}
		super.proxyToServerConnectionStarted();
	}

	@Override
	public void proxyToServerConnectionSSLHandshakeStarted() {
		if (logger.isDebugEnabled()) {
			logger.debug("6 -> proxyToServerConnectionSSLHandshakeStarted...");
		}
		super.proxyToServerConnectionSSLHandshakeStarted();
	}

	@Override
	public void proxyToServerConnectionFailed() {
		if (logger.isDebugEnabled()) {
			logger.debug("7 -> proxyToServerConnectionFailed...");
		}
		super.proxyToServerConnectionFailed();
	}

	@Override
	public void proxyToServerConnectionSucceeded(ChannelHandlerContext serverCtx) {
		if (logger.isDebugEnabled()) {
			logger.debug("7 -> proxyToServerConnectionSucceeded, serverCtx: " + serverCtx);
		}
		super.proxyToServerConnectionSucceeded(serverCtx);
	}

}
