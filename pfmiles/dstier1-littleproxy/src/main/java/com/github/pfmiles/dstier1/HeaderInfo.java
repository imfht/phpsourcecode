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
import java.util.TreeMap;
import java.util.function.BiFunction;
import java.util.function.Function;

import io.netty.handler.codec.http.HttpHeaders;
import org.apache.commons.lang3.StringUtils;

/**
 * A read-only headers info. All header keys are considered case-insensitive. So
 * you can use a 'host' key to get a 'Host' header's value.
 * 
 * @author pf-miles
 *
 */
public class HeaderInfo extends TreeMap<String, String> {

	private static final long serialVersionUID = -3551320498196620960L;

	HeaderInfo(HttpHeaders httpHeaders) {
		super(String.CASE_INSENSITIVE_ORDER);
		for (Map.Entry<String, String> e : httpHeaders) {
			if (StringUtils.isNotBlank(e.getKey()) && StringUtils.isNotBlank(e.getValue())) {
				super.putIfAbsent(e.getKey(), e.getValue());
			}
		}
	}

	@Override
	public String putIfAbsent(String key, String value) {
		throw new UnsupportedOperationException("read only");
	}

	@Override
	public boolean remove(Object key, Object value) {
		throw new UnsupportedOperationException("read only");
	}

	@Override
	public String remove(Object key) {
		throw new UnsupportedOperationException("read only");
	}

	@Override
	public String computeIfAbsent(String key, Function<? super String, ? extends String> mappingFunction) {
		throw new UnsupportedOperationException("read only");
	}

	@Override
	public String computeIfPresent(String key,
			BiFunction<? super String, ? super String, ? extends String> remappingFunction) {
		throw new UnsupportedOperationException("read only");
	}

	@Override
	public String compute(String key, BiFunction<? super String, ? super String, ? extends String> remappingFunction) {
		throw new UnsupportedOperationException("read only");
	}

	@Override
	public String merge(String key, String value,
			BiFunction<? super String, ? super String, ? extends String> remappingFunction) {
		throw new UnsupportedOperationException("read only");
	}

	@Override
	public void putAll(Map<? extends String, ? extends String> map) {
		throw new UnsupportedOperationException("read only");
	}

	@Override
	public void clear() {
		throw new UnsupportedOperationException("read only");
	}

	@Override
	public java.util.Map.Entry<String, String> pollFirstEntry() {
		throw new UnsupportedOperationException("read only");
	}

	@Override
	public java.util.Map.Entry<String, String> pollLastEntry() {
		throw new UnsupportedOperationException("read only");
	}

	@Override
	public boolean replace(String key, String oldValue, String newValue) {
		throw new UnsupportedOperationException("read only");
	}

	@Override
	public String replace(String key, String value) {
		throw new UnsupportedOperationException("read only");
	}

	@Override
	public void replaceAll(BiFunction<? super String, ? super String, ? extends String> function) {
		throw new UnsupportedOperationException("read only");
	}

}
