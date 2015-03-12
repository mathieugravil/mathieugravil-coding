(function($, window, undefined) {

    (function() {
        var method;
        var noop = function noop() {};
        var methods = [
            'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
            'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
            'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
            'timeStamp', 'trace', 'warn'
        ];
        var length = methods.length;
        var console = (window.console = window.console || {});

        while (length--) {
            method = methods[length];

            // Only stub undefined methods.
            if (!console[method]) {
                console[method] = noop;
            }
        }
    }());



    $.razorflow = function(element, options) {
        var plugin = this;

        var url = options['url'];

        var $wrapper = $(element);
        var $element = $("<div/>");
        $wrapper.empty().append($element);

        // the "constructor" method that gets called when the object is created
        plugin.init = function() {

            if(!url)
            {
                console.error("Missing a URL setting");
                return 0;
            }
            $wrapper.css ({
                margin: '0',
                padding: '0',
                border: '0'
            });

            if(options['width']) {
                plugin.setWidth(options['width']);
            }
            if(options['height']) {
                plugin.setHeight(options['height']);
            }

            $element.empty().addClass('rfDesktop rfDashboard k-reset');
            $element.html("<div class='rfLoading' style='width: 92%; margin-left: 4%; margin-right: 4%; text-align:center; margin-top: 100px'><div class='rfSpinTarget'></div><div style='padding-top: 35px; font-family: sans-serif; font-size: 15px'>Loading Dashboard...</div></div>");
            new Spinner().spin($element.find('.rfSpinTarget')[0]);

            // Get the templates
            var templateHolder = $("#templateHolder");

            var loadDashboard = function() {
                var separator = url.indexOf("?") === -1 ? "?" : "&";
                $.ajax({
                    url: url + separator + "dbAsJson&jqplugin",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {

                        var staticRoot = data.dashboard.__webRoot;
                        var assets = data.dashboard.__assetList;
                        window.rfScripts1 = assets[0];
                        window.rfScripts2 = assets[1];

                        LazyLoad.css(assets[2], function() {
                            window.rfScripts1.push(function() {
                                window.dashboardUrl = url + "?x";
                                window.rf.embedded = true;

                                var xrf = window.rf.main($element);

                                xrf.ee.on('dashboardFinishedRender', function(){
                                    if(options['onDashboardRendered']) {
                                        options['onDashboardRendered']();
                                    }
                                });


                                var oldWidth = $wrapper.width();
                                var checkCycle = 0;

                                var checkWidths = function() {
                                    var delay = 200;
                                    checkCycle ++;

                                    if(($wrapper.width() !== oldWidth) && checkCycle > 10) 
                                    {
                                        $element.width($wrapper.width());

                                        xrf.adjustWidths ();

                                        _.delay(function() {
                                            xrf.ee.emit('windowResized');
                                            xrf.ee.emit('dimChanged');
                                            xrf.ee.emit('rawWinResized');
                                            xrf.ee.emit('windowResizedEnd');
                                        }, 100);

                                        oldWidth = $wrapper.width();
                                        checkCycle = 0;
                                    }
                                    _.delay(checkWidths, delay);
                                };
                                _.delay(checkWidths, 1000);

                            });
                            head.js.apply(null, window.rfScripts1);
                        });

                    },
                    failure: function() {
                    }
                });
            };

            if(templateHolder.length == 0)
            {
                templateHolder = $("<div/>", {id:'templateHolder'});
                templateHolder.hide();
                templateHolder.appendTo('body').load(url + "?templateOnly", function() {
                    loadDashboard();
                });
            }
            else {
                loadDashboard();
            }


            return 0;
        };

        plugin.setWidth = function(width) {
            $wrapper.width(width);
        };

        plugin.setHeight = function(height) {
            $wrapper.css({
                height: height,
                'overflow-y': 'scroll'
            })
        }

        plugin.init();
    };

    // add the plugin to the jQuery.fn object
    $.fn.razorflow = function(options) {
        if(!options)
        {
            return;
        }
        return this.each(function() {
            if (undefined == $(this).data('razorflow')) {
                var plugin = new $.razorflow(this, options);
                $(this).data('razorflow', plugin);
            }
        });
    }
})(jQuery, this);


LazyLoad=function(k){function p(b,a){var g=k.createElement(b),c;for(c in a)a.hasOwnProperty(c)&&g.setAttribute(c,a[c]);return g}function l(b){var a=m[b],c,f;if(a)c=a.callback,f=a.urls,f.shift(),h=0,f.length||(c&&c.call(a.context,a.obj),m[b]=null,n[b].length&&j(b))}function w(){var b=navigator.userAgent;c={async:k.createElement("script").async===!0};(c.webkit=/AppleWebKit\//.test(b))||(c.ie=/MSIE/.test(b))||(c.opera=/Opera/.test(b))||(c.gecko=/Gecko\//.test(b))||(c.unknown=!0)}function j(b,a,g,f,h){var j=
    function(){l(b)},o=b==="css",q=[],d,i,e,r;c||w();if(a)if(a=typeof a==="string"?[a]:a.concat(),o||c.async||c.gecko||c.opera)n[b].push({urls:a,callback:g,obj:f,context:h});else{d=0;for(i=a.length;d<i;++d)n[b].push({urls:[a[d]],callback:d===i-1?g:null,obj:f,context:h})}if(!m[b]&&(r=m[b]=n[b].shift())){s||(s=k.head||k.getElementsByTagName("head")[0]);a=r.urls;d=0;for(i=a.length;d<i;++d)g=a[d],o?e=c.gecko?p("style"):p("link",{href:g,rel:"stylesheet"}):(e=p("script",{src:g}),e.async=!1),e.className="lazyload",
    e.setAttribute("charset","utf-8"),c.ie&&!o?e.onreadystatechange=function(){if(/loaded|complete/.test(e.readyState))e.onreadystatechange=null,j()}:o&&(c.gecko||c.webkit)?c.webkit?(r.urls[d]=e.href,t()):(e.innerHTML='@import "'+g+'";',u(e)):e.onload=e.onerror=j,q.push(e);d=0;for(i=q.length;d<i;++d)s.appendChild(q[d])}}function u(b){var a;try{a=!!b.sheet.cssRules}catch(c){h+=1;h<200?setTimeout(function(){u(b)},50):a&&l("css");return}l("css")}function t(){var b=m.css,a;if(b){for(a=v.length;--a>=0;)if(v[a].href===
    b.urls[0]){l("css");break}h+=1;b&&(h<200?setTimeout(t,50):l("css"))}}var c,s,m={},h=0,n={css:[],js:[]},v=k.styleSheets;return{css:function(b,a,c,f){j("css",b,a,c,f)},js:function(b,a,c,f){j("js",b,a,c,f)}}}(this.document);
(function(f,w){function m(){}function g(a,b){if(a){"object"===typeof a&&(a=[].slice.call(a));for(var c=0,d=a.length;c<d;c++)b.call(a,a[c],c)}}function v(a,b){var c=Object.prototype.toString.call(b).slice(8,-1);return b!==w&&null!==b&&c===a}function k(a){return v("Function",a)}function h(a){a=a||m;a._done||(a(),a._done=1)}function n(a){var b={};if("object"===typeof a)for(var c in a)a[c]&&(b={name:c,url:a[c]});else b=a.split("/"),b=b[b.length-1],c=b.indexOf("?"),b={name:-1!==c?b.substring(0,c):b,url:a};
return(a=p[b.name])&&a.url===b.url?a:p[b.name]=b}function q(a){var a=a||p,b;for(b in a)if(a.hasOwnProperty(b)&&a[b].state!==r)return!1;return!0}function s(a,b){b=b||m;a.state===r?b():a.state===x?d.ready(a.name,b):a.state===y?a.onpreload.push(function(){s(a,b)}):(a.state=x,z(a,function(){a.state=r;b();g(l[a.name],function(a){h(a)});j&&q()&&g(l.ALL,function(a){h(a)})}))}function z(a,b){var b=b||m,c;/\.css[^\.]*$/.test(a.url)?(c=e.createElement("link"),c.type="text/"+(a.type||"css"),c.rel="stylesheet",
c.href=a.url):(c=e.createElement("script"),c.type="text/"+(a.type||"javascript"),c.src=a.url);c.onload=c.onreadystatechange=function(a){a=a||f.event;if("load"===a.type||/loaded|complete/.test(c.readyState)&&(!e.documentMode||9>e.documentMode))c.onload=c.onreadystatechange=c.onerror=null,b()};c.onerror=function(){c.onload=c.onreadystatechange=c.onerror=null;b()};c.async=!1;c.defer=!1;var d=e.head||e.getElementsByTagName("head")[0];d.insertBefore(c,d.lastChild)}function i(){e.body?j||(j=!0,g(A,function(a){h(a)})):
(f.clearTimeout(d.readyTimeout),d.readyTimeout=f.setTimeout(i,50))}function t(){e.addEventListener?(e.removeEventListener("DOMContentLoaded",t,!1),i()):"complete"===e.readyState&&(e.detachEvent("onreadystatechange",t),i())}var e=f.document,A=[],B=[],l={},p={},E="async"in e.createElement("script")||"MozAppearance"in e.documentElement.style||f.opera,C,j,D=f.head_conf&&f.head_conf.head||"head",d=f[D]=f[D]||function(){d.ready.apply(null,arguments)},y=1,x=3,r=4;d.load=E?function(){var a=arguments,b=a[a.length-
1],c={};k(b)||(b=null);g(a,function(d,e){d!==b&&(d=n(d),c[d.name]=d,s(d,b&&e===a.length-2?function(){q(c)&&h(b)}:null))});return d}:function(){var a=arguments,b=[].slice.call(a,1),c=b[0];if(!C)return B.push(function(){d.load.apply(null,a)}),d;c?(g(b,function(a){if(!k(a)){var b=n(a);b.state===w&&(b.state=y,b.onpreload=[],z({url:b.url,type:"cache"},function(){b.state=2;g(b.onpreload,function(a){a.call()})}))}}),s(n(a[0]),k(c)?c:function(){d.load.apply(null,b)})):s(n(a[0]));return d};d.js=d.load;d.test=
function(a,b,c,e){a="object"===typeof a?a:{test:a,success:b?v("Array",b)?b:[b]:!1,failure:c?v("Array",c)?c:[c]:!1,callback:e||m};(b=!!a.test)&&a.success?(a.success.push(a.callback),d.load.apply(null,a.success)):!b&&a.failure?(a.failure.push(a.callback),d.load.apply(null,a.failure)):e();return d};d.ready=function(a,b){if(a===e)return j?h(b):A.push(b),d;k(a)&&(b=a,a="ALL");if("string"!==typeof a||!k(b))return d;var c=p[a];if(c&&c.state===r||"ALL"===a&&q()&&j)return h(b),d;(c=l[a])?c.push(b):l[a]=[b];
return d};d.ready(e,function(){q()&&g(l.ALL,function(a){h(a)});d.feature&&d.feature("domloaded",!0)});if("complete"===e.readyState)i();else if(e.addEventListener)e.addEventListener("DOMContentLoaded",t,!1),f.addEventListener("load",i,!1);else{e.attachEvent("onreadystatechange",t);f.attachEvent("onload",i);var u=!1;try{u=null==f.frameElement&&e.documentElement}catch(F){}u&&u.doScroll&&function b(){if(!j){try{u.doScroll("left")}catch(c){f.clearTimeout(d.readyTimeout);d.readyTimeout=f.setTimeout(b,50);
return}i()}}()}setTimeout(function(){C=!0;g(B,function(b){b()})},300)})(window);
!function(e,t,n){function o(e,n){var r=t.createElement(e||"div"),i;for(i in n)r[i]=n[i];return r}function u(e){for(var t=1,n=arguments.length;t<n;t++)e.appendChild(arguments[t]);return e}function f(e,t,n,r){var o=["opacity",t,~~(e*100),n,r].join("-"),u=.01+n/r*100,f=Math.max(1-(1-e)/t*(100-u),e),l=s.substring(0,s.indexOf("Animation")).toLowerCase(),c=l&&"-"+l+"-"||"";return i[o]||(a.insertRule("@"+c+"keyframes "+o+"{"+"0%{opacity:"+f+"}"+u+"%{opacity:"+e+"}"+(u+.01)+"%{opacity:1}"+(u+t)%100+"%{opacity:"+e+"}"+"100%{opacity:"+f+"}"+"}",a.cssRules.length),i[o]=1),o}function l(e,t){var i=e.style,s,o;if(i[t]!==n)return t;t=t.charAt(0).toUpperCase()+t.slice(1);for(o=0;o<r.length;o++){s=r[o]+t;if(i[s]!==n)return s}}function c(e,t){for(var n in t)e.style[l(e,n)||n]=t[n];return e}function h(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var i in r)e[i]===n&&(e[i]=r[i])}return e}function p(e){var t={x:e.offsetLeft,y:e.offsetTop};while(e=e.offsetParent)t.x+=e.offsetLeft,t.y+=e.offsetTop;return t}var r=["webkit","Moz","ms","O"],i={},s,a=function(){var e=o("style",{type:"text/css"});return u(t.getElementsByTagName("head")[0],e),e.sheet||e.styleSheet}(),d={lines:12,length:7,width:5,radius:10,rotate:0,corners:1,color:"#000",speed:1,trail:100,opacity:.25,fps:20,zIndex:2e9,className:"spinner",top:"auto",left:"auto",position:"relative"},v=function m(e){if(!this.spin)return new m(e);this.opts=h(e||{},m.defaults,d)};v.defaults={},h(v.prototype,{spin:function(e){this.stop();var t=this,n=t.opts,r=t.el=c(o(0,{className:n.className}),{position:n.position,width:0,zIndex:n.zIndex}),i=n.radius+n.length+n.width,u,a;e&&(e.insertBefore(r,e.firstChild||null),a=p(e),u=p(r),c(r,{left:(n.left=="auto"?a.x-u.x+(e.offsetWidth>>1):parseInt(n.left,10)+i)+"px",top:(n.top=="auto"?a.y-u.y+(e.offsetHeight>>1):parseInt(n.top,10)+i)+"px"})),r.setAttribute("aria-role","progressbar"),t.lines(r,t.opts);if(!s){var f=0,l=n.fps,h=l/n.speed,d=(1-n.opacity)/(h*n.trail/100),v=h/n.lines;(function m(){f++;for(var e=n.lines;e;e--){var i=Math.max(1-(f+e*v)%h*d,n.opacity);t.opacity(r,n.lines-e,i,n)}t.timeout=t.el&&setTimeout(m,~~(1e3/l))})()}return t},stop:function(){var e=this.el;return e&&(clearTimeout(this.timeout),e.parentNode&&e.parentNode.removeChild(e),this.el=n),this},lines:function(e,t){function i(e,r){return c(o(),{position:"absolute",width:t.length+t.width+"px",height:t.width+"px",background:e,boxShadow:r,transformOrigin:"left",transform:"rotate("+~~(360/t.lines*n+t.rotate)+"deg) translate("+t.radius+"px"+",0)",borderRadius:(t.corners*t.width>>1)+"px"})}var n=0,r;for(;n<t.lines;n++)r=c(o(),{position:"absolute",top:1+~(t.width/2)+"px",transform:t.hwaccel?"translate3d(0,0,0)":"",opacity:t.opacity,animation:s&&f(t.opacity,t.trail,n,t.lines)+" "+1/t.speed+"s linear infinite"}),t.shadow&&u(r,c(i("#000","0 0 4px #000"),{top:"2px"})),u(e,u(r,i(t.color,"0 0 1px rgba(0,0,0,.1)")));return e},opacity:function(e,t,n){t<e.childNodes.length&&(e.childNodes[t].style.opacity=n)}}),function(){function e(e,t){return o("<"+e+' xmlns="urn:schemas-microsoft.com:vml" class="spin-vml">',t)}var t=c(o("group"),{behavior:"url(#default#VML)"});!l(t,"transform")&&t.adj?(a.addRule(".spin-vml","behavior:url(#default#VML)"),v.prototype.lines=function(t,n){function s(){return c(e("group",{coordsize:i+" "+i,coordorigin:-r+" "+ -r}),{width:i,height:i})}function l(t,i,o){u(a,u(c(s(),{rotation:360/n.lines*t+"deg",left:~~i}),u(c(e("roundrect",{arcsize:n.corners}),{width:r,height:n.width,left:n.radius,top:-n.width>>1,filter:o}),e("fill",{color:n.color,opacity:n.opacity}),e("stroke",{opacity:0}))))}var r=n.length+n.width,i=2*r,o=-(n.width+n.length)*2+"px",a=c(s(),{position:"absolute",top:o,left:o}),f;if(n.shadow)for(f=1;f<=n.lines;f++)l(f,-2,"progid:DXImageTransform.Microsoft.Blur(pixelradius=2,makeshadow=1,shadowopacity=.3)");for(f=1;f<=n.lines;f++)l(f);return u(t,a)},v.prototype.opacity=function(e,t,n,r){var i=e.firstChild;r=r.shadow&&r.lines||0,i&&t+r<i.childNodes.length&&(i=i.childNodes[t+r],i=i&&i.firstChild,i=i&&i.firstChild,i&&(i.opacity=n))}):s=l(t,"animation")}(),typeof define=="function"&&define.amd?define(function(){return v}):e.Spinner=v}(window,document);