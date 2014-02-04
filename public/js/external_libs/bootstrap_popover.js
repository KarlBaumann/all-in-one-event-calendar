/* ===========================================================
	 * bootstrap-popover.js v2.0.4
	 * http://twitter.github.com/bootstrap/javascript.html#popovers
	 * ===========================================================
	 * Copyright 2012 Twitter, Inc.
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
	 * =========================================================== */

timely.define(["jquery_timely","external_libs/bootstrap_tooltip"],function(e,t){if(!e.fn.popover){var n=function(e,t){this.init("popover",e,t)};n.prototype=e.extend({},e.fn.tooltip.Constructor.prototype,{constructor:n,setContent:function(){var e=this.tip(),t=this.getTitle(),n=this.getContent();e.find(".popover-title")[this.isHTML(t)?"html":"text"](t),e.find(".popover-content > *")[this.isHTML(n)?"html":"text"](n),e.removeClass("fade top bottom left right in")},hasContent:function(){return this.getTitle()||this.getContent()},getContent:function(){var e,t=this.$element,n=this.options;return e=t.attr("data-content")||(typeof n.content=="function"?n.content.call(t[0]):n.content),e},tip:function(){return this.$tip||(this.$tip=e(this.options.template)),this.$tip}}),e.fn.popover=function(t){return this.each(function(){var r=e(this),i=r.data("popover"),s=typeof t=="object"&&t;i||r.data("popover",i=new n(this,s)),typeof t=="string"&&i[t]()})},e.fn.popover.Constructor=n,e.fn.popover.defaults=e.extend({},e.fn.tooltip.defaults,{placement:"right",content:"",template:'<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'})}if(!e.fn.constrained_popover){var r=function(e,t){this.init("constrained_popover",e,t)};r.prototype=e.extend({},e.fn.popover.Constructor.prototype,{constructor:r,show:function(){var e,t,n,r,i,s,o,u,a={};if(this.hasContent()&&this.enabled){e=this.tip(),this.setContent(),this.options.animation&&e.addClass("fade"),o=typeof this.options.placement=="function"?this.options.placement.call(this,e[0],this.$element[0]):this.options.placement,t=/in/.test(o),e.remove().css({top:0,left:0,display:"block"}).appendTo(t?this.$element:document.body),n=this.getPosition(t),i=e[0].offsetWidth,s=e[0].offsetHeight;switch(t?o.split(" ")[1]:o){case"left":r=this.defineBounds(n),typeof r.top=="undefined"?a.top=n.top+n.height/2-s/2:a.top=r.top-s/2,typeof r.left=="undefined"?a.left=n.left-i:a.left=r.left-i,u={top:a.top,left:a.left};break;case"right":r=this.defineBounds(n),typeof r.top=="undefined"?a.top=n.top+n.height/2-s/2:a.top=r.top-s/2,typeof r.left=="undefined"?a.left=n.left+n.width:a.left=r.left+n.width,u={top:a.top,left:a.left}}e.css(u).addClass(o).addClass("in")}},defineBounds:function(t){var n,r,i,s,o,u,a={},f=e(this.options.container);return f.length?(r=f.offset(),i=r.top,s=r.left,o=i+f.height(),u=s+f.width(),t.top+t.height/2<i&&(a.top=i),t.top+t.height/2>o&&(a.top=o),t.left-t.width/2<s&&(a.left=s),t.left-t.width/2>u&&(a.left=u),a):!1}}),e.fn.constrained_popover=function(t){return this.each(function(){var n=e(this),i=n.data("constrained_popover"),s=typeof t=="object"&&t;i||n.data("constrained_popover",i=new r(this,s)),typeof t=="string"&&i[t]()})},e.fn.constrained_popover.Constructor=r,e.fn.constrained_popover.defaults=e.extend({},e.fn.popover.defaults,{container:"",content:this.options})}});