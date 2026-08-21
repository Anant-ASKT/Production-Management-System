<?php
require_once "config.php";

function fetchTableData($tableName, $fields = ['*'], $condition = "", $orderby = "") {
	global $oc_conn;
    $tableName = $oc_conn->real_escape_string($tableName);
    if ($fields === ['*']) {
        $fieldsString = '*';
    } else {
        //$fieldsString = implode(", ", array_map([$conn, 'real_escape_string'], $fields));
         $fieldsString = implode(", ",  $fields);
    }

    $query = "SELECT $fieldsString FROM $tableName";

    if (!empty($condition)) {
        $query .= " WHERE $condition";
    }
    
    if (!empty($orderby)) {
        $query .= " order by $orderby";
    }
 /*   if(count($fields)>5)
    echo $query;*/
    $result = $oc_conn->query($query);
    
    if (!$result) {
        return ["error" => $oc_conn->error];
    }
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $result->free();
    //print_r($data);
    return $data;
}

function insertOrUpdateData($tableName, $data, $update=true) {
    global $oc_conn;
    if (!is_array($data) || empty($data)) {
        return "Error: Data must be a non-empty associative array.";
    }

    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), "?"));
    $values = array_values($data);

    $updateColumns = [];
    foreach (array_keys($data) as $column) {
        $updateColumns[] = "$column = VALUES($column)";
    }
    $updateClause = implode(", ", $updateColumns);

    if($update){
        $sql = "INSERT INTO $tableName ($columns) VALUES ($placeholders) ON DUPLICATE KEY UPDATE $updateClause";
    }
    else{
       $sql = "INSERT INTO $tableName ($columns) VALUES ($placeholders)"; 
    }

    $stmt = $oc_conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $oc_conn->error);
    }

    $types = str_repeat("s", count($values)); 
    if (!$stmt->bind_param($types, ...$values)) {
        throw new Exception("Binding parameters failed: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        throw new Exception("Execution failed: " . $stmt->error);
    }

    $affectedRows = $stmt->affected_rows;
    
    if ($affectedRows === 1) {
        return "Inserted";
    } elseif ($affectedRows === 2) {
        return "Updated";
    } elseif ($affectedRows === 0) {
        return "No Change";
    }

    return "Unknown Status";
}

function getmaxidFromTable($tableName,$idcol){
    $colname = array("ifnull(max(".$idcol."),0) as ".$idcol);
    $data = fetchTableData($tableName,$colname);
    return $data[0][$idcol];
}

function arrayToHtmlTable($tblid,$data,$titleheader="") {
    if (empty($data) || !is_array($data)) {
        return "<p>No data available</p>";
    }
    $headers = array_keys($data[0]);
    $table = "<table border='1' class='stripedalt tabel-hover table-mobile' id='".$tblid."'>";
    if(!empty($titleheader)){
        $table .= $titleheader;
    }
    $table .= "<thead>";
    $table .= "<tr>";
    $table .= "<th>SNo.</th>";
    foreach ($headers as $header) {
        $table .= "<th>" . htmlspecialchars($header) . "</th>";
    }
    $table .= "</tr></thead><tbody>";
     $i=0;
    foreach ($data as $row) {
        $table .= "<tr>";
         $table .= "<td data-label='SNo.'>" . ++$i . "</td>";

        foreach ($headers as $index => $header) {
            $cell = $row[$header];
            $center = is_numeric($cell) ? " style='text-align:center;'" : "";
            $table .= "<td data-label='" . htmlspecialchars($header) . "'" . $center . ">" . htmlspecialchars($cell) . "</td>";
        }
        $table .= "</tr>";
    }
    
    $table .= "</tbody></table>";
    
    return $table;
}

function hasAnyNonEmptyValue(array $data): bool {
    foreach ($data as $value) {
        if (trim((string)$value) !== '') {
            return true;
        }
    }
    return false;
}

function setExportButtons($reportdiv,$table,$filename){
    $html = "<div class='row justify-content-end'>
                <button class=\"btn btn-light m-2 border border-black\" onclick=\"PrintHtml('".$reportdiv."');\">
                    <i class=\"fa fa-print\" style=\"color:grey;\"></i> Print
                </button>
                <button class=\"btn btn-light m-2 border border-black\" onclick=\"generatemultitablePDF('".$reportdiv."','".$filename."');\">
                    <i class=\"fa fa-file-pdf\" style=\"color:red;\"></i> PDF
                </button>
                <button class=\"btn btn-light m-2 border border-black\" id=\"btnexpn4main\" onclick=\"gridtoexcel_withfilter_chunk('".$table."','".$filename."','landscape',function(){});\">
                    <i class=\"fa fa-file-excel\" style=\"color:green;\"></i> Excel
                </button>
            </div>";
    return $html;
}

function addNameValuesToArray($dataArray, $tableName, $idColumn, $nameColumn) {
    global $oc_conn;
    if (empty($dataArray)) return array(); // return early if empty

    // Step 1: Extract unique IDs
    $ids = array();
    foreach ($dataArray as $row) {
        if (isset($row[$idColumn])) {
            $ids[] = $row[$idColumn];
        }
    }
    $ids = array_unique($ids);
    $idsList = implode(",", array_map('intval', $ids)); // sanitize

    // Step 2: Fetch names from master table
    $query = "SELECT `$idColumn`, `$nameColumn` FROM `$tableName` WHERE `$idColumn` IN ($idsList)";
    $result = mysqli_query($oc_conn, $query) or die("Lookup query failed: " . mysqli_error($oc_conn));

    $idNameMap = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $idNameMap[$row[$idColumn]] = $row[$nameColumn];
    }

    // Step 3: Add Namevalue to original array
    foreach ($dataArray as &$row) {
        $row['Namevalue'] = isset($row[$idColumn], $idNameMap[$row[$idColumn]]) 
            ? $idNameMap[$row[$idColumn]] 
            : null;
    }

    return $dataArray;
}

function getimagesfrompath($dir){
    $images = glob($dir . '*.{jpg,jpeg,JPG,JPEG}', GLOB_BRACE);
    if (!empty($images)) {
        return $images[0]; 
    } else {
        return '';
    }
}
?>