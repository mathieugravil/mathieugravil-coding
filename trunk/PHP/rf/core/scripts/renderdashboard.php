<?php
    $templateOnly = false;

    if(isset($_GET['templateOnly'])) {
        $templateOnly = true;
    }

    if(!$templateOnly){
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <title>RazorFlow Dashboard</title>

    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
    <?php require "dashboardhead.php"; ?>
    <script type='text/javascript'>
    // Lazyload library
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

//fgnass.github.com/spin.js#v1.2.7
!function(e,t,n){function o(e,n){var r=t.createElement(e||"div"),i;for(i in n)r[i]=n[i];return r}function u(e){for(var t=1,n=arguments.length;t<n;t++)e.appendChild(arguments[t]);return e}function f(e,t,n,r){var o=["opacity",t,~~(e*100),n,r].join("-"),u=.01+n/r*100,f=Math.max(1-(1-e)/t*(100-u),e),l=s.substring(0,s.indexOf("Animation")).toLowerCase(),c=l&&"-"+l+"-"||"";return i[o]||(a.insertRule("@"+c+"keyframes "+o+"{"+"0%{opacity:"+f+"}"+u+"%{opacity:"+e+"}"+(u+.01)+"%{opacity:1}"+(u+t)%100+"%{opacity:"+e+"}"+"100%{opacity:"+f+"}"+"}",a.cssRules.length),i[o]=1),o}function l(e,t){var i=e.style,s,o;if(i[t]!==n)return t;t=t.charAt(0).toUpperCase()+t.slice(1);for(o=0;o<r.length;o++){s=r[o]+t;if(i[s]!==n)return s}}function c(e,t){for(var n in t)e.style[l(e,n)||n]=t[n];return e}function h(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var i in r)e[i]===n&&(e[i]=r[i])}return e}function p(e){var t={x:e.offsetLeft,y:e.offsetTop};while(e=e.offsetParent)t.x+=e.offsetLeft,t.y+=e.offsetTop;return t}var r=["webkit","Moz","ms","O"],i={},s,a=function(){var e=o("style",{type:"text/css"});return u(t.getElementsByTagName("head")[0],e),e.sheet||e.styleSheet}(),d={lines:12,length:7,width:5,radius:10,rotate:0,corners:1,color:"#000",speed:1,trail:100,opacity:.25,fps:20,zIndex:2e9,className:"spinner",top:"auto",left:"auto",position:"relative"},v=function m(e){if(!this.spin)return new m(e);this.opts=h(e||{},m.defaults,d)};v.defaults={},h(v.prototype,{spin:function(e){this.stop();var t=this,n=t.opts,r=t.el=c(o(0,{className:n.className}),{position:n.position,width:0,zIndex:n.zIndex}),i=n.radius+n.length+n.width,u,a;e&&(e.insertBefore(r,e.firstChild||null),a=p(e),u=p(r),c(r,{left:(n.left=="auto"?a.x-u.x+(e.offsetWidth>>1):parseInt(n.left,10)+i)+"px",top:(n.top=="auto"?a.y-u.y+(e.offsetHeight>>1):parseInt(n.top,10)+i)+"px"})),r.setAttribute("aria-role","progressbar"),t.lines(r,t.opts);if(!s){var f=0,l=n.fps,h=l/n.speed,d=(1-n.opacity)/(h*n.trail/100),v=h/n.lines;(function m(){f++;for(var e=n.lines;e;e--){var i=Math.max(1-(f+e*v)%h*d,n.opacity);t.opacity(r,n.lines-e,i,n)}t.timeout=t.el&&setTimeout(m,~~(1e3/l))})()}return t},stop:function(){var e=this.el;return e&&(clearTimeout(this.timeout),e.parentNode&&e.parentNode.removeChild(e),this.el=n),this},lines:function(e,t){function i(e,r){return c(o(),{position:"absolute",width:t.length+t.width+"px",height:t.width+"px",background:e,boxShadow:r,transformOrigin:"left",transform:"rotate("+~~(360/t.lines*n+t.rotate)+"deg) translate("+t.radius+"px"+",0)",borderRadius:(t.corners*t.width>>1)+"px"})}var n=0,r;for(;n<t.lines;n++)r=c(o(),{position:"absolute",top:1+~(t.width/2)+"px",transform:t.hwaccel?"translate3d(0,0,0)":"",opacity:t.opacity,animation:s&&f(t.opacity,t.trail,n,t.lines)+" "+1/t.speed+"s linear infinite"}),t.shadow&&u(r,c(i("#000","0 0 4px #000"),{top:"2px"})),u(e,u(r,i(t.color,"0 0 1px rgba(0,0,0,.1)")));return e},opacity:function(e,t,n){t<e.childNodes.length&&(e.childNodes[t].style.opacity=n)}}),function(){function e(e,t){return o("<"+e+' xmlns="urn:schemas-microsoft.com:vml" class="spin-vml">',t)}var t=c(o("group"),{behavior:"url(#default#VML)"});!l(t,"transform")&&t.adj?(a.addRule(".spin-vml","behavior:url(#default#VML)"),v.prototype.lines=function(t,n){function s(){return c(e("group",{coordsize:i+" "+i,coordorigin:-r+" "+ -r}),{width:i,height:i})}function l(t,i,o){u(a,u(c(s(),{rotation:360/n.lines*t+"deg",left:~~i}),u(c(e("roundrect",{arcsize:n.corners}),{width:r,height:n.width,left:n.radius,top:-n.width>>1,filter:o}),e("fill",{color:n.color,opacity:n.opacity}),e("stroke",{opacity:0}))))}var r=n.length+n.width,i=2*r,o=-(n.width+n.length)*2+"px",a=c(s(),{position:"absolute",top:o,left:o}),f;if(n.shadow)for(f=1;f<=n.lines;f++)l(f,-2,"progid:DXImageTransform.Microsoft.Blur(pixelradius=2,makeshadow=1,shadowopacity=.3)");for(f=1;f<=n.lines;f++)l(f);return u(t,a)},v.prototype.opacity=function(e,t,n,r){var i=e.firstChild;r=r.shadow&&r.lines||0,i&&t+r<i.childNodes.length&&(i=i.childNodes[t+r],i=i&&i.firstChild,i=i&&i.firstChild,i&&(i.opacity=n))}):s=l(t,"animation")}(),typeof define=="function"&&define.amd?define(function(){return v}):e.Spinner=v}(window,document);

        LazyLoad.css(window.rfStyles, function() {
            window.rfScripts1.push(function() {
                $(function(){
                    window.rf.main();
                })
            });
            head.js.apply(null, window.rfScripts1);
        });
    </script>
</head>

<body style="margin:0;padding:0;border:0;overflow-y:scroll;" class='rfDashboard'>
<div class='rfDashboard k-reset rfStandaloneDb'>
    <div class='rfLoading' style='width: 92%; margin-left: 4%; margin-right: 4%; text-align:center; margin-top: 100px'>
        <div id='rfSpinTarget'></div>
        <div style='padding-top: 35px; font-family: sans-serif; font-size: 15px'>
        Loading Dashboard...
        </div>
    </div>
    <script>
        var spinner = new Spinner().spin(document.getElementById('rfSpinTarget'));
    </script>

    <div id="rfDevTools" style="display:none">
        <ul id="rfDTMenu">
            <li>
                <b>RazorFlow Developer Tools</b>
            </li>
            <li data-action='queries'>
                View Queries
            </li>
            <li data-action='datasource'>
                DataSources
            </li>
            <li data-action='logs'>
                Logs
            </li>
            <li data-action='diagnostics'>
                Diagnostics
            </li>
            <li style='float:right'>
                Support
                <ul>
                    <li>
                        <a href="http://razorflow.com/docs/manual/php">RazorFlow PHP Documentation</a>
                    </li>
                    <li>
                        <a href="http://support.razorflow.com/knowledgebase/topics/29082-faq">FAQ</a>
                    </li>
                    <li data-action="diagnostics">
                        <a>Contact Support</a>
                    </li>
                    <li>
                        <a href="http://support.razorflow.com/forums/195839-suggest-a-feature">Suggest a Feature</a>
                    </li>
                    <li>
                        <a href="http://support.razorflow.com/forums/195839-suggest-a-feature">Report a Bug</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div id="modalContainer" >

    </div>
    <div class="dbChrome rfDesktop dbTop">
        <div id="dbHeader" class="dbHeader">
            <h1></h1>
        </div>
    </div>
    <div id="dbTarget" class="rfDesktop">
    </div>
    <div class="dbChrome rfDesktop dbBottom">
        <div id="dbFooter" class="dbFooter">
            <p></p>
        </div>
    </div>
</div>

<?php } ?>
<script id="rfComponentTemplate" type="text/x-kendo-template">
    <div id="container_#= id #" class="rfComponent rfChromedComponent rfHeight_#= height # componentClosed componentClosedDelayed # if (hasSortMenu) { # hasSortMenu # } else { # noSortMenu # } #">
        <ul class="rfMenu">
            <li class='captionItem' ><i class='#= iconClass # rfCaptionIcon'></i> <p class='rfCaptionText'></p><i class='icon-circle-arrow-right rfCaptionIcon rfRightIcon hideOnDesktop'></i></li>
            # if(hasSortMenu) { #
            <li class='sortFilterItem'><i class='icon-filter'></i><span class='hideOnMobile'>Filter</span>
                <ul>
                    <li>
                        <div class="sortFilterContainer menuContainer k-content">
                            <div class='sortWrapper'>
                            </div>
                            <div class='dsPager k-pager-wrap'>
                            </div>
                        </div>
                    </li>
                </ul>
            </li>
            # } #
        </ul>
        <div id="core_#= id #" class="rfCore">

        </div>
    </div>
</script>

<script id="rfChromelessComponentTemplate" type="text/x-kendo-template">
    <div id="container_#= id #" class="rfComponent rfChromelessComponent rfHeight_#= height #">
        <div id="core_#= id #" class="rfCore k-header k-widget k-content">

        </div>
    </div>
</script>

<script id="rfSimpleKPIComponentTemplate" type="text/x-kendo-template">
    <div class="kpiWrapper">
        <div class="kpiValue">
            #= kpiValueString #
        </div>
        <div class="kpiLabel">
            #= caption #
        </div>
    </div>
</script>

<script id="rfComplexKPIComponentTemplate" type="text/x-kendo-template">
    <div class="complexKpiWrapper">
        <div class="kpiValue">
            #= kpiValueString #
        </div>
        <div class="kpiRHS">
            <div class="kpiLabel">
                #= caption #
            </div>
            <div id="spark_#= id #_#= sparkId #" class='kpiSpark'></div>
        </div>
    </div>
</script>

<script id="rfGaugeTemplate" type="text/x-kendo-template">
    <div class="gaugeKPIWrapper">
        <div id="gauge_#= id #_#= gaugeId #" class='kpiGauge'>

        </div>
        <div class="kpiLabel">
            #= caption #
        </div>
    </div>

</script>

<script id="rfErrorDisplayTemplate" class='rfTemplate' type="text/x-kendo-template">
    <div class="errorDisplayWindow">
        <h2>Error loading Dashboard</h2>
        <p>RazorFlow cannot display this dashboard information because it encountered an error and could not recover.</p>
        <p>This might be because of an internal RazorFlow error, or an incorrect use of the APIs. The exact error reported is:</p>
        <textarea style="width: 90%;height: 200px">
Message:
#= message #

Trace:
#= trace #

</textarea>
        <p>Resources that may be able to help:
        </p>
            <ol>
                <li>
                    <a href="http://razorflow.com/docs/manual/php/">RazorFlow PHP Documentation</a>
                </li>
                <li>
                    <a href="http://support.razorflow.com/">Contact Support</a>
                </li>
            </ol>
        <p>
            If you feel that this is an error with RazorFlow, please report the issue. <a href='' class='gatherDiagnosticsButton'>Gather diagnostics and report issue</a>;
        </p>
    </div>
</script>

<script id="rfSpreadsheetRequiredTemplate" class='rfTemplate' type="text/x-kendo-template">
    <div class="errorDisplayWindow windowContainer">
        <h2>Please upload data</h2>
        <p>Please upload the data</p>

        # for (var i = 0; i < required.length; i++) { var item = required[i]; #
            <div class='rfSpreadsheetUploadRequest'>
                <h2>Upload for #= item['id'] #</h2>
                <p>Upload an excel file here.</p>
                <form method='post' action='#= url #&endpoint=#= item.id #_upload'>
                    <input type='file' name='spreadsheet_#= item.id #' class='rfFileUpload' data-id='#= item['id'] #' />
                </form>
                <p class='uploadResult' # if(!item.fileExists){# style='display:none' #}#>Currently using <span class='rfUploadFileName'>#= item.fileName #</span></p>
            </div>
        # } #
    </div>
</script>

<script id="rfAuthHelperTemplate" class='rfTemplate' type="text/x-kendo-template">
    <div class="errorDisplayWindow windowContainer">
        <h2>Authentication Required</h2>
        # if(authType === 'google') { #
            <p>Please sign in using your google account</p>
            <p><a href='<?php echo $_SERVER["PHP_SELF"] . "?auth=true" ?>' class='googButton'>Click Here</a></p>
        # } #
    </div>
</script>

<script id="rfDiagnosticsTemplate" class='rfTemplate' type="text/x-kendo-template">
    <div class="errorDisplayWindow">
        <h2>Diagnostics</h2>
        <p>Please include the following information while submitting any error/support request.</p>
        <p>You can optionally include the Debug Logs. This will include information like SQL Queries (No username and password) - but will make it easier for the RazorFlow team to diagnose and fix the error.</p>
        <p>You can find information on contacting us at: <a href="http://support.razorflow.com">support.razorflow.com</a></p>
        <textarea class="diagText">
        </textarea>
        <p><input type="checkbox" class='includeDiagCheckbox'/> [Optional] Include Debug Logs</p>
    </div>
</script>

<script id="rfFilterFormTemplate" type="text/x-kendo-template">
    <div class='rfFilterForm k-header k-content k-widget'>
        <form>
            # for(var itemKey in items) {
            var item = items[itemKey];
            var itemId = 'rfFormItem_' + id + '_' + itemKey;
            #
            <div class='rfControlGroup'>
                <label class='rfFormLabel' for='#= itemId #'>#= item.caption #</label>
                <div class='rfFormControl'>
                    # if(item.type === "bool") { #
                    <input type="checkbox"
                        # if(item.defaultValue === true) {# checked # } # id="#= itemId #"
                        class='k-checkbox rfFormCheckbox'
                        data-changetype='bool'
                        data-modify='#= itemKey #'/>
                    # } #
                    # if(item.type === "text") { #
                    <span class='k-textbox rfControlItem'>
                        <input type="text"
                               value="#= item.defaultValue #"
                               id="#= itemId #"
                               data-changetype='text'
                               data-modify='#= itemKey #'/>
                    </span>
                    # } #
                    # if(item.type === "multiselect") { #
                    <select multiple="multiple" id="#= itemId #"
                            class='rfControlItem'
                            data-changetype='multiselect'
                            data-modify='#= itemKey #'>
                        # for(var j = 0; j < item.values.length; j ++) { #
                        <option value='#= item.values[j] #'>#= item.values[j] #</option>
                        # } #
                    </select>
                    # } #
                    # if(item.type === "select") { #
                    <select id="#= itemId #" class="rfControlItem" data-filterRole='select' data-changetype='select' data-modify='#= itemKey #'>
                        # if(!item.selected) { #
                            <option selected="selected"  value='__null'>-No Selection-</option>
                        # } #
                        # for(var j = 0; j < item.values.length; j ++) { #
                        <option # if(j === item.selected ) {# selected="selected" #} # value='#= item.values[j] #'>#= item.values[j] #</option>
                        # } #
                    </select>
                    # } #
                    # if(item.type === "timerange") { #
                    <input type="#= datetype #"
                           class="rfControlHalf"
                           id="#= itemId #"
                           value="#= item.defaultValue[0] #"
                           data-filterRole='timerange'
                           data-changetype='timerange'
                           data-modify='#= itemKey #'
                           data-index='0'
                            />
                    <p class="rfRangeToSeparator"> to </p>
                    <input type="#= datetype #" class="rfControlHalf"
                           id="#= itemId #_2"
                           value="#= item.defaultValue[1] #"
                           data-filterRole='timerange'
                           data-changetype='timerange'
                           data-modify='#= itemKey #'
                           data-index='1'/>
                    # } #
                    # if(item.type === "numrange") { #
                    <input type="#= numtype #" class="rfControlHalf"
                           id="#= itemId #"
                           value="#= item.defaultValue[0] #"
                           data-filterRole='numrange'
                           data-changetype='numrange'
                           data-modify='#= itemKey #'
                           data-index='0'/>
                    <p class="rfRangeToSeparator"> to </p>
                    <input type="#= numtype #" class="rfControlHalf"
                           id="#= itemId #_2"
                           value="#= item.defaultValue[1] #"
                           data-filterRole='numrange'
                           data-changetype='numrange'
                           data-modify='#= itemKey #'
                           data-index='1'/>
                    # } #
                </div>
                <div style='clear:both'></div>
            </div>
            # } #
            <div class='rfSubmitButton'>
                <button class='rfResetTarget k-button'>Remove Filters</button>
                <button class='rfSubmitTarget k-button'>Apply Filters</button>
            </div>
        </form>
    </div>
</script>

<script id="rfDiagnosticTextTemplate" type="text/x-kendo-template" >RazorFlow Build ID: #= buildId #

Server Info: #= serverInformation #

# if (message) {#
ERROR
=========================================
#= message #


STACK TRACE
=========================================
#= trace #

# } #

# if(displayLogs) {#

Debug Logs
==========================================
#= debugLogs #
# } #
</script>
<div style="display:none">
    <div id="queryDisplayWindow">
        <div id="paneSplit" style="height: 100%; width: 100%;">
            <div>
                <div class="pane-content">
                    <div id="componentList">

                    </div>
                </div>
            </div>
            <div>
                <div class="pane-content">
                    <div id="queryList">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script id="rfLogoutTemplate" type="text/x-kendo-template" >
<div class='rfLogoutLinks k-content'>
<div class='k-block' style='text-align: right; padding: 5px'>
<p>Currently logged in as: #= __loggedInAs #. <a href="#= __logoutUrl #">Logout</a></p>
</div>
</div>
</script>
<script id="rfQueryListTemplate" type="text/x-kendo-template" >
    <div class='rfQueryListItem'>
        # if (time) { #
        <h1 style="font-size: 25px; border-bottom: 1px solid black; margin-top: 10px">Query</h1>
        # } #
        <div style="font-family: monospace; font-size: 10px;background-color: white; padding: 8px; margin: 4px; border: 1px solid ebebeb">
            <pre>
#= queryString #
            </pre>
        </div>
        # if(time) { #
        <p style="margin: 5px; font-size: 15px;">Finished in #= time # seconds, with #= count # rows. Here are the first two rows:</p>
        <div class='queryTable'></div>

        # } #
    </div>

</script>

<?php if(!$templateOnly) { ?>
</body>
</html>
<?php } ?>
