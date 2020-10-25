package org.littleshoot.proxy.impl;

import java.io.IOException;
import java.lang.reflect.Field;
import java.lang.reflect.Method;
import java.lang.reflect.Modifier;
import java.net.InetAddress;
import java.nio.charset.StandardCharsets;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collection;
import java.util.Collections;
import java.util.Date;
import java.util.LinkedHashSet;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.Properties;
import java.util.Set;
import java.util.TimeZone;
import java.util.concurrent.Callable;
import java.util.concurrent.ExecutionException;
import java.util.concurrent.TimeUnit;
import java.util.function.Function;
import java.util.regex.Pattern;

import com.github.pfmiles.dstier1.ExeOrder;
import com.github.pfmiles.dstier1.RequestInfo;
import com.github.pfmiles.dstier1.SiteMappingManager;
import com.github.pfmiles.dstier1.T1Conf;
import com.github.pfmiles.dstier1.T1Filter;
import com.github.pfmiles.dstier1.impl.SortableFilterMethod;
import com.github.pfmiles.dstier1.impl.SwappedByteBuf;
import com.github.pfmiles.dstier1.impl.TimedCacheItem;
import com.github.pfmiles.dstier1.impl.ValueHolder;
import com.github.pfmiles.dstier1.impl.WellKnownPortsMapping;
import com.google.common.base.Splitter;
import com.google.common.cache.Cache;
import com.google.common.cache.CacheBuilder;
import com.google.common.collect.ImmutableList;
import com.google.common.collect.ImmutableSet;
import io.netty.buffer.ByteBuf;
import io.netty.buffer.Unpooled;
import io.netty.channel.udt.nio.NioUdtProvider;
import io.netty.handler.codec.http.DefaultFullHttpRequest;
import io.netty.handler.codec.http.DefaultFullHttpResponse;
import io.netty.handler.codec.http.DefaultHttpContent;
import io.netty.handler.codec.http.DefaultHttpResponse;
import io.netty.handler.codec.http.FullHttpRequest;
import io.netty.handler.codec.http.FullHttpResponse;
import io.netty.handler.codec.http.HttpContent;
import io.netty.handler.codec.http.HttpHeaders;
import io.netty.handler.codec.http.HttpMessage;
import io.netty.handler.codec.http.HttpMethod;
import io.netty.handler.codec.http.HttpObject;
import io.netty.handler.codec.http.HttpRequest;
import io.netty.handler.codec.http.HttpResponse;
import io.netty.handler.codec.http.HttpResponseStatus;
import io.netty.handler.codec.http.HttpVersion;
import io.netty.handler.codec.http.LastHttpContent;
import io.netty.util.ReferenceCountUtil;
import org.apache.commons.lang3.StringUtils;
import org.apache.commons.lang3.math.NumberUtils;
import org.apache.commons.lang3.tuple.ImmutablePair;
import org.apache.commons.lang3.tuple.Pair;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * Utilities for the proxy.
 */
public class ProxyUtils {
	private static final int CORE_NUM = Runtime.getRuntime().availableProcessors();
	/**
	 * Hop-by-hop headers that should be removed when proxying, as defined by the
	 * HTTP 1.1 spec, section 13.5.1
	 * (http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html#sec13.5.1).
	 * Transfer-Encoding is NOT included in this list, since LittleProxy does not
	 * typically modify the transfer encoding. See also
	 * {@link #shouldRemoveHopByHopHeader(String)}.
	 *
	 * Header names are stored as lowercase to make case-insensitive comparisons
	 * easier.
	 */
	private static final Set<String> SHOULD_NOT_PROXY_HOP_BY_HOP_HEADERS = ImmutableSet.of(
			HttpHeaders.Names.CONNECTION.toLowerCase(Locale.US),
			HttpHeaders.Names.PROXY_AUTHENTICATE.toLowerCase(Locale.US),
			HttpHeaders.Names.PROXY_AUTHORIZATION.toLowerCase(Locale.US), HttpHeaders.Names.TE.toLowerCase(Locale.US),
			HttpHeaders.Names.TRAILER.toLowerCase(Locale.US),
			/*
			 * Note: Not removing Transfer-Encoding since LittleProxy does not normally
			 * re-chunk content. HttpHeaders.Names.TRANSFER_ENCODING.toLowerCase(Locale.US),
			 */
			HttpHeaders.Names.UPGRADE.toLowerCase(Locale.US), "Keep-Alive".toLowerCase(Locale.US));

	private static final Logger LOG = LoggerFactory.getLogger(ProxyUtils.class);

	private static final TimeZone GMT = TimeZone.getTimeZone("GMT");

	/**
	 * Splits comma-separated header values (such as Connection) into their
	 * individual tokens.
	 */
	private static final Splitter COMMA_SEPARATED_HEADER_VALUE_SPLITTER = Splitter.on(',').trimResults()
			.omitEmptyStrings();

	/**
	 * Date format pattern used to parse HTTP date headers in RFC 1123 format.
	 */
	private static final String PATTERN_RFC1123 = "EEE, dd MMM yyyy HH:mm:ss zzz";

	// Schemes are case-insensitive:
	// http://tools.ietf.org/html/rfc3986#section-3.1
	private static Pattern HTTP_PREFIX = Pattern.compile("^https?://.*", Pattern.CASE_INSENSITIVE);

	// Pattern of a valid 'site'.
	public static Pattern SITE_PATTERN = Pattern.compile("^https?://[a-zA-Z\\d\\-]+(\\.[a-zA-Z\\d\\-]+)*(:\\d{1,5})?$",
			Pattern.CASE_INSENSITIVE);
	// static method cache of filters
	private static Cache<Pair<Class<? extends T1Filter>, String>, TimedCacheItem<Method>> filterMethodsCache = CacheBuilder
			.newBuilder().concurrencyLevel(CORE_NUM * 2).initialCapacity(CORE_NUM * 8).maximumSize(2048)
			.expireAfterAccess(60L, TimeUnit.SECONDS).build();
	// prevents null-value-attack
	private static final Method DUMMY;
	// content field cache for httpContents
	private static Cache<Pair<Class<? extends HttpContent>, String>, TimedCacheItem<Field>> contentFieldsCache = CacheBuilder
			.newBuilder().concurrencyLevel(CORE_NUM * 2).initialCapacity(CORE_NUM * 8).maximumSize(256)
			.expireAfterAccess(60L, TimeUnit.SECONDS).build();
	private static final Field DUMMY_F;
	static {
		try {
			DUMMY = T1Filter.class.getMethod("active", RequestInfo.class);
			DUMMY_F = ProxyUtils.class.getDeclaredField("DUMMY_F");
		} catch (Exception e) {
			throw new RuntimeException(e);
		}
	}

	/**
	 * Strips the host from a URI string. This will turn "http://host.com/path" into
	 * "/path".
	 * 
	 * @param uri
	 *            The URI to transform.
	 * @return A string with the URI stripped.
	 */
	public static String stripHost(final String uri) {
		if (!HTTP_PREFIX.matcher(uri).matches()) {
			// It's likely a URI path, not the full URI (i.e. the host is
			// already stripped).
			return uri;
		}
		final String noHttpUri = StringUtils.substringAfter(uri, "://");
		final int slashIndex = noHttpUri.indexOf("/");
		if (slashIndex == -1) {
			return "/";
		}
		final String noHostUri = noHttpUri.substring(slashIndex);
		return noHostUri;
	}

	/**
	 * Formats the given date according to the RFC 1123 pattern.
	 * 
	 * @param date
	 *            The date to format.
	 * @return An RFC 1123 formatted date string.
	 * 
	 * @see #PATTERN_RFC1123
	 */
	public static String formatDate(final Date date) {
		return formatDate(date, PATTERN_RFC1123);
	}

	/**
	 * Formats the given date according to the specified pattern. The pattern must
	 * conform to that used by the {@link SimpleDateFormat simple date format}
	 * class.
	 * 
	 * @param date
	 *            The date to format.
	 * @param pattern
	 *            The pattern to use for formatting the date.
	 * @return A formatted date string.
	 * 
	 * @throws IllegalArgumentException
	 *             If the given date pattern is invalid.
	 * 
	 * @see SimpleDateFormat
	 */
	public static String formatDate(final Date date, final String pattern) {
		if (date == null)
			throw new IllegalArgumentException("date is null");
		if (pattern == null)
			throw new IllegalArgumentException("pattern is null");

		final SimpleDateFormat formatter = new SimpleDateFormat(pattern, Locale.US);
		formatter.setTimeZone(GMT);
		return formatter.format(date);
	}

	/**
	 * If an HttpObject implements the market interface LastHttpContent, it
	 * represents the last chunk of a transfer.
	 * 
	 * @see io.netty.handler.codec.http.LastHttpContent
	 * 
	 * @param httpObject
	 * @return
	 * 
	 */
	public static boolean isLastChunk(final HttpObject httpObject) {
		return httpObject instanceof LastHttpContent;
	}

	/**
	 * If an HttpObject is not the last chunk, then that means there are other
	 * chunks that will follow.
	 * 
	 * @see io.netty.handler.codec.http.FullHttpMessage
	 * 
	 * @param httpObject
	 * @return
	 */
	public static boolean isChunked(final HttpObject httpObject) {
		return !isLastChunk(httpObject);
	}

	/**
	 * Parses the host and port an HTTP request is being sent to.
	 * 
	 * @param httpRequest
	 *            The request.
	 * @return The host and port string.
	 */
	public static String parseHostAndPort(final HttpRequest httpRequest) {
		String uriHostAndPort = parseHostAndPort(httpRequest.getUri());
		if (StringUtils.isBlank(uriHostAndPort)) {
			List<String> hosts = httpRequest.headers().getAll(HttpHeaders.Names.HOST);
			if (hosts != null && !hosts.isEmpty()) {
				uriHostAndPort = hosts.get(0);
			}
		}
		return uriHostAndPort;
	}

	/**
	 * Parses the host and port an HTTP request is being sent to.
	 * 
	 * @param uri
	 *            The URI.
	 * @return The host and port string.
	 */
	static String parseHostAndPort(final String uri) {
		final String tempUri;
		if (!HTTP_PREFIX.matcher(uri).matches()) {
			// Browsers particularly seem to send requests in this form when
			// they use CONNECT.
			tempUri = uri;
		} else {
			// We can't just take a substring from a hard-coded index because it
			// could be either http or https.
			tempUri = StringUtils.substringAfter(uri, "://");
		}
		String hostAndPort;
		if (tempUri.contains("/")) {
			hostAndPort = tempUri.substring(0, tempUri.indexOf("/"));
		} else {
			hostAndPort = tempUri;
		}
		// if no port info, add it
		if (!hostAndPort.contains(":")) {
			String scheme = StringUtils.substringBefore(uri, "://");
			if (StringUtils.isNotBlank(scheme)) {
				Integer port = WellKnownPortsMapping.getPortByName(scheme);
				if (port != null) {
					hostAndPort = hostAndPort + ":" + port;
				}
			}
		}
		return hostAndPort;
	}

	/**
	 * Make a copy of the response including all mutable fields.
	 * 
	 * @param original
	 *            The original response to copy from.
	 * @return The copy with all mutable fields from the original.
	 */
	public static HttpResponse copyMutableResponseFields(final HttpResponse original) {

		HttpResponse copy = null;
		if (original instanceof DefaultFullHttpResponse) {
			ByteBuf content = ((DefaultFullHttpResponse) original).content();
			copy = new DefaultFullHttpResponse(original.getProtocolVersion(), original.getStatus(), content);
		} else {
			copy = new DefaultHttpResponse(original.getProtocolVersion(), original.getStatus());
		}
		final Collection<String> headerNames = original.headers().names();
		for (final String name : headerNames) {
			final List<String> values = original.headers().getAll(name);
			copy.headers().set(name, values);
		}
		return copy;
	}

	/**
	 * Adds the Via header to specify that the message has passed through the proxy.
	 * The specified alias will be appended to the Via header line. The alias may be
	 * the hostname of the machine proxying the request, or a pseudonym. From RFC
	 * 7230, section 5.7.1:
	 * 
	 * <pre>
	     The received-by portion of the field value is normally the host and
	     optional port number of a recipient server or client that
	     subsequently forwarded the message.  However, if the real host is
	     considered to be sensitive information, a sender MAY replace it with
	     a pseudonym.
	 * </pre>
	 *
	 * 
	 * @param httpMessage
	 *            HTTP message to add the Via header to
	 * @param alias
	 *            the alias to provide in the Via header for this proxy
	 */
	public static void addVia(HttpMessage httpMessage, String alias) {
		String newViaHeader = new StringBuilder().append(httpMessage.getProtocolVersion().majorVersion()).append('.')
				.append(httpMessage.getProtocolVersion().minorVersion()).append(' ').append(alias).toString();

		final List<String> vias;
		if (httpMessage.headers().contains(HttpHeaders.Names.VIA)) {
			List<String> existingViaHeaders = httpMessage.headers().getAll(HttpHeaders.Names.VIA);
			vias = new ArrayList<String>(existingViaHeaders);
			vias.add(newViaHeader);
		} else {
			vias = Collections.singletonList(newViaHeader);
		}

		httpMessage.headers().set(HttpHeaders.Names.VIA, vias);
	}

	/**
	 * Returns <code>true</code> if the specified string is either "true" or "on"
	 * ignoring case.
	 * 
	 * @param val
	 *            The string in question.
	 * @return <code>true</code> if the specified string is either "true" or "on"
	 *         ignoring case, otherwise <code>false</code>.
	 */
	public static boolean isTrue(final String val) {
		return checkTrueOrFalse(val, "true", "on");
	}

	/**
	 * Returns <code>true</code> if the specified string is either "false" or "off"
	 * ignoring case.
	 * 
	 * @param val
	 *            The string in question.
	 * @return <code>true</code> if the specified string is either "false" or "off"
	 *         ignoring case, otherwise <code>false</code>.
	 */
	public static boolean isFalse(final String val) {
		return checkTrueOrFalse(val, "false", "off");
	}

	public static boolean extractBooleanDefaultFalse(final Properties props, final String key) {
		final String throttle = props.getProperty(key);
		if (StringUtils.isNotBlank(throttle)) {
			return throttle.trim().equalsIgnoreCase("true");
		}
		return false;
	}

	public static boolean extractBooleanDefaultTrue(final Properties props, final String key) {
		final String throttle = props.getProperty(key);
		if (StringUtils.isNotBlank(throttle)) {
			return throttle.trim().equalsIgnoreCase("true");
		}
		return true;
	}

	public static int extractInt(final Properties props, final String key) {
		return extractInt(props, key, -1);
	}

	public static int extractInt(final Properties props, final String key, int defaultValue) {
		final String readThrottleString = props.getProperty(key);
		if (StringUtils.isNotBlank(readThrottleString) && NumberUtils.isNumber(readThrottleString)) {
			return Integer.parseInt(readThrottleString);
		}
		return defaultValue;
	}

	public static boolean isCONNECT(HttpObject httpObject) {
		return httpObject instanceof HttpRequest && HttpMethod.CONNECT.equals(((HttpRequest) httpObject).getMethod());
	}

	/**
	 * Returns true if the specified HttpRequest is a HEAD request.
	 *
	 * @param httpRequest
	 *            http request
	 * @return true if request is a HEAD, otherwise false
	 */
	public static boolean isHEAD(HttpRequest httpRequest) {
		return HttpMethod.HEAD.equals(httpRequest.getMethod());
	}

	private static boolean checkTrueOrFalse(final String val, final String str1, final String str2) {
		final String str = val.trim();
		return StringUtils.isNotBlank(str) && (str.equalsIgnoreCase(str1) || str.equalsIgnoreCase(str2));
	}

	/**
	 * Returns true if the HTTP message cannot contain an entity body, according to
	 * the HTTP spec. This code is taken directly from
	 * {@link io.netty.handler.codec.http.HttpObjectDecoder#isContentAlwaysEmpty(HttpMessage)}.
	 *
	 * @param msg
	 *            HTTP message
	 * @return true if the HTTP message is always empty, false if the message
	 *         <i>may</i> have entity content.
	 */
	public static boolean isContentAlwaysEmpty(HttpMessage msg) {
		if (msg instanceof HttpResponse) {
			HttpResponse res = (HttpResponse) msg;
			int code = res.getStatus().code();

			// Correctly handle return codes of 1xx.
			//
			// See:
			// - http://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html Section 4.4
			// - https://github.com/netty/netty/issues/222
			if (code >= 100 && code < 200) {
				// According to RFC 7231, section 6.1, 1xx responses have no content
				// (https://tools.ietf.org/html/rfc7231#section-6.2):
				// 1xx responses are terminated by the first empty line after
				// the status-line (the empty line signaling the end of the header
				// section).

				// Hixie 76 websocket handshake responses contain a 16-byte body, so their
				// content is not empty; but Hixie 76
				// was a draft specification that was superceded by RFC 6455. Since it is rarely
				// used and doesn't conform to
				// RFC 7231, we do not support or make special allowance for Hixie 76 responses.
				return true;
			}

			switch (code) {
			case 204:
			case 205:
			case 304:
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns true if the HTTP response from the server is expected to indicate its
	 * own message length/end-of-message. Returns false if the server is expected to
	 * indicate the end of the HTTP entity by closing the connection.
	 * <p>
	 * This method is based on the allowed message length indicators in the HTTP
	 * specification, section 4.4:
	 * 
	 * <pre>
	     4.4 Message Length
	     The transfer-length of a message is the length of the message-body as it appears in the message; that is, after any transfer-codings have been applied. When a message-body is included with a message, the transfer-length of that body is determined by one of the following (in order of precedence):
	
	     1.Any response message which "MUST NOT" include a message-body (such as the 1xx, 204, and 304 responses and any response to a HEAD request) is always terminated by the first empty line after the header fields, regardless of the entity-header fields present in the message.
	     2.If a Transfer-Encoding header field (section 14.41) is present and has any value other than "identity", then the transfer-length is defined by use of the "chunked" transfer-coding (section 3.6), unless the message is terminated by closing the connection.
	     3.If a Content-Length header field (section 14.13) is present, its decimal value in OCTETs represents both the entity-length and the transfer-length. The Content-Length header field MUST NOT be sent if these two lengths are different (i.e., if a Transfer-Encoding
	     header field is present). If a message is received with both a Transfer-Encoding header field and a Content-Length header field, the latter MUST be ignored.
	     [LP note: multipart/byteranges support has been removed from the HTTP 1.1 spec by RFC 7230, section A.2. Since it is seldom used, LittleProxy does not check for it.]
	     5.By the server closing the connection. (Closing the connection cannot be used to indicate the end of a request body, since that would leave no possibility for the server to send back a response.)
	 * </pre>
	 *
	 * The rules for Transfer-Encoding are clarified in RFC 7230, section 3.3.1 and
	 * 3.3.3 (3):
	 * 
	 * <pre>
	     If any transfer coding other than
	     chunked is applied to a response payload body, the sender MUST either
	     apply chunked as the final transfer coding or terminate the message
	     by closing the connection.
	 * </pre>
	 *
	 *
	 * @param response
	 *            the HTTP response object
	 * @return true if the message will indicate its own message length, or false if
	 *         the server is expected to indicate the message length by closing the
	 *         connection
	 */
	public static boolean isResponseSelfTerminating(HttpResponse response) {
		if (isContentAlwaysEmpty(response)) {
			return true;
		}

		// if there is a Transfer-Encoding value, determine whether the final encoding
		// is "chunked", which makes the message self-terminating
		List<String> allTransferEncodingHeaders = getAllCommaSeparatedHeaderValues(HttpHeaders.Names.TRANSFER_ENCODING,
				response);
		if (!allTransferEncodingHeaders.isEmpty()) {
			String finalEncoding = allTransferEncodingHeaders.get(allTransferEncodingHeaders.size() - 1);

			// per #3 above: "If a message is received with both a Transfer-Encoding header
			// field and a Content-Length header field, the latter MUST be ignored."
			// since the Transfer-Encoding field is present, the message is self-terminating
			// if and only if the final Transfer-Encoding value is "chunked"
			return HttpHeaders.Values.CHUNKED.equals(finalEncoding);
		}

		String contentLengthHeader = HttpHeaders.getHeader(response, HttpHeaders.Names.CONTENT_LENGTH);
		if (contentLengthHeader != null && !contentLengthHeader.isEmpty()) {
			return true;
		}

		// not checking for multipart/byteranges, since it is seldom used and its use as
		// a message length indicator was removed in RFC 7230

		// none of the other message length indicators are present, so the only way the
		// server can indicate the end
		// of this message is to close the connection
		return false;
	}

	/**
	 * Retrieves all comma-separated values for headers with the specified name on
	 * the HttpMessage. Any whitespace (spaces or tabs) surrounding the values will
	 * be removed. Empty values (e.g. two consecutive commas, or a value followed by
	 * a comma and no other value) will be removed; they will not appear as empty
	 * elements in the returned list. If the message contains repeated headers,
	 * their values will be added to the returned list in the order in which the
	 * headers appear. For example, if a message has headers like:
	 * 
	 * <pre>
	 *     Transfer-Encoding: gzip,deflate
	 *     Transfer-Encoding: chunked
	 * </pre>
	 * 
	 * This method will return a list of three values: "gzip", "deflate", "chunked".
	 * <p>
	 * Placing values on multiple header lines is allowed under certain
	 * circumstances in RFC 2616 section 4.2, and in RFC 7230 section 3.2.2 quoted
	 * here:
	 * 
	 * <pre>
	 A sender MUST NOT generate multiple header fields with the same field
	 name in a message unless either the entire field value for that
	 header field is defined as a comma-separated list [i.e., #(values)]
	 or the header field is a well-known exception (as noted below).
	
	 A recipient MAY combine multiple header fields with the same field
	 name into one "field-name: field-value" pair, without changing the
	 semantics of the message, by appending each subsequent field value to
	 the combined field value in order, separated by a comma.  The order
	 in which header fields with the same field name are received is
	 therefore significant to the interpretation of the combined field
	 value; a proxy MUST NOT change the order of these field values when
	 forwarding a message.
	 * </pre>
	 * 
	 * @param headerName
	 *            the name of the header for which values will be retrieved
	 * @param httpMessage
	 *            the HTTP message whose header values will be retrieved
	 * @return a list of single header values, or an empty list if the header was
	 *         not present in the message or contained no values
	 */
	public static List<String> getAllCommaSeparatedHeaderValues(String headerName, HttpMessage httpMessage) {
		List<String> allHeaders = httpMessage.headers().getAll(headerName);
		if (allHeaders.isEmpty()) {
			return Collections.emptyList();
		}

		ImmutableList.Builder<String> headerValues = ImmutableList.builder();
		for (String header : allHeaders) {
			List<String> commaSeparatedValues = splitCommaSeparatedHeaderValues(header);
			headerValues.addAll(commaSeparatedValues);
		}

		return headerValues.build();
	}

	/**
	 * Duplicates the status line and headers of an HttpResponse object. Does not
	 * duplicate any content associated with that response.
	 *
	 * @param originalResponse
	 *            HttpResponse to be duplicated
	 * @return a new HttpResponse with the same status line and headers
	 */
	public static HttpResponse duplicateHttpResponse(HttpResponse originalResponse) {
		DefaultHttpResponse newResponse = new DefaultHttpResponse(originalResponse.getProtocolVersion(),
				originalResponse.getStatus());
		newResponse.headers().add(originalResponse.headers());

		return newResponse;
	}

	/**
	 * Attempts to resolve the local machine's hostname.
	 *
	 * @return the local machine's hostname, or null if a hostname cannot be
	 *         determined
	 */
	public static String getHostName() {
		try {
			return InetAddress.getLocalHost().getHostName();
		} catch (IOException e) {
			LOG.debug("Ignored exception", e);
		} catch (RuntimeException e) {
			// An exception here must not stop the proxy. Android could throw a
			// runtime exception, since it not allows network access in the main
			// process.
			LOG.debug("Ignored exception", e);
		}
		LOG.info("Could not lookup localhost");
		return null;
	}

	/**
	 * Determines if the specified header should be removed from the proxied
	 * response because it is a hop-by-hop header, as defined by the HTTP 1.1 spec
	 * in section 13.5.1. The comparison is case-insensitive, so "Connection" will
	 * be treated the same as "connection" or "CONNECTION". From
	 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html#sec13.5.1 :
	 * 
	 * <pre>
	   The following HTTP/1.1 headers are hop-by-hop headers:
	    - Connection
	    - Keep-Alive
	    - Proxy-Authenticate
	    - Proxy-Authorization
	    - TE
	    - Trailers [LittleProxy note: actual header name is Trailer]
	    - Transfer-Encoding [LittleProxy note: this header is not normally removed when proxying, since the proxy does not re-chunk
	                        responses. The exception is when an HttpObjectAggregator is enabled, which aggregates chunked content and removes
	                        the 'Transfer-Encoding: chunked' header itself.]
	    - Upgrade
	
	   All other headers defined by HTTP/1.1 are end-to-end headers.
	 * </pre>
	 *
	 * @param headerName
	 *            the header name
	 * @return true if this header is a hop-by-hop header and should be removed when
	 *         proxying, otherwise false
	 */
	public static boolean shouldRemoveHopByHopHeader(String headerName) {
		return SHOULD_NOT_PROXY_HOP_BY_HOP_HEADERS.contains(headerName.toLowerCase(Locale.US));
	}

	/**
	 * Splits comma-separated header values into tokens. For example, if the value
	 * of the Connection header is "Transfer-Encoding, close", this method will
	 * return "Transfer-Encoding" and "close". This method strips trims any optional
	 * whitespace from the tokens. Unlike
	 * {@link #getAllCommaSeparatedHeaderValues(String, HttpMessage)}, this method
	 * only operates on a single header value, rather than all instances of the
	 * header in a message.
	 *
	 * @param headerValue
	 *            the un-tokenized header value (must not be null)
	 * @return all tokens within the header value, or an empty list if there are no
	 *         values
	 */
	public static List<String> splitCommaSeparatedHeaderValues(String headerValue) {
		return ImmutableList.copyOf(COMMA_SEPARATED_HEADER_VALUE_SPLITTER.split(headerValue));
	}

	/**
	 * Determines if UDT is available on the classpath.
	 *
	 * @return true if UDT is available
	 */
	public static boolean isUdtAvailable() {
		try {
			return NioUdtProvider.BYTE_PROVIDER != null;
		} catch (NoClassDefFoundError e) {
			return false;
		}
	}

	/**
	 * Creates a new {@link FullHttpResponse} with the specified String as the body
	 * contents (encoded using UTF-8).
	 *
	 * @param httpVersion
	 *            HTTP version of the response
	 * @param status
	 *            HTTP status code
	 * @param body
	 *            body to include in the FullHttpResponse; will be UTF-8 encoded
	 * @return new http response object
	 */
	public static FullHttpResponse createFullHttpResponse(HttpVersion httpVersion, HttpResponseStatus status,
			String body) {
		byte[] bytes = body.getBytes(StandardCharsets.UTF_8);
		ByteBuf content = Unpooled.copiedBuffer(bytes);

		return createFullHttpResponse(httpVersion, status, "text/html; charset=utf-8", content, bytes.length);
	}

	/**
	 * Creates a new {@link FullHttpResponse} with no body content
	 *
	 * @param httpVersion
	 *            HTTP version of the response
	 * @param status
	 *            HTTP status code
	 * @return new http response object
	 */
	public static FullHttpResponse createFullHttpResponse(HttpVersion httpVersion, HttpResponseStatus status) {
		return createFullHttpResponse(httpVersion, status, null, null, 0);
	}

	/**
	 * Creates a new {@link FullHttpResponse} with the specified body.
	 *
	 * @param httpVersion
	 *            HTTP version of the response
	 * @param status
	 *            HTTP status code
	 * @param contentType
	 *            the Content-Type of the body
	 * @param body
	 *            body to include in the FullHttpResponse; if null
	 * @param contentLength
	 *            number of bytes to send in the Content-Length header; should equal
	 *            the number of bytes in the ByteBuf
	 * @return new http response object
	 */
	public static FullHttpResponse createFullHttpResponse(HttpVersion httpVersion, HttpResponseStatus status,
			String contentType, ByteBuf body, int contentLength) {
		DefaultFullHttpResponse response;

		if (body != null) {
			response = new DefaultFullHttpResponse(httpVersion, status, body);
			response.headers().set(HttpHeaders.Names.CONTENT_LENGTH, contentLength);
			response.headers().set(HttpHeaders.Names.CONTENT_TYPE, contentType);
		} else {
			response = new DefaultFullHttpResponse(httpVersion, status);
		}

		return response;
	}

	/**
	 * Given an HttpHeaders instance, removes 'sdch' from the 'Accept-Encoding'
	 * header list (if it exists) and returns the modified instance.
	 *
	 * Removes all occurrences of 'sdch' from the 'Accept-Encoding' header.
	 * 
	 * @param headers
	 *            The headers to modify.
	 */
	public static void removeSdchEncoding(HttpHeaders headers) {
		List<String> encodings = headers.getAll(HttpHeaders.Names.ACCEPT_ENCODING);
		headers.remove(HttpHeaders.Names.ACCEPT_ENCODING);

		for (String encoding : encodings) {
			if (encoding != null) {
				// The former regex should remove occurrences of 'sdch' while the
				// latter regex should take care of the dangling comma case when
				// 'sdch' was the first element in the list and there are other
				// encodings.
				encoding = encoding.replaceAll(",? *(sdch|SDCH)", "").replaceFirst("^ *, *", "");

				if (StringUtils.isNotBlank(encoding)) {
					headers.add(HttpHeaders.Names.ACCEPT_ENCODING, encoding);
				}
			}
		}
	}

	/**
	 * Tell if the specified site information is a valid three-factor-form string,
	 * 'https://aaa.com:443' for example.
	 * 
	 * @param site
	 *            the specified site
	 * @return if the specified site information valid
	 */
	public static boolean isValidSite(String site) {
		if (StringUtils.isBlank(site))
			return false;
		return SITE_PATTERN.matcher(site).matches();
	}

	/**
	 * Extract the three-factor-form site information from the specified request.
	 * 'Three-factor' means 'protocol', 'host' and 'port'.
	 * 
	 * @param httpRequest
	 *            the specified request from which site information is extracted.
	 * @return the three-factor-form site information, 'https://aaa.com:443' for
	 *         example.
	 */
	public static String extractSite(HttpRequest httpRequest) {
		String hostAndPort = parseHostAndPort(httpRequest);
		if (StringUtils.isBlank(hostAndPort)) {
			LOG.error("Cannot parse host and port info, site extraction failed, null returned.");
			return null;
		}
		String uri = httpRequest.getUri();
		if (HTTP_PREFIX.matcher(uri).matches()) {
			// is absolute uri, has protocol info
			return StringUtils.substringBefore(uri, "://") + "://" + hostAndPort;
		} else {
			// is a path, not a uri, use default http protocol
			LOG.info(
					"Cannot resolve protocol info(maybe a origin-form request), use 'http' by default when extracting site info.");
			return "http://" + hostAndPort;
		}
	}

	/**
	 * Set the site information for the specified request, using the specified site
	 * information.
	 * 
	 * @param httpRequest
	 *            the request being set
	 * @param site
	 *            the specified site information, is of form: 'three-factor-form',
	 *            'https://aaa.com:443' for example, it contains tree factors of
	 *            'protocol', 'host' and 'port'.
	 */
	public static void setSite(HttpRequest httpRequest, String site) {
		if (!isValidSite(site)) {
			LOG.error("Invalid site value: '" + String.valueOf(site) + "', set site failed.");
			return;
		}
		// 1.replace uri in initial line
		String uri = httpRequest.getUri();
		// 1.1 only replace when absolute form request
		if (HTTP_PREFIX.matcher(uri).matches()) {
			StringBuilder newUri = new StringBuilder();
			newUri.append(site).append("/");
			String noProto = StringUtils.substringAfter(uri, "://");
			if (noProto.contains("/")) {
				String pathInfo = StringUtils.substringAfter(noProto, "/");
				if (StringUtils.isNotBlank(pathInfo)) {
					// has path info
					newUri.append(pathInfo);
				}
			}
			httpRequest.setUri(newUri.toString());
		}
		// 2.replace host header
		String hostAndPort = StringUtils.substringAfter(site, "://");
		if (!StringUtils.contains(hostAndPort, ":")) {
			// try to figure out port when no port info contains
			String scheme = StringUtils.substringBefore(site, "://");
			Integer p = WellKnownPortsMapping.getPortByName(scheme);
			if (p != null) {
				hostAndPort = hostAndPort + ":" + p;
			}
		}
		httpRequest.headers().set(HttpHeaders.Names.HOST, hostAndPort);
	}

	/**
	 * Extract & sort methods from filters, prepare metas, ready to executing.
	 * 
	 * @param fs
	 *            all filters activated during this request
	 * @return arranged <req, rsp> filtering methods
	 */
	public static Pair<List<SortableFilterMethod>, List<SortableFilterMethod>> buildSortedFilterMethods(
			Set<T1Filter> fs) {
		if (fs == null || fs.isEmpty()) {
			return null;
		}
		List<SortableFilterMethod> reqMs = new ArrayList<>(fs.size());
		List<SortableFilterMethod> rspMs = new ArrayList<>(fs.size());
		for (T1Filter f : fs) {
			Method reqMethod = resolveFilterMethod(f, "onRequesting", HttpObject.class, Map.class);
			if (reqMethod == null) {
				LOG.error("Cannot find 'onRequesting' method in filter: '" + f.getClass().getName()
						+ "', this filter will be ignored.");
				continue;
			}
			Method rspMethod = resolveFilterMethod(f, "onResponding", HttpObject.class, Map.class);
			if (rspMethod == null) {
				LOG.error("Cannot find 'onResponding' method in filter: '" + f.getClass().getName()
						+ "', this filter will be ignored.");
				continue;
			}
			int reqPri = resolveMethodPriority(reqMethod);
			reqMs.add(new SortableFilterMethod(f, reqMethod, reqPri));

			int rspPri = resolveMethodPriority(rspMethod);
			rspMs.add(new SortableFilterMethod(f, rspMethod, rspPri));
		}
		// sort by priority asc
		Collections.sort(reqMs);
		Collections.sort(rspMs);
		return ImmutablePair.of(reqMs, rspMs);
	}

	/**
	 * get priority from the ExeOrder annotation, or '0' when no annotation found.
	 */
	private static int resolveMethodPriority(Method m) {
		// 1.extract priority in ExeOrder annotation
		ExeOrder o = m.getDeclaredAnnotation(ExeOrder.class);
		if (o != null) {
			return o.value();
		} else {
			// 2.or have a priority of 0 when no annotation found
			return 0;
		}
	}

	/**
	 * Find method instance with the specified name
	 * 
	 * @param o
	 *            the filter object from which the method will be found
	 * @param methodName
	 *            the name of the method
	 * @return the found method
	 */
	private static Method resolveFilterMethod(T1Filter o, String methodName, Class<?>... paramTypes) {
		if (o == null || StringUtils.isBlank(methodName)) {
			return null;
		}
		Class<? extends T1Filter> c = o.getClass();
		Method m = null;
		Pair<Class<? extends T1Filter>, String> k = ImmutablePair.of(c, methodName);
		try {
			TimedCacheItem<Method> i = filterMethodsCache.get(k, new Callable<TimedCacheItem<Method>>() {

				@Override
				public TimedCacheItem<Method> call() throws Exception {
					if (LOG.isDebugEnabled()) {
						LOG.debug("T1Filter method cache miss, reflecting filter method for filter: '" + c.getName()
								+ "', method: '" + methodName + "'.");
					}
					Method method = c.getMethod(methodName, paramTypes);
					if (method == null) {
						if (LOG.isDebugEnabled()) {
							LOG.debug("Could not find method with name: '" + methodName + "' in filter class: '"
									+ c.getName() + "', returning null and turn on 1 min null-value protection.");
						}
						return newOneMinDummy();
					} else {
						// performance boost
						method.setAccessible(true);
						return new TimedCacheItem<Method>(method);
					}
				}
			});
			m = i.getItem();
			// check for expire in biz def
			if (m == DUMMY) {
				m = null;
				if (new Date().after(i.getExpireOn())) {
					filterMethodsCache.invalidate(k);
				}
			}
		} catch (ExecutionException e) {
			LOG.error("Resolving filter method throws exception, Filter class: '" + c.toString() + "', method name: '"
					+ methodName + "', returning null and turn on error protection for 1 minite.", e);
			filterMethodsCache.put(k, newOneMinDummy());
			m = null;
		}
		return m;
	}

	// creates a new 1 min expire dummy item
	private static TimedCacheItem<Method> newOneMinDummy() {
		Calendar c = Calendar.getInstance();
		c.add(Calendar.MINUTE, 1);
		return new TimedCacheItem<Method>(DUMMY, c.getTime());
	}

	// creates a new 1 min expire dummy item
	private static TimedCacheItem<Field> newOneMinDummyF() {
		Calendar c = Calendar.getInstance();
		c.add(Calendar.MINUTE, 1);
		return new TimedCacheItem<Field>(DUMMY_F, c.getTime());
	}

	/**
	 * tell if this request need filtering
	 * 
	 * @param request
	 *            the current incoming request
	 * @param serverConf
	 *            server config
	 * @param perReqVals
	 *            values shared during a request process
	 * @return true/false
	 */
	public static boolean needFiltering(HttpRequest request, T1Conf serverConf, ValueHolder perReqVals) {
		Boolean nf = perReqVals.getNeedFiltering();
		if (nf == null) {
			// 1.when reverse mode, site mapping is a must.
			if (serverConf.isReverseMode()) {
				if (serverConf.getSiteMappingManager() == null) {
					perReqVals.setNeedFiltering(Boolean.FALSE);
					return false;
				}
				String fromSite = resolveFromSite(request, perReqVals);
				String toSite = resolveToSite(serverConf.getSiteMappingManager(), fromSite, perReqVals);
				if (StringUtils.isBlank(toSite)) {
					perReqVals.setNeedFiltering(Boolean.FALSE);
					return false;
				}
			}
			// 2.if no filters will be activated, false
			if (serverConf.getFiltersFactory() == null) {
				perReqVals.setNeedFiltering(Boolean.FALSE);
				return false;
			}
			Pair<List<SortableFilterMethod>, List<SortableFilterMethod>> filterMethods = perReqVals.getFilterMethods();
			if (filterMethods == null) {
				RequestInfo roReq = new RequestInfo(request);
				Collection<T1Filter> filters = serverConf.getFiltersFactory().buildFilters(roReq);
				if (filters == null || filters.isEmpty()) {
					filterMethods = ImmutablePair.of(null, null);
					perReqVals.setFilterMethods(filterMethods);
					perReqVals.setNeedFiltering(Boolean.FALSE);
					return false;
				}
				// dedup and keep insertion order...
				Set<T1Filter> fs = new LinkedHashSet<T1Filter>(filters);
				// remove inactive filters
				fs.removeIf(f -> !f.active(roReq));
				// Pair<reqMethods, rspMethods>
				filterMethods = ProxyUtils.buildSortedFilterMethods(fs);
				if (filterMethods == null) {
					filterMethods = ImmutablePair.of(null, null);
					perReqVals.setFilterMethods(filterMethods);
					perReqVals.setNeedFiltering(Boolean.FALSE);
					return false;
				}
				perReqVals.setFilterMethods(filterMethods);
			}
			List<SortableFilterMethod> reqMethods = filterMethods.getLeft();
			List<SortableFilterMethod> rspMethods = filterMethods.getRight();
			if (reqMethods == null || reqMethods.isEmpty() || rspMethods == null || rspMethods.isEmpty()) {
				perReqVals.setNeedFiltering(Boolean.FALSE);
				return false;
			}
			if (reqMethods.size() != rspMethods.size()) {
				throw new RuntimeException("It's impossible to get here, just for coding validity.");
			}
			perReqVals.setNeedFiltering(Boolean.TRUE);
			return true;
		}
		return nf;
	}

	public static String resolveToSite(SiteMappingManager siteMappingManager, String fromSite, ValueHolder perVals) {
		String toSite = perVals.getToSite();
		if (toSite == null) {
			toSite = siteMappingManager.siteMapping(fromSite);
			if (toSite == null) {
				toSite = "";// prevents 'null-value-attacks'
			}
			perVals.setToSite(toSite);
		}
		return toSite;
	}

	public static String resolveFromSite(HttpRequest originalRequest, ValueHolder perVals) {
		String fromSite = perVals.getFromSite();
		if (fromSite == null) {
			fromSite = ProxyUtils.extractSite(originalRequest);
			if (fromSite == null) {
				fromSite = "";// prevents 'null-value-attacks'
			}
			perVals.setFromSite(fromSite);
		}
		return fromSite;
	}

	/**
	 * Tell if the specified request is a https request
	 */
	public static boolean isHttpsRequest(HttpRequest request, ValueHolder perReqVals) {
		Boolean https = perReqVals.getHttpsRequest();
		if (https == null) {
			https = Boolean.FALSE;
			// if the uri starts with https, true
			if (request.getUri().startsWith("https://")) {
				https = Boolean.TRUE;
			} else {
				// may be an 'origin' form request
				// try figure from the mapped 'toSite'
				String toSite = perReqVals.getToSite();
				if (StringUtils.isNotBlank(toSite) && toSite.startsWith("https://")) {
					https = Boolean.TRUE;
				} else {
					// else if the host header indicates a 443 port connection, true
					String hostAndPort = parseHostAndPort(request);
					if (hostAndPort != null && hostAndPort.contains(":")
							&& "443".equals(StringUtils.substringAfter(hostAndPort, ":"))) {
						https = Boolean.TRUE;
					}
				}
			}
			perReqVals.setHttpsRequest(https);
		}
		return https;
	}

	/**
	 * Tell if the specified http object can have a content
	 */
	public static <T extends HttpObject> boolean hasContent(T obj) {
		return obj instanceof HttpContent && LastHttpContent.EMPTY_LAST_CONTENT != obj;
	}

	/**
	 * try to set the byteBuf of the specified httpContent
	 * 
	 * @param ctt
	 *            the specified httpContent
	 * @param buf
	 */
	public static void setContentBuf(HttpContent ctt, ByteBuf buf) {
		try {
			// 1.try to get the 'content' field of the httpContent
			Field cttField = tryGetContentField(ctt, "content");
			if (cttField == null) {
				LOG.error("Cannot find 'content' field in HttpContet object: " + String.valueOf(ctt));
				return;
			}
			// 2.set the 'content' field using the specified buf
			cttField.set(ctt, buf);
		} catch (Throwable t) {
			LOG.error("Set content byteBuf failed.", t);
		}
	}

	/**
	 * try to get a field in the specified object, if found, ensure it is accessible
	 * and return
	 * 
	 * @param ctt
	 *            the object in which the 'content' field to be found
	 * @param fieldName
	 *            the name of the field to find
	 * 
	 * @return the found 'content' field, and ensured accessible
	 */
	private static Field tryGetContentField(HttpContent ctt, String fieldName) {
		if (ctt == null) {
			return null;
		}
		Class<? extends HttpContent> c = ctt.getClass();
		Field f = null;
		Pair<Class<? extends HttpContent>, String> k = ImmutablePair.of(c, fieldName);
		try {
			TimedCacheItem<Field> i = contentFieldsCache.get(k, new Callable<TimedCacheItem<Field>>() {

				@Override
				public TimedCacheItem<Field> call() throws Exception {
					if (LOG.isDebugEnabled()) {
						LOG.debug("HttpContent field cache miss, reflecting field for httpContent: '" + c.getName()
								+ "', field: '" + fieldName + "'.");
					}
					Field field = getDeclaredFieldRecursively(c, fieldName);
					if (field == null) {
						if (LOG.isDebugEnabled()) {
							LOG.debug("Could not find field with name: '" + fieldName + "' in httpContent class: '"
									+ c.getName() + "', returning null and turn on 1 min null-value protection.");
						}
						return newOneMinDummyF();
					} else {
						// make the field accessible
						field.setAccessible(true);
						// make it no final
						if ((field.getModifiers() & Modifier.FINAL) != 0) {
							Field mods = Field.class.getDeclaredField("modifiers");
							mods.setAccessible(true);
							mods.setInt(field, field.getModifiers() & ~Modifier.FINAL);
						}
						return new TimedCacheItem<Field>(field);
					}
				}
			});
			f = i.getItem();
			// check for expire in biz def
			if (f == DUMMY_F) {
				f = null;
				if (new Date().after(i.getExpireOn())) {
					contentFieldsCache.invalidate(k);
				}
			}
		} catch (ExecutionException e) {
			LOG.error("Resolving content field throws exception, HttpContent class: '" + c.toString()
					+ "', field name: '" + fieldName + "', returning null and turn on error protection for 1 minite.",
					e);
			contentFieldsCache.put(k, newOneMinDummyF());
			f = null;
		}
		return f;
	}

	private static Field getDeclaredFieldRecursively(Class<?> c, String fieldName) {
		Field field = null;
		while (field == null && !Object.class.equals(c)) {
			try {
				field = c.getDeclaredField(fieldName);
			} catch (NoSuchFieldException e) {
				c = c.getSuperclass();
				field = null;
			}
		}
		return field;
	}

	/**
	 * <pre>
	 * 1.resovle the byteBuf 
	 * 2.if swapped and the current effecting buf is not the original one, restore the original buf into the origianl httpObject, and create a new httpObject carrying the current effecting buf then write
	 * 3.release all the created ones
	 * </pre>
	 * 
	 * @param httpObject
	 *            the object to write
	 * @param writing
	 *            the writing logic
	 */
	public static void swappableBufWriteTrick(HttpObject httpObject, Function<HttpObject, Void> writing) {
		// resolve the byteBuf
		SwappedByteBuf sbb = null;
		if (hasContent(httpObject)) {
			ByteBuf buf = ((HttpContent) httpObject).content();
			if (buf instanceof SwappedByteBuf) {
				sbb = (SwappedByteBuf) buf;
			}
		}
		if (sbb != null && sbb.getCur() != sbb.getOrig()) {
			// restore the original buf
			setContentBuf((HttpContent) httpObject, sbb.getOrig());
			// create a new httpObject with the current effecting buf to write
			httpObject = createSameHttpContentWithNewBuf((HttpContent) httpObject, sbb.getCur());
		}
		try {
			writing.apply(httpObject);
		} finally {
			if (sbb != null) {
				// release all the created ones
				for (ByteBuf c : sbb.getCreated()) {
					ReferenceCountUtil.release(c);
				}
			}
		}
	}

	/*
	 * create a new full httpContent with the same headers to the spciefied
	 * httpContent, and with the provided byteBuf
	 */
	private static HttpObject createSameHttpContentWithNewBuf(HttpContent ctt, ByteBuf buf) {
		HttpObject ret = null;
		if (ctt instanceof FullHttpRequest) {
			FullHttpRequest req = (FullHttpRequest) ctt;
			FullHttpRequest r = new DefaultFullHttpRequest(req.getProtocolVersion(), req.getMethod(), req.getUri(),
					buf);
			r.headers().add(req.headers());
			r.trailingHeaders().add(req.trailingHeaders());
			ret = r;
		} else if (ctt instanceof FullHttpResponse) {
			FullHttpResponse resp = (FullHttpResponse) ctt;
			FullHttpResponse r = new DefaultFullHttpResponse(resp.getProtocolVersion(), resp.getStatus(), buf);
			r.headers().add(resp.headers());
			r.trailingHeaders().add(resp.trailingHeaders());
			ret = r;
		} else {
			ret = new DefaultHttpContent(buf);
		}
		return ret;
	}

	/**
	 * try to release all the created bufs if the specified httpObject is a
	 * httpContent with SwappedByteBuf
	 * 
	 * @param httpObject
	 *            the specified httpObject
	 */
	public static void releaseAnyCreatedBuf(HttpObject httpObject) {
		if (hasContent(httpObject)) {
			// resolve the byteBuf
			ByteBuf buf = ((HttpContent) httpObject).content();
			if (buf instanceof SwappedByteBuf) {
				SwappedByteBuf sbb = (SwappedByteBuf) buf;
				// restore the original buf
				setContentBuf((HttpContent) httpObject, sbb.getOrig());
				// release all the created bufs
				for (ByteBuf c : sbb.getCreated()) {
					if (c.refCnt() > 0) {
						ReferenceCountUtil.release(c, c.refCnt());
					}
				}
			}
		}
	}

}
