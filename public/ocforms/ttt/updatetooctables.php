<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require('../../header.php');
if($companyid!=""){
?>
<style type="text/css">
.group {
  border: 1px solid #ccc;
  padding: 5px 15px 5px 15px;
  margin: 20px 0;
  border-radius: 5px;
  background-color: #f9f9f9;
}
.group>h3{
  color:#6a0101b3;
  text-decoration: underline;
  text-underline-position: under;
  margin-bottom:20px;
}
</style>
<div class="main">
    <div class="row">
        <div class="col-md-12 mt-1 mb-2 pt-3 pb-3 text-center" style="box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <span class="uppercase  bold" style="font-size: 1.75rem;text-shadow:2px 2px gray;">Update To OpenCart</span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mt-1 mb-2">
            <div class="flexgrid-container bg-light" id="gridContainer" style="width:100%;"></div>
        </div>
    </div>
</div>
<div class="modal" id="opencartformmodal" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="max-width: 80%;" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4>Mapped Data</h4>
          <button type="button" class="close" data-dismiss="modal">&#10006;</button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
                <label class="control-label">Manufacturer Name</label>
                <input type="text" class="form-control" id="manufacturer" />
                <section class="group">
                    <h3>Category Details</h3>
                    <label class="control-label">Main Category</label>
                    <input type="text" class="form-control" id="category" />
                    <label class="control-label">Sub Category</label>
                    <input type="text" class="form-control" id="subcategory" />
                    <label class="control-label">Product Type</label>
                    <input type="text" class="form-control" id="subsubcategory" />
                </section>
                <section class="group">
                    <h3>Product Details</h3>
                    <label class="control-label">Model No</label>
                    <input type="text" class="form-control" id="modelno" />
                    <label class="control-label">Product</label>
                    <input type="text" class="form-control" id="product" />
                    <label class="control-label">SKU</label>
                    <input type="text" class="form-control" id="sku" />
                </section>

                <section class="group">
                    <h3>Attributes</h3>
                    <label class="control-label">Colour</label>
                    <input type="text" class="form-control" id="color" />
                    <label class="control-label">Size</label>
                    <input type="text" class="form-control" id="size" />
                    <label class="control-label">Material Composition</label>
                    <input type="text" class="form-control" id="composition" />
                    <label class="control-label">Embellishment</label>
                    <input type="text" class="form-control" id="embellishment" />
                </section>

                <section class="group">
                    <h3>Descriptions</h3>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-deepblue btn-sm pull-right" id="aifetch">Fetch From AI</button>
                        </div>
                    </div>
                    <label class="control-label">Product Description</label>
                    <textarea class="form-control" id="productdescription" style="height: 150px;resize: none;"></textarea>
                    <label class="control-label">Meta Title</label>
                    <input type="text" class="form-control" id="metatitle" />
                    <label class="control-label">Meta Description</label>
                    <textarea class="form-control" id="metadescription" style="height: 150px;resize: none;"></textarea>
                </section>
            </div>
            <div class="col-md-6">
                <img src="" alt="Product Image" id="productimage" style="max-width: 100%; height: auto;" />
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
<?php
}
require('../../footer.php');
?>
<script type="text/javascript">
    $(document).ready(function(){
        $.ajax({
            type: "POST",
            url: "controller.php?action=designs",
            contentType: false,
            processData: false,
            cache: false,
            dataType: "json",
            success: function(res) {
                //console.info(res);
                const columns = [
                    { key: 'rowno', label: 'Sno', width: '5%',align:'center' },
                    { key:'barcode', label: 'Barcode', width:'15%'},
                    { key: 'item_name', label: 'Model No.', hidden:true },
                    { key: 'itemtype', label: 'Product Type',  width: '20%' },
                    { key: 'product_name', label: 'Product Name', width: '30%' },
                    { key: 'manufacturing_process', label: 'Category', hidden:true },
                    { key: 'gender', label: 'Sub-Category', hidden:true },
                    { key: 'colourname', label: 'Colour', width: '15%' },
                    { key: 'size', label: 'Size', width: '5%' },
                    { key: 'composition_details', label: 'Composition', hidden:true },
                    { key: 'embellishment', label: 'Embellishment', hidden:true },
                    { key: 'designername', label: 'Designer Name', hidden:true },
                    { key: 'craftsman', label: 'Craftsman', hidden:true },
                    { key: 'manufacturer', label: 'Manufacturer', width: '15%' }, //hidden:true
                    { key: 'client', label: 'Client', hidden:true },
                    { key: 'img_path', label: 'Image', hidden:true },
                    { key: 'sno', label: 'Id', hidden:true },
                    { key: 'h', label: '#', width: '10%' ,align:'center'}
                ];
                const updatedRes = res.map((item, index) => ({
                  ...item,
                  rowno: index + 1,
                  h:{ type: "button", label: "SaveTo OC", className:"btn btn-deepblue btn-sm", onclick: function (event) {
      SaveToOpenCart(event); } }
                }));
                const grid = new FlexGrid('gridContainer', columns, updatedRes,'customTableId',0,null,null,true,10,true);
                grid.cellpadding = '10px';
                grid.headerbackground = '#4CAF50';
                grid.rowbackground = '#ffffff';
                grid.rowhovercolor = '#d3d3d3';
                grid.bordercolor = '#ddd';
                grid.headertextcolor = '#fff';
                grid.alternaterowcolor = '#f9f9f9';
            }
        });
    });

    document.getElementById('aifetch').addEventListener('click', () => {
        const imageUrl = document.getElementById('productimage').getAttribute("src");
        const productInfo = {
            name: document.getElementById('product').value,
            color: document.getElementById('color').value,
            size: document.getElementById('size').value,
            composition: document.getElementById('composition').value
        };

        if (!imageUrl || !productInfo) {
            alert("Please provide both image URL and product info.");
            return;
        }

        fetch('vision-api-call.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                image_url: imageUrl,
                info: JSON.stringify(productInfo)
            })
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            var response = data.choices[0].message.content;
            const productDescription = extractSection(response, 'Product Description', 'Product Tags');
            const tagsRaw = extractSection(response, 'Product Tags', 'Meta Tag Title');
            const productTags = tagsRaw
                .split('\n')
                .map(tag => tag.replace(/^- /, '').trim())
                .filter(tag => tag !== '');

            const metaTitle = extractSection(response, 'Meta Tag Title', 'Meta Tag Description');
            const metaDescription = extractSection(response, 'Meta Tag Description', 'Meta Tag Keywords');

            const keywordsMatch = response.match(/\*\*Meta Tag Keywords:\*\*\s*(.*)/s);
            const metaKeywords = keywordsMatch ? keywordsMatch[1].split(',').map(k => k.trim()) : [];
        })
        .catch(err => {
            console.error(err);
            alert("Something went wrong.");
        });
    });

    function SaveToOpenCart(event){
        const button = event.target; 
        const selectedRow = button.closest('tr');
        var manufacturer = $(selectedRow).find("td:eq(13)").text();
        var sku = $(selectedRow).find("td:eq(1)").text();
        var modelno = $(selectedRow).find("td:eq(2)").text();
        var category = $(selectedRow).find("td:eq(5)").text();
        var sub_category = $(selectedRow).find("td:eq(6)").text();
        var sub_sub_category = $(selectedRow).find("td:eq(3)").text();
        var product = $(selectedRow).find("td:eq(4)").text();
        var att_basic_colour = $(selectedRow).find("td:eq(7)").text();
        var att_basic_size = $(selectedRow).find("td:eq(8)").text();
        var att_basic_composition = $(selectedRow).find("td:eq(9)").text();
        var att_basic_embellishment = $(selectedRow).find("td:eq(10)").text();
        var imagedir = $(selectedRow).find("td:eq(15)").text();
        var sno = $(selectedRow).find("td:eq(16)").text();
        alert(sku + " " + modelno + " " + category + " " + sub_category + " " + sub_sub_category + " " + product + " " + att_basic_colour + " " + att_basic_size + " " + att_basic_composition + " " + att_basic_embellishment + " " + manufacturer + " " + imagedir + " " + sno);
                $("#manufacturer").val(manufacturer);
                $("#category").val(category);
                $("#subcategory").val(sub_category);
                $("#subsubcategory").val(sub_sub_category);
                $("#modelno").val(modelno);
                $("#product").val(product);
                $("#sku").val(sku);
                $("#color").val(att_basic_colour);
                $("#size").val(att_basic_size);
                $("#composition").val(att_basic_composition);
                $("#embellishment").val(att_basic_embellishment);
                fetch('controller.php?action=images&dir='+imagedir)
                    .then(response => response.text())
                    .then(filename => {
                        console.info(filename);
                        if (filename.trim() !== '') {
                            $("#productimage").prop("src",filename);
                        } else {
                            $("#productimage").prop("src","");
                        }
                });
        $("#opencartformmodal").modal("show");
    }

function extractSection(text, start, end) {
    const regex = new RegExp(`\\*\\*${start}:\\*\\*[\\s\\n]*(.*?)\\s*\\*\\*${end}:\\*\\*`, 's');
    const match = text.match(regex);
    return match ? match[1].trim() : '';
}
</script>