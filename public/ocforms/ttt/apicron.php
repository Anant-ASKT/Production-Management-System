<?php
require_once "common_functions.php";
$data = fetchTableData("auto_designer_specification_master m left join auto_designer_master d on d.id=m.designer_name left join auto_itemtype_master i on i.id=m.item_type left join auto_gender_master g on g.id=m.gender left join auto_itemname_master it on it.id=m.item_name left join auto_composition_master_stock c on c.id=m.composition left join auto_colour_master cl on cl.id=m.colour left join auto_size_master s on s.id=m.sizes", ["i.itemtype","g.name as `gender`","it.itemname as `product_name`","c.composition_details", "cl.colourname","m.img_path","group_concat(s.size) as sizes","group_concat(m.sno) as snos"],"ifnull(m.description_id,'')=''","group by itemtype, gender, product_name, composition_details, colourname order by itemtype,gender,product_name,composition_details,colourname limit 1");
foreach($data as $row){
	$productInfoArray = array("product_type"=>$row['itemtype'],"product_gender"=>$row['gender'],"name"=>$row['product_name'],"color"=>$row['colourname'],"composition"=>$row['composition_details']);
    $imagepath = $row['img_path'] ?? '';
    $imageUrl = base64_encode(file_get_contents($imagepath));

    $productInfoText = <<<EOD
        Based on this product image and the following details, generate the following content in the exact order and format:
        1. **Product Description** (about 100 words)
        2. **Product Tags** (comma-separated or bullet list)
        3. **Meta Tag Title**
        4. **Meta Tag Description**
        5. **Meta Tag Keywords** (comma-separated)
        6. **Recommended Product Name**
        7. **Corrected Composition**

        Product Info:
        EOD;
 
    foreach ($productInfoArray as $key => $value) {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        $productInfoText .= "\n- " . ucfirst($key) . ": " . trim($value);
    }
	$response = ChatGPTAPI($productInfoText,$imageUrl);
	$data = json_decode($response, true);
    $result = $data['choices'][0]['message']['content'] ?? '';
    $productDescription = extractSection($result, 'Product Description', 'Product Tags');
    $tagsRaw = extractSection($result, 'Product Tags', 'Meta Tag Title');
    $productTags = implode(', ', array_filter(array_map(function ($tag) {
        return trim(preg_replace('/^- /', '', $tag));
    }, explode("\n", $tagsRaw))));

    $metaTitle = extractSection($result, 'Meta Tag Title', 'Meta Tag Description');
    $metaDescription = extractSection($result, 'Meta Tag Description', 'Meta Tag Keywords');

    $keywordsMatch = extractSection($result,'Meta Tag Keywords','Recommended Product Name')
    $metaKeywords = !empty($keywordsMatch) ? implode(', ', array_map('trim', explode(',', $keywordsMatch))) : '';

    $productName = extractSection($result, 'Recommended Product Name', 'Corrected Composition');
    $CorrectComposition = extractSection($result,'Corrected Composition');

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $oc_conn->begin_transaction();
    try{
        foreach($row["snos"] as $productid){
            $oc_conn->query("INSERT INTO tblproduct_description (product_id,product_description,product_tags,meta_title,meta_description,meta_keywords,recommended_name,corrected_composition)values('".$productid."','".$productDescription."','".$productTags."','".$metaTitle."','".$metaDescription."','".$metaKeywords."','".$productName."','".$CorrectComposition."')");
            $insert_id= oc_conn->insert_id;
            $oc_conn->query("UPDATE auto_designer_specification_master set description_id = '".$insert_id."' where sno='".$productid."'");
        }
        $oc_conn->commit();
    }
    catch (mysqli_sql_exception $e) {
        $oc_conn->rollback();
    }
}
?>