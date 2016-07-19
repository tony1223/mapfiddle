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

  
  <div id='control' data-points="" style="position:fixed;padding:10px;left:30px;top:140px;background:white;border-radius:5px;opacity:0.9;min-width:340px;">
    <button data-type="0" class="js-type btn btn-primary">Point (點) </button>
    <button data-type="1" class="js-type btn btn-default">Line (線) </button>
    <button data-type="2" class="js-type btn btn-default ">Area (區) </button>
    <ul class="points" style="margin-top:10px;text-align:left;">
     
    </ul>
    <button class="js-close btn btn">Close Line/Area</button>
    
    <p id="status"></p>

    <hr />
    Power by <a href="https://github.com/tony1223/" target="_blank">TonyQ</a>
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
      render:function(){
        var that= this;
        this.currentMarkers.forEach(function(m){
          that.map.removeLayer(m);
        });
        this.currentMarkers = [];

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

    $(".js-close").on("click",function(){
      map.closeLineOrArea();
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