<?php include(__DIR__."/_header.php"); ?>

<?php function css_section(){  ?>
  <link rel="stylesheet" href="https://npmcdn.com/leaflet@0.7.7/dist/leaflet.css" />

<?php } ?>

<script>
  window.points = JSON.parse(<?=json_encode($points)?>);
  window.type = (<?=json_encode($fiddle_type)?>) || 0;
  if(window.points.push == null){
    window.points = [];
  }
</script>
<div id="container" class="container">
	<h1 style="text-align:center;"> Create your marker immediately. </h1>
  
  <p>
    Marker Name: <input type="text" name="title" value="<?=h($fiddle_title)?>" />
      <button class="js-saveOrUpdate btn btn-default">Save</button>
      <!-- <button class="js-fork btn btn-default">Fork</button> -->

  </p>

  <div id="mapid" style="width: 100%; height: 600px"></div>
  
  
  <div id='control' data-points="" style="z-index: 1000;position:fixed;padding:10px;left:30px;top:140px;background:#EEEEEE;border-radius:5px;opacity:0.9;min-width:340px;">
    
    <div class="header">
      <a class="js-close-control" style='float:right;font-size:20px;text-decoration: none;' href="">－</a>
      <button data-type="0" class="js-type btn btn-default">Point (點) </button>
      <button data-type="1" class="js-type btn btn-default">Line (線) </button>
      <button data-type="2" class="js-type btn btn-default ">Area (區) </button>
      
      <div style="clear:both"></div>
    </div>

    <div class="body" style="margin-top:10px;">
      <p>
        <input name="search" type="text" /> <button class="js-search-btn btn btn-default" >搜尋地址</button>
        <div id="search-result"></div>
      </p>
      <ul class="points" style="margin-top:10px;text-align:left;">
       
      </ul>
      <a class="js-close btn btn">Close Line/Area</a>
      <br />
      <a class="js-clear btn btn">Clear all points</a>
      
      <p id="status"></p>
      <hr />
      Power by <a href="https://github.com/tony1223/" target="_blank">TonyQ</a>
    </div>
  </div>


  <script src="https://npmcdn.com/leaflet@0.7.7/dist/leaflet.js"></script>
  <script   src="https://code.jquery.com/jquery-2.2.4.min.js"   integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="   crossorigin="anonymous"></script> 

  <script>

    var map = {
      map:null,
      type: window.type,
      mode:null,
      insert_index:null,
      points:window.points,
      currentMarkers:[],
      tips:[],
      tipMakers:[],
      changeType:function(type){
        if(type == 0){
          if(this.points.length > 1){

            if(confirm("only one marker in point be allowed,others will be deleted,are you sure?")){
             this.points = [this.points[0]];
            }
          }
        }
        this.mode = null;
        this.insert_index = null;
        this.type = type;        
        //TODO: handle type change 
        this.render();
      },
      handleClick:function(latlng){
        var newitem = {latlng:latlng};

        if(this.type == 0){
          this.points =[ newitem ];
        }else{
        
          if(this.mode == "insert"){
            this.points.splice(this.insert_index+1, 0,newitem);
            this.mode = null;
            this.insert_index = null;
          }else{
            this.points.push(newitem);
          }
          

        }


        this.render();
      },
      bind:function(map){
        this.map = map;
      },
      closeLineOrArea:function(){
        this.points.push(this.points[0]);
        this.render();
      },
      setInsertMode:function(ind){
        this.mode = "insert";
        this.insert_index = ind;
        this.render();
      },
      cancelInsertMode:function(){
        this.mode = null;
        this.insert_index = null;
        this.render();
      },
      removePoint:function(ind){
        this.points.splice(ind, 1);
        this.render();
      },
      clearPoints:function(){
        this.points = [];
        this.render();
      },
      setCenter:function(latlng){
        latlng && this.map.panTo(new L.LatLng(latlng[0],latlng[1]));
      },
      setTips:function(tips){
        this.tips = tips;
        this.render();
      },
      render:function(){
        var that= this;
        this.currentMarkers.forEach(function(m){
          that.map.removeLayer(m);
        });
        this.currentMarkers = []; 

        this.tipMakers.forEach(function(m){
          that.map.removeLayer(m);
        });
        this.tips.forEach(function(p){
          var marker = L.circle(p.latlng,30,{
              color: 'red',
              fillColor: '#f03',
              fillOpacity: 0.5
          }).addTo(that.map);

          marker.bindPopup(p.address);
          that.currentMarkers.push(marker);
        });

        if(this.type == 0 || this.points.length == 1){
          this.points.forEach(function(p){
            var marker = L.marker(p.latlng).addTo(that.map);
            that.currentMarkers.push(marker);
          });
        }else if(this.type == 1){
          var latlngs = this.points.map(function(p){ return p.latlng ;});
          var polyline = L.polyline(latlngs, {color: 'red'}).addTo(that.map);
          that.currentMarkers.push(polyline);
        }else if(this.type == 2){
          var latlngs = this.points.map(function(p){ return p.latlng ;});
          var polyline = L.polygon(latlngs, {color: 'red'}).addTo(that.map);
          that.currentMarkers.push(polyline);
        }

        if(this.mode =="insert"){
          var marker = L.marker(this.points[this.insert_index].latlng).addTo(that.map);
          that.currentMarkers.push(marker);
          if(this.points[this.insert_index+1]){
            marker = L.marker(this.points[this.insert_index+1].latlng).addTo(that.map);
            that.currentMarkers.push(marker);
          }
        }

        if((this.type == 1) && this.points.length > 1){
          $(".js-close").show();
        }else{
          $(".js-close").hide();
        }
        $(".points .point").remove();

        var out=[];
        this.points.map(function(p,ind){
          out.push("<li> "+ Math.floor(p.latlng.lat *100000)/100000 +","+ Math.floor(p.latlng.lng *100000)/100000 );
          out.push(" &nbsp;&nbsp;<a style='float:right;' class='js-item-delete' href='#' data-index='" +ind+ "'>delete</a> ");
          
          if(ind != that.points.length -1){
            out.push("&nbsp;&nbsp;<a style='float:right;margin-right:10px;' class='js-item-insert' href='#' data-index='" +ind+ "'>insert</a> ");
          }

          out.push(" </li>");
        });
        $(".points").html(out.join(""));

        if(this.mode){
          $("#status").html("Mode:"+this.mode + " <a href='#' class='js-cancel-insert'>Cancel</a>");
        }else{
          $("#status").html("");
        }

        $(".js-type").each(function(){
          if($(this).data("type") == that.type){
            $(this).addClass("btn-primary");    
          }else{
            $(this).removeClass("btn-primary");
          }
        });
      }
    };

    $(".js-type").on("click",function(){
      var type = $(this).data("type");

      map.changeType(type);
    });

    $(".js-close").on("click",function(e){
      map.closeLineOrArea();
      e.preventDefault();
    });
    
    $(".js-clear").on("click",function(e){
      map.clearPoints();
      e.preventDefault();
    });

    $(".points").on("click",".js-item-delete",function(){
      var ind = $(this).data("index");
      map.removePoint(ind);

    });

    $(".points").on("click",".js-item-insert",function(){
      var ind = $(this).data("index");
      map.setInsertMode(ind);
    });

    $("#status").on("click",".js-cancel-insert",function(){
      map.cancelInsertMode();
    });
    
    $(".js-saveOrUpdate").click(function(){
      if(map.points.length == 0){
        alert("At least one point. (至少要設定一個座標點)");
        return false;
      }
      $.post("/api/saveOrUpdate/",{
        pointers:JSON.stringify(map.points),
        title:$("[name=title]").val(),
        type:map.type
      },function(res){
        if(!res.isSuccess){
          alert("save fail");
          return true;
        }
        self.location.href='/marker/'+ res.data.key;
      })
    });


    var searchGEOCoding = function(){
      var search = $("[name=search]").val();

      $.get("https://maps.googleapis.com/maps/api/geocode/json",{
        address:$("[name=search]").val(),
        key:"AIzaSyBSIFJslwcgjr4ttFgt0TX3KSG6sqLkzY8"
      },function(res){
        if(res.status != "OK"){
          alert("search fail");
          return true;
        }

        var items = res.results.slice(0,3).map(function(r){
          return {
            latlng:[r.geometry.location.lat,r.geometry.location.lng],
            address:r.formatted_address
          };
        });

        map.setTips(items);

        var out = items.map(function(i){
          return "<li><a  class='js-search-item' data-latlng='"+i.latlng[0]+","+i.latlng[1]+"' href='#'>"+i.address+"</a></li>"
        });
        $("#search-result").html("<ul>"+out.join("")+"</ul>");

        var jqs = $(".js-search-item");
        if(jqs.length){
          jqs[0].click();
        }
      });
    }
    $("[name=search]").on("keyup",function(e){
      if(e.keyCode == 13){ 
        searchGEOCoding();
      }
    });

    $(".js-search-btn").click(function(){
      searchGEOCoding();
    });

    $("#search-result").on("click",".js-search-item",function(e){
      map.setCenter($(this).data("latlng").split(","));
      e.preventDefault();
    });

    $(".js-close-control").on("click",function(){
      var close = $(".js-close-control").html() =="－";
      if(close){
        $("#control .body").hide();        
        $(".js-close-control").html("＋");
      }else{
        $("#control .body").show();        
        $(".js-close-control").html("－");
      }
    });
  </script>
  <script>

    var center = map.points.length == 0 ? [25.043325,121.5195076] : map.points[0].latlng;

    var mymap = L.map('mapid').setView(center, 15);

    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw', {
      maxZoom: 18,
      attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
        '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
        'Imagery © <a href="http://mapbox.com">Mapbox</a>',
      id: 'mapbox.streets'
    }).addTo(mymap);

    map.bind(mymap);
    mymap.on("click",function(e){
      map.handleClick(e.latlng);
    });
    map.render();

  </script>
  
</div>

<?php function js_section(){ ?>

<?php } ?>


<?php include(__DIR__."/_footer.php"); ?>