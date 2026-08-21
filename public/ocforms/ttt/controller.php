<?php
require_once "common_functions.php";
$action = $_GET["action"];
if ($action==="designs"){
	$data = fetchTableData("auto_designer_specification_master m left join auto_designer_master d on d.id=m.designer_name left join auto_itemtype_master i on i.id=m.item_type left join auto_gender_master g on g.id=m.gender left join auto_itemname_master it on it.id=m.item_name left join auto_composition_master_stock c on c.id=m.composition left join auto_colour_master cl on cl.id=m.colour left join auto_size_master s on s.id=m.sizes left join auto_embellishment_master e on e.id=m.embellishment left join auto_manufacturing_process_master mp on mp.id=m.manufacturing_process left join auto_manufacture_master man on man.id=m.manufecture left join auto_client_master cli on cli.id=m.client left join auto_craftsman_master cr on cr.id=craftsman", ["d.designername","i.itemtype","g.name as `gender`","it.itemname as `product_name`","c.composition_details", "cl.colourname","s.size","e.embellishmentname","mp.manufacturing_process", "man.name as `manufacturer`","cli.name as `client`","m.barcode","cr.name as `craftsman`", "m.sno","m.item_name","m.img_path"]);
    echo json_encode($data);
}
else if($action==="images"){
	$dir = $_GET['dir'];
	$image = getimagesfrompath($dir);
	echo $image;
}
?>