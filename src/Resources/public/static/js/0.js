(this["webpackJsonpPluginpayone-payment"]=this["webpackJsonpPluginpayone-payment"]||[]).push([[0],{"3veU":function(e,t,n){"use strict";n.r(t);n("SqvT"),t.default={template:'{% block payone_payment_plugin_icon %}\n    <img class="payone-payment-plugin-icon" :src="\'payonepayment/plugin.png\' | asset">\n{% endblock %}\n'}},SqvT:function(e,t,n){var r=n("d6Kt");r.__esModule&&(r=r.default),"string"==typeof r&&(r=[[e.i,r,""]]),r.locals&&(e.exports=r.locals);(0,n("ydqr").default)("6b6c4413",r,!0,{})},d6Kt:function(e,t,n){},ydqr:function(e,t,n){"use strict";function r(e,t){for(var n=[],r={},s=0;s<t.length;s++){var a=t[s],i=a[0],o={id:e+":"+s,css:a[1],media:a[2],sourceMap:a[3]};r[i]?r[i].parts.push(o):n.push(r[i]={id:i,parts:[o]})}return n}n.r(t),n.d(t,"default",(function(){return h}));var s="undefined"!=typeof document;if("undefined"!=typeof DEBUG&&DEBUG&&!s)throw new Error("vue-style-loader cannot be used in a non-browser environment. Use { target: 'node' } in your Webpack config to indicate a server-rendering environment.");var a={},i=s&&(document.head||document.getElementsByTagName("head")[0]),o=null,u=0,d=!1,l=function(){},p=null,c="data-vue-ssr-id",f="undefined"!=typeof navigator&&/msie [6-9]\b/.test(navigator.userAgent.toLowerCase());function h(e,t,n,s){d=n,p=s||{};var i=r(e,t);return v(i),function(t){for(var n=[],s=0;s<i.length;s++){var o=i[s];(u=a[o.id]).refs--,n.push(u)}t?v(i=r(e,t)):i=[];for(s=0;s<n.length;s++){var u;if(0===(u=n[s]).refs){for(var d=0;d<u.parts.length;d++)u.parts[d]();delete a[u.id]}}}}function v(e){for(var t=0;t<e.length;t++){var n=e[t],r=a[n.id];if(r){r.refs++;for(var s=0;s<r.parts.length;s++)r.parts[s](n.parts[s]);for(;s<n.parts.length;s++)r.parts.push(m(n.parts[s]));r.parts.length>n.parts.length&&(r.parts.length=n.parts.length)}else{var i=[];for(s=0;s<n.parts.length;s++)i.push(m(n.parts[s]));a[n.id]={id:n.id,refs:1,parts:i}}}}function g(){var e=document.createElement("style");return e.type="text/css",i.appendChild(e),e}function m(e){var t,n,r=document.querySelector("style["+c+'~="'+e.id+'"]');if(r){if(d)return l;r.parentNode.removeChild(r)}if(f){var s=u++;r=o||(o=g()),t=C.bind(null,r,s,!1),n=C.bind(null,r,s,!0)}else r=g(),t=S.bind(null,r),n=function(){r.parentNode.removeChild(r)};return t(e),function(r){if(r){if(r.css===e.css&&r.media===e.media&&r.sourceMap===e.sourceMap)return;t(e=r)}else n()}}var y,b=(y=[],function(e,t){return y[e]=t,y.filter(Boolean).join("\n")});function C(e,t,n,r){var s=n?"":r.css;if(e.styleSheet)e.styleSheet.cssText=b(t,s);else{var a=document.createTextNode(s),i=e.childNodes;i[t]&&e.removeChild(i[t]),i.length?e.insertBefore(a,i[t]):e.appendChild(a)}}function S(e,t){var n=t.css,r=t.media,s=t.sourceMap;if(r&&e.setAttribute("media",r),p.ssrId&&e.setAttribute(c,t.id),s&&(n+="\n/*# sourceURL="+s.sources[0]+" */",n+="\n/*# sourceMappingURL=data:application/json;base64,"+btoa(unescape(encodeURIComponent(JSON.stringify(s))))+" */"),e.styleSheet)e.styleSheet.cssText=n;else{for(;e.firstChild;)e.removeChild(e.firstChild);e.appendChild(document.createTextNode(n))}}}}]);