<?php
$content = file_get_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php');

$oldHtml = <<<HTML
        {{-- Cards --}}
        <div
            id="specificationCards"
            class="specification-grid">

        </div>
HTML;

$newHtml = <<<HTML
        {{-- Table --}}
        <div class="table-responsive p-3">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product Name</th>
                        <th>SKU</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th class="text-end" style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="specificationCards">
                </tbody>
            </table>
        </div>
HTML;

$content = str_replace($oldHtml, $newHtml, $content);

$newJs = <<<'JS'
                function createSpecificationCard(specification, index) {
                    const tr = document.createElement('tr');

                    const sku = specification.sku || '-';
                    const itemName = specification.item_name_text || specification.item_name || '-';
                    const colour = specification.colour_text || specification.colour || '-';
                    const size = specification.size_text || specification.sizes || '-';
                    const specId = specification.sno || specification.id || '';

                    tr.innerHTML = `
                        <td>${escapeHtml(itemName)}</td>
                        <td>${escapeHtml(sku)}</td>
                        <td>${escapeHtml(size)}</td>
                        <td>${escapeHtml(colour)}</td>
                        <td class="text-end">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary btn-view-spec"
                                data-id="${escapeHtml(specId)}">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </td>
                    `;
                    
                    return tr;
                }
JS;

$content = preg_replace('/function createSpecificationCard\([\s\S]*?return card;\s*\}/', $newJs, $content, 1);

file_put_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php', $content);
echo "Datatable structure applied.";
