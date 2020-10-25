/* ============================================================
 * retina-replace.min.js v1.0
 * http://github.com/leonsmith/retina-replace-js
 * ============================================================
 * Author: Leon Smith
 * Twitter: @nullUK
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */
(function(a){var e=function(d,c){this.options=c;var b=a(d),g=b.is("img"),f=g?b.attr("src"):b.backgroundImageUrl(),f=this.options.generateUrl(b,f);a("<img/>").attr("src",f).load(function(){g?b.attr("src",a(this).attr("src")):(b.backgroundImageUrl(a(this).attr("src")),b.backgroundSize(a(this)[0].width,a(this)[0].height));b.attr("data-retina","complete")})};e.prototype={constructor:e};a.fn.retinaReplace=function(d){var c;c=void 0===window.devicePixelRatio?1:window.devicePixelRatio;return 1>=c?this:this.each(function(){var b=
a(this),c=b.data("retinaReplace"),f=a.extend({},a.fn.retinaReplace.defaults,b.data(),"object"==typeof d&&d);c||b.data("retinaReplace",c=new e(this,f));if("string"==typeof d)c[d]()})};a.fn.retinaReplace.defaults={suffix:"_2x",generateUrl:function(a,c){var b=c.lastIndexOf("."),e=c.substr(b+1);return c.substr(0,b)+this.suffix+"."+e}};a.fn.retinaReplace.Constructor=e;a.fn.backgroundImageUrl=function(d){return d?this.each(function(){a(this).css("background-image",'url("'+d+'")')}):a(this).css("background-image").replace(/url\(|\)|"|'/g,
"")};a.fn.backgroundSize=function(d,c){var b=Math.floor(d/2)+"px "+Math.floor(c/2)+"px";a(this).css("background-size",b);a(this).css("-webkit-background-size",b)};a(function(){a("[data-retina='true']").retinaReplace()})})(window.jQuery);
