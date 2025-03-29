<?php
$normal='class=""';
$red='class="text-danger fw-bold"'; 
$orange='class="text-warning fw-bold"'; 
$green='class="text-success fw-bold"'; 
$class_r=$normal;
$class_y=$normal;
$class_b=$normal;
$class_ir=$normal;
$class_iy=$normal;
$class_ib=$normal;

$class_pf=$normal;
$class_temp_1=$normal;
$class_temp_2=$normal;
$class_temp_3=$normal;
$class_temp_4=$normal;
$class_temp_5=$normal;
$class_load=$normal;
$class_load_r=$normal;
$class_load_y=$normal;
$class_load_b=$normal;
//$class_on_off_status=$normal;
$temp_fail=1;

$v_min_r=180;
$v_min_y=180;
$v_min_b=180;
$v_max_r=240;
$v_max_y=240;
$v_max_b=240;
$c_max_r=20;
$c_max_y=20;
$c_max_b=20;
$temp=45;
$pf1=0.85;
$pf2=-0.85;
$load=80;


$v_max_lr=230;
$v_max_ly=230;
$v_max_lb=230;
$pf2 = round((1 - $pf1 + 1)-2, 3);
?>