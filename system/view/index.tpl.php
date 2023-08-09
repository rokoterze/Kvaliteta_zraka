<br><strong style="font-size: 25px; color: #B000FF;">Dobrodošli u MVC aplikaciju!</strong><br><br>
<hr style="border: 2px solid lightblue;">

<?php 
foreach($data['url_links'] as $key => $data){?>
    
    <strong style="color: #B000FF;">API id: </strong><strong><?=$key?></strong>
    <strong style="color: #B000FF;">[<?=$data['controller']?>]<br><br></strong> 

        &nbsp&nbsp<strong style="color: blue;">URL [example] => </strong><strong> http://localhost/kvaliteta_zraka/?page=<?=$data['urlPath']?></strong><br><br>
        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<strong style="color: grey;">Description: </strong><strong style="color: red"><?=$data['description']?></strong><br><br>
        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<strong style="color: grey;">Parameters [<strong style="color: red;">*</strong>Required]: </strong><?=$data['parameters']?><br>
    <hr style="border: 2px solid lightblue;">

<?php
}