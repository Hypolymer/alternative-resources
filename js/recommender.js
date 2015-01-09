function sendNewRecommend() {
    updateAlt();
    document.getElementById("stage").innerHTML = "";
    var searchISBN = "recommender.php?num=" + document.getElementById(
        "ISSN").value;

    $.getJSON(searchISBN, function(data) {
        var html = "";
        $.each(data.mlt.likeItems.likeItem, function(index, value) {
            html += "<li>";
            html += [
                '<a href="http://www.worldcat.org/oclc/'
            ] + value['@ocn'] + ['">'];
            html += value['@title'] + " ";
            html += value['@author'] + " ";
            html += value['@pubDate'];
            html += "</a>";
            html += "</li>";
        });
        $('.result').html("<ul>" + html + "</ul>");
    });
}
//<!--this script is to create recommendations based on title/author-->
function sendNewBibRecommend() {
  var inputOCLCN = document.getElementById("ISSN").value;
  console.log(inputOCLCN);
  if (inputOCLCN == "" || inputOCLCN == null)
    {
    console.log("No OCLC Number provided, do a title author search");
    document.getElementById("stage").innerHTML = "";
    var searchTitleAuthor = "bibrecommender.php?bibsearch=" + document.getElementById(
            "LoanTitle").value + " " + document.getElementById("LoanAuthor")
        .value;
    var number = $.get(searchTitleAuthor, function(data) {
        var searchISBN = "recommender.php?num=" + data;
        //alert(data)
        updateAlt(data);
        $.getJSON(searchISBN, function(data) {
            var html = "";
            $.each(data.mlt.likeItems.likeItem, function(
                index, value) {
                html += "<li>";
                html += [
                    '<a href="http://www.worldcat.org/oclc/'
                ] + value['@ocn'] + ['">'];
                html += value['@title'] + " ";
                html += value['@author'] + " ";
                html += value['@pubDate'];
                html += "</a>";
                html += "</li>";
            });
            $('.result').html("<ul>" + html + "</ul>");
        }).error(function(jqXHR, textStatus, errorThrown) {
            alert("No Recommendations");
        });
    });
  }
  else
    {
      console.log("OCLCN provided, proceeding with that value");
      updateAlt(inputOCLCN);

          var searchISBN = "recommender.php?num=" + inputOCLCN;
          //alert(data)
          $.getJSON(searchISBN, function(data) {
              var html = "";
              $.each(data.mlt.likeItems.likeItem, function(
                  index, value) {
                  html += "<li>";
                  html += [
                      '<a href="http://www.worldcat.org/oclc/'
                  ] + value['@ocn'] + ['">'];
                  html += value['@title'] + " ";
                  html += value['@author'] + " ";
                  html += value['@pubDate'];
                  html += "</a>";
                  html += "</li>";
              });
              $('.result').html("<ul>" + html + "</ul>");
          }).error(function(jqXHR, textStatus, errorThrown) {
              alert("No Recommendations");
          });
    }
}
