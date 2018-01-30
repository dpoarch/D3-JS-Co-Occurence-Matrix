
<!DOCTYPE html>
<html class="ocks-org do-not-copy">
<meta charset="utf-8">
<title>Similarity Matrix Co-occurrence</title>
<style>

@import url(css/style.css?aea6f0a);

</style>
<script src="js/d3.v2.min.js" charset="utf-8"></script>

<h1>Similarity Matrix</h1>

<aside style="margin-top:80px;">
<p>Order: <select id="order">.
  <option value="id">by ID</option>
  <option value="name">by Name</option>
  <option value="count">by Frequency</option>
  <option value="group">by Cluster</option>
  <option value="publishDate">by Published Date</option>
  <option value="url">by Url</option>
</select>
<table class="legend">
  <tr><th colspan="2">Legend</th></tr>
  <tr><td class="color red"></td><td>Very high similarity</td></tr>
  <tr><td class="color orange"></td><td>High similarity</td></tr>
  <tr><td class="color yellow"></td><td>Medium similarity</td></tr>
  <tr><td class="color green"></td><td>Low similarity</td></tr>
  <tr><td class="color blue"></td><td>Very low similarity</td></tr>
</table>

<form>
<p class="alert"><strong>Note:</strong> click a cell to see information.</p>
<h3>X Axis Document</h3>
<div class="information">
  <div><strong>ID: </strong><p id="idx"></p></div>
  <div><strong>Title: </strong><p id="titlex"></p></div>
  <div><strong>Cluster: </strong><p id="groupx"></p></div>
  <div><strong>Published Date: </strong><p id="datex"></p></div>
  <div><strong>URL: </strong><a href="" id="urlx" target="_blank"></a></div>
</div>
<h3>Y Axis Document</h3>
<div class="information">
  <div><strong>ID: </strong><p id="idy"></p></div>
  <div><strong>Title: </strong><p id="titley"></p></div>
  <div><strong>Cluster: </strong><p id="groupy"></p></div>
  <div><strong>Published Date: </strong><p id="datey"></p></div>
  <div><strong>URL: </strong><a href="" id="urly" target="_blank"></a></div>
</div>
</form>
</aside>

<script>

var margin = {top: 80, right: 0, bottom: 10, left: 80},
    width = 720,
    height = 720;

var x = d3.scale.ordinal().rangeBands([0, width]),
    z = d3.scale.linear().domain([0, 4]).clamp(true),
    c = d3.scale.category10().domain(d3.range(10));

var div = d3.select("body").append("div")
    .attr("class", "tooltip")
    .style("opacity", 0);

var div2 = d3.select("body").append("div")
    .attr("class", "tooltip2")
    .style("opacity", 0);

var svg = d3.select("body").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
    .style("margin-left", -margin.left + "px")
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

d3.json("upload.json", function(miserables) {
  var matrix = [],
      nodes = miserables.nodes,
      n = nodes.length;

  // Compute index per node.
  nodes.forEach(function(node, i) {
    node.index = i;
    node.count = 0;
    matrix[i] = d3.range(n).map(function(j) { return {x: j, y: i, z: 0}; });
  });

  // Convert links to matrix; count character occurrences.
  miserables.links.forEach(function(link) {
    matrix[link.source][link.target].z += link.value;
    matrix[link.target][link.source].z += link.value;
    matrix[link.source][link.source].z += link.value;
    matrix[link.target][link.target].z += link.value;
    nodes[link.source].count += link.value;
    nodes[link.target].count += link.value;
  });

  // Precompute the orders.
  var orders = {
    id: d3.range(n).sort(function(a, b) { return d3.ascending(nodes[a].id, nodes[b].id); }),
    name: d3.range(n).sort(function(a, b) { return d3.ascending(nodes[a].name, nodes[b].name); }),
    count: d3.range(n).sort(function(a, b) { return nodes[b].count - nodes[a].count; }),
    group: d3.range(n).sort(function(a, b) { return nodes[b].group - nodes[a].group; }),
    publishDate: d3.range(n).sort(function(a, b) { return d3.ascending(nodes[a].publishDate, nodes[b].publishDate); }),
    url: d3.range(n).sort(function(a, b) { return d3.ascending(nodes[a].url, nodes[b].url); })
  };

  // The default sort order.
  x.domain(orders.name);

  svg.append("rect")
      .attr("class", "background")
      .attr("width", width)
      .attr("height", height);

  var row = svg.selectAll(".row")
      .data(matrix)
    .enter().append("g")
      .attr("class", "row")
      .attr("transform", function(d, i) { return "translate(0," + x(i) + ")"; })
      .each(row);

  row.append("line")
      .attr("x2", width);

  row.append("text")
      .attr("x", -6)
      .attr("y", x.rangeBand() / 2)
      .attr("dy", ".32em")
      .attr("text-anchor", "end")
      .attr("id", function(d, i) { return nodes[i].id; })
      .attr("group", function(d, i) { return nodes[i].group; })
      .attr("date", function(d, i) { return nodes[i].publishDate; })
      .attr("url", function(d, i) { return nodes[i].url; })
      .text(function(d, i) { return nodes[i].name; })
      .on("mouseover", function(d) {
        var minusx = 0;
          if(document.documentElement.clientWidth > 1250 && document.documentElement.clientWidth < 1268){
            minusx = 200;
          }else if(document.documentElement.clientWidth > 1267 && document.documentElement.clientWidth < 1350){
            minusx = 20;
          }
          else if(document.documentElement.clientWidth > 1349 && document.documentElement.clientWidth < 1450){
            minusx = 50;
          }else if(document.documentElement.clientWidth > 1449 && document.documentElement.clientWidth < 1550){
            minusx = 120;
          }else if(document.documentElement.clientWidth > 1549 && document.documentElement.clientWidth < 1650){
            minusx = 180;
          }else if(document.documentElement.clientWidth > 1649 && document.documentElement.clientWidth < 1750){
            minusx = 200;
          }else if(document.documentElement.clientWidth > 1749 && document.documentElement.clientWidth < 1850){
            minusx = 260;
          }else if(document.documentElement.clientWidth > 1849 && document.documentElement.clientWidth < 1980){
            minusx = 320;
          }
            div2.transition()
                .duration(200)
                .style("opacity", .9);
            div2.html("<table><tr><td><strong>ID: </strong></td><td>" + d3.select(this)[0][0].getAttribute("id") + "</td></tr><tr><td><strong>Title: </strong></td><td>" +  d3.select(this)[0][0].innerHTML + "</td></tr><tr><td><strong>Cluster: </strong></td><td>" + d3.select(this)[0][0].getAttribute("group") + "</td></tr><tr><td><strong>Published Date: </strong></td><td>" + d3.select(this)[0][0].getAttribute("date") + "</td></tr><tr><td><strong>URL: </strong></td><td>" + d3.select(this)[0][0].getAttribute("url") + "</td></tr></table>")
                .style("left", (d3.event.pageX - minusx) + "px")
                .style("top", (d3.event.pageY - 200) + "px");
        })
        .on("mouseout", function(d) {
            div2.transition()
                .duration(500)
                .style("opacity", 0)
        });

  var column = svg.selectAll(".column")
      .data(matrix)
    .enter().append("g")
      .attr("class", "column")
      .attr("transform", function(d, i) { return "translate(" + x(i) + ")rotate(-90)"; });

  column.append("line")
      .attr("x1", -width);

  column.append("text")
      .attr("x", 6)
      .attr("y", x.rangeBand() / 2)
      .attr("dy", ".32em")
      .attr("text-anchor", "start")
      .attr("id", function(d, i) { return nodes[i].id; })
      .attr("group", function(d, i) { return nodes[i].group; })
      .attr("date", function(d, i) { return nodes[i].publishDate; })
      .attr("url", function(d, i) { return nodes[i].url; })
      .text(function(d, i) { return nodes[i].name; })
      .on("mouseover", function(d) {
        if(document.documentElement.clientWidth > 1250 && document.documentElement.clientWidth < 1268){
            minusx = 200;
          }else if(document.documentElement.clientWidth > 1267 && document.documentElement.clientWidth < 1350){
            minusx = 20;
          }
          else if(document.documentElement.clientWidth > 1349 && document.documentElement.clientWidth < 1450){
            minusx = 50;
          }else if(document.documentElement.clientWidth > 1449 && document.documentElement.clientWidth < 1550){
            minusx = 120;
          }else if(document.documentElement.clientWidth > 1549 && document.documentElement.clientWidth < 1650){
            minusx = 180;
          }else if(document.documentElement.clientWidth > 1649 && document.documentElement.clientWidth < 1750){
            minusx = 200;
          }else if(document.documentElement.clientWidth > 1749 && document.documentElement.clientWidth < 1850){
            minusx = 260;
          }else if(document.documentElement.clientWidth > 1849 && document.documentElement.clientWidth < 1980){
            minusx = 320;
          }
            div2.transition()
                .duration(200)
                .style("opacity", .9);
            div2.html("<table><tr><td><strong>ID: </strong></td><td>" + d3.select(this)[0][0].getAttribute("id") + "</td></tr><tr><td><strong>Title: </strong></td><td>" +  d3.select(this)[0][0].innerHTML + "</td></tr><tr><td><strong>Cluster: </strong></td><td>" + d3.select(this)[0][0].getAttribute("group") + "</td></tr><tr><td><strong>Published Date: </strong></td><td>" + d3.select(this)[0][0].getAttribute("date") + "</td></tr><tr><td><strong>URL: </strong></td><td>" + d3.select(this)[0][0].getAttribute("url") + "</td></tr></table>")
                .style("left", (d3.event.pageX - minusx) + "px")
                .style("top", (d3.event.pageY - 150) + "px");
        })
        .on("mouseout", function(d) {
            div2.transition()
                .duration(500)
                .style("opacity", 0)
        });

  function row(row) {
    var cell = d3.select(this).selectAll(".cell")
        .data(row.filter(function(d) { return d.z; }))
      .enter().append("rect")
        .attr("class", "cell")
        .attr("x", function(d) { return x(d.x); })
        .attr("width", x.rangeBand())
        .attr("height", x.rangeBand())
        .style("fill-opacity", 1)
        .style("fill",
          function(d) {
             var color = "#FF3333";
              // if (nodes[d.x].group == nodes[d.y].group) {
                  if (d.z >= 0 && d.z <= .19) {
                      return "#FF3333";
                  } else if (d.z >= .2 && d.z <= .39) {
                      return "#FF6633";
                  } else if (d.z >= .4 && d.z <= .59) {
                      return "#FF9966";
                  } else if (d.z >= .6 && d.z <= .79) {
                      return "#FFCC99";
                  } else if (d.z >= .8 && d.z <= 1.00) {
                      return "#FFCCCC";
                  } else if (d.z > 1) {
                      return "#FF3333";
                  }else{
                      return "#FF3333";
                  }
              // } else {
              //     return color;
              // }
          })
        .on("mouseover", function(d,i) {
          var minusx = 0;
          if(document.documentElement.clientWidth > 1250 && document.documentElement.clientWidth < 1268){
            minusx = 200;
          }else if(document.documentElement.clientWidth > 1267 && document.documentElement.clientWidth < 1350){
            minusx = 20;
          }
          else if(document.documentElement.clientWidth > 1349 && document.documentElement.clientWidth < 1450){
            minusx = 50;
          }else if(document.documentElement.clientWidth > 1449 && document.documentElement.clientWidth < 1550){
            minusx = 120;
          }else if(document.documentElement.clientWidth > 1549 && document.documentElement.clientWidth < 1650){
            minusx = 180;
          }else if(document.documentElement.clientWidth > 1649 && document.documentElement.clientWidth < 1750){
            minusx = 200;
          }else if(document.documentElement.clientWidth > 1749 && document.documentElement.clientWidth < 1850){
            minusx = 260;
          }else if(document.documentElement.clientWidth > 1849 && document.documentElement.clientWidth < 1980){
            minusx = 320;
          }
              div.transition()
                .duration(200)
                .style("opacity", .9);
              div.html("<table><tr><td><strong>ID X: </strong></td><td>" + nodes[d.x].id + "</td></tr><tr><td><strong>Title X: </strong></td><td>" + nodes[d.x].name + "</td></tr><tr><td><strong>Cluster X: </strong></td><td>" + nodes[d.x].group + "</td></tr><tr><td><strong>Published Date X: </strong></td><td>" + nodes[d.x].publishDate + "</td></tr><tr><td><strong>URL X: </strong></td><td>" + nodes[d.x].url + "</td></tr><tr><td><strong>ID Y: </strong></td><td>" + nodes[d.y].id + "</td></tr><tr><td><strong>Title Y: </strong></td><td>" + nodes[d.y].name + "</td></tr><tr><td><strong>Cluster Y: </strong></td><td>" + nodes[d.y].group + "</td></tr><tr><td><strong>Published Date Y: </strong></td><td>" + nodes[d.y].publishDate + "</td></tr><tr><td><strong>URL Y: </strong></td><td>" + nodes[d.y].url + "</td></tr></table>")
                .style("left", (d3.event.pageX - minusx) + "px")
                .style("top", (d3.event.pageY - 200) + "px");
        })
        .on("mouseout", function(d) {
            div.transition()
                .duration(500)
                .style("opacity", 0);
        }).on("click", function(d) {
            document.getElementById("urlx").href=nodes[d.x].url; 
            document.getElementById("urly").href=nodes[d.y].url; 
            document.getElementById("urlx").text=nodes[d.x].url; 
            document.getElementById("urly").text=nodes[d.y].url; 
            document.getElementById("idx").innerHTML =nodes[d.x].id; 
            document.getElementById("idy").innerHTML =nodes[d.y].id; 
            document.getElementById("titlex").innerHTML =nodes[d.x].name; 
            document.getElementById("titley").innerHTML =nodes[d.y].name;
            document.getElementById("groupx").innerHTML =nodes[d.x].group; 
            document.getElementById("groupy").innerHTML =nodes[d.y].group;
            document.getElementById("datex").innerHTML =nodes[d.x].publishDate; 
            document.getElementById("datey").innerHTML =nodes[d.y].publishDate;  
        });
  }

  function mouseover(p) {
    d3.selectAll(".row text").classed("active", function(d, i) { return i == p.y; });
    d3.selectAll(".column text").classed("active", function(d, i) { return i == p.x; });
  }

  function mouseout() {
    d3.selectAll("text").classed("active", false);
  }

  d3.select("#order").on("change", function() {
    clearTimeout(timeout);
    order(this.value);
  });

  function order(value) {
    x.domain(orders[value]);

    var t = svg.transition().duration(2500);

    t.selectAll(".row")
        .delay(function(d, i) { return x(i) * 4; })
        .attr("transform", function(d, i) { return "translate(0," + x(i) + ")"; })
      .selectAll(".cell")
        .delay(function(d) { return x(d.x) * 4; })
        .attr("x", function(d) { return x(d.x); });

    t.selectAll(".column")
        .delay(function(d, i) { return x(i) * 4; })
        .attr("transform", function(d, i) { return "translate(" + x(i) + ")rotate(-90)"; });
  }

  var timeout = setTimeout(function() {
    order("group");
    d3.select("#order").property("selectedIndex", 2).node().focus();
  }, 5000);
});

</script>

