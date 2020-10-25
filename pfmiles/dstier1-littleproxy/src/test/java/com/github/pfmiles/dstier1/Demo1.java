package com.github.pfmiles.dstier1;

import java.nio.charset.Charset;
import java.util.ArrayList;
import java.util.Collection;
import java.util.List;
import java.util.Map;

import com.github.pfmiles.dstier1.impl.T1Utils;
import io.netty.handler.codec.http.HttpMethod;
import io.netty.handler.codec.http.HttpObject;
import io.netty.handler.codec.http.HttpResponse;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * Simple http to http site mapping and reponse content interfering
 * 
 * @author pf-miles
 *
 */
public class Demo1 {

	private static final Logger logger = LoggerFactory.getLogger(Demo1.class);

	public static void main(String[] args) throws Exception {
		T1Conf conf = resolveConf();
		T1Server s = new T1Server(conf);
		s.start();
	}

	private static T1Conf resolveConf() {
		T1Conf ret = new T1Conf();
		ret.setPort(8080);
		ret.setNic(null);
		ret.setLocalOnly(false);
		ret.setIdleConnectionTimeout(3600);
		ret.setSiteMappingManager(new SiteMappingManager() {

			@Override
			protected String doSiteMapping(String origSite) {
				if (T1Utils.siteEquals("http://abc.com:8080", origSite)) {
					return "http://bumonitor.stable.alipay.net:8080";
				} else {
					return null;
				}
			}
		});
		ret.setFiltersFactory(new FiltersFactory() {

			@Override
			public Collection<T1Filter> buildFilters(RequestInfo reqInfo) {
				List<T1Filter> fs = new ArrayList<>();
				fs.add(new T1Filter() {

					private Charset respEncoding = null;

					@Override
					public boolean active(RequestInfo req) {
						return HttpMethod.GET.equals(req.getMethod());
					}

					@Override
					@ExeOrder(1)
					public HttpResponse onRequesting(HttpObject httpObj, Map<String, Object> ctx) {
						return null;
					}

					@Override
					public HttpObject onResponding(HttpObject httpObj, Map<String, Object> ctx) {
						if (httpObj instanceof HttpResponse) {
							this.respEncoding = T1Utils.getContentEncoding((HttpResponse) httpObj);
						}
						byte[] data = T1Utils.getContentBytes(httpObj);
						if (data.length > 0) {
							String dataStr = new String(data, this.respEncoding);
							dataStr = doModify(dataStr);
							data = dataStr.getBytes(this.respEncoding);
							T1Utils.setContentBytes(httpObj, data);
						}
						return httpObj;
					}

					private String doModify(String dataStr) {
						if (dataStr.indexOf("<head>") != -1) {
							logger.info(
									"To find '<head>' tag and insert '<script>window.onload = function(){window.alert(\"Hello world!\");};</script>' after it.");
							return dataStr.replace("<head>",
									"<head><script>window.onload = function(){window.alert(\"Hello world!\");};</script>");
						} else {
							return dataStr;
						}
					}
				});
				return fs;
			}
		});
		return ret;
	}
}
