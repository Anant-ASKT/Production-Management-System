<?php
$content = file_get_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php');

$oldJs = <<<'JS'
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

$newJs = <<<'JS'
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
                    
                    const viewBtn = tr.querySelector('.btn-view-spec');
                    if (viewBtn) {
                        viewBtn.addEventListener('click', function() {
                            if (typeof openViewModal === 'function') {
                                openViewModal(specification);
                            }
                        });
                    }
                    
                    return tr;
                }
JS;

$content = str_replace($oldJs, $newJs, $content);
file_put_contents('resources/views/admin/ai_photo_enhancing/pending.blade.php', $content);
echo "Event listener added.";
