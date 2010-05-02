<ul id="cat_left"><!-- в админке вместе с названием прикрепляется картинка прописаная в стилях тега ЛИ  url(/img/cat1.gif) для каждой категори своя картинка можно загрузить новую для заводимой категори можно выбрать из списка уже заведенных-->
   
   <?php 
   
$cats=   $this->db->get('catalog_cats');
   foreach ($cats->result() as $cat){
   
   
   
    echo '<li style="list-style-image: url(/'.$cat->img.')"><a href="/content/show_page/cat_cat/'.$cat->id.'.html">'.$cat->name.'</a></li>';
   }
    ?>
</ul>
