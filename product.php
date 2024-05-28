<?php 
   require_once('header.php'); 
   include('selector.inc');
   ?>
<?php
   if(!isset($_REQUEST['id'])) {
       header('location: index.php');
       exit;
   } else {
       // Check the id is valid or not
       $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
       $statement->execute(array($_REQUEST['id']));
       $total = $statement->rowCount();
       $result = $statement->fetchAll(PDO::FETCH_ASSOC);
       if( $total == 0 ) {
           header('location: index.php');
           exit;
       }
   }
   $sofsku = null;
   $dmsku = null;
   $wmsku = null;
   
   foreach($result as $row) {
       $p_name = $row['p_name'];
       $p_old_price = $row['p_old_price'];
       $p_current_price = $row['p_current_price'];
       $p_qty = $row['p_qty'];
       $p_featured_photo = $row['p_featured_photo'];
       $p_description = $row['p_description'];
       $p_short_description = $row['p_short_description'];
       $p_feature = $row['p_feature'];
       $p_condition = $row['p_condition'];
       $p_return_policy = $row['p_return_policy'];
       $p_total_view = $row['p_total_view'];
       $p_is_featured = $row['p_is_featured'];
       $p_is_active = $row['p_is_active'];
       $ecat_id = $row['ecat_id'];
       $sofsku = $row['sof_sku'];
       $dmsku = $row['dm_sku'];
       $wmsku = $row['wm_sku'];
   }
   if(!$sofsku || !$dmsku || !$wmsku){
       header('location: index.php');
       exit;
   }
   // Getting all categories name for breadcrumb
   $statement = $pdo->prepare("SELECT t1.ecat_id, t1.ecat_name, t1.mcat_id, t2.mcat_id, t2.mcat_name, t2.tcat_id, t3.tcat_id, t3.tcat_name FROM tbl_end_category t1 JOIN tbl_mid_category t2 ON t1.mcat_id = t2.mcat_id JOIN tbl_top_category t3 ON t2.tcat_id = t3.tcat_id WHERE t1.ecat_id=?");
   $statement->execute(array($ecat_id));
   $total = $statement->rowCount();
   $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
   foreach ($result as $row) {
       $ecat_name = $row['ecat_name'];
       $mcat_id = $row['mcat_id'];
       $mcat_name = $row['mcat_name'];
       $tcat_id = $row['tcat_id'];
       $tcat_name = $row['tcat_name'];
   }
   
   
   $p_total_view = $p_total_view + 1;
   
   $statement = $pdo->prepare("UPDATE tbl_product SET p_total_view=? WHERE p_id=?");
   $statement->execute(array($p_total_view,$_REQUEST['id']));
   
   
   if(isset($_POST['form_review'])) {
       
       $statement = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=? AND cust_id=?");
       $statement->execute(array($_REQUEST['id'],$_SESSION['customer']['cust_id']));
       $total = $statement->rowCount();
       
       if($total) {
           $error_message = LANG_VALUE_68; 
       } else {
           $statement = $pdo->prepare("INSERT INTO tbl_rating (p_id,cust_id,comment,rating) VALUES (?,?,?,?)");
           $statement->execute(array($_REQUEST['id'],$_SESSION['customer']['cust_id'],$_POST['comment'],$_POST['rating']));
           $success_message = LANG_VALUE_163;    
       }
       
   }
   
   // Getting the average rating for this product
   $t_rating = 0;
   $statement = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=?");
   $statement->execute(array($_REQUEST['id']));
   $tot_rating = $statement->rowCount();
   if($tot_rating == 0) {
       $avg_rating = 0;
   } else {
       $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
       foreach ($result as $row) {
           $t_rating = $t_rating + $row['rating'];
       }
       $avg_rating = $t_rating / $tot_rating;
   }
   ?>
<?php
   if($error_message1 != '') {
       echo "<script>alert('".$error_message1."')</script>";
   }
   if($success_message1 != '') {
       echo "<script>alert('".$success_message1."')</script>";
       header('location: product.php?id='.$_REQUEST['id']);
   }
   ?>
<div class="page">
<div class="container">
   <div class="row">
      <div class="col-md-12">
         <div class="breadcrumb mb_30">
            <ul>
               <li><a href="<?php echo BASE_URL; ?>">Home</a></li>
               <li>></li>
               <li><a href="<?php echo BASE_URL.'product-category.php?id='.$tcat_id.'&type=top-category' ?>"><?php echo $tcat_name; ?></a></li>
               <li>></li>
               <li><a href="<?php echo BASE_URL.'product-category.php?id='.$mcat_id.'&type=mid-category' ?>"><?php echo $mcat_name; ?></a></li>
               <li>></li>
               <li><a href="<?php echo BASE_URL.'product-category.php?id='.$ecat_id.'&type=end-category' ?>"><?php echo $ecat_name; ?></a></li>
               <li>></li>
               <li><?php echo $p_name; ?></li>
            </ul>
         </div>
         <!- DESIGN TO DISPLAY LOWEST PRICE PRODUCT -->
         <?php
            $sofresult = getSofItem($sofsku);
            $dmresult = getDmItem($dmsku);
            $wmresult = getWmItem($wmsku);

            $p1 = trim($sofresult['price'],'$');
            $p2 = trim($dmresult['price'],'$');
            $p3 = trim($wmresult['price'],'$');
            
            $lowestPrice = min($p1,$p2,$p3);
            $lname = $lprice = null;
            if ($lowestPrice === $p1) {
                $lname = $ressofresult['name'];
                $logoPath = $base_url . "/assets/img/sof1.png";
            } elseif ($lowestPrice === $p2) {
                $lname = $dmresult['name'];
                $logoPath = $base_url . "/assets/img/desimandi.png";
            } elseif ($lowestPrice === $p3){
                $lname = $wmresult['name'];
                $logoPath = $base_url . "/assets/img/walmart.png";
            }
            ?>
         <!-- END OF CODE TO FIND LOWEST PRICE -->
         <!- DESIGN TO DISPLAY LOWEST PRICE PRODUCT -->
         <div class="container blinking" style="text-align: center;">
            <div class="product-info">
               <!-- Logo -->
               <img src="<?php echo $logoPath; ?>" alt="Logo" class="logo">
               <!-- Product Name -->
               <h3><?php echo $lname; ?></h3>
               <!-- Product Price -->
               <h4><?php echo '$'. $lowestPrice; ?></h4>
            </div>
         </div>
         <!--END OF DESIGN TO DISPLAY LOWEST PRICE PRODUCT -->
         <div class="product">
            <div class="row">
               <div class="col-md-12">
                  <div class="col-md-4 item item-product-cat sof">
                     <div id="corner-triangle">
                        <div class="corner-triangle-text text-capitalize"><a target="_blank" href="https://www.saveonfoods.com/" target="_blank"><img src="<?php echo $base_url; ?>/assets/img/sof1.png" style="position: absolute;"></a></div>
                     </div>
                     <div class="inner">
                        <div class="thumb" style="position: relative;">
                           <div class="photo" style="background-image:url(<?php echo isset($sofresult['primaryImage']['default']) ? $sofresult['primaryImage']['default'] : '' ?>); "></div>
                           <div class="overlay"></div>
                        </div>
                        <div class="text">
                           <h3><a target="_blank" href="<?= $sofresult['link'] ?>"><?= $sofresult['name'] ?></a></h3>
                           <h4>
                              <?= $sofresult['price'] ?>
                           </h4>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-4 item item-product-cat dm">
                     <?php 
                        if ($dmresult['price']) {
                            ?>
                     <div class="inner">
                        <div id="corner-triangle">
                           <div class="corner-triangle-text text-capitalize"><a target="_blank" href="https://www.desimandi.ca/" target="_blank"><img src="<?php echo $base_url; ?>/assets/img/desimandi.png" ></a></div>
                        </div>
                        <div class="thumb"style="position: relative;">
                           <div class="photo" style="background-image:url(<?php echo isset($dmresult['img_src']) ? trim($dmresult['img_src'],"`") : '' ?>);">
                           </div>
                           <div class="overlay"></div>
                        </div>
                        <div class="text">
                           <h3><a target="_blank" href="<?= $dmresult['link'] ?>"><?= $dmresult['name'] ?></a></h3>
                           <h4><?= $dmresult['price'] ?></h4>
                        </div>
                     </div>
                     <?php
                        }
                        ?>
                  </div>
                  <div class="col-md-4 item item-product-cat wm">
                     <?php 
                        if ($wmresult['price']) {
                            ?>
                     <div id="corner-triangle">
                        <div class="corner-triangle-text text-capitalize"><a target="_blank" href="https://www.walmart.ca/" target="_blank"><img src="https://www.dumaschamber.com/media/com_mtree/images/listings/o/973.png" style="position: absolute;"></a></div>
                     </div>
                     <div class="inner">
                        <div class="thumb" style="position: relative;">
                           <div class="photo" style="background-image:url(<?php echo isset($wmresult['image']) ? $wmresult['image'] : '' ?>);">
                           </div>
                           <div class="overlay"></div>
                        </div>
                        <div class="text">
                           <h3><a target="_blank" href="<?= $wmresult['link'] ?>"><?= $wmresult['name'] ?></a></h3>
                           <h4><?= "$".$wmresult['price'] ?></h4>
                        </div>
                     </div>
                     <?php
                        }
                        ?>                            
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php require_once('footer.php'); ?>