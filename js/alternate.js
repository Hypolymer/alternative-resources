function updateAlt($altNum) {
    $("#alternatePane").empty();
    var url = "getAlternate.php?num=" + $altNum;

    alert(url);

    $.getJSON(url, function (data) {
        var html = "";
        var altData = data;
        $.each(data, function (index, value) {
            html += "<li>";
            html += [
                '<a href="http://www.worldcat.org/oclc/'
            ] + value['oclcn'] + ['">'];
            html += value['title'] + " ";
            html += value['author'] + " ";
            html += value['format'];
            html += value['language'];
            html += value['edition'];
            html += "</a>";
            html += "</li>";
        });
        //$('#alternatePane').html("<h3>It changed</h3>");
        $('#alternatePane').html("<ul>" + html + "</ul>");
    });
}
