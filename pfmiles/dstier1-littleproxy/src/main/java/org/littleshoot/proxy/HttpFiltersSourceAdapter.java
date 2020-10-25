package org.littleshoot.proxy;

import com.github.pfmiles.dstier1.impl.ValueHolder;
import io.netty.channel.ChannelHandlerContext;
import io.netty.handler.codec.http.HttpRequest;

/**
 * Convenience base class for implementations of {@link HttpFiltersSource}.
 */
public class HttpFiltersSourceAdapter implements HttpFiltersSource {

	public HttpFilters filterRequest(HttpRequest originalRequest, ValueHolder perReqVals) {
		return filterRequest(originalRequest, null, perReqVals);
	}

	@Override
	public HttpFilters filterRequest(HttpRequest originalRequest, ChannelHandlerContext ctx, ValueHolder perReqVals) {
		return new HttpFiltersAdapter(originalRequest, ctx, perReqVals);
	}

	@Override
	public int getMaximumRequestBufferSizeInBytes() {
		return 0;
	}

	@Override
	public int getMaximumResponseBufferSizeInBytes() {
		return 0;
	}

}
