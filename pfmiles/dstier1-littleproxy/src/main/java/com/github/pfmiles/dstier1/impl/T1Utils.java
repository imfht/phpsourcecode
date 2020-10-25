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

import java.nio.charset.Charset;
import java.nio.charset.UnsupportedCharsetException;

import io.netty.buffer.ByteBuf;
import io.netty.handler.codec.http.FullHttpMessage;
import io.netty.handler.codec.http.HttpContent;
import io.netty.handler.codec.http.HttpHeaders;
import io.netty.handler.codec.http.HttpMessage;
import io.netty.handler.codec.http.HttpObject;
import io.netty.util.CharsetUtil;
import org.apache.commons.lang3.StringUtils;
import org.littleshoot.proxy.impl.ProxyUtils;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * @author pf-miles
 *
 */
public abstract class T1Utils {

	private static final Logger logger = LoggerFactory.getLogger(T1Utils.class);

	private static final byte[] EMPTY_BYTES = new byte[0];

	/**
	 * Convenience method to tell if the specified two sites are the same
	 * 
	 * @param site1
	 * @param site2
	 * @return true if the two sites are the same, 'http://a.com' and
	 *         'http://a.com:80' for example, false otherwise.
	 */
	public static boolean siteEquals(String site1, String site2) {
		if (site1 == null || !ProxyUtils.SITE_PATTERN.matcher(site1).matches()) {
			throw new IllegalArgumentException("Site: '" + site1 + "' is invalid.");
		}
		if (site2 == null || !ProxyUtils.SITE_PATTERN.matcher(site2).matches()) {
			throw new IllegalArgumentException("Site: '" + site2 + "' is invalid.");
		}
		site1 = site1.toLowerCase();
		site2 = site2.toLowerCase();

		String proto1 = StringUtils.substringBefore(site1, "://");
		String proto2 = StringUtils.substringBefore(site2, "://");
		if (!proto1.equals(proto2)) {
			return false;
		}

		String domain1 = null;
		String noPro1 = StringUtils.substringAfter(site1, "://");
		if (noPro1.contains(":")) {
			domain1 = StringUtils.substringBefore(noPro1, ":");
		} else {
			domain1 = noPro1;
		}
		String domain2 = null;
		String noPro2 = StringUtils.substringAfter(site2, "://");
		if (noPro2.contains(":")) {
			domain2 = StringUtils.substringBefore(noPro2, ":");
		} else {
			domain2 = noPro2;
		}
		if (!domain1.equals(domain2)) {
			return false;
		}

		int port1 = -1;
		if (noPro1.contains(":")) {
			port1 = Integer.parseInt(StringUtils.substringAfter(noPro1, ":"));
		} else {
			// well-known default ports for http/https
			Integer p = WellKnownPortsMapping.getPortByName(proto1);
			if (p != null) {
				port1 = p;
			}
		}
		int port2 = -1;
		if (noPro2.contains(":")) {
			port2 = Integer.parseInt(StringUtils.substringAfter(noPro2, ":"));
		} else {
			// well-known default ports for http/https
			Integer p = WellKnownPortsMapping.getPortByName(proto2);
			if (p != null) {
				port2 = p;
			}
		}
		if (port1 != port2) {
			return false;
		}

		return true;
	}

	/**
	 * Tell if the specified request/response is transferring in chunked encoding
	 * 
	 * @param msg
	 *            could either be a HttpRequest or HttpResponse
	 */
	public static boolean isChunkedTransfer(HttpMessage msg) {
		return HttpHeaders.isTransferEncodingChunked(msg);
	}

	/**
	 * Get the content encoding from the specified request/response, or null when
	 * not specified.
	 * 
	 * @param msg
	 *            http request or response
	 * @return encoding of the message body, or null when no encoding info found
	 */
	public static Charset getContentEncoding(HttpMessage msg) {
		return getContentEncoding(msg, CharsetUtil.UTF_8);
	}

	private static Charset getContentEncoding(HttpMessage message, Charset defaultCharset) {
		CharSequence contentTypeValue = message.headers().get(HttpHeaders.Names.CONTENT_TYPE);
		if (contentTypeValue != null) {
			return getContentEncodingFromCttTypeValue(contentTypeValue, defaultCharset);
		} else {
			return defaultCharset;
		}
	}

	private static Charset getContentEncodingFromCttTypeValue(CharSequence contentTypeValue, Charset defaultCharset) {
		if (contentTypeValue != null) {
			CharSequence charsetCharSequence = getCharsetAsSequenceFromCttTypeValue(contentTypeValue);
			if (charsetCharSequence != null) {
				try {
					return Charset.forName(charsetCharSequence.toString());
				} catch (UnsupportedCharsetException ignored) {
					return defaultCharset;
				}
			} else {
				return defaultCharset;
			}
		} else {
			return defaultCharset;
		}
	}

	private static CharSequence getCharsetAsSequenceFromCttTypeValue(CharSequence contentTypeValue) {
		if (contentTypeValue == null) {
			return null;
		}
		int indexOfCharset = StringUtils.indexOfIgnoreCase(contentTypeValue, "charset=");
		if (indexOfCharset == -1) {
			return null;
		}
		int indexOfEncoding = indexOfCharset + "charset=".length();
		if (indexOfEncoding < contentTypeValue.length()) {
			CharSequence charsetCandidate = contentTypeValue.subSequence(indexOfEncoding, contentTypeValue.length());
			int indexOfSemicolon = StringUtils.indexOf(charsetCandidate, ";");
			if (indexOfSemicolon == -1) {
				return charsetCandidate;
			}
			return charsetCandidate.subSequence(0, indexOfSemicolon);
		}
		return null;
	}

	/**
	 * Get the body content(if any) from the specified http object(a
	 * request/response or httpContent).
	 * 
	 * a request/response or a httpContent
	 * 
	 * @return the http content bytes array if it has, or a byte array of length 0
	 *         when no content found
	 */
	public static byte[] getContentBytes(HttpObject obj) {
		/*
		 * note that FullHttpMessage also implements HttpContent, so we need to test
		 * HttpContent only
		 */
		if (ProxyUtils.hasContent(obj)) {
			return readBytesUnchanged(((HttpContent) obj).content());
		} else {
			return EMPTY_BYTES;
		}
	}

	// read the bytes but also reset the cursors
	private static byte[] readBytesUnchanged(ByteBuf bb) {
		int readableBytes = bb.readableBytes();
		if (readableBytes > 0) {
			bb.markReaderIndex();
			byte[] ret = new byte[readableBytes];
			bb.readBytes(ret);
			bb.resetReaderIndex();
			return ret;
		} else {
			return EMPTY_BYTES;
		}
	}

	/**
	 * Set the bytes into the specified httpObject. May create new byteBufs and set
	 * data into it when the original httpObject's byte buffer has not enough
	 * capacity to write.
	 * 
	 * @param httpObj
	 *            the original request/response or chunk
	 * @param data
	 *            the data to be set
	 * 
	 */
	public static void setContentBytes(HttpObject httpObj, byte[] data) {
		if (!(ProxyUtils.hasContent(httpObj))) {
			throw new IllegalArgumentException("Cannot set bytes into a no content object: " + String.valueOf(httpObj));
		}
		if (data == null) {
			data = EMPTY_BYTES;
		}
		ByteBuf bb = ((HttpContent) httpObj).content();// note that FullHttpMessage is also a HttpContent instance
		if (isInplacelyWritable(bb, data.length)) {
			// 1.fist try to modify bytes in place
			setBytes(bb, data);
		} else {
			// 2.if no capacity to write, swap the whole byteBuf in httpContent
			HttpContent httpCtt = (HttpContent) httpObj;
			ByteBuf orig = httpCtt.content();
			SwappedByteBuf sbuf = new SwappedByteBuf(orig);
			ProxyUtils.setContentBuf(httpCtt, sbuf);
			setBytes(sbuf, data);
		}
		if (httpObj instanceof FullHttpMessage) {
			// also set the content length respectively
			HttpHeaders.setContentLength((HttpMessage) httpObj, data.length);
		}
	}

	// tell if there's enough space to write
	private static boolean isInplacelyWritable(ByteBuf bb, int dataSizeToWrite) {
		int readIdx = bb.readerIndex();
		int writeIdx = bb.writerIndex();
		// discard the current unread data
		bb.writerIndex(readIdx);
		// try to enlarge the buf if not enough space
		int rst = bb.ensureWritable(dataSizeToWrite, false);
		// recover the writer index
		bb.writerIndex(writeIdx);
		return (rst == 0 || rst == 2);
	}

	// discard old data and set new ones
	private static void setBytes(ByteBuf bb, byte[] data) {
		if (data == null) {
			data = EMPTY_BYTES;
		}
		int readIdx = bb.readerIndex();
		int writeIdx = bb.writerIndex();
		// discard the current unread data
		bb.writerIndex(readIdx);
		// try to enlarge the buf if not enough space
		int rst = bb.ensureWritable(data.length, false);
		if (rst == 0 || rst == 2) {
			bb.writeBytes(data);
		} else {
			// restore the unread data
			bb.writerIndex(writeIdx);
			if (bb instanceof SwappedByteBuf) {
				// if is of type swapped buf, swap the whole buf
				((SwappedByteBuf) bb).swapWriteBytes(data);
			} else {
				/*
				 * not enough capacity, write operation failed
				 */
				throw new IllegalArgumentException(
						"Not enough capacity to write or buf swapping failed, 'setBytes' failed, the byteBuf will be left unchanged.");
			}
		}
	}

	/**
	 * get the string value in header using the specified header name
	 */
	public static String getHeaderValue(HttpMessage msg, String name) {
		return HttpHeaders.getHeader(msg, name);
	}
}
