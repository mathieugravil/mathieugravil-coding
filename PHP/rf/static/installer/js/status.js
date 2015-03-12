var populateTable = function(data, $tableid, keytype){
    $.each(data[keytype], function(idx, val){
        if (val.indexOf('$') == -1) {
          var statustype = '<span class="label label-info">Method</span>';
        } 
        else {
          var statustype = '<span class="label label-inverse">Property</span>';
        }
        var version = "1.0";
        $tableid.append("<tr><td>" + statustype + "</td><td>" + val + "</td><td>" + version + "</td></tr>");        
    }); 
}


var drawPage = function(data){

    populateTable(data, $('#examplelist'), 'examples');
    populateTable(data, $('#docslist'), 'documentation');
    populateTable(data, $('#apilist'), 'api');
    populateTable(data, $('#reviewlist'), 'reviewed');
}

$(document).ready(function(){
   $.ajax({
    url: '/rfdev/status/fetch_results.php',
    dataType: 'json',
    success: function(data){
        drawPage(data);
    }
   }); 
});