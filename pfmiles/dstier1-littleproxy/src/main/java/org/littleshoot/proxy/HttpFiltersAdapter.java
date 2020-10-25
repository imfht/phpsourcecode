package org.littleshoot.proxy;

import java.net.InetSocketAddress;

import com.github.pfmiles.dstier1.impl.ValueHolder;
import io.netty.channel.ChannelHandlerContext;
import io.netty.handler.codec.http.HttpObject;
import io.netty.handler.codec.http.HttpRequest;
import io.netty.handler.codec.http.HttpResponse;

/**
 * Convenience base class for implementations of {@link HttpFilters}.
 */
public class HttpFiltersAdapter implements HttpFilters {
	/**
	 * A default, stateless, no-op {@link HttpFilters} instance.
	 */
	public static final HttpFiltersAdapter NOOP_FILTER = new HttpFiltersAdapter(null);

	protected final HttpRequest originalRequest;
	protected final ChannelHandlerContext ctx;
	/**
	 * Holds values which live during only one requesting progress.
	 */
	protected final ValueHolder perReqVals;

	public HttpFiltersAdapter(HttpRequest originalRequest, ChannelHandlerContext ctx) {
		this(originalRequest, ctx, null);
	}

	public HttpFiltersAdapter(HttpRequest originalRequest) {
		this(originalRequest, null);
	}

	public HttpFiltersAdapter(HttpRequest request, ChannelHandlerContext ctx, ValueHolder perReqVals) {
		this.originalRequest = request;
		this.ctx = ctx;
		this.perReqVals = perReqVals;
	}

	@Override
	public HttpResponse clientToProxyRequest(HttpObject httpObject) {
		return null;
	}

	@Override
	public HttpResponse proxyToServerRequest(HttpObject httpObject) {
		return null;
	}

	@Override
	public void proxyToServerRequestSending() {
	}

	@Override
	public void proxyToServerRequestSent() {
	}

	@Override
	public HttpObject serverToProxyResponse(HttpObject httpObject) {
		return httpObject;
	}

	@Override
	public void serverToProxyResponseTimedOut() {
	}

	@Override
	public void serverToProxyResponseReceiving() {
	}

	@Override
	public void serverToProxyResponseReceived() {
	}

	@Override
	public HttpObject proxyToClientResponse(HttpObject httpObject) {
		return httpObject;
	}

	@Override
	public void proxyToServerConnectionQueued() {
	}

	@Override
	public InetSocketAddress proxyToServerResolutionStarted(String resolvingServerHostAndPort) {
		return null;
	}

	@Override
	public void proxyToServerResolutionFailed(String hostAndPort) {
	}

	@Override
	public void proxyToServerResolutionSucceeded(String serverHostAndPort, InetSocketAddress resolvedRemoteAddress) {
	}

	@Override
	public void proxyToServerConnectionStarted() {
	}

	@Override
	public void proxyToServerConnectionSSLHandshakeStarted() {
	}

	@Override
	public void proxyToServerConnectionFailed() {
	}

	@Override
	public void proxyToServerConnectionSucceeded(ChannelHandlerContext serverCtx) {
	}

	@Override
	public HttpResponse proxyToClientShortCircuitResponse(HttpResponse httpResponse) {
		return httpResponse;
	}

	@Override
	public void proxyToServerSiteMapping(String fromSite, String toSite) {
	}
}
