<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?if(!@$winTitle){$winTitle="Buratino-Shop. Russische Lebensmittel:".$title;}?>

<title><?=$winTitle?></title>


<meta name="robots" content="index, follow" />

<meta name="author and developer" content="G.Grigorian" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="/style/style_shop.css" rel="stylesheet"  type="text/css" />

<link href="/style/suckerfish.css" rel="stylesheet"  type="text/css" />
<link href="/style/template_css.css" rel="stylesheet"  type="text/css" />
<link href="/style/style1.css" rel="stylesheet"  type="text/css" />
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/highslide-with-html.packed.js"></script>
<link rel="stylesheet" type="text/css" media="screen, projection" href="/style/gcart.css" />

<link rel="stylesheet" type="text/css" href="/style/colorbox.css" />
<link href="/style/suckerfish.css" rel="stylesheet"  type="text/css" />
<link href="/style/template_css.css" rel="stylesheet"  type="text/css" />
<link href="/style/style1.css" rel="stylesheet"  type="text/css" />
  <script type="text/javascript" src="/js/simple.carousel.0.1.min_.js"></script>
  <script type="text/javascript" src="/js/uc_cart.js"></script>
  <script src="/js/jquery.validate.js" type="text/javascript"></script>
   <script src="/js/validate_cart.js" type="text/javascript"></script>
  
  

<!--[if IE 6]>
<style type="text/css">
img { behavior: url(/style/iepngfix.htc); }


div#flashwrap {
background: none;
filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="/img/icon_shop.png", sizingMethod="crop");}

</style>
<link href="/style/ie6_css.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if IE 7]>
<link href="/style/ie7_css.css" rel="stylesheet" type="text/css" />
<![endif]-->

<script language="javascript" type="text/javascript">


hs.graphicsDir = '/js/highslide/graphics/';
hs.showCredits = false;
hs.loadingText = 'Загрузка...';
hs.loadingTitle = 'Нажмите для отмены';
hs.restoreTitle = 'Удерживая левую кнопку мыши, можно перетаскивать изображение.';
hs.outlineWhileAnimating = true;
hs.outlineType = 'drop-shadow';
hs.marginLeft = 0;
hs.marginRight = 0;
hs.marginTop = 0;
hs.marginBottom = 0;
hs.minWidth = 500;
hs.minHeight = 480;
hs.maxHeight = 680;





</script>
<script type="text/javascript" src="/js/jquery.colorbox.js"></script>

		<script type="text/javascript">
			$(document).ready(function(){
				//Examples of how to assign the ColorBox event to elements
			$("a[rel='example4']").colorbox({slideshow:false});

				$("#click").click(function(){
					$('#click').css({"background-color":"#f00", "color":"#fff", "cursor":"inherit"}).text("Open this window again and this message will still be here.");
					return false;
				});
			});
		</script>


<script language="javascript" type="text/javascript">

function copyAddress() {
  

  // Hide the target information fields.
  $('#copyAdres').slideToggle("slow");
  

}


function addToCart(prod_id,token) {
	// we want to store the values from the form input box, then send via ajax below
	var qty     = $('#qty_'+prod_id).attr('value');

	//var token     = $('#token').attr('value');
	
	//var lname     = $('#lname').attr('value');
		$.ajax({
			type: "POST",
			url: "/shop/add_to_cart/",
			data: "prod_id="+ prod_id +"&token="+ token+"&qty="+ qty,
		
			success: function(data){
		
		
		
		
			    if(data==='OK'){
				
				var new_cart=$.post();


$.post("/shop/show_top_cart/", function(new_cart){
  $("#korzinka").html(new_cart);

 });



	$("#internal_add-to-cart_"+prod_id).fadeOut("slow");
	$("#add-to-cart_"+prod_id).html("Danke :) Die Ware ist im Warenkorb :) <img src='/img/basket_remove' src='12'>");

//$("#korzinka").fadeOut("slow");

				
				
				
				
					
				}
				else{
					
					
					alert(data);
				}


			}
		});




	}



	

</script>

<style type="text/css">
	pre { text-align: left; }
</style>



</head>

<body id="bg">
<div id="pagewrap">
        <div id="topwrap">
                <a href="./" id="logo"></a>
                                <div id="flashwrap">
                        <div>
                <a class="mainlevel" href="/shop/show_cart/">
                Show Cart</a>
			
            <div id="korzinka"><br /><?=$this->shop_model->show_top_basket();?></div>    

</div>
                </div>
                                <!--topwrap-->
        </div>
        <div id="menu">
                <div id="horiz-menu">
                        <script type="text/javascript">
<!--//--><![CDATA[//><!--

sfHover = function() {
	var sfEls = document.getElementById("horiznav").getElementsByTagName("LI");
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" sfHover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" sfHover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", sfHover);

//--><!]]>
</script>


<?=$this->load->view('main_menu');?>

         </div>
        </div>
        <div id="contentwrap">
                <div id="l_w-1">
                                                <!--leftblock-->
                        <div id="conwrap">
                                                                <div id="r_w1-2">
                                                                                                                        <div id="pathway">

                                                                                       </div>
                                        <div id="mainbody">
                                                			



























<script>

function deleteBasketItem(row_id){

	$.ajax({
		type: "POST",
		url: "/shop/remove_from_cart_rowid/"+row_id,
		
		success: function(data){
	
	if(data==='OK'){

		$("#"+row_id).fadeOut("slow");

		$.get('/shop/show_total.html', function(total) {
			  $('#total').html(total);
			  
			});


		}
	else{
	
		alert(data);    


		}
	}
	});


	




	
}





</script>
			<div id="wrapper">
				<h2><?=$title?></h2>

				<div id="sidebar">
				</div>


<div id="content">
<?=$message?>













		                                        </div>
                                                                                                                        <!--prawa zagniezdzona -->
                                </div>

                                <!--conwrap -->
                        </div>
                </div>
                                <div id="r_w">
                        				                </div>
                                <!--contentwrap-->
                        </div>
         <!--pagewrap-->
        <div id="footer">     
    
<div id="upinfo">Shop-Hotline: 0676 510 29</div> 

    
<!-- Footer Hyperlinks -->
<div id="links">
	

  </div><!-- /block-inner -->

	
	</div>

<!-- Copyright Information -->
<div id="copyright">Development and Support &copy; <a href="http://www.gevork.ru" target="_blank">G.Grigorian</a>. All rights reserved.</div>
     </div>
</div>
</body>
</html>
