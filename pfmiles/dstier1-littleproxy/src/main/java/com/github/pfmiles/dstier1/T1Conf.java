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

import java.beans.BeanInfo;
import java.beans.Introspector;
import java.beans.PropertyDescriptor;

import org.littleshoot.proxy.MitmManager;
import org.littleshoot.proxy.TransportProtocol;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * The configuration object which aggregates all the confs available on the T1
 * server.
 * 
 * @author pf-miles
 *
 */
public class T1Conf {
	private static final Logger logger = LoggerFactory.getLogger(T1Conf.class);

	private static final int MAX_INITIAL_LINE_LENGTH_DEFAULT = 8192;
	private static final int MAX_HEADER_SIZE_DEFAULT = 8192 * 2;
	private static final int MAX_CHUNK_SIZE_DEFAULT = 8192 * 2;

	private static final int CORE_NUM = Runtime.getRuntime().availableProcessors();
	/**
	 * port this proxy server listens to
	 */
	private int port = 8080;
	/**
	 * address this proxy would use to listen to (192.168.0.1 or localhost as such),
	 * this item has a higher priority than 'localOnly'
	 */
	private String requestAddress = null;
	/**
	 * network interface card name which the proxy used to send out server
	 * requests(a.k.a.Outbound requests), "eth0" or "en0" as such.
	 */
	private String nic = null;
	/**
	 * whether this proxy allow local connections only, this makes the proxy listen
	 * with address: 127.0.0.1, this conf item has a lower priority than
	 * 'requestAddress'
	 */
	private boolean localOnly = false;
	/**
	 * The 'man-in-the-middle' proxy pattern's manager, it's necessary when proxying
	 * for https transactions.
	 */
	private MitmManager mitmManager = null;
	/**
	 * if turn on dnssec verification('true' or 'on' for turn on and 'false' or
	 * 'off' for off)
	 */
	private String dnssec = "off";
	/**
	 * Determine if this proxy acts transparently(Won't do any modification to
	 * proxied requests).
	 */
	private boolean transparent = false;
	/**
	 * Idle connections are disconnected after X seconds of inactivity
	 */
	private int idleConnectionTimeout = 70;
	/**
	 * number of milliseconds to wait to connect to the upstream server
	 */
	private int connectTimeout = 5000;
	/**
	 * The maximum length of the initial line (e.g. "GET / HTTP/1.0") If the length
	 * of the initial line exceeds this value, a TooLongFrameException will be
	 * raised. ---- inherited from jetty
	 */
	private int maxInitialLineLength = MAX_INITIAL_LINE_LENGTH_DEFAULT;
	/**
	 * The maximum length of all headers. If the sum of the length of each header
	 * exceeds this value, a TooLongFrameException will be raised. ---- inherited
	 * from jetty
	 */
	private int maxHeaderSize = MAX_HEADER_SIZE_DEFAULT;
	/**
	 * The maximum length of the content or each chunk. If the content length
	 * exceeds this value, the transfer encoding of the decoded request will be
	 * converted to 'chunked' and the content will be split into multiple
	 * HttpContents. If the transfer encoding of the HTTP request is 'chunked'
	 * already, each chunk will be split into smaller chunks if the length of the
	 * chunk exceeds this value. If you prefer not to handle HttpContents in your
	 * handler, insert HttpObjectAggregator after this decoder in the
	 * ChannelPipeline. ---- inherited from jetty
	 */
	private int maxChunkSize = MAX_CHUNK_SIZE_DEFAULT;
	/**
	 * The name of this proxy server, used for logging and naming threads.
	 */
	private String name = "T1";
	/**
	 * Specify the transportProtocol to use for incoming connections.
	 */
	private String transportProtocol = TransportProtocol.TCP.name();
	/**
	 * Specify the read bandwidth throttles for this proxy server. 0 indicates not
	 * throttling.
	 */
	private int readThrottleBytesPerSecond = 0;
	/**
	 * Specify the write bandwidth throttles for this proxy server. 0 indicates not
	 * throttling.
	 */
	private int writeThrottleBytesPerSecond = 0;
	/**
	 * if this proxy acts as an reverse-proxy, defaults to reverse proxy. When in
	 * reverse mode, the proxy will allow requests in origin form. Otherwise, the
	 * proxy only accepts absolute form requests. According to http 1.1 protocol(RFC
	 * 7230, section 5.3.2), clients must send requests in absolute form when
	 * communicating with a forward proxy. And also note that when in reverse mode,
	 * site mapping is a must, so you must specify a well behaved
	 * SiteMappingManager when this value is true.
	 */
	private boolean reverseMode = true;
	/**
	 * The pseudonym to be used when crafting a 'Via' header. But whatever this
	 * value is set to be, the hostname information is automatically appended to the
	 * pseudonym when constructing the 'Via' header.
	 */
	private String viaPseudonym = "T1";
	/**
	 * the number of acceptor threads to create. Acceptor threads accept HTTP
	 * connections from the client and queue them for processing by client-to-proxy
	 * worker threads. The default value is cpu core number / 2, and guaranteed not
	 * less than 1.
	 */
	private int acceptorThreads = CORE_NUM / 2 < 1 ? 1 : (int) (CORE_NUM / 2);
	/**
	 * the number of client-to-proxy worker threads to create. Worker threads
	 * perform the actual processing of client requests. The default value is cpu
	 * core number * 2 (consider the 'hyper-thread' situation).
	 */
	private int clientToProxyWorkerThreads = CORE_NUM * 2;
	/**
	 * the number of proxy-to-server worker threads to create. Proxy-to-server
	 * worker threads make requests to upstream servers and process responses from
	 * the server. The default value is cpu core number * 2 (consider the
	 * 'hyper-thread' situation).
	 */
	private int proxyToServerWorkerThreads = CORE_NUM * 2;
	// /**
	// * The max request buffer size to automatically collect the request data when
	// * filtering the incoming requests from client. Requests which have a size
	// less
	// * than this value will be fully-collected as a 'whole-request', that means no
	// * following chunks need to be processed. Those which larger than this value,
	// * need to be processed in a chunked manner in user provided filter
	// * implementations.
	// */
	// private int maxFullyCollectReqSize = 256 * 1024;
	// /**
	// * The max response buffer size to automatically collect the response data
	// when
	// * filtering the out-going responses from server. Responses which have a size
	// * less than this value will be fully-collected as a 'whole-response', that
	// * means no following chunks need to be processed. Those which larger than
	// this
	// * value, need to be processed in a chunked manner in user provided filter
	// * implementations.
	// */
	// private int maxFullyCollectRspSize = 256 * 1024;
	/**
	 * Specify the site mapping manager.
	 */
	private SiteMappingManager siteMappingManager;
	/**
	 * Filter factory to be used to create filter instances to process reqs/rsps.
	 * Filters will be created in a per-request manner, so they are thread-safe and
	 * can keep states.
	 */
	private FiltersFactory filtersFactory;
	/**
	 * Whether respond with a 'bad gateway' response and terminate the processing
	 * immediately when any filter method execution throws exception, or just ignore
	 * and continue execution. Defaults to not fail fast(ignore and continue).
	 */
	private boolean failFastOnFilterError = false;

	public boolean isFailFastOnFilterError() {
		return failFastOnFilterError;
	}

	public void setFailFastOnFilterError(boolean failFastOnFilterError) {
		this.failFastOnFilterError = failFastOnFilterError;
	}

	public FiltersFactory getFiltersFactory() {
		return filtersFactory;
	}

	public void setFiltersFactory(FiltersFactory filtersFactory) {
		this.filtersFactory = filtersFactory;
	}

	public boolean isReverseMode() {
		return reverseMode;
	}

	public void setReverseMode(boolean reverseMode) {
		this.reverseMode = reverseMode;
	}

	public SiteMappingManager getSiteMappingManager() {
		return siteMappingManager;
	}

	public void setSiteMappingManager(SiteMappingManager siteMappingManager) {
		this.siteMappingManager = siteMappingManager;
	}

	public int getAcceptorThreads() {
		return acceptorThreads;
	}

	public void setAcceptorThreads(int acceptorThreads) {
		this.acceptorThreads = acceptorThreads;
	}

	public int getClientToProxyWorkerThreads() {
		return clientToProxyWorkerThreads;
	}

	public void setClientToProxyWorkerThreads(int clientToProxyWorkerThreads) {
		this.clientToProxyWorkerThreads = clientToProxyWorkerThreads;
	}

	public int getProxyToServerWorkerThreads() {
		return proxyToServerWorkerThreads;
	}

	public void setProxyToServerWorkerThreads(int proxyToServerWorkerThreads) {
		this.proxyToServerWorkerThreads = proxyToServerWorkerThreads;
	}

	public String getViaPseudonym() {
		return viaPseudonym;
	}

	public void setViaPseudonym(String viaPseudonym) {
		this.viaPseudonym = viaPseudonym;
	}

	public int getReadThrottleBytesPerSecond() {
		return readThrottleBytesPerSecond;
	}

	public void setReadThrottleBytesPerSecond(int readThrottleBytesPerSecond) {
		this.readThrottleBytesPerSecond = readThrottleBytesPerSecond;
	}

	public int getWriteThrottleBytesPerSecond() {
		return writeThrottleBytesPerSecond;
	}

	public void setWriteThrottleBytesPerSecond(int writeThrottleBytesPerSecond) {
		this.writeThrottleBytesPerSecond = writeThrottleBytesPerSecond;
	}

	public String getTransportProtocol() {
		return transportProtocol;
	}

	public void setTransportProtocol(String transportProtocol) {
		this.transportProtocol = transportProtocol;
	}

	public int getPort() {
		return port;
	}

	public void setPort(int port) {
		this.port = port;
	}

	public String getNic() {
		return nic;
	}

	public void setNic(String nic) {
		this.nic = nic;
	}

	public boolean isLocalOnly() {
		return localOnly;
	}

	public void setLocalOnly(boolean localOnly) {
		this.localOnly = localOnly;
	}

	public MitmManager getMitmManager() {
		return mitmManager;
	}

	public void setMitmManager(MitmManager mitmManager) {
		this.mitmManager = mitmManager;
	}

	public String getDnssec() {
		return dnssec;
	}

	public void setDnssec(String dnssec) {
		this.dnssec = dnssec;
	}

	public boolean isTransparent() {
		return transparent;
	}

	public void setTransparent(boolean transparent) {
		this.transparent = transparent;
	}

	public int getIdleConnectionTimeout() {
		return idleConnectionTimeout;
	}

	public void setIdleConnectionTimeout(int idleConnectionTimeout) {
		this.idleConnectionTimeout = idleConnectionTimeout;
	}

	public int getConnectTimeout() {
		return connectTimeout;
	}

	public void setConnectTimeout(int connectTimeout) {
		this.connectTimeout = connectTimeout;
	}

	public int getMaxInitialLineLength() {
		return maxInitialLineLength;
	}

	public void setMaxInitialLineLength(int maxInitialLineLength) {
		this.maxInitialLineLength = maxInitialLineLength;
	}

	public int getMaxHeaderSize() {
		return maxHeaderSize;
	}

	public void setMaxHeaderSize(int maxHeaderSize) {
		this.maxHeaderSize = maxHeaderSize;
	}

	public int getMaxChunkSize() {
		return maxChunkSize;
	}

	public void setMaxChunkSize(int maxChunkSize) {
		this.maxChunkSize = maxChunkSize;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getRequestAddress() {
		return requestAddress;
	}

	public void setRequestAddress(String requestAddress) {
		this.requestAddress = requestAddress;
	}

	@Override
	public String toString() {
		StringBuilder sb = new StringBuilder();
		try {
			BeanInfo bi = Introspector.getBeanInfo(this.getClass());
			for (PropertyDescriptor pd : bi.getPropertyDescriptors()) {
				if ("class".equals(pd.getName())) {
					continue;
				}
				sb.append(pd.getName()).append(": ").append(pd.getReadMethod().invoke(this)).append('\n');
			}
		} catch (Exception e) {
			logger.error("T1Conf to string failed.", e);
		}
		return sb.toString();
	}

}
