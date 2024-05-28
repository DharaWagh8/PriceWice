
<h3><?php echo LANG_VALUE_49; ?></h3>
    <div id="left" class="span3">

        <ul id="menu-group-1" class="nav menu">
            <?php

                            $j=0;
                            $statement1 = $pdo->prepare("SELECT * FROM tbl_mid_category");
                            $statement1->execute();
                            $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($result1 as $row1) {
                                $j++;
                                ?>
                                <li class="deeper parent">
                                    <a class="" href="product-category.php?id=<?php echo $row1['mcat_id']; ?>&type=mid-category">
                                        <span data-toggle="collapse" data-parent="#menu-group-1" href="#cat-lvl2-id-<?php echo $i.$j; ?>" class="sign" aria-expanded="true"><i class="fa fa-plus"></i></span>
                                        <span class="lbl lbl1"><?php echo $row1['mcat_name']; ?></span> 
                                    </a>
                                    <ul class="children nav-child unstyled small collapse show in" id="cat-lvl2-id-<?php echo $i.$j; ?>">
                                        <?php
                                            $k=0;
                                            $statement2 = $pdo->prepare("SELECT * FROM tbl_end_category WHERE mcat_id=?");
                                            $statement2->execute(array($row1['mcat_id']));
                                            $result2 = $statement2->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result2 as $row2) {
                                                $k++;
                                                ?>
                                                <li class="item-<?php echo $i.$j.$k; ?>">
                                                    <a class="" href="product-category.php?id=<?php echo $row2['ecat_id']; ?>&type=end-category">
                                                        <span class="sign"></span>
                                                        <span class="lbl lbl1"><?php echo $row2['ecat_name']; ?></span>
                                                    </a>
                                                </li>
                                                <?php
                                            }
                                        ?>
                                    </ul>
                                </li>
                                <?php
                            }
                            ?>
                    <?php
        
            ?>
        </ul>

    </div>