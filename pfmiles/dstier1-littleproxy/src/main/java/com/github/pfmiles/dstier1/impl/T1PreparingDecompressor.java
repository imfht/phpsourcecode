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

import java.util.List;

import com.github.pfmiles.dstier1.T1Conf;
import io.netty.channel.ChannelHandler;
import io.netty.channel.ChannelHandlerContext;
import io.netty.handler.codec.http.HttpContentDecompressor;
import io.netty.handler.codec.http.HttpObject;
import io.netty.handler.codec.http.HttpRequest;
import io.netty.util.ReferenceCountUtil;
import org.littleshoot.proxy.impl.ClientToProxyConnection;
import org.littleshoot.proxy.impl.ProxyToServerConnection;
import org.littleshoot.proxy.impl.ProxyUtils;

/**
 * The decompressor for t1 fitlering and site mapping(and also prepare for some
 * request-specific shared values). If no site mapping or filtering exists upon
 * a specific request, then no need to process.
 * 
 * @author pf-miles
 *
 */
public class T1PreparingDecompressor extends HttpContentDecompressor {

	@Override
	protected void decode(ChannelHandlerContext ctx, HttpObject msg, List<Object> out) throws Exception {
		/*
		 * if decode failure, pass through, this class does not responsible for decode
		 * error reporting
		 */
		if (!msg.getDecoderResult().isSuccess()) {
			out.add(ReferenceCountUtil.retain(msg));
			return;
		}
		if (msg instanceof HttpRequest) {
			/*
			 * 1.if it's a initial request, init the per-request values, and determine
			 * whether this req should be handled
			 */
			HttpRequest request = (HttpRequest) msg;
			/*
			 * when msg is request, this compressor is configured in the
			 * clientToProxyConnection's pipeline
			 */
			ClientToProxyConnection ctp = (ClientToProxyConnection) ctx.pipeline().get("handler");
			T1Conf serverConf = ctp.getProxyServer().getT1Conf();
			ValueHolder vals = new ValueHolder();
			ctp.setPerReqVals(vals);
			if (ProxyUtils.needFiltering(request, serverConf, vals)) {
				super.decode(ctx, msg, out);
			} else {
				out.add(ReferenceCountUtil.retain(msg));
			}
		} else {
			/*
			 * 2.otherwise invoke super.decode to continue handling the content if is in a
			 * process of request
			 */
			ValueHolder vals = resolvePerReqVals(ctx, msg);
			if (vals != null && vals.getNeedFiltering() != null && vals.getNeedFiltering()) {
				super.decode(ctx, msg, out);
			} else {
				out.add(ReferenceCountUtil.retain(msg));
			}
		}
	}

	/**
	 * Get perReqVals in ClientToProxyConnection
	 */
	private ValueHolder resolvePerReqVals(ChannelHandlerContext ctx, HttpObject msg) {
		ChannelHandler handler = ctx.pipeline().get("handler");
		if (handler instanceof ClientToProxyConnection) {
			// in clientToProxyConnection pipeline
			ClientToProxyConnection ctp = (ClientToProxyConnection) handler;
			return ctp.getPerReqVals();
		} else {
			// in proxyToServerConnection pipeline
			ProxyToServerConnection pts = (ProxyToServerConnection) handler;
			return pts.getClientConnection().getPerReqVals();
		}
	}
}
