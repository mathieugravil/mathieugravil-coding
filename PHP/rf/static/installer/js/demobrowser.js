$(function() {
	var resizeDemos = function () {
		// $('.rfDesktopDemo').each(function(){
  //           var item = $(this);
  //           var height = window.rfDemoOffset ? window.rfDemoOffset : 30;
		// 	item.height ($(window).height() - height);
		// });
	};

    var handheld = ( navigator.userAgent.match(/(iPad|iPhone|iPod|Android)/i) || $.browser.msie ? true : false );
    var noBrowser = handheld || ($(window).width() < 970);

    if(noBrowser)
    {
        $('.rfDemoContainer').removeClass('span9').addClass('span7').hide();
        $(".rfDemoList").removeClass("span3").addClass('span5');

        $(".dbLink").each(function(){
            var slug = $(this).attr('data-slug');
            var urlbase = "";

            var realUrl = urlbase + slug + '.php';

            // $(this).attr('href',realUrl);
            $(this).attr('target', '_blank');
        })
    }


	// resizeDemos();

	$("#myTabs").tab();

    window.prettyPrint && prettyPrint()
    
    
    $('.dbLink').click(function(e){
        var slugName = $(this).attr('data-slug');
        if(typeof(_gaq) !== "undefined")
            _gaq.push(['_trackEvent', 'Demos', 'viewed', slugName]);
        if(typeof(mixpanel) !== "undefined")
            mixpanel.track("Demo Viewed", {"type": slugName});
        
        if (noBrowser)
            return true;
        var demoName = $(this).parent().find('h3').text();
        $("#rfDemoName").text(demoName);
        $("#rfDemoModal").modal({
            show: true
        });
    	e.preventDefault();
    	var slug = $(this).attr('data-slug');
        var urlbase = window.rfUrlBase ? window.rfUrlBase : "";

        var realUrl = urlbase + slug + '.php';

        $("#openButton").attr('href', realUrl).attr('target', '_blank');
        
        if($.browser.msie && parseInt($.browser.version, 10) === 8) {
            $('.iosFrame').attr('src', "/rfdev/demos/ie8error.html");
            $('.rfDesktopDemo').attr('src', realUrl);
        }
        else {
    	   $('.rfDemoFrame').attr('src', realUrl);
        }

        $("#sourceContent").load(urlbase + "index.php?item=" + slug, function() {
                prettyPrint();
        });
       

    	$('.dbNav li').removeClass('active');
    	$(this).parent().addClass('active');

        return false;
    });

    $("#mobileViewButton").click (function() {
        var frame = $('.iosFrame');
        frame.src = frame.src;
    })

    

    $("#sourceButton").click(function(){
        if(!window.currentItem)
            return 0;


        return 0;
    });

    if(window.rfUrlBase)
    {
        // $('.dbLink').each(function() {
        //     var base = $(this).attr('href');
        //     $(this).attr('href', window.rfUrlBase +  base);
        // });
    }

});