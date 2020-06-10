<?php 

require __DIR__ . '/vendor/autoload.php';

require "inc/config.inc.php";
require 'inc/idena.class.php';
require 'inc/idenary.class.php';

$idena = new IdenaAuth($CONFIG);
$idenary = new Idenary($CONFIG);

$user_address = $idena->is_auth();

if ($user_address=='') { 
  header('Location: '.$CONFIG["logout_url"]);
  die();
  
}


$token = $_SESSION["idena_token"];
$status = $idenary->get_address_status($user_address);

$square_size=15; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Idenary - Express your humanity !</title>
  <meta content="" name="descriptison">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets2/img/favicon.png" rel="icon">
  <link href="assets2/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets2/vendor/icofont/icofont.min.css" rel="stylesheet">
  <link href="assets2/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets2/vendor/owl.carousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="assets2/vendor/venobox/venobox.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets2/css/style.css" rel="stylesheet">

	<style>
        #grid {
        	min-width: 1108px;
        	width: auto;
        }
        
        .row2 {
        	display: block;
        	clear: left;
        	height: <?=($square_size) ?>px;
        	padding: 0px;
        	margin: 0;
        }
        
        [owner='<?=$user_address?>'] {
        	border: 0;
        }
        
        .square {
        	display: block;
        	float: left;
        	height: <?=$square_size ?>px;
        	width: <?=$square_size ?>px;
        	background-color: #ddd;
        	line-height: 1;
        	box-sizing: content-box;
        	color: #000;
        	padding: 0;
        	margin: 0;
        	border: 0;
        	font-size: <?=(1.8*$square_size)/30 ?>em;
         /* 2x */;
        }
        
        .border > div > .square {
        	border: 1px solid #aaa;
        }
        
        .border > .row2 {
        	height: <?=($square_size+2) ?>px;
        }
        
        .border > div > [owner='<?=$user_address?>'] {
        	border: 1px solid #F00;
        }
        
        .big-square {
        	font-size: 3em;
        	height: 50px;
        	width: 50px;
        }
        
        .clickable {
        	cursor: pointer;
        }
        
        .ico-fade {
        	-webkit-animation: icofont-fade 1s infinite steps(8);
        	animation: icofont-fade 1s infinite steps(8);
        	display: inline-block;
        }
	
	</style>
  
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top">
    <div class="container d-flex align-items-center">

      <h1 class="logo mr-auto"><a href="index.html">idenary</a></h1>

      <nav class="nav-menu d-none d-lg-block">
        <ul>
          <li class="active"><a href="/">Home</a></li>
          <li><a href="/logout.php" title="logout">Logout</a></li>
          <li><a href="#faq">FAQ</a></li>


        </ul>
      </nav><!-- .nav-menu -->

    </div>
  </header><!-- End Header -->  
  
  
<main id="main" style="margin-top:50px;">
  
  
  <section id="about" class=" section-bg">
      <div class="container">
        <div class="text-center">
          <h2>Hello <?=$user_address?><br/>Your status is <?=$status?></h2>
        </div>
      </div>
  </section>
  

  <section id="draw" class="draw">
      <div class="container" style="overflow:auto;">

          
          <div id="palette" class="border">
            
            <div id="palette-0" class="row2"><div data-value="eraser" class="square big-square clickable palette_square_0"><i class="icofont-eraser"></i></div></div>
            <div id="palette-1" class="row2"></div>
            <div id="palette-2" class="row2"></div>
            <div id="palette-3" class="row2"></div>

            <div class="row">
              
              <div class="col-md-3">
                 <div  class="">
                   <div class="square big-square" id="current_square" style="background-color:#FFFFFF" ><i id="current_square_i" class="icofont-" style="color:#000000"></i></div>&nbsp;Current drawing tool
                </div>
              </div>
              <div class="col-md-3" style="height:30px;">
                  &nbsp;<span id="credits">?</span> squares remaining 
              </div>
              <div class="col-md-3">
                <button id="toggle_grid" class="btn">Toggle borders</button>
              </div>

            </div>

          </div>
          
          <div id="grid" class="border"></div>
          
      </div>
  </section>



    <!-- ======= Frequently Asked Questions Section ======= -->
    <section id="faq" class="faq section-bg">
      <div class="container">

        <div class="section-title">
          <h2>Frequently Asked Questions</h2>
          <p>Idenary is just born, and is willing to bring some freshness and disruption. This section will be fed with your most itching concerns.</p>
        </div>

        <div class="faq-list">
          <ul>
            <li data-aos="fade-up">
              <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" class="collapse" href="#faq-list-1">Website seems a bit crude, why are you launching in an early state?<i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-1" class="collapse show" data-parent=".faq-list">
                <p>
                  Idenary is a live project, that is born to evolve rather than be perfectly polished.<br/>The Idena hackathon was the sparkle that triggered it all. Although the state of the current website is not as good as what we'd like to show, we had to launch and release now to be in time for the hackathon deadline.<br />
                  This was an epic rush to assemble everything in due time, so we count it as a success anyway!.<br />
                  We have way more in store for you, this is just the beginning!
                </p>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="100">
              <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-2" class="collapsed">How to sign-in? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-2" class="collapse" data-parent=".faq-list">
                <p>
                  You need an Idena id to sign in. No personal information is required. No email, no login: just the idena app that will give you an address and access to the "sign in with Idena" feature.<br />
                  Please head over to the <a href="https://idena.io">Official idena website</a> and install the client.
                </p>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="200">
              <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-3" class="collapsed">I can sign in, but I can't do anything? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-3" class="collapse" data-parent=".faq-list">
                <p>
                  Every idena identity class allows for different actions and quotas. If you did not pass an Idena validation session yet, you can look but not touch :)<br />
                  "Newbie" is the minimum state to interact.
                </p>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="200">
              <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-4" class="collapsed">Take my money! <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-4" class="collapse" data-parent=".faq-list">
                <p>
                  Sure, we do accept donations<br />
                  DNA 0xcb433bdcf16510935a7dedbdefa9a2254cf61b25<br/>
                  ETH 0x12bf3bfbA5D34c36D6F48aafB64c26F30e59d02A<br/>
                </p>
              </div>
            </li>


          </ul>
        </div>

      </div>
    </section><!-- End Frequently Asked Questions Section -->


  </main><!-- End #main -->


  <!-- ======= Footer ======= -->
  <footer id="footer">

    <div class="footer-top">
      <div class="container">
        <div class="row">

          <div class="col-lg-12 col-md-12 foo-ter-contact text-center">
             <h3>We have many items on our roadmap and are open to suggests!</h3>
          </div>  
        </div>
      </div>
    </div>

    <div class="container d-md-flex py-4">
      <div class="mr-md-auto text-center text-md-left">
        <div class="copyright">
          &copy; Copyright <strong><span>IDENARY</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
          Base design by <a href="https://bootstrapmade.com/" target="_blank" rel="nofollow" style="color:#fff">BootstrapMade</a>
        </div>
      </div>
      <div class="social-links text-center text-md-right pt-3 pt-md-0">
        <a href="https://twitter.com/idenary_com" target="_blank" class="twitter"><i class="bx bxl-twitter"></i></a>
        <a href="https://discord.gg/GAbu57d" rel="nofollow" target="_blank" class=""><i class="bx bxl-discord"></i></a>
        <a href="https://github.com/Idenary/idenary.com"  target="_blank" class=""><i class="bx bxl-github"></i></a>
      </div>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top"><i class="bx bx-up-arrow-alt"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets2/vendor/jquery/jquery.min.js"></script>
  <script src="assets2/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets2/vendor/jquery.easing/jquery.easing.min.js"></script>
  <!-- script src="assets2/vendor/php-email-form/validate.js"></script -->
  <script src="assets2/vendor/waypoints/jquery.waypoints.min.js"></script>
  <script src="assets2/vendor/counterup/counterup.min.js"></script>
  <script src="assets2/vendor/owl.carousel/owl.carousel.min.js"></script>
  <script src="assets2/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets2/vendor/venobox/venobox.min.js"></script>

  <!-- Template Main JS File -->
  <script src="assets2/js/main.js"></script>


<script>
	<?php $palette = $idenary->addressPalette($user_address);?>
	
	var data = <?=json_encode($grid) ?>;
var palette_allowed = <?=json_encode($palette) ?>;
var current_square = {
	"icon": "",
	"bgcolor": "FFFFFF",
	"color": "000000",
	"rotate": 0
};
var user_address = "<?=$user_address ?>";
var user_token = "<?=$token ?>";
var width = <?=$square_size ?>;
var height = width;

function update_current(el, type) {
	value = el.data("value");
	current_square[type] = value;
	if(type == "icon") {
		$("#current_square_i").attr('class', "icofont-" + value);
	}
	if(type == "color") {
		$("#current_square_i").css('color', "#" + value);
	}
	if(type == "bgcolor") {
		$("#current_square").css('background-color', "#" + value);
	}
	if(type == "rotate") {
		$("#current_square").css('rotate', "" + value);
	}
}

function update_palette() {
	palette_allowed.forEach(function(r) {
		square = '<div data-value="' + r["data"] + '" class="square big-square clickable palette_square_' + r["type"] + '"';
		if(r["type"] == "2") {
			square = square + 'style="background-color:#' + r["data"] + '"';
		}
		square = square + '><i class="icofont-';
		if(r["type"] == "0") {
			square = square + r["data"];
		}
		if(r["type"] == "1") {
			square = square + "pencil-alt-2";
		}
		square = square + '"';
		if(r["type"] == "1") {
			square = square + 'style="color:#' + r["data"] + '"';
		}
		square = square + '></i></div>';
		$("#palette-" + r["type"]).append(square);
	});
	$(".palette_square_0").click(function() {
		update_current($(this), 'icon')
	});
	$(".palette_square_1").click(function() {
		update_current($(this), 'color')
	});
	$(".palette_square_2").click(function() {
		update_current($(this), 'bgcolor')
	});
	$(".palette_square_3").click(function() {
		update_current($(this), 'rotate')
	});
};
update_palette();
$("#toggle_grid").click(function() {
	$("#grid").toggleClass("border");
});

function init_board(data) {
	var row = null;
	data.forEach(function(square) {
		if(parseInt(square["id"]) % 64 == 0) {
			row = $("<div class='row2'></div>");
			$("#grid").append(row);
		}
		s = '<div class="square board_square clickable';
		if(square.owner == "" || square.owner == user_address) {
			s = s + "clickable";
		}
		s = s + '" id="' + square.id + '" style="background-color:#' + square.bgcolor + '" owner="' + square.address + '"><i class="icofont-' + square.item + '" style="color:#' + square.color + '"></i></div>';
		row.append(s);
	});
	$(".board_square").click(function() {
		console.log($(this));
		data = {};
		data["id"] = $(this).attr('id');
		data["color"] = current_square["color"];
		data["bgcolor"] = current_square["bgcolor"];
		data["rotate"] = current_square["rotate"];
		data["item"] = current_square["icon"];
		send("Paint", data);
	});
};

function paint(data) {
	square = $("#" + data["id"]);
	square.children().attr('class', "icofont-" + data["item"]);
	square.children().css('color', "#" + data["color"]);
	square.css('background-color', "#" + data["bgcolor"]);
	square.attr("owner", data["address"]);
	//square.css('rotate', ""+value);
}

function update_credits(credits) {
	$("#credits").html(credits);
}
url = "wss://idenary.com/wss/draw"
var ws = new WebSocket(url);

function send(action, data) {
	console.log(data);
	ws.send(JSON.stringify({
		Action: action,
		Data: data
	}));
}
ws.onopen = function() {
	send("Register", user_token);
};
ws.onmessage = function(evt) {
	message = JSON.parse(evt.data);
	console.log(message);
	event = message["Event"];
	data = message["Data"];
	if(event == "Init") {
		init_board(data["Grid"]);
	} else if(event == "Paint") {
		paint(data);
	} else if(event == "Credits") {
		update_credits(data);
	}
};
 
</script>
</body>
</html>